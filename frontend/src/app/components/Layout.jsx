import Head from 'next/head';

export default function Layout({ children }) {
  return (
    <>
      <Head>
        <meta charset="utf-8" />
        <title>Conduit</title>
        <link href="//code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" />
        <link href="//fonts.googleapis.com/css?family=Titillium+Web:700|Source+Serif+Pro:400,700|Merriweather+Sans:400,700|Source+Sans+Pro:400,300,600,700" rel="stylesheet" />
      </Head>
      <body>
        {children}
      </body>
    </>
  );
}