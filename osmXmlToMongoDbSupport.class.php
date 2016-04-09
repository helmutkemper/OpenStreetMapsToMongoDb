<?php

  class osmXmlToMongoDbSupport extends osmXmlToMongoDb
  {
    protected $collectionTmpNodesCObj;
    protected $collectionTmpNodeTagCObj;
    protected $collectionTmpWaysCObj;
    protected $collectionTmpWayTagCObj;
    protected $collectionTmpWayNodeCObj;
    protected $collectionSetupFill;
    protected $collectionSetupMap;

    protected $collectionNodesCObj;
    protected $collectionWaysCObj;

    public function setDataBase( $dataBaseAStr )
    {
      parent::setDataBase( $dataBaseAStr );

      $this->collectionTmpNodesCObj    = $this->dataBaseCObj->tmpNodes;
      $this->collectionTmpNodeTagCObj  = $this->dataBaseCObj->tmpNodeTag;
      $this->collectionTmpWaysCObj     = $this->dataBaseCObj->tmpWays;
      $this->collectionTmpWayTagCObj   = $this->dataBaseCObj->tmpWayTag;
      $this->collectionTmpWayNodeCObj  = $this->dataBaseCObj->tmpWaysNode;

      $this->collectionSetupFill       = $this->dataBaseCObj->setupFill;
      $this->collectionSetupMap        = $this->dataBaseCObj->setupMap;

      $this->collectionNodesCObj       = $this->dataBaseCObj->nodes;
      $this->collectionWaysCObj        = $this->dataBaseCObj->ways;
    }

    public function createIndex()
    {
      //$this->collectionTmpNodesCObj->createIndex( array( "id_node" => 1 ) );
      //$this->collectionTmpNodesCObj->createIndex( array( "latitde" => 1, "longitude" => 1 ) );

      //$this->collectionTmpNodeTagCObj->createIndex( array( "id_node" => 1 ) );

      //$this->collectionTmpWaysCObj->createIndex( array( "id_way" => 1 ) );

      //$this->collectionTmpWayTagCObj->createIndex( array( "id_way" => 1 ) );

      //$this->collectionTmpWayNodeCObj->createIndex( array( "id_way" => 1 ) );
      //$this->collectionTmpWayNodeCObj->createIndex( array( "id_node" => 1 ) );
      //$this->collectionTmpWayNodeCObj->createIndex( array( "id_way" => 1, "id_node" => 1 ) );

      //$this->collectionNodesCObj->createIndex( array( "latitude" => 1, "longitude" => 1 ) );
      //$this->collectionNodesCObj->createIndex( array( "tags" => "text" ) );

      //$this->collectionWaysCObj->createIndex( array( "longitudeMin" => 1, "longitudeMax" => 1, "latitudeMin" => 1, "latitudeMax" => 1 ) );
      //$this->collectionWaysCObj->createIndex( array( "tags" => "text" ) );
    }

    public function insertNode( $dataAArr )
    {
      $this->collectionNodesCObj->insert( $dataAArr );
    }

    public function insertWay( $dataAArr )
    {
      $this->collectionWaysCObj->insert( $dataAArr );
    }

    public function findNode( $dataAArr, $fieldListAArr = null )
    {
      return $this->collectionNodesCObj->find( $dataAArr, $fieldListAArr );
    }

    public function findWay( $dataAArr, $fieldListAArr = null )
    {
      return $this->collectionWaysCObj->find( $dataAArr, $fieldListAArr );
    }

    public function concatenateNodeData( $limitAUInt = null, $skipAUInt = null )
    {
      $cursorTmpNodeLObj = $this->collectionTmpNodesCObj->find(
        array()
      );

      if( !is_null( $skipAUInt ) )
      {
        $cursorTmpNodeLObj->skip( $skipAUInt );
      }

      if( !is_null( $limitAUInt ) )
      {
        $cursorTmpNodeLObj->limit( $limitAUInt );
      }

      foreach( $cursorTmpNodeLObj as $nodeDataLArr )
      {
        unset( $nodeDataLArr[ "_id" ] );

        $nodeTagLArr = array();

        $cursorTmpNodeTag = $this->collectionTmpNodeTagCObj->find(
          array(
            "id_node" => $nodeDataLArr["id_node"]
          )
        );

        $keyOutLArr = array();
        foreach($cursorTmpNodeTag as $nodeTagDataLArr)
        {
          $keyLArr = array_merge(explode(":", $nodeTagDataLArr["k"]), explode(":", $nodeTagDataLArr["v"]));

          if( $nodeTagDataLArr["k"] == "type" )
          {
            $fillDataLArr = $this->collectionSetupFill->findOne( array( "point_key" => $nodeTagDataLArr[ "k" ] ) );
            if( count( $fillDataLArr ) > 0 )
            {
              $nodeDataLArr[ "type" ] = $nodeTagDataLArr[ "v" ];
            }
          }

          $keyRef = &$keyOutLArr;
          $countLUInt = count( $keyLArr ) - 1;
          foreach($keyLArr as $keyIdLUInt => $keyValueLStr)
          {
            if ( $countLUInt == $keyIdLUInt)
            {
              if (is_bool($keyValueLStr))
              {
                $keyRef["val"] = ( bool )$keyValueLStr;
              }
              else
              {
                if ( is_numeric( $keyValueLStr ) )
                {
                  $valueLSInt = ( int ) $keyValueLStr;
                  $keyRef["val"] = ( $keyValueLStr == $valueLSInt ) ? $valueLSInt : ( float ) $keyValueLStr;
                }
                else
                {
                  $keyRef["val"] = $keyValueLStr;
                }
              }

              $keyRef = &$keyOutLArr;
            }
            else
            {
              $keyRef = &$keyRef[ $keyValueLStr ];
            }
          }
        }

        $nodeDataLArr["location"] = array(
          $nodeDataLArr["latitude"],
          $nodeDataLArr["longitude"]
        );

        unset(
          $nodeDataLArr["latitude"],
          $nodeDataLArr["longitude"]
        );

        try
        {
          $this->collectionNodesCObj->insert( array_merge( $nodeDataLArr, array( "tags" => $keyRef ) ) );
        }
        catch( Exception $e )
        {
          print $e->getMessage();
          print "<br>";
          var_dump( $keyRef );
          print "<br>";
        }
      }
    }

    public function createNode( $nodeIdAUInt, $changeSetAUInt, $userOsmIdAUInt, $versionAUInt, $visibleABol, $latitudeAFlt, $longitudeAFlt, $timeStampATstp, $idUserAUInt = 1, $idLoaderAUInt = 1 )
    {
      $this->collectionTmpNodesCObj->insert(
        array(
          "id_node" => ( int ) $nodeIdAUInt,
          "id_changesets" => ( int ) $changeSetAUInt,
          "id_user" => ( int ) $userOsmIdAUInt,
          "version" => ( int ) $versionAUInt,
          "visible" => ( bool ) $visibleABol,
          "latitude" => ( double ) $latitudeAFlt,
          "longitude" => ( double ) $longitudeAFlt
        )
      );
    }

    public function concatenateWayTagsAndNodes()
    {
      // Procura pelo setup do mapa
      $setupMapCollectionLObj  = $this->dataBaseCObj->setupMap;
      $setupMapCursorLObj = $setupMapCollectionLObj->findOne();

      // Procura pelo setup do desenho
      $mapConstructLineStyleLArr = array();
      $setupFillDataLArr = array();
      $setupFillCollectionLObj = $this->dataBaseCObj->setupFill;
      $setupFillCursorLObj = $setupFillCollectionLObj->find();
      foreach( $setupFillCursorLObj as $dataQueryGlobalDataTagLObj )
      {
        $setupFillDataLArr[] = $dataQueryGlobalDataTagLObj;
      }

      // Procura por todos os ways contidos no mapa
      // todo: limitar isto para paginar
      $cursorWaysLObj = $this->collectionTmpWaysCObj->find( array() );
      foreach( $cursorWaysLObj as $wayDataLArr )
      {
        // Procura por todas as tags do way
        $cursorWayTagCObj = $this->collectionTmpWayTagCObj->find(
          array(
            "id_way" => $wayDataLArr[ "id_way" ]
          )
        );

        $keyOutLArr = array();
        $nodeTypeLStr = null;
        foreach($cursorWayTagCObj as $nodeTagDataLArr)
        {
          // Monta as tags em um único array
          // todo: transforma isto em função
          $keyRef = &$keyOutLArr;
          $keyLArr = array_merge(explode(":", $nodeTagDataLArr["k"]), explode(":", $nodeTagDataLArr["v"]));
          $countLUInt = count( $keyLArr ) - 1;
          foreach($keyLArr as $keyIdLUInt => $keyValueLStr)
          {
            if ( $countLUInt == $keyIdLUInt)
            {
              if (is_bool($keyValueLStr))
              {
                $keyRef["val"] = ( bool )$keyValueLStr;
              }
              else
              {
                if ( is_numeric( $keyValueLStr ) )
                {
                  $valueLSInt = ( int ) $keyValueLStr;
                  $keyRef["val"] = ( $keyValueLStr == $valueLSInt ) ? $valueLSInt : ( float ) $keyValueLStr;
                }
                else
                {
                  $keyRef["val"] = $keyValueLStr;
                }
              }

              $keyRef = &$keyOutLArr;
            }
            else
            {
              $keyRef = &$keyRef[ $keyValueLStr ];
            }
          }
        }
        // transformar em função até aqui

        // Pega todos os pontos do way e calcula as latitudes e longitudes máximas
        // e mínimas de cada way
        $cursorWayNodeLObj = $this->collectionTmpWayNodeCObj->find(
          array(
            "id_way" => $wayDataLArr[ "id_way" ]
          )
        );
        $wayNodesLArr = array();
        $latitudeMinLSDbl       =  999999999;
        $latitudeMaxLSDbl       = -999999999;
        $longitudeMinLSDbl      =  999999999;
        $longitudeMaxLSDbl      = -999999999;
        foreach( $cursorWayNodeLObj as $wayNodeDataLArr )
        {
          $cursorNodeLObj = $this->collectionTmpNodesCObj->find(
            array(
              "id_node" => $wayNodeDataLArr[ "id_node" ]
            )
          );

          $countNodesLUInt = 0;
          $middleLatLSFld  = 0;
          $middleLngLSFld  = 0;
          foreach( $cursorNodeLObj as $nodeDataLArr )
          {
            $middleLatLSFld   = $nodeDataLArr[ "latitude" ];
            $middleLngLSFld   = $nodeDataLArr[ "longitude" ];
            $countNodesLUInt += 1;

            $latitudeMinLSDbl  = min( $latitudeMinLSDbl, $nodeDataLArr[ "latitude" ] );
            $latitudeMaxLSDbl  = max( $latitudeMaxLSDbl, $nodeDataLArr[ "latitude" ] );
            $longitudeMinLSDbl = min( $longitudeMinLSDbl, $nodeDataLArr[ "longitude" ] );
            $longitudeMaxLSDbl = max( $longitudeMaxLSDbl, $nodeDataLArr[ "longitude" ] );

            $wayNodesLArr[] = array(
              $nodeDataLArr[ "latitude" ],
              $nodeDataLArr[ "longitude" ]
            );
          }

          $middleLatLSFld /= $countNodesLUInt;
          $middleLngLSFld /= $countNodesLUInt;
        }

        try
        {
          $dataToInsertLArr = array_merge(
            $wayDataLArr,
            array(
              "vMax" => array(
                $latitudeMaxLSDbl,
                $longitudeMaxLSDbl
              ),
              "vMin" => array(
                $latitudeMinLSDbl,
                $longitudeMinLSDbl
              ),
              "tags" => $keyRef,
              "middle" => array(
                $middleLatLSFld,
                $middleLngLSFld
              ),
              "nodes" => $wayNodesLArr,
              "nodeFirst" => $wayNodesLArr[ 0 ],
              "nodeLast" => $wayNodesLArr[ count($wayNodesLArr) - 1 ]
            )
          );

          if( !isset( $dataToInsertLArr[ "tags" ][ "layer" ][ "val" ] ) )
          {
            $dataToInsertLArr[ "tags" ][ "layer" ][ "val" ] = $setupMapCursorLObj[ "layer_default" ];
          }

          $this->collectionWaysCObj->insert(
            $dataToInsertLArr
          );
        }
        catch( Exception $e )
        {
          try
          {
            unset( $dataToInsertLArr[ "tags" ][ "" ] );
            $this->collectionWaysCObj->insert(
              $dataToInsertLArr
            );
          }
          catch( Exception $e )
          {
            print $e->getMessage();
            print "<br>";
            var_dump( $keyRef );
            print "<br>";
          }
        }
      }
    }

    //createWay( $nodeAttributesAArr["ID"], $nodeAttributesAArr["CHANGESET"], $nodeAttributesAArr["UID"], $nodeAttributesAArr["VERSION"], $nodeAttributesAArr["VISIBLE"], $nodeAttributesAArr["TIMESTAMP"], $this->idUserCUInt, $this->idLoaderCUInt );
    public function createWay( $nodeIdAUInt, $changeSetAUInt, $userOsmIdAUInt, $versionAUInt, $visibleABol, $timeStampATstp, $idUserAUInt = 1, $idLoaderAUInt = 1 )
    {
      $this->collectionTmpWaysCObj->insert(
        array(
          "id_way" => ( int ) $nodeIdAUInt,
          "id_changesets" => ( int ) $changeSetAUInt,
          "id_user" => ( int ) $userOsmIdAUInt,
          "version" => ( int ) $versionAUInt,
          "visible" => ( bool ) $visibleABol
        )
      );
    }

    public function createNodeTag( $xmlPreviousIdReferenceAUInt, $versionAUInt, $tagKAttributeAStr, $tagVAttributeAStr )
    {
      $this->collectionTmpNodeTagCObj->insert(
        array(
          "id_node" => ( int ) $xmlPreviousIdReferenceAUInt,
          "version" => ( int ) $versionAUInt,
          "k" => /*utf8_encode*/( $tagKAttributeAStr ),
          "v" => /*utf8_encode*/( $tagVAttributeAStr )
        )
      );
    }

    public function createWayTag( $xmlPreviousIdReferenceAUInt, $versionAUInt, $tagKAttributeAStr, $tagVAttributeAStr )
    {
      $this->collectionTmpWayTagCObj->insert(
        array(
          "id_way" => ( int ) $xmlPreviousIdReferenceAUInt,
          "version" => ( int ) $versionAUInt,
          "k" => /*utf8_encode*/( $tagKAttributeAStr ),
          "v" => /*utf8_encode*/( $tagVAttributeAStr )
        )
      );
    }

    public function createWayNode( $xmlPreviousIdReferenceAUInt, $nodeIdAUInt )
    {
      $this->collectionTmpWayNodeCObj->insert(
        array(
          "id_way" => ( int ) $xmlPreviousIdReferenceAUInt,
          "id_node" => ( int ) $nodeIdAUInt
        )
      );
    }

    public function createOsmUser( $idUserOsmAUInt, $nameUserOsmAStr, $idUserAUInt = 1 )
    {

    }
  }
