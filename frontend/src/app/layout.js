"use client";

import Header from "./components/Header";
import Footer from "./components/Footer";
import 'leaflet/dist/leaflet.css';
import Map from "./components/Map.tsx";

export default function Layout({ children }) {
  return (
    <html lang="ja">
        <head>
        <meta charset="utf-8" />
        <title>ふるさとknow they（仮名）</title>
        <link
          href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css"
          rel="stylesheet"
          type="text/css"
        />
        <link
          href="//fonts.googleapis.com/css?family=Titillium+Web:700|Source+Serif+Pro:400,700|Merriweather+Sans:400,700|Source+Sans+Pro:400,300,600,700,300italic,400italic,600italic,700italic"
          rel="stylesheet"
          type="text/css"
        />

        <link rel="stylesheet" href="//demo.productionready.io/main.css" />
      </head>
      <body>
      <div className="App">
        <Map />
      </div>
      </body>
    </html>
  );
}