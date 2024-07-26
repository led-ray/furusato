import React, { useState, useEffect, ReactElement } from 'react';
import axios from 'axios';
import { GeoJSON, MapContainer, ScaleControl, TileLayer, ZoomControl } from 'react-leaflet';
import { Col, Container, Row, Spinner } from 'react-bootstrap';
import L, { LatLng, Layer } from 'leaflet';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'leaflet/dist/leaflet.css';
import './App.css';
import ReactDOMServer from 'react-dom/server';
import { Feature, GeoJsonObject, GeometryObject, GeoJsonProperties } from 'geojson';
import { openDB } from 'idb';
import { feature } from 'topojson-client';

const DB_NAME = 'GeoJSONCache';
const STORE_NAME = 'GeoJSONStore';

const initDB = async () => {
  return openDB(DB_NAME, 1, {
    upgrade(db) {
      db.createObjectStore(STORE_NAME);
    },
  });
};

const useTopoJSONData = (url) => {
  const [area, setArea] = useState<GeoJsonObject | undefined>(undefined);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const handleError = (error: any) => {
      setIsLoading(false);
      console.error(error);
    };

    const getData = async () => {
      setIsLoading(true);

      try {
        const db = await initDB();
        const tx = db.transaction(STORE_NAME, 'readonly');
        const store = tx.objectStore(STORE_NAME);

        // IndexedDBからデータを取得
        const cachedData = await store.get(url);
        if (cachedData) {
          setArea(cachedData);
          setIsLoading(false);
          return;
        }

        // データを取得してIndexedDBに保存
        const result = await axios.get(url);
        const topoJSONData = result.data;
        const geoJSONData = feature(topoJSONData, topoJSONData.objects[Object.keys(topoJSONData.objects)[0]]);
        setArea(geoJSONData);
        const txWrite = db.transaction(STORE_NAME, 'readwrite');
        const storeWrite = txWrite.objectStore(STORE_NAME);
        await storeWrite.put(geoJSONData, url);
      } catch (error) {
        handleError(error);
      } finally {
        setIsLoading(false);
      }
    };

    getData();
  }, [url]);

  return [area, isLoading];
};

