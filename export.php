<?php

  $mDbConnLObj       = new MongoClient();
  $mDbDBaseLObj      = $mDbConnLObj->test;
  $mDbCollectionLObj = $mDbDBaseLObj->setupFill;

  $mySqlConnectionLObj = new mysqli( "localhost", "root", "" );
  if ( $mySqlConnectionLObj->connect_errno > 0 )
  {
    die ( "Connection error / Erro de conexao: {$mySqlConnectionLObj->connect_error}" );
  }
  
  $mySqlConnectionLObj->select_db( "pe_osm" );

  $queryLObj = $mySqlConnectionLObj->query( "SELECT * FROM map_fill ORDER BY id ASC" );
  while( $lineLObj = $queryLObj->fetch_object() )
  {
    $mDbCollectionLObj->insert(
      array(
        "humam_type"               => utf8_encode( $lineLObj->humam_type ), //str
        "visible_zoom_bigger_than" => ( int ) $lineLObj->visible_zoom_bigger_than, //uint
        "point_key"                => utf8_encode( $lineLObj->point_key ), //str
        "value"                    => utf8_encode( $lineLObj->value ), //str
        "element"                  => utf8_encode( $lineLObj->element ), //str
        "comment"                  => utf8_encode( $lineLObj->comment ), //str
        "rendering"                => utf8_encode( $lineLObj->rendering ), // str
        "photo"                    => utf8_encode( $lineLObj->photo ), // str
        "thickness"                => ( int ) $lineLObj->thickness, //uint
        "style"                    => utf8_encode( $lineLObj->style ), // str
        "image_tile"               => utf8_encode( $lineLObj->image_tile ), // str
        "type"                     => utf8_encode( $lineLObj->type ), // str
        "color_red"                => ( int ) $lineLObj->color_red, // uint
        "color_green"              => ( int ) $lineLObj->color_green, // uint
        "color_blue"               => ( int ) $lineLObj->color_blue, // uint
        "color_alpha"              => ( int ) $lineLObj->color_alpha // uint
      )
    );
  }