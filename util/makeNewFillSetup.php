<?php

  $conn = new MongoClient();
  $db   = $conn->qconsp;
  $coll = $db->_setupFill;
  $newColl = $db->setupFill;


  $cur  = $coll->find();

  foreach( $cur as $data )
  {
    unset( $data[ "_id" ], $data[ "visible_zoom_bigger_than" ] );

    $dataToInsert = $data;

    for( $i = 1; $i != 31; $i += 1 )
    {
      $dataToInsert[ "humamType" ] = $data[ "humam_type" ];
      $dataToInsert[ "pointKey" ] = $data[ "point_key" ];
      $dataToInsert[ "pointValue" ] = $data[ "value" ];
      $dataToInsert[ "imageTile" ] = $data[ "image_tile" ];
      $dataToInsert[ "zoomFactor" ] = new MongoInt64( $i );
      $dataToInsert[ "thickness" ] = new MongoInt64( ( int ) ( $data[ "thickness" ] * 1 ) );
      $dataToInsert[ "thicknessAlphaFactor" ] = new MongoInt64( 0 );
      $dataToInsert[ "colorRed" ] = new MongoInt64( ( int )$data[ "color_red" ] );
      $dataToInsert[ "colorGreen" ] = new MongoInt64( ( int )$data[ "color_green" ] );
      $dataToInsert[ "colorBlue" ] = new MongoInt64( ( int )$data[ "color_blue" ] );
      $dataToInsert[ "colorAlpha" ] = new MongoInt64( ( int )$data[ "color_alpha" ] );
      $dataToInsert[ "layerAdd" ] = new MongoInt64( 0 );
      $dataToInsert[ "setupId" ] = new MongoInt64( 0 );
      $dataToInsert[ "visible" ] = true;

      /*
      $dataToInsert[ "textVisible" ] = false;
      $dataToInsert[ "textFont" ] = "./fonts/arial.ttf";
      $dataToInsert[ "textSize" ] = new MongoInt64( 10 );
      $dataToInsert[ "textColorRed" ] = new MongoInt64( 0 );
      $dataToInsert[ "textColorGreen" ] = new MongoInt64( 0 );
      $dataToInsert[ "textColorBlue" ] = new MongoInt64( 0 );
      $dataToInsert[ "textColorAlpha" ] = new MongoInt64( 0 );
      */

      unset( $dataToInsert[ "color_red" ], $dataToInsert[ "color_green" ], $dataToInsert[ "color_blue" ], $dataToInsert[ "color_alpha" ], $dataToInsert[ "image_tile" ] );
      unset( $dataToInsert[ "humam_type" ], $dataToInsert[ "point_key" ], $dataToInsert[ "value" ] );

      $newColl->insert( $dataToInsert );

      unset( $dataToInsert[ "_id" ] );

    }
  }