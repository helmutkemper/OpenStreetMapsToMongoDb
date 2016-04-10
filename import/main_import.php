<?php
  //?start_debug=1&send_sess_end=1&debug_start_session=1&debug_session_id=12019&debug_port=10137&debug_host=127.0.1.1%2C127.0.0.1
  session_start();

  include_once "../class/XML/osmXmlToMongoDb.class.php";
  include_once "../class/XML/osmXmlToMongoDbSupport.class.php";
  include_once "../class/XML/osmXml.class.php";

  include_once "../class/size.class.php";

  error_reporting( E_ERROR | E_WARNING | E_PARSE );
  set_time_limit( 0 );
  ignore_user_abort( false );

  $parserXmlLObj = new osmXml( size::MByte( 5 ) );
  $parserXmlLObj->connect();
  $parserXmlLObj->setDataBase( "qconsp" );
  $parserXmlLObj->createIndex();
  //$parserXmlLObj->processOsmFile( "../osm/brazil-latest.osm" );

  //todo: se nodes tags array = 0 inserir?
  //$parserXmlLObj->concatenateNodeData();
  //$parserXmlLObj->concatenateWayTagsAndNodes();
