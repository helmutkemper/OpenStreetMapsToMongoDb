<?php

  class gisDraw extends osmXmlToMongoDb
  {
    const LATITUDE  = 0;
    const LONGITUDE = 1;

    private $backGroundRedCUInt   = 255;
    private $backGroundGreenCUInt = 255;
    private $backGroundBlueCUInt  = 255;
    private $backGroundAlphaCUInt = 0;
    private $backGroundHeaderType = "Content-Type: image/png";
    private $imageSavePathCStr    = null;
    private $imageSaveAndShowCBol = true;
    private $latitudeCenterCSFlt = -8.089557;
    private $longitudeCenterCSFlt = -34.910497;
    private $lengthMetersFromCenterCSInt = 1000;

    private $CoastLineIdNotInCArr = array();
    private $CoastLineNodesLArr = array();

    private $setupMapCursorCObj;
    private $imageResourceCArr = array();
    private $mapConstructLineStyleCArr = array();

    private $nameOfStreet = array();

    public function __construct ()
    {

    }

    public function setImageSavePath( $imageSavePathAStr, $imageSaveAndShowCBol = true )
    {
      $this->imageSavePathCStr    = $imageSavePathAStr;
      $this->imageSaveAndShowCBol = $imageSaveAndShowCBol;
    }

    public function setImageType( $typeAStr )
    {
      switch( strtolower( $typeAStr ) )
      {
        case "png":
          $this->backGroundHeaderType = "Content-Type: image/png";
          break;

        case "gif":
          $this->backGroundHeaderType = "Content-Type: image/gif";
          break;

        case "wbmp":
          $this->backGroundHeaderType = "Content-Type: image/wbmp";
          break;

        case "jpeg":
        case "jpg":
          $this->backGroundHeaderType = "Content-Type: image/jpeg";
          break;

        default:
          throw new Exception( "Error: gisDraw::setImageType( type must be 'png', 'gif', 'wbmp' or 'jpeg/jpg' );" );
      }
    }

    public function setRGBA( $backGroundRedAUInt, $backGroundGreenAUInt, $backGroundBlueAUInt, $backGroundAlphaAUInt )
    {
      $this->backGroundRedCUInt   = $backGroundRedAUInt;
      $this->backGroundGreenCUInt = $backGroundGreenAUInt;
      $this->backGroundBlueCUInt  = $backGroundBlueAUInt;
      $this->backGroundAlphaCUInt = $backGroundAlphaAUInt;
    }

    public function setRed( $backGroundRedAUInt )
    {
      $this->backGroundRedCUInt = $backGroundRedAUInt;
    }

    public function setGreen( $backGroundGreenAUInt )
    {
      $this->backGroundGreenCUInt = $backGroundGreenAUInt;
    }

    public function setBlue( $backGroundBlueAUInt )
    {
      $this->backGroundBlueCUInt = $backGroundBlueAUInt;
    }

    public function setAlpha( $backGroundAlphaAUInt )
    {
      $this->backGroundAlphaCUInt = $backGroundAlphaAUInt;
    }

    public static function degreesToRadians ( $degreesASFlt )
    {
      return M_PI / 180 * $degreesASFlt;
    }

    public function setCenter( $latitudeASFlt, $longitudeASFlt, $lengthMetersASInt = 100 )
    {
      $this->latitudeCenterCSFlt         = $latitudeASFlt;
      $this->longitudeCenterCSFlt        = $longitudeASFlt;
      $this->lengthMetersFromCenterCSInt = $lengthMetersASInt;
    }

    public function getCoastLine( $waysCollectionLObj, $zoomFactorAUInt, $fator )
    {
      if( !is_array( $this->CoastLineIdNotInCArr ) )
      {
        $this->CoastLineIdNotInCArr = array();
      }

      $waysCursorLObj = $waysCollectionLObj->find(
        array(
          Array
          (
            '_id' => array(
              '$nin' => $this->CoastLineIdNotInCArr
            ),
            'tags.natural.val' => 'coastline',
            'nodes' => array(
              '$geoWithin' => array(
                '$box' => array(
                  array( $this->latitudeCenterCSFlt - ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ), $this->longitudeCenterCSFlt + ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ) ),
                  array( $this->latitudeCenterCSFlt + ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ), $this->longitudeCenterCSFlt - ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ) )
                )
              )
            )
          )
        )
      );

      foreach( $waysCursorLObj as $waysKeyLObj => $waysDataLObj ){
        $coastLineDataLArr[] = $waysDataLObj;
      }

      $maxInteractionCounter = count( $coastLineDataLArr ) * 2;
      $coastLineFinalDataLArr = $coastLineDataLArr[ 0 ][ "nodes" ];
      $nodeFirst = $coastLineDataLArr[ 0 ][ "nodeFirst" ];
      $nodeLast = $coastLineDataLArr[ 0 ][ "nodeLast" ];
      unset( $coastLineDataLArr[ 0 ] );

      for( $i = 0; $i != $maxInteractionCounter; $i += 1 ){
        foreach( $coastLineDataLArr as $coastLineIndividualKeyLArr => $coastLineIndividualDataLArr ){

          if( ( $nodeFirst[ 0 ] = $coastLineIndividualDataLArr[ "nodeFirst" ][ 0 ] ) && ( $nodeFirst[ 1 ] = $coastLineIndividualDataLArr[ "nodeFirst" ][ 1 ] ) ){
            $coastLineFinalDataLArr = array_merge( $coastLineIndividualDataLArr[ "nodes" ], $coastLineFinalDataLArr );
            $nodeFirst = $coastLineIndividualDataLArr[ "nodeFirst" ];
            unset( $coastLineDataLArr[ $coastLineIndividualKeyLArr ] );
          }

          if( ( $nodeLast[ 0 ] = $coastLineIndividualDataLArr[ "nodeLast" ][ 0 ] ) && ( $nodeLast[ 1 ] = $coastLineIndividualDataLArr[ "nodeLast" ][ 1 ] ) ){
            $coastLineFinalDataLArr = array_merge( $coastLineFinalDataLArr, $coastLineIndividualDataLArr[ "nodes" ] );
            $nodeLast = $coastLineIndividualDataLArr[ "nodeLast" ];
            unset( $coastLineDataLArr[ $coastLineIndividualKeyLArr ] );
          }
        }
      }

      return $coastLineFinalDataLArr;
    }

    public function distance( $x1, $y1, $x2, $y2 )
    {
      return sqrt( pow( ( $x2 - $x1 ), 2 ) + pow( ( $y2 - $y1 ), 2 ) );
    }

    private function getMapSetup(){
      $setupMapCollectionLObj  = $this->dataBaseCObj->setupMap;
      $this->setupMapCursorCObj = $setupMapCollectionLObj->findOne();
    }

    private function getMapFillSetup( $zoomFactorAUInt ){
      $logErrorCollectionLObj = $this->dataBaseCObj->logError;

      $setupFillCollectionLObj = $this->dataBaseCObj->setupFill;
      $setupFillCursorLObj = $setupFillCollectionLObj->find(
        array(
          "zoomFactor" => $zoomFactorAUInt
        )
      );
      foreach( $setupFillCursorLObj as $dataQueryGlobalDataTagLObj )
      {
        $imageTypeLX = null;

        if ( is_file( $dataQueryGlobalDataTagLObj[ "imageTile" ] ) && ( is_readable( $dataQueryGlobalDataTagLObj[ "imageTile" ] ) ) )
        {
          $imageTypeLX = explode( ".", $dataQueryGlobalDataTagLObj[ "imageTile" ] );
          $imageTypeLX = $imageTypeLX[ count( $imageTypeLX ) - 1 ];
          $imageTypeLX = strtolower( $imageTypeLX );
        }
        else
        {
          $logErrorCollectionLObj->insert(
            array(
              "error" => "access file error",
              "file" => $dataQueryGlobalDataTagLObj[ "imageTile" ],
              "type" => $imageTypeLX,
              "original" => $dataQueryGlobalDataTagLObj
            )
          );
          //todo:melhorar isto
          if( strlen( $dataQueryGlobalDataTagLObj[ "imageTile" ] ) > 0 )
          {
            $dataQueryGlobalDataTagLObj[ "type" ] = "none";
          }
        }

        $this->mapConstructLineStyleCArr[ $dataQueryGlobalDataTagLObj[ "pointKey" ] ][ $dataQueryGlobalDataTagLObj[ "pointValue" ] ] = array(
          "thickness" => ( int ) $dataQueryGlobalDataTagLObj[ "thickness" ],
          "style" => $dataQueryGlobalDataTagLObj[ "style" ],
          "imageTile" => array(
            "path" => $dataQueryGlobalDataTagLObj[ "imageTile" ],
            "type" => $imageTypeLX
          ),
          "type" => $dataQueryGlobalDataTagLObj[ "type" ],
          "colorRed" => ( int ) $dataQueryGlobalDataTagLObj[ "colorRed" ],
          "colorGreen" => ( int ) $dataQueryGlobalDataTagLObj[ "colorGreen" ],
          "colorBlue" => ( int ) $dataQueryGlobalDataTagLObj[ "colorBlue" ],
          "colorAlpha" => ( int ) $dataQueryGlobalDataTagLObj[ "colorAlpha" ]
        );
      }
      unset( $dataQueryGlobalDataTagLObj );
    }

    private function layerResourcePrepare( $sizeAUInt, $sizeAUInt ){
      $this->imageResourceCArr = array();

      //Exp 01
      //As imagens do mapa devem ser montadas em camadas, onde uma ponte fica em uma camada superior e um túnel fica em uma camada inferior
      //Por isto, $this->imageResourceCArr é um array e as camadas são montadas ao final do mapa.
      //@see http://wiki.openstreetmap.org/wiki/Key:layer
      for( $layerCounterLUInt = ( $this->setupMapCursorCObj[ "layerMin" ] * $this->setupMapCursorCObj[ "layerMultiplier" ] ); $layerCounterLUInt != ( ( $this->setupMapCursorCObj[ "layerMax" ] * $this->setupMapCursorCObj[ "layerMultiplier" ] ) + 1 ); $layerCounterLUInt += 1 )
      {
        //$this->imageResourceCArr[ $layerCounterLUInt ] = imagecreatetruecolor( $widthAUInt, $heightAUInt );
        $this->imageResourceCArr[ $layerCounterLUInt ] = imagecreatetruecolor( $sizeAUInt, $sizeAUInt );

        if( $this->backGroundHeaderType == "Content-Type: image/png" )
        {
          imagesavealpha( $this->imageResourceCArr[ $layerCounterLUInt ], true );
        }

        if ( $layerCounterLUInt != $this->setupMapCursorCObj[ "layerMin" ] )
        {
          imagefill( $this->imageResourceCArr[ $layerCounterLUInt ], 0, 0, imagecolorallocatealpha( $this->imageResourceCArr[ $layerCounterLUInt ], 255, 255, 255, 127 ) );
        }
        else
        {
          imagefill( $this->imageResourceCArr[ $layerCounterLUInt ], 0, 0, imagecolorallocatealpha( $this->imageResourceCArr[ $layerCounterLUInt ], $this->backGroundRedCUInt, $this->backGroundGreenCUInt, $this->backGroundBlueCUInt, $this->backGroundAlphaCUInt ) );
        }
      }
    }

    public function draw ( $sizeAUInt, $zoomFactorAUInt )
    {
      if( $zoomFactorAUInt > 30 )
      {
        $zoomFactorAUInt = 30;
      }

      $this->getMapSetup();

$apagarEsteLixoCoastLine = false;

      $this->layerResourcePrepare( $sizeAUInt, $sizeAUInt );

      $xPreviousLUInt = 0;
      $yPreviousLUInt = 0;

      $polygonDrawPointsLArr = array();
      $polygonDrawLayer = 0;
      $polygonDrawColorLUInt = null;

      $this->getMapFillSetup( $zoomFactorAUInt );

      try
      {
        $mainMapDataLArr  =  array();
        $fator = 1;

        $waysCollectionLObj = $this->dataBaseCObj->ways;

        $waysCursorLObj = $waysCollectionLObj->find(
          array(
            //'_id' => new MongoId( "56b91242fef3cb9c33a27285" ),
            //'_id' => new MongoId( "56b91207fef3cb9c33992122" ),
            //'_id' => new MongoId( "56b9158efef3cb9f3386f17a" ),
            //'_id' => new MongoId( "56b91207fef3cb9c33991d89" ),
            /*'_id' => array(
              '$in' => array(
                new MongoId( "56b91242fef3cb9c33a27285" ),
                new MongoId( "56b91207fef3cb9c33992122" ),
                new MongoId( "56b9158efef3cb9f3386f17a" ),
                new MongoId( "56b91207fef3cb9c33991d89" )
              )
            ),*/
            //'tags.natural.val' => 'coastline',
            'nodes' => array(
              '$geoWithin' => array(
                '$box' => array(
                  array( $this->latitudeCenterCSFlt - ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ), $this->longitudeCenterCSFlt + ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ) ),
                  array( $this->latitudeCenterCSFlt + ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ), $this->longitudeCenterCSFlt - ( $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ] * $fator ) )
                )
              )
            )
          )
        );


        //$waysCursorLObj->limit( $this->setupMapCursorCObj[ "dbLimit" ][ $zoomFactorAUInt ] );
        $setupFillCollectionLObj = $this->dataBaseCObj->setupFill;
        foreach( $waysCursorLObj as $waysDataLObj )
        {
          foreach( $waysDataLObj[ "tags" ] as $tagsKeyLArr => $tagsDataLArr )
          {
            $passLBol = false;
            $setupFillCursorLObj = $setupFillCollectionLObj->find(
              array(
                "pointKey" => $tagsKeyLArr,
                "pointValue" => $tagsDataLArr[ "val" ],
                "visible" => true,
                "zoomFactor" => $zoomFactorAUInt,
                "type" => array(
                  '$ne' => "none"
                )
              )
            );
            //foreach( $setupFillCursorLObj as $dataQueryGlobalDataTagLObj )
            if( $setupFillCursorLObj->count() != 0 )
            {
              $passLBol = true;
              $waysDataLObj[ "tags" ][ "type" ][ "val" ] = $tagsKeyLArr;
              break;
            }

            if( $passLBol == true )
            {
              break;
            }
          }

          if( $passLBol == false )
          {
            continue;
          }

          //Exp 02
          //Como falado em Exp 01, o mapa é formado em camadas, porém, nem todas as tags têm uma camada informada, por isto, adicionamos a camada
          //padrão, caso a mesma não esteja prevista.
          if( !isset( $waysDataLObj[ "tags" ][ "layer" ][ "val" ] ) )
          {
            $waysDataLObj[ "tags" ][ "layer" ][ "val" ] = $this->setupMapCursorCObj[ "layerDefault" ];
          }

          $mainMapDataLArr[] = $waysDataLObj;
        }

        $latitudeMaxLDbl  = $this->latitudeCenterCSFlt + $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ];
        $latitudeMinLDbl  = $this->latitudeCenterCSFlt - $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ];
        $longitudeMaxLDbl = $this->longitudeCenterCSFlt + $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ];
        $longitudeMinLDbl = $this->longitudeCenterCSFlt - $this->setupMapCursorCObj[ "zoomFactorScale" ][ $zoomFactorAUInt ];

        if ( $latitudeMinLDbl < 0 )
        {
          $addLatitude = abs( $latitudeMinLDbl );
        }
        else
        {
          $addLatitude = $latitudeMinLDbl * -1;
        }

        $latitudeMaxLDbl += $addLatitude;

        if ( $longitudeMinLDbl < 0 )
        {
          $addLongitude = abs( $longitudeMinLDbl );
        }
        else
        {
          $addLongitude = $longitudeMinLDbl * -1;
        }

        $longitudeMaxLDbl += $addLongitude;

        $widthFactorAUDbl  = ( $sizeAUInt / $latitudeMaxLDbl );
        $heightFactorAUDbl = ( $sizeAUInt / $longitudeMaxLDbl );

        foreach( $mainMapDataLArr as $wayMapKetLArr => $wayMapDataLArr )
        {
          $wayMapDataLArr[ "tags" ][ "layer" ][ "val" ] = $this->setupMapCursorCObj[ "layerMultiplier" ] * ( int ) $wayMapDataLArr[ "tags" ][ "layer" ][ "val" ];

          if( $wayMapDataLArr[ "tags" ][ "highway" ][ "val" ] == "primary" )
          {
            $wayMapDataLArr[ "tags" ][ "layer" ][ "val" ] = +2;
          }
          else if( $wayMapDataLArr[ "tags" ][ "highway" ][ "val" ] == "secondary" )
          {
            $wayMapDataLArr[ "tags" ][ "layer" ][ "val" ] = +1;
          }

          $layerActualLSInt = ( int )$wayMapDataLArr[ "tags" ][ "layer" ][ "val" ];

          $nodeTotalKeyCountLUInt = count( $wayMapDataLArr[ "nodes" ] ) - 1;

          $wayTypeLStr = $wayMapDataLArr[ "tags" ][ "type" ][ "val" ];
          $wayTypeSubLStr = $wayMapDataLArr[ "tags" ][ $wayMapDataLArr[ "tags" ][ "type" ][ "val" ] ][ "val" ];
          $fillTypeLStr = $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "type" ];

          switch( $fillTypeLStr )
          {
            case "line":
            case "style":
            case "filledPolygon":

              if($apagarEsteLixoCoastLine == true)
              {
                if( $wayMapDataLArr[ "tags" ][ "natural" ][ "val" ] == "coastline" )
                {
                  $coastLine[] = "" . $wayMapDataLArr[ "_id" ];
                }
              }

              if( ( $wayMapDataLArr[ "tags" ][ "highway" ][ "val" ] == "primary" ) || ( $wayMapDataLArr[ "tags" ][ "highway" ][ "val" ] == "secondary" ) )
              {
                $wayMapDataLArr[ "tags" ][ "name" ][ "val" ] = utf8_decode( $wayMapDataLArr[ "tags" ][ "name" ][ "val" ] );

                imageText::setText(
                  $wayMapDataLArr[ "tags" ][ "name" ][ "val" ]
                );
              }

              foreach( $wayMapDataLArr[ "nodes" ] as $nodesKeyLArr => $nodesDataLArr )
              {
                $xActualLUInt = ( ( float )$nodesDataLArr[ self::LATITUDE ] + $addLatitude ) * $widthFactorAUDbl;
                $yActualLUInt = ( ( float )$nodesDataLArr[ self::LONGITUDE ] + $addLongitude ) * $heightFactorAUDbl;

                switch( $fillTypeLStr )
                {
                  case "line":

                    if( $nodesKeyLArr == 0 )
                    {
                      $xPreviousLUInt = $xActualLUInt;
                      $yPreviousLUInt = $yActualLUInt;

                      imagesetstyle( $this->imageResourceCArr[ $layerActualLSInt ], array( imagecolorallocatealpha( $this->imageResourceCArr[ $layerActualLSInt ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorRed" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorGreen" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorBlue" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorAlpha" ] ) ) );
                      imagesetthickness( $this->imageResourceCArr[ $layerActualLSInt ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ] );
                    }
                    else
                    {
                      if( ( $wayMapDataLArr[ "tags" ][ "highway" ][ "val" ] == "primary" ) || ( $wayMapDataLArr[ "tags" ][ "highway" ][ "val" ] == "secondary" ) )
                      {
                        if( !in_array( $wayMapDataLArr[ "tags" ][ "name" ][ "val" ], $this->nameOfStreet ) )
                        {
                          //$this->nameOfStreet[] = $wayMapDataLArr[ "tags" ][ "name" ][ "val" ];
                          imageText::textTo( $this->imageResourceCArr[ $layerActualLSInt + 3 ], ( int )$xPreviousLUInt, ( int )$yPreviousLUInt, ( int )$xActualLUInt, ( int )$yActualLUInt );
                        }
                      }

                      //bresenham::lineTo( $this->imageResourceCArr[ $layerActualLSInt ], ( int ) $xPreviousLUInt, ( int ) $yPreviousLUInt, ( int ) $xActualLUInt, ( int ) $yActualLUInt, IMG_COLOR_STYLED, $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ] );
                      //imagefilledellipse ( $this->imageResourceCArr[ $layerActualLSInt ], $xPreviousLUInt, $yPreviousLUInt, $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], IMG_COLOR_STYLED );
                      //imagefilledellipse ( $this->imageResourceCArr[ $layerActualLSInt ], $xActualLUInt, $yActualLUInt, $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], IMG_COLOR_STYLED );
                      imageline( $this->imageResourceCArr[ $layerActualLSInt ], $xPreviousLUInt, $yPreviousLUInt, $xActualLUInt, $yActualLUInt, IMG_COLOR_STYLED );

                      if( $wayMapDataLArr[ "tags" ][ "natural" ][ "val" ] == "coastline" )
                      {
                        //***********************************************************************************************************************************************************************
                        //bresenham::lineTo( $this->imageResourceCArr[ $layerActualLSInt ], ( int ) $xPreviousLUInt, ( int ) $yPreviousLUInt, $_x2, $_y2, IMG_COLOR_STYLED, $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ] );
                        $angle = atan2( ( $yPreviousLUInt - $yActualLUInt ), ( $xPreviousLUInt - $xActualLUInt ) );
                        //$_x2 = ( int ) $xActualLUInt + 5 * cos( $angle + pi()/2 );
                        //$_y2 = ( int ) $yActualLUInt + 5 * sin( $angle + pi()/2 );
                        //imageline( $this->imageResourceCArr[ $layerActualLSInt ], $xActualLUInt, $yActualLUInt, $_x2, $_y2, IMG_COLOR_STYLED );

                        $_x2 = ( int ) $xActualLUInt + 5 * cos( $angle + pi()/4 );
                        $_y2 = ( int ) $yActualLUInt + 5 * sin( $angle + pi()/4 );
                        imageline( $this->imageResourceCArr[ $layerActualLSInt ], $xActualLUInt, $yActualLUInt, $_x2, $_y2, IMG_COLOR_STYLED );

                        $_x2 = ( int ) $xActualLUInt + 5 * cos( $angle - pi()/4 );
                        $_y2 = ( int ) $yActualLUInt + 5 * sin( $angle - pi()/4 );
                        imageline( $this->imageResourceCArr[ $layerActualLSInt ], $xActualLUInt, $yActualLUInt, $_x2, $_y2, IMG_COLOR_STYLED );

                      }

                      $xPreviousLUInt = $xActualLUInt;
                      $yPreviousLUInt = $yActualLUInt;
                    }

                    break;

                  case "style":

                    if( $nodesKeyLArr == 0 )
                    {
                      $xPreviousLUInt = $xActualLUInt;
                      $yPreviousLUInt = $yActualLUInt;

                      $imageFillLArr = null;
                      if( strlen( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "style" ] ) > 0 )
                      {
                        $imageFillLArr = array();
                        $tmpLArr = json_decode( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "style" ], true );
                        foreach( $tmpLArr as $tmpKeyLUInt => $tmpValueLArr )
                        {
                          $imageFillLArr[] = imagecolorallocatealpha( $this->imageResourceCArr[ $layerActualLSInt ], ( int )$tmpValueLArr[ "r" ], ( int )$tmpValueLArr[ "g" ], ( int )$tmpValueLArr[ "b" ], ( int )$tmpValueLArr[ "a" ] );
                        }
                      }

                      imagesetstyle( $this->imageResourceCArr[ $layerActualLSInt ], $imageFillLArr );
                      imagesetthickness( $this->imageResourceCArr[ $layerActualLSInt ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ] );
                    }
                    else
                    {
                      imagefilledellipse ( $this->imageResourceCArr[ $layerActualLSInt ], $xPreviousLUInt, $yPreviousLUInt, $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], IMG_COLOR_STYLED );
                      imagefilledellipse ( $this->imageResourceCArr[ $layerActualLSInt ], $xActualLUInt, $yActualLUInt, $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ], IMG_COLOR_STYLED );
                      imageline( $this->imageResourceCArr[ $layerActualLSInt ], $xPreviousLUInt, $yPreviousLUInt, $xActualLUInt, $yActualLUInt, IMG_COLOR_STYLED );
                      $xPreviousLUInt = $xActualLUInt;
                      $yPreviousLUInt = $yActualLUInt;
                    }

                    break;

                  case "filledPolygon":

                    if( $nodesKeyLArr == 0 )
                    {
                      $polygonDrawPointsLArr = array();

                      $polygonDrawColorLUInt = imagecolorallocatealpha( $this->imageResourceCArr[ $layerActualLSInt ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorRed" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorGreen" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorBlue" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorAlpha" ] );
                      if( !is_null( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "imageTile" ][ "type" ] ) )
                      {
                        switch( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "imageTile" ][ "type" ] )
                        {
                          case "png":
                            $fillTileImageLObj = imagecreatefrompng( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "imageTile" ][ "path" ] );
                            $polygonDrawColorLUInt = IMG_COLOR_TILED;
                            break;

                          case "gif":
                            $fillTileImageLObj = imagecreatefromgif( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "imageTile" ][ "path" ] );
                            $polygonDrawColorLUInt = IMG_COLOR_TILED;
                            break;

                          case "jpeg":
                          case "jpg":
                            $fillTileImageLObj = imagecreatefromjpeg( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "imageTile" ][ "path" ] );
                            $polygonDrawColorLUInt = IMG_COLOR_TILED;
                            break;

                          case "wbmp":
                            $fillTileImageLObj = imagecreatefromwbmp( $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "imageTile" ][ "path" ] );
                            $polygonDrawColorLUInt = IMG_COLOR_TILED;
                            break;
                        }

                        imagesettile( $this->imageResourceCArr[ $layerActualLSInt ], $fillTileImageLObj );
                      }

                      $polygonDrawPointsLArr[] = $xActualLUInt;
                      $polygonDrawPointsLArr[] = $yActualLUInt;

                      imagesetstyle( $this->imageResourceCArr[ $layerActualLSInt ], array( imagecolorallocatealpha( $this->imageResourceCArr[ $layerActualLSInt ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorRed" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorGreen" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorBlue" ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "colorAlpha" ] ) ) );
                      imagesetthickness( $this->imageResourceCArr[ $layerActualLSInt ], $this->mapConstructLineStyleCArr[ $wayTypeLStr ][ $wayTypeSubLStr ][ "thickness" ] );
                    }
                    else if( $nodesKeyLArr == $nodeTotalKeyCountLUInt )
                    {
                      if( count( $polygonDrawPointsLArr ) / 2 > 2 )
                      {
                        imagefilledpolygon( $this->imageResourceCArr[ $polygonDrawLayer ], $polygonDrawPointsLArr, count( $polygonDrawPointsLArr ) / 2, $polygonDrawColorLUInt );
                      }
                    }
                    else
                    {
                      $polygonDrawPointsLArr[] = $xActualLUInt;
                      $polygonDrawPointsLArr[] = $yActualLUInt;
                    }
                    break;
                }
              }
          }
        }
      }
      catch( Exception $e )
      {
        die( $e->getMessage() );
      }

      if($apagarEsteLixoCoastLine == true)
      {
        print "<pre>";
        print_r( $coastLine );
        die();
      }

      $imageResourceRotateLObj = imagecreatetruecolor( $sizeAUInt, $sizeAUInt );
      $imageResourceReduceLObj = imagecreatetruecolor( $sizeAUInt, $sizeAUInt );
      for( $layerCounterLUInt = ( $this->setupMapCursorCObj[ "layerMin" ] * $this->setupMapCursorCObj[ "layerMultiplier" ] ); $layerCounterLUInt != ( ( $this->setupMapCursorCObj[ "layerMax" ] * $this->setupMapCursorCObj[ "layerMultiplier" ] ) + 1 ); $layerCounterLUInt += 1 )
      {
        imagecopy ( $imageResourceRotateLObj, $this->imageResourceCArr[ $layerCounterLUInt ], 0, 0, 0, 0, $sizeAUInt, $sizeAUInt );
        imagedestroy( $this->imageResourceCArr[ $layerCounterLUInt ] );
      }

      $imageResourceRotateLObj = imagerotate( $imageResourceRotateLObj, 90.0, imagecolorallocatealpha( $imageResourceRotateLObj, $this->backGroundRedCUInt, $this->backGroundGreenCUInt, $this->backGroundBlueCUInt, $this->backGroundAlphaCUInt ) );
      imagecopyresized ( $imageResourceReduceLObj, $imageResourceRotateLObj, 0, 0, 0, 0, $sizeAUInt, $sizeAUInt, $sizeAUInt, $sizeAUInt );

      imagedestroy( $imageResourceRotateLObj );

      if( $this->backGroundHeaderType == "Content-Type: image/png" )
      {
        imagesavealpha( $imageResourceReduceLObj, true );
      }
      imagealphablending( $imageResourceReduceLObj, false );