const Top: React.FC = () => {
  const initialPosition = new LatLng(35.7, 139.5);
  const [polygonVisible, setPolygonVisible] = useState(false);
  const [products, setProducts] = useState([]);
  const [displayProperty, setDisplayProperty] = useState("人口");
  const [geojsonKey, setGeojsonKey] = useState(0);

  // TopoJSONデータの取得
  const [geojsonData, isPolygonLoading] = useTopoJSONData('/gisdata.topojson');

  // 返礼品情報を画面に表示する関数
  const displayProducts = (products) => {
    setProducts(products);
  };

  const fetchRakutenProducts = async (locationName: string) => {
    const endpoint = 'http://localhost:8080/api/rakuten/search';
    const params = {
      keyword: locationName + ' ふるさと納税'
    };

    try {
      const response = await axios.get(endpoint, { params });
      const products = response.data.Items;
      console.log('Fetched products:', products);
      displayProducts(products);
    } catch (error) {
      console.error('Error fetching products:', error);
    }
  };

  // 市町村名を引数として受け取る関数
  const handleFeatureClick = (locationName: string) => {
    console.log('Fetching products for:', locationName);
    fetchRakutenProducts(locationName);
  };

  // 地物毎の処理
  const onEachPolygonFeature: OnEachFeature = (feature, layer) => {
    const fp = feature.properties;
    const location_name = (fp && fp.市町村) ? decodeURIComponent(fp.市町村) : '-';
    const display_value = (fp && fp[displayProperty]) ? decodeURIComponent(fp[displayProperty]) : '-';

    // クリックイベントのハンドラを追加
    layer.on({
      click: () => {
        console.log('Clicked feature:', feature);
        console.log('Location name:', location_name);
        handleFeatureClick(location_name);

        const element: ReactElement = (
          <Container className="popup-container">
            <Row className="row-style-narrow">
              <Col className="popup-title">{location_name}</Col>
            </Row>
            <Row className="row-style-narrow">
              <Col className="col-title">{displayProperty}</Col>
              <Col xs={6}>{display_value}</Col>
            </Row>
          </Container>
        );
        layer.bindPopup(`${ReactDOMServer.renderToString(element)}`, {
        }).openPopup();
      }
    });
  };

  let quantiles = [];
  if (geojsonData) {
    quantiles = calculateQuantiles(geojsonData, displayProperty);
  }

  const handleButtonClick = (property: string) => {
    setDisplayProperty(property);
    setPolygonVisible(true);
    setGeojsonKey(prevKey => prevKey + 1);
  };

  return (
    <>
      <div className="top">
        {isPolygonLoading && (
          <div className="loading">
            <Spinner animation="border" role="status" variant="light" className="spinner" />
          </div>
        )}

        <MapContainer
          zoom={10}
          zoomControl={false}
          center={initialPosition}
          tap={false}
          preferCanvas={true}
          renderer={L.canvas()}
        >
          <ScaleControl position="bottomright" imperial={false} />
          <ZoomControl position="bottomright" />
          <TileLayer
            attribution='© <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noreferrer">OpenStreetMap</a>'
            url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
          />

          {polygonVisible && geojsonData && (
            <GeoJSON
              key={geojsonKey}
              data={geojsonData}
              onEachFeature={onEachPolygonFeature}
              style={(feature) => confirmedStyle(feature?.properties, quantiles, displayProperty)}
            />
          )}

          <div className="category-container">
            <ul className='category-list'>
              <li onClick={() => handleButtonClick("人口")}>
                人口
              </li>
              <li onClick={() => handleButtonClick("学校")}>
                学校数
              </li>
              <li onClick={() => handleButtonClick("福祉施設")}>
                福祉施設数
              </li>
              <li onClick={() => handleButtonClick("地震予測")}>
                地震予測(30年以内,震度6以上の確率%)
              </li>
            </ul>
          </div>
        </MapContainer>

        <div className="product-container">
          <h2>返礼品一覧</h2>
          <ul className="product-list">
            {products.map((product, index) => (
              <li key={index} className="product-item">
                <a href={product.Item.itemUrl} target="_blank" rel="noopener noreferrer">
                  <img src={product.Item.mediumImageUrls[0].imageUrl} alt={product.Item.itemName} />
                  <p>{product.Item.itemName}</p>
                </a>
              </li>
            ))}
          </ul>
        </div>
      </div>
    </>
  );
};

type OnEachFeature = (feature: Feature<GeometryObject, GeoJsonProperties>, layer: Layer) => void;
type Style = {
  fillColor: string | undefined,
  color: string | undefined,
  weight: number | undefined
}

interface FeatureProperties {
  人口: number;
  学校数: number;
  福祉施設: number;
  地震予測: number;
}

function getQuantileStyle(value, quantiles) {
  if (value <= quantiles[0]) {
    return { fillColor: "#000000", color: "#000000", weight: 1.0 };
  } else if (value <= quantiles[1]) {
    return { fillColor: "#ffbfbf", color: "#000000", weight: 1.0 };
  } else if (value <= quantiles[2]) {
    return { fillColor: "#ff8080", color: "#000000", weight: 1.0 };
  } else if (value <= quantiles[3]) {
    return { fillColor: "#ff4040", color: "#000000", weight: 1.0 };
  } else {
    return { fillColor: "#ff0000", color: "#000000", weight: 1.0 };
  }
}

function confirmedStyle(fp: GeoJsonProperties | null, quantiles: number[], displayProperty: string): Style {
  const value = fp ? fp[displayProperty] : 0;
  return getQuantileStyle(value, quantiles);
}

function calculateQuantiles(data: GeoJsonObject, property: string) {
  const values = data.features.map((feature) => feature.properties[property]);
  values.sort((a, b) => a - b);
  const quantiles = [
    values[Math.floor(values.length * 0.2)],
    values[Math.floor(values.length * 0.4)],
    values[Math.floor(values.length * 0.6)],
    values[Math.floor(values.length * 0.8)]
  ];
  return quantiles;
}

type UseGeoJSONData = (url: string) => [GeoJsonObject | undefined, boolean];

export default Top;
