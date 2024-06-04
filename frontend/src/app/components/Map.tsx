"use client";

import React, { ReactElement, useEffect, useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'leaflet/dist/leaflet.css';
import './App.css';
import L, { LatLng, Layer } from 'leaflet';
import { GeoJSON, MapContainer, ScaleControl, TileLayer, ZoomControl } from 'react-leaflet';
import { Col, Container, Row, Card, Spinner } from 'react-bootstrap';
import axios from 'axios';
import ReactDOMServer from 'react-dom/server';
import { Feature, GeoJsonObject, GeometryObject, GeoJsonProperties } from 'geojson';

const Map: React.FC = () => {
  const [isWaiting, setIsWaiting] = useState(true);
  useEffect(() => {
    setTimeout(() => {
      setIsWaiting(false);
    }, 400);
  }, []);
  return (
    <>
      {isWaiting ? (
        <div className="loading">
          <Spinner animation="border" role="status" variant="light" className="spinner" />
        </div>
      ) : (
        <Top />
      )}
    </>
  );
};

const Top: React.FC = () => {
  const initialPosition = new LatLng(35.7, 139.5);
  const [polygonVisible, setPolygonVisible] = useState(false);
  const [polygonData, isPolygonLoading] = useGeoJSONData("/test.geojson", polygonVisible);

  return (
    <>
      <div className="top">
        {isPolygonLoading && (
          <div className="loading">
            <Spinner animation="border" role="status" variant="light" className="spinner" />
          </div>
        )}
        <div>
          <Card>
            <Card.Header>
              <span>ふるさとknow they（仮名）</span>
            </Card.Header>
            <ul className='category-list'>
              <li onClick={() => { setPolygonVisible(!polygonVisible) }} >
                人口
              </li>
            </ul>
          </Card>
        </div>

        <MapContainer
          zoom={13}
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
          {polygonVisible && polygonData && (
            <GeoJSON
              data={polygonData}
              onEachFeature={onEachPolygonFeature}
              style={(feature) => confirmedStyle(feature?.properties)}
            />
          )}
        </MapContainer>
      </div>
    </>
  );
};

type OnEachFeature = (feature: Feature<GeometryObject, GeoJsonProperties>, layer: Layer) => void;
type Style = {
  fillcolor: string | undefined,
  color: string | undefined,
  weight: number | undefined
}

// 地物毎の処理
const onEachPolygonFeature: OnEachFeature = (feature, layer) => {
  const fp = feature.properties;
  const location_name = (fp && fp.N03_007) ? decodeURIComponent(fp.N03_007) : '-';
  const element: ReactElement = (
    <Container className="container">
      <Row className="row-style-narrow">
        <Col className="col-title">人口</Col>
        <Col xs={6}>{location_name}</Col>
      </Row>
    </Container>
  );
  layer.bindPopup(`${ReactDOMServer.renderToString(element)}`, {
    maxHeight: 450,
  });
};

interface FeatureProperties {
  N03_007: number;
}

function confirmedStyle(fp: GeoJsonProperties | null): Style {
  const confirmed = fp.N03_007;
  if (confirmed < 12000) {
    return { fillcolor: "#ffb8b8" ,color: "#000000" ,weight: 1.0 };
  } else if (confirmed < 14000) {
    return { fillcolor: "#ff9999" ,color: "#000000" ,weight: 1.0 };
  } else if (confirmed < 15000) {
    return { fillcolor: "#ff3100" ,color: "#000000" ,weight: 1.0 };
  } else {
    return { fillcolor: "#000000" ,color: "#000000" ,weight: 1.0 };
  }
}

type UseGeoJSONData = (url: string, showToggle: boolean) => [GeoJsonObject | undefined, boolean];
const useGeoJSONData: UseGeoJSONData = (url, showToggle) => {
  const [area, setArea] = useState<GeoJsonObject | undefined>(undefined);
  const [isLoading, setIsLoading] = useState(false);

  useEffect(() => {
    const handleError = (error: any) => {
      setIsLoading(false);
      console.error(error);
    };

    const getData = async () => {
      setIsLoading(true);
      await axios.get(url)
        .then((result) => setArea(result.data))
        .catch((error) => handleError(error));
      setIsLoading(false);
    };

    if (showToggle && area == null) {
      getData();
    }
  }, [showToggle]);

  return [area, isLoading];
}

export default Map;