//imagefilter($imageResourceReduceLObj, IMG_FILTER_SMOOTH,200);

      switch( $this->backGroundHeaderType )
      {
        case "Content-Type: image/png":
          header( $this->backGroundHeaderType );

          if( $this->imageSaveAndShowCBol == true )
          {
            imagepng( $imageResourceReduceLObj );
          }
          imagepng( $imageResourceReduceLObj, $this->imageSavePathCStr );
          break;

        case "Content-Type: image/gif":
          header( $this->backGroundHeaderType );

          if( $this->imageSaveAndShowCBol == true )
          {
            imagegif( $imageResourceReduceLObj );
          }
          imagegif( $imageResourceReduceLObj, $this->imageSavePathCStr );
          break;

        case "Content-Type: image/wbmp":
          header( $this->backGroundHeaderType );

          if( $this->imageSaveAndShowCBol == true )
          {
            imagewbmp( $imageResourceReduceLObj );
          }
          imagewbmp( $imageResourceReduceLObj, $this->imageSavePathCStr );
          break;

        case "Content-Type: image/jpeg":
          header( $this->backGroundHeaderType );

          if( $this->imageSaveAndShowCBol == true )
          {
            imagejpeg( $imageResourceReduceLObj );
          }
          imagejpeg( $imageResourceReduceLObj, $this->imageSavePathCStr );
          break;
      }

      imagedestroy( $imageResourceReduceLObj );
    }
  }
