<?php

  /**
   * Class osmXmlToMysql
   * This class load and convert .osm ( open street map XML file ) into MySql database.
   * and export data creation to files openStreetMapFile_XX.sql.gz
   *
   * For use this class tour need:
   * XAMPP ( basic installation of PHP and MySQL );
   * MySql root password;
   * Free space into disk;
   * Coffee and time;
   *
   * Warning: The .osm file is so big to use filezise() in PHP.
   *
   * Esta classe converte .osm ( Arquivo XML do open street map ) em banco de dados MySql
   * e exporta os dados de criação do banco em arquivos openStreetMapFile_XX.sql.gz
   *
   * Para usar esta classe você necessita de:
   * XAMPP ( instalação básica do PHP e do MySql );
   * Senha do MySql com acesso do root;
   * Espaço livre em disco;
   * Café e muito tempo;
   *
   * Atenção: O arquivo .osm é muito grande para se usar o comando filezise() do PHP.
   */
  class osmXmlToMySql
  {
    private $mySqlConnectionCObj;
    private $mySqlConnectedSuccessfulCBol = false;
    private $mySqlDataBaseCStr = null;

    protected $xmlReferenceId;
    protected $xmlReferenceVersion;

    public function __construct ()
    {
      $this->xmlReferenceId      = 0;
      $this->xmlReferenceVersion = 0;
    }

    public function connectToServer ( $mySqlHostAStr = "127.0.0.1", $mySqlUserAStr = "root", $mySqlPasswordAStr = "" )
    {
      $this->mySqlConnectionCObj = new mysqli( $mySqlHostAStr, $mySqlUserAStr, $mySqlPasswordAStr );
      if ( $this->mySqlConnectionCObj->connect_errno > 0 )
      {
        die ( "Connection error / Erro de conexão: {$this->mySqlConnectionCObj->connect_error}" );
      }
      $this->mySqlConnectedSuccessfulCBol = true;
    }

    public function selectDataBase ( $dataBaseNameAStr )
    {
      $this->mySqlDataBaseCStr = $dataBaseNameAStr;
      $this->mySqlConnectionCObj->select_db( $this->mySqlDataBaseCStr );

      $this->mySqlConnectedSuccessfulCBol = true;
    }

    public function createDataBaseAndSelect ( $dataBaseNameAStr )
    {
      if ( $this->mySqlConnectionCObj )
      {
        $this->mySqlDataBaseCStr = $dataBaseNameAStr;

        $queryLStr = "CREATE DATABASE IF NOT EXISTS {$this->mySqlDataBaseCStr} DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";
        $this->mySqlConnectionCObj->query( $queryLStr );

        $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

        $this->mySqlConnectionCObj->select_db( $this->mySqlDataBaseCStr );

        $this->mySqlConnectedSuccessfulCBol = true;
      }
    }

    public function createTables ()
    {
      $queryLStr = "
CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.nodes (
  id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  id_changesets bigint unsigned NOT NULL COMMENT 'table changesets field id',
  id_user bigint unsigned NOT NULL COMMENT 'English: There is a table users, however, this field has been left so you can filter information provided by a particular user of Open Street Maps if necessary. Português: Não existe uma tabela usuários, porém, este campo foi deixado para que se possa filtrar informações fornecidas por um determinado usuário do Open Street Maps caso necessário.',
  id_load bigint unsigned NOT NULL COMMENT 'English: Identifying data payload. For use not yet been defined. Português: Identificação da carga de dados. Para uso ainda não definido.',
  version bigint unsigned NOT NULL COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  visible BOOLEAN NOT NULL COMMENT 'English: Not sure yet if this field comes from .osm file. When in doubt, I put it. Português: Não tenho certeza ainda se este campo vem do arquivo .osm. Na dúvida, coloquei.',
  latitude decimal(10,7) NOT NULL COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  longitude decimal(10,7) NOT NULL COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  timestamp timestamp COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  load_date timestamp COMMENT 'English: Identifying data payload. For use not yet been defined. Português: Identificação da carga de dados. Para uso ainda não definido.',
  PRIMARY KEY (`id`),
  KEY `id_changesets` (`id_changesets`),
  KEY `version` (`version`),
  KEY `id_user` (`id_user`),
  KEY `id_load` (`id_load`)
) COMMENT '' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
      ";

      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );









      $queryLStr = "
CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.ways (
  id bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key',
  id_changesets bigint unsigned NOT NULL COMMENT 'table changesets field id',
  id_user bigint unsigned NOT NULL COMMENT 'English: There is a table users, however, this field has been left so you can filter information provided by a particular user of Open Street Maps if necessary. Português: Não existe uma tabela usuários, porém, este campo foi deixado para que se possa filtrar informações fornecidas por um determinado usuário do Open Street Maps caso necessário.',
  id_load bigint unsigned NOT NULL COMMENT 'English: Identifying data payload. For use not yet been defined. Português: Identificação da carga de dados. Para uso ainda não definido.',
  version bigint unsigned NOT NULL COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  visible BOOLEAN NOT NULL COMMENT 'English: Not sure yet if this field comes from .osm file. When in doubt, I put it. Português: Não tenho certeza ainda se este campo vem do arquivo .osm. Na dúvida, coloquei.',
  timestamp timestamp COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  load_date timestamp COMMENT 'English: Identifying data payload. For use not yet been defined. Português: Identificação da carga de dados. Para uso ainda não definido.',
  PRIMARY KEY (`id`),
  KEY `id_changesets` (`id_changesets`),
  KEY `version` (`version`),
  KEY `id_user` (`id_user`),
  KEY `id_load` (`id_load`)
) COMMENT '' DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
      ";

      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );









      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.node_tags (\r\n".
                   "  id bigint unsigned NOT NULL AUTO_INCREMENT,\r\n".
                   "  id_reference bigint unsigned NOT NULL,\r\n".
                   "  version bigint unsigned NOT NULL COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',\r\n".
                   "  k VARCHAR(255) DEFAULT '',\r\n".
                   "  v VARCHAR(255) DEFAULT '',\r\n".
                   "  PRIMARY KEY (`id`),\r\n".
                   "  KEY `id_reference` (`id_reference`),\r\n".
                   "  KEY `k` (`k`),\r\n".
                   "  KEY `v` (`v`)\r\n".
                   ") DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;\r\n";

      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "
CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.way_tags (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  id_reference bigint unsigned NOT NULL,
  version bigint unsigned NOT NULL COMMENT 'English: This field comes from .osm file. Português: Este campo vem do arquivo .osm',
  k VARCHAR(255) DEFAULT '',
  v VARCHAR(255) DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `id_reference` (`id_reference`),
  KEY `k` (`k`),
  KEY `v` (`v`)
) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
      ";

      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );





      $queryLStr = "
CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.way_nodes (
  id bigint unsigned NOT NULL AUTO_INCREMENT,
  id_way bigint unsigned NOT NULL,
  id_node bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_way` (`id_way`),
  KEY `id_node` (`id_node`)
) DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;
      ";

      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createNode( $nodeIdAUInt, $changeSetAUInt, $userIdAUInt, $versionAUInt, $visibleABol, $latitudeAFlt, $longitudeAFlt, $loadDateATstp, $loadIdAUInt )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.nodes ( id, id_changesets, id_user, version, visible, latitude, longitude, load_date, id_load ) VALUES( {$nodeIdAUInt}, {$changeSetAUInt}, {$userIdAUInt}, {$versionAUInt}, {$visibleABol}, {$latitudeAFlt}, {$longitudeAFlt}, '{$loadDateATstp}', {$loadIdAUInt} );";
      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createWay( $nodeIdAUInt, $changeSetAUInt, $userIdAUInt, $versionAUInt, $visibleABol, $loadDateATstp, $loadIdAUInt )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.ways ( id, id_changesets, id_user, version, visible, load_date, id_load ) VALUES( {$nodeIdAUInt}, {$changeSetAUInt}, {$userIdAUInt}, {$versionAUInt}, {$visibleABol}, '{$loadDateATstp}', {$loadIdAUInt} );";
      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createNodeTag( $xmlPreviousIdReferenceAUInt, $versionAUInt, $tagKAttributeAStr, $tagVAttributeAStr )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.node_tags ( id_reference, version, k, v ) VALUES( {$xmlPreviousIdReferenceAUInt}, {$versionAUInt}, '{$tagKAttributeAStr}', '{$tagVAttributeAStr}' );";
      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createWayTag( $xmlPreviousIdReferenceAUInt, $versionAUInt, $tagKAttributeAStr, $tagVAttributeAStr )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.way_tags ( id_reference, version, k, v ) VALUES( {$xmlPreviousIdReferenceAUInt}, {$versionAUInt}, '{$tagKAttributeAStr}', '{$tagVAttributeAStr}' );";
      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createWayNode( $xmlPreviousIdReferenceAUInt, $nodeIdAUInt )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.way_nodes ( id_way, id_node ) VALUES( {$xmlPreviousIdReferenceAUInt}, {$nodeIdAUInt} );";
      //$this->mySqlConnectionCObj->query( $queryLStr );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }
  }