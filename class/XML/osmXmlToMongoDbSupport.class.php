<?php

  /**
   * @autor Helmut Kemper
   * @email helmut.kemper@gmail.com
   * @site http://www.kemper.com.br
   *
   * Class osmXmlToMongoDbSupport
   *
   * Descrição:
   *
   * Classe responsavel pelo preparo do banco de dados e pela formatação adequada das coleções de dados para que as
   * mesmas atendam as necessidades da aplicação.
   *
   * Necessidade:
   *
   * O projeto é baseado no formato de arquivo OSM do OpenStreetMaps, porém, o mesmo é excelente para ser arquivado em
   * um banco relacional.
   * Esta classe tem a funcionalidade de converter o formato relacional dos dados no formato de coleções mais adequado
   * ao MongoDB, e sempre que possível, aplicar as regras de performance do banco.
   *
   * Para que esta classe seja usada, é necessário uma etapa anterior de importação do arquivo OSM para o banco de
   * dados.
   *
   * Você vai encontrar isto no meu GtHub ou no meu site Pessoal.
   *
   * Descrição das tabelas:
   *
   * As tabelas abaixo são uma consequência natural do arquivo XML e podem ser apagadas do servidor após a importação e
   * formatação dos dados.
   * |------------------|----------------------------------------------------------------------------------------------|
   * |tmpNodes          | Coleção de dados com os pontos de interesse contidos no mapa.                                |
   * |                  | Perceba como pontos de interesse locais como cidades, vilas, farmácias, hoteis, semáforos,   |
   * |                  | etc.                                                                                         |
   * |------------------|----------------------------------------------------------------------------------------------|
   * |tmpNodesTags      | Coleção de dados com informações pertinentes aos pontos de interesse.                        |
   * |                  | Nessa coleção podem ser encontradas informações como nome, tipo, fonte, etc.                 |
   * |------------------|----------------------------------------------------------------------------------------------|
   * |tmpWays           | Coleção de dados com informações de todas as linhas do mapa.                                 |
   * |                  | Linhas do mapa podem ser qualquer coisa,desde ruas, desenho das edificações, linha costeira, |
   * |                  | etc.                                                                                         |
   * |------------------|----------------------------------------------------------------------------------------------|
   * |tmpWaysTags       | Coleção de dados com informações pertinentes as linhas de construção do mapa.                |
   * |                  | Nessa coleção podem ser encontradas informações como nome, tipo, fonte, etc.                 |
   * |------------------|----------------------------------------------------------------------------------------------|
   * |tmpWaysNodes      | Coleção de dados com a relação entre ways, nodes e tags.                                     |
   * |------------------|----------------------------------------------------------------------------------------------|
   *
   *
   * As tabelas abaixo são de configuração da interface gráfica e interface do usuário
   * |------------------|----------------------------------------------------------------------------------------------|
   * |setupFill         | Coleção de dados com as informações de como desenhar a parte gráfica do mapa.                |
   * |                  | Esta coleção determina que pele usar para preencher polígonos específicos,cor e espessura das|
   * |                  | linhas de construção das ruas, como o mapa se comporta para cada opção de zoom, etc.         |
   * |------------------|----------------------------------------------------------------------------------------------|
   * |setupMap          | Coleção de dados com as configurações da ferramenta draw, responsável por transformar o XML  |
   * |                  | em um mapa gráfico de fácil entendimento por parte do usuário.                               |
   * |                  | Alterar esta coleção pode afetar de forma imprevista a forma como o mapa é desenhado.        |
   * |------------------|----------------------------------------------------------------------------------------------|
   *
   *
   * As tabelas abaixo são as tabelas principais do sistema.
   * |------------------|----------------------------------------------------------------------------------------------|
   * |nodes             | Coleção de dados já formatada e pronta para uso com todos os pontos de interesse do mapa.    |
   * |------------------|----------------------------------------------------------------------------------------------|
   * |ways              | Coleção de dados já formatada e pronta para uso com todas as linhas de construção do mapa.   |
   * |                  | Entenda como linhas, todas as linhas que formam a interface gráfica do mapa, como ruas, con- |
   * |                  | torno das construções, linha costeira, borda dos parques, etc.                               |
   * |------------------|----------------------------------------------------------------------------------------------|
   *
   * Licença:
   *
   * Este código é aberto e feito com o intuito de ajudar, porém, sem garantias de funcionar ou obrigação de suporte de
   * qualquer forma da minha parte.
   *
   * Você é livre para copiar e usar o código desse repositório conforme as suas necessidades e livre para lucrar com
   * ele, sem ter que me pagar royalties, desde que siga as regras abaixo:
   *
   * - Para usar este código, você se compromete a divulgar meu nome e trabalho como sendo o criador original da
   *   ferramenta de integração com o seu sistema de mapas no site da sua aplicação;
   * - Você se compromete a me enviar de forma documentada qualquer correção e/ou melhorias feitas no código para que eu
   *   decida quais das mesmas sejam adicionas ao projeto original de forma aberta a toda a comunidade, sem custos;
   * - Você se compromete a contribuir tecnicamente com a comunidade de desenvolvedores de forma gratuita;
   * - Você se compromete a me manter informado onde o código é usado e me enviar material de divulgação, para seja
   *   feita propaganda desse código e casos de sucesso;
   * - Você se compromete a não usar o código em aplicações que possam colocar a vida de pessoas em risco desnecessários
   *   e/ou aplicações militares sem autorização prévia da minha parte.
   */
  class osmXmlToMongoDbSupport extends osmXmlToMongoDb
  {
    /**
     * Coleção de dados com os pontos de interesse contidos no mapa.
     * Perceba como pontos de interesse locais como cidades, vilas, farmácias, hoteis, semáforos, etc.
     *
     * @var $collectionTmpNodesCObj Object Conexão com a coleção tmpNodes.
     */
    protected $collectionTmpNodesCObj;

    /**
     * Coleção de dados com informações pertinentes aos pontos de interesse.
     * Nessa coleção podem ser encontradas informações como nome, tipo, fonte, etc.
     *
     * @var $collectionTmpNodeTagCObj Object conexão com a coleção tmpNodeTag.
     */
    protected $collectionTmpNodeTagCObj;

    /**
     * Coleção de dados com informações de todas as linhas do mapa.
     * Linhas do mapa podem ser qualquer coisa,desde ruas, desenho das edificações, linha costeira, etc.
     *
     * @var $collectionTmpWaysCObj Object conexão com a coleção tmpWays.
     */
    protected $collectionTmpWaysCObj;

    /**
     * Coleção de dados com informações pertinentes as linhas de construção do mapa.
     * Nessa coleção podem ser encontradas informações como nome, tipo, fonte, etc.
     *
     * @var $collectionTmpWayTagCObj Object conexão com a coleção tmpWayTag.
     */
    protected $collectionTmpWayTagCObj;

    /**
     * Coleção de dados com a relação entre ways, nodes e tags.
     *
     * @var $collectionTmpWayNodeCObj Object conexão com a coleção tmpWaysNode.
     */
    protected $collectionTmpWayNodeCObj;

    /**
     * Coleção de dados com as informações de como desenhar a parte gráfica do mapa.
     * Esta coleção determina que pele usar para preencher polígonos específicos,cor e espessura das linhas de
     * construção das ruas, como o mapa se comporta para cada opção de zoom, etc.
     *
     * @var $collectionSetupFill Object conexão com a coleção setupFill.
     */
    protected $collectionSetupFill;

    /**
     * Coleção de dados com as configurações da ferramenta draw, responsável por transformar o XML em um mapa gráfico de
     * fácil entendimento por parte do usuário.
     *
     * @warning Alterar esta coleção pode afetar de forma imprevista a forma como o mapa é desenhado.
     *
     * @var $collectionSetupMap Object conexão com a coleção setupMap.
     */
    protected $collectionSetupMap;

    /**
     * Coleção de dados já formatada e pronta para uso com todos os pontos de interesse do mapa.
     *
     * @var $collectionNodesCObj Object conexão com a coleção nodes.
     */
    protected $collectionNodesCObj;

    /**
     * Coleção de dados já formatada e pronta para uso com todas as linhas de construção do mapa.
     * Entenda como linhas, todas as linhas que formam a interface gráfica do mapa, como ruas, contorno das construções,
     * linha costeira, borda dos parques, etc.
     *
     * @var $collectionWaysCObj Object conexão com a coleção ways.
     */
    protected $collectionWaysCObj;

    /**
     * Conecta ao banco de dados.
     *
     * @param String $dataBaseAStr String nome do banco de dados
     */
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

    /**
     * Testa se um índice já existe na coleção de dados para evitar tentativa de criação em duplicidade.
     *
     * Perceba que esta função apenas testa se o índice existe de forma simples, ou seja, ele não verifica se o índice é
     * ASC ou DESC por exemplo.
     *
     * @param $indexListAArr Array lista de indices. ex.: $this->mongoCollection->getIndexInfo();
     * @param $nameAStr String nome da chave usada na criação do índice.
     *
     * @return bool true caso o índice exista na coleção.
     */
    private function getIndexExists( $indexListAArr, $nameAStr ) {
      foreach( $indexListAArr as $indexDataLArr ){
        if( $indexDataLArr[ "name" ] == $nameAStr ){
          return true;
        }
      }
      return false;
    }

    /**
     * Cria todos os indices do banco de dados.
     *
     * Perceba que os índices do banco de dados são de fundamental importância para o desempenho do banco.
     *
     * Lembre-se de criar todos os índices antes de popular as coleções, pois, coleções como nodes têm mais de 30
     * milhoes de registros apenas para o Brasil e o MongoDB não vai criar os índices de forma correta após algumas
     * coleções serem populadas.
     */
    public function createIndex()
    {
      // coleção tmpNodes
      $indexInfoLArr =  $this->collectionTmpNodesCObj->getIndexInfo();

      if( !$this->getIndexExists( $indexInfoLArr, "osm_id_node" ) ){
        $this->collectionTmpNodesCObj->createIndex( array( "id_node" => 1 ), array( "name" => "osm_id_node" ) );
      }

      if( !$this->getIndexExists( $indexInfoLArr, "osm_lat_lng" ) ){
        $this->collectionTmpNodesCObj->createIndex( array( "latitde" => 1, "longitude" => 1 ), array( "name" => "osm_lat_lng" ) );
      }

      // coleção tmpNodeTag
      $indexInfoLArr =  $this->collectionTmpNodeTagCObj->getIndexInfo();

      if( !$this->getIndexExists( $indexInfoLArr, "_id_node_" ) ){
        $this->collectionTmpNodeTagCObj->createIndex( array( "id_node" => 1 ), array( "name" => "osm_id_node" ) );
      }

      // coleção nodes
      $indexInfoLArr =  $this->collectionNodesCObj->getIndexInfo();

      if( !$this->getIndexExists( $indexInfoLArr, "_id_node_" ) ){
        $this->collectionNodesCObj->createIndex( array( "location" => "2d", "bits" => 26 ), array( "name" => "osm_location_2d" ) );
      }

      if( !$this->getIndexExists( $indexInfoLArr, "_id_node_" ) ){
        $this->collectionNodesCObj->createIndex( array( "tags" => 1 ), array( "name" => "osm_id_node" ) );
      }

      if( !$this->getIndexExists( $indexInfoLArr, "_id_node_" ) ){
        $this->collectionNodesCObj->createIndex( array( "tags.name.val" => 1 ), array( "name" => "osm_tag_name" ) );
      }

      if( !$this->getIndexExists( $indexInfoLArr, "_id_node_" ) ){
        $this->collectionNodesCObj->createIndex( array( "tags.place.val" => 1 ), array( "name" => "osm_tag_place" ) );
      }


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
