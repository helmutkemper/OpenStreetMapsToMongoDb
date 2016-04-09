<?php

  session_start();

  include_once "./size.class.php";

  include_once "./osmXmlToMongoDb.class.php";
  include_once "./osmXmlToMongoDbSupport.class.php";

  include_once "./genericMysql.class.php";
  include_once "./db.class.php";

  include_once "./userMySqlSupport.class.php";
  include_once "./user.class.php";

  include_once "./osmXmlMySqlSupport.class.php";
  include_once "./osmXml.class.php";

  include_once "./gisDrawMySqlSupport.class.php";
  include_once "./gisDraw.class.php";

  include_once "./wikiOsmGetData.class.php";

  include_once "./class/graphic/bresenham.class.php";
  include_once "./class/graphic/imageText.class.php";

  error_reporting( E_ERROR | E_WARNING | E_PARSE );
  //set_time_limit( 15 );
  ignore_user_abort( false );

  //new wikiOsmGetData( "http://wiki.openstreetmap.org/wiki/Map_Features" );

  $drawLObj = new gisDraw();
  $drawLObj->connect();
  $drawLObj->setDataBase( "qconsp" );
  try
  {
    $drawLObj->setImageSavePath( "./map_test.png", true );
    $drawLObj->setImageType( "png" );
    $drawLObj->setRGBA( 0xA4, 0xA4, 0xA4, 0 );
    //$drawLObj->setCenter( -8.094567, -34.914875, 100 );
    $drawLObj->setCenter( -8.114946, -34.895026, 100 );
    //$drawLObj->setCenter( -8.123032, -34.920540, 10 );
    //$drawLObj->setCenter( -23.54869, -46.63517, 10 );

    // Fernando de Noronha
    //$drawLObj->setCenter( -3.853993, -32.425251, 200 );
    //$drawLObj->setCenter( -23.9256882, -52.6576609, 200 );
    //$drawLObj->draw( 1000, 1000, 15 ); // 6000
    $drawLObj->draw( 6000, 15 ); // 6000
  }
  catch( Exception $e )
  {
    var_dump( $e->getMessage() );
  }