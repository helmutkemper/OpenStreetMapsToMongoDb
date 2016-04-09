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
   * e exporta os dados de criaÃ§Ã£o do banco em arquivos openStreetMapFile_XX.sql.gz
   *
   * Para usar esta classe vocÃª necessita de:
   * XAMPP ( instalaÃ§Ã£o bÃ¡sica do PHP e do MySql );
   * Senha do MySql com acesso do root;
   * EspaÃ§o livre em disco;
   * CafÃ© e muito tempo;
   *
   * AtenÃ§Ã£o: O arquivo .osm Ã© muito grande para se usar o comando filezise() do PHP.
   */
  class osmXmlMySqlSupport extends db
  {
    protected $mySqlConnectedSuccessfulCBol = false;
    protected $mySqlDataBaseCStr = null;

    protected $xmlReferenceId;
    protected $xmlReferenceVersion;

    public function __construct ()
    {
      $this->xmlReferenceId      = 0;
      $this->xmlReferenceVersion = 0;
    }

    public function createDataBaseAndSelect ( $dataBaseNameAStr )
    {
      if ( $this->mySqlConnectionCObj )
      {
        $this->mySqlDataBaseCStr = $dataBaseNameAStr;

        $queryLStr = "CREATE DATABASE IF NOT EXISTS {$this->mySqlDataBaseCStr} DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";
        $this->query( $queryLStr, __FILE__, __LINE__ );

        $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

        $this->database( $this->mySqlDataBaseCStr );

        $this->mySqlConnectedSuccessfulCBol = true;
      }
    }

    public function createTables ()
    {
      try
      {
        $queryLStr = "SELECT id FROM nodes LIMIT 1";
        $queryLObj = $this->query($queryLStr, __FILE__, __LINE__);
        if($queryLObj->num_rows == 1)
        {
          return;
        }
      }
      catch(Exception $e)
      {

      }

      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.error_event (
  id bigint(20) unsigned NOT NULL,
  file_name varchar(255) NULL,
  func_line BIGINT(20),
  error longtext,
  query_text longtext
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.error_event
  ADD PRIMARY KEY (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.error_event
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key';";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.nodes (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  id_changesets bigint(20) unsigned NOT NULL COMMENT 'English: I do not know why the osp system sends me this. PortuguÃªs: Eu nÃ£o sei por que o sistema osp me envia isto.',
  id_user bigint(20) unsigned NOT NULL COMMENT 'English: There is a table users, however, this field has been left so you can filter information provided by a particular user of Open Street Maps if necessary. PortuguÃªs: NÃ£o existe uma tabela usuÃ¡rios, porÃ©m, este campo foi deixado para que se possa filtrar informaÃ§Ãµes fornecidas por um determinado usuÃ¡rio do Open Street Maps caso necessÃ¡rio.',
  id_load bigint(20) unsigned NOT NULL COMMENT 'English: Identifying data payload. For use not yet been defined. PortuguÃªs: IdentificaÃ§Ã£o da carga de dados. Para uso ainda nÃ£o definido.',
  version bigint(20) unsigned NOT NULL COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm',
  visible tinyint(1) NOT NULL COMMENT 'English: Not sure yet if this field comes from .osm file. When in doubt, I put it. PortuguÃªs: NÃ£o tenho certeza ainda se este campo vem do arquivo .osm. Na dÃºvida, coloquei.',
  latitude decimal(10,7) NOT NULL COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm',
  longitude decimal(10,7) NOT NULL COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm',
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.node_tags (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  id_reference bigint(20) unsigned NOT NULL COMMENT 'English: This field refers to the id of the `tags` table. PortuguÃªs: Este campo se refere ao id da tabela `tags`',
  version bigint(20) unsigned NOT NULL COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm',
  k varchar(255) DEFAULT NULL COMMENT 'English: This field contains a label for the type of information. Example: city, street, etc. PortuguÃªs: Este campo contÃ©m um rÃ³tulo para o tipo de informaÃ§Ã£o. Exemplo: cidade, rua, etc.',
  v varchar(255) DEFAULT NULL COMMENT 'English: This field contains information relating to the point. Example: name of the city, traffic, etc. PortuguÃªs: Este campo contÃ©m informaÃ§Ãµes referente ao ponto. Exemplo: nome da cidade, semÃ¡foro, etc.'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.osm_user (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  name varchar(255) DEFAULT NULL COMMENT 'This field comes from the OSM system and was left if there is a need to identify / delete information provided by a original user.'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.sys_loder (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  name varchar(255) NOT NULL COMMENT 'English: Serves to identify when and who gave new load of data in the database. PortuguÃªs: Serve para identificar quando e quem deu a nova carga de dados no banco.',
  description longtext NOT NULL COMMENT 'English: Serves to identify when and who gave new load of data in the database. PortuguÃªs: Serve para identificar quando e quem deu a nova carga de dados no banco.',
  date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'English: Date when the new load data is done in the database. PortuguÃªs: Data de quando a nova carga de dados foi feita no banco de dados.'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.sys_user (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  name varchar(255) NOT NULL COMMENT 'English: Complete name from local system user. PortuguÃªs: Nome completo do usuÃ¡rio do sistema local.',
  nickname varchar(255) DEFAULT NULL COMMENT 'English: Nickname from local system name. PortuguÃªs: Apelido do usuÃ¡rio do sistema.',
  password varchar(255) NOT NULL COMMENT 'English: RASH of password. PortuguÃªs: RASH da senha do usuÃ¡rio.',
  email varchar(255) NOT NULL COMMENT 'English: Valid e-mail address PortuguÃªs: EndereÃ§o de e-mail vÃ¡lido.',
  level set('root','admin','user','guest') NOT NULL COMMENT 'English: root - can all, admin - can add new data set, user - can add new data set, however, data must pass through the administrator, guest - just can consult. PortuguÃªs: root - pode tudo, admin - pode adicionar novos dados, user - pode adicionar novos dados, porÃ©m, os dados devem ser moderados., guest - apenas pode consultar.'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.ways (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  id_changesets bigint(20) unsigned NOT NULL COMMENT 'English: I do not know why the osp system sends me this. PortuguÃªs: Eu nÃ£o sei por que o sistema osp me envia isto.',
  id_user bigint(20) unsigned NOT NULL COMMENT 'English: There is a table users, however, this field has been left so you can filter information provided by a particular user of Open Street Maps if necessary. PortuguÃªs: NÃ£o existe uma tabela usuÃ¡rios, porÃ©m, este campo foi deixado para que se possa filtrar informaÃ§Ãµes fornecidas por um determinado usuÃ¡rio do Open Street Maps caso necessÃ¡rio.',
  id_load bigint(20) unsigned NOT NULL COMMENT 'English: Identifying data payload. For use not yet been defined. PortuguÃªs: IdentificaÃ§Ã£o da carga de dados. Para uso ainda nÃ£o definido.',
  version bigint(20) unsigned NOT NULL COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm',
  visible tinyint(1) NOT NULL COMMENT 'English: Not sure yet if this field comes from .osm file. When in doubt, I put it. PortuguÃªs: NÃ£o tenho certeza ainda se este campo vem do arquivo .osm. Na dÃºvida, coloquei.',
  timestamp timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.way_nodes (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  id_way bigint(20) unsigned NOT NULL COMMENT 'English: This field refers to the id of the `ways` table. PortuguÃªs: Este campo se refere ao id da tabela `ways`',
  id_node bigint(20) unsigned NOT NULL COMMENT 'English: This field refers to the id of the `nodes` table. PortuguÃªs: Este campo se refere ao id da tabela `nodes`'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "CREATE TABLE IF NOT EXISTS {$this->mySqlDataBaseCStr}.way_tags (
  id bigint(20) unsigned NOT NULL COMMENT 'primary key',
  id_sys_user bigint(20) unsigned NOT NULL COMMENT 'English: User id of the local maps system. PortuguÃªs: Id do usuÃ¡rio do sistema de mapas local',
  id_reference bigint(20) unsigned NOT NULL COMMENT 'English: This field refers to the id of the `tags` table. PortuguÃªs: Este campo se refere ao id da tabela `tags`',
  version bigint(20) unsigned NOT NULL COMMENT 'English: This field comes from .osm file. PortuguÃªs: Este campo vem do arquivo .osm',
  k varchar(255) DEFAULT NULL COMMENT 'English: This field contains a label for the type of information. Example: city, street, etc. PortuguÃªs: Este campo contÃ©m um rÃ³tulo para o tipo de informaÃ§Ã£o. Exemplo: cidade, rua, etc.',
  v varchar(255) DEFAULT NULL COMMENT 'English: This field contains information relating to the point. Example: name of the city, traffic, etc. PortuguÃªs: Este campo contÃ©m informaÃ§Ãµes referentes ao ponto. Exemplo: nome da cidade, semÃ¡foro, etc.'
) ENGINE=InnoDB DEFAULT CHARACTER SET utf8 DEFAULT COLLATE utf8_general_ci;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.nodes
  ADD PRIMARY KEY (id),
  ADD KEY id_sys_user (id_sys_user),
  ADD KEY id_changesets (id_changesets),
  ADD KEY id_user (id_user),
  ADD KEY id_load (id_load),
  ADD KEY version (version),
  ADD KEY visible (visible);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.node_tags
  ADD PRIMARY KEY (id),
  ADD KEY id_reference (id_reference),
  ADD KEY k (k),
  ADD KEY v (v),
  ADD KEY id_sys_user (id_sys_user);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.osm_user
  ADD PRIMARY KEY (id),
  ADD KEY id_sys_user (id_sys_user);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.sys_loder
  ADD PRIMARY KEY (id),
  ADD KEY id_sys_user (id_sys_user);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.sys_user
  ADD PRIMARY KEY (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.ways
  ADD PRIMARY KEY (id),
  ADD KEY id_sys_user (id_sys_user),
  ADD KEY id_changesets (id_changesets),
  ADD KEY version (version),
  ADD KEY id_user (id_user),
  ADD KEY id_load (id_load);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.way_nodes
  ADD PRIMARY KEY (id),
  ADD KEY id_way (id_way),
  ADD KEY id_node (id_node),
  ADD KEY id_sys_user (id_sys_user);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.way_tags
  ADD PRIMARY KEY (id),
  ADD KEY id_reference (id_reference),
  ADD KEY k (k),
  ADD KEY v (v),
  ADD KEY id_sys_user (id_sys_user);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.nodes
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key';";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.node_tags
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.osm_user
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.sys_loder
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.sys_user
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.ways
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'primary key';";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.way_nodes
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.way_tags
  MODIFY id bigint(20) unsigned NOT NULL AUTO_INCREMENT;";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.nodes
  ADD CONSTRAINT nodes_ibfk_1 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.node_tags
  ADD CONSTRAINT node_tags_ibfk_1 FOREIGN KEY (id_reference) REFERENCES nodes (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT node_tags_ibfk_2 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      /*$queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.osm_user
  ADD CONSTRAINT osm_user_ibfk_1 FOREIGN KEY (id) REFERENCES nodes (id_user) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT osm_user_ibfk_2 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );*/


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.sys_loder
  ADD CONSTRAINT sys_loder_ibfk_1 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.ways
  ADD CONSTRAINT ways_ibfk_1 FOREIGN KEY (id_user) REFERENCES osm_user (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT ways_ibfk_2 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.way_nodes
  ADD CONSTRAINT way_nodes_ibfk_1 FOREIGN KEY (id_node) REFERENCES nodes (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT way_nodes_ibfk_2 FOREIGN KEY (id_way) REFERENCES ways (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT way_nodes_ibfk_3 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );


      $queryLStr = "ALTER TABLE {$this->mySqlDataBaseCStr}.way_tags
  ADD CONSTRAINT way_tags_ibfk_1 FOREIGN KEY (id_reference) REFERENCES ways (id) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT way_tags_ibfk_2 FOREIGN KEY (id_sys_user) REFERENCES sys_user (id);";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      //todo: apagar isto!
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.sys_user (id, name, nickname, password, email, level) VALUES ( '1', 'OSM Main User', 'OSM User', '202cb962ac59075b964b07152d234b70', 'kemper@kemper.com.br', 'root' );";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.sys_loder (id, id_sys_user, name, description, date) VALUES ( NULL, '1', 'Carga de teste', 'Carga de teste do sistema', CURRENT_TIMESTAMP );";

      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createNode( $nodeIdAUInt, $changeSetAUInt, $userOsmIdAUInt, $versionAUInt, $visibleABol, $latitudeAFlt, $longitudeAFlt, $timeStampATstp, $idUserAUInt = 1, $idLoaderAUInt = 1 )
    {
      //$queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.nodes ( id, id_sys_user, id_changesets, id_user, version, visible, latitude, longitude, timestamp, id_load ) SELECT {$nodeIdAUInt}, {$idUserAUInt}, {$changeSetAUInt}, {$userOsmIdAUInt}, {$versionAUInt}, {$visibleABol}, {$latitudeAFlt}, {$longitudeAFlt}, '{$timeStampATstp}', '{$idLoaderAUInt}' WHERE NOT EXISTS ( SELECT id FROM {$this->mySqlDataBaseCStr}.nodes WHERE id = {$nodeIdAUInt} );";
      //$queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.nodes ( id, id_sys_user, id_changesets, id_user, version, visible, latitude, longitude, timestamp, id_load ) VALUES( {$nodeIdAUInt}, {$idUserAUInt}, {$changeSetAUInt}, {$userOsmIdAUInt}, {$versionAUInt}, {$visibleABol}, {$latitudeAFlt}, {$longitudeAFlt}, '{$timeStampATstp}', '{$idLoaderAUInt}' );";
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.nodes ( id, id_sys_user, id_changesets, id_user, version, visible, latitude, longitude, timestamp, id_load ) VALUES( {$nodeIdAUInt}, {$idUserAUInt}, {$changeSetAUInt}, {$userOsmIdAUInt}, {$versionAUInt}, {$visibleABol}, {$latitudeAFlt}, {$longitudeAFlt}, '{$timeStampATstp}', '{$idLoaderAUInt}' );";
      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    //createWay( $nodeAttributesAArr["ID"], $nodeAttributesAArr["CHANGESET"], $nodeAttributesAArr["UID"], $nodeAttributesAArr["VERSION"], $nodeAttributesAArr["VISIBLE"], $nodeAttributesAArr["TIMESTAMP"], $this->idUserCUInt, $this->idLoaderCUInt );
    protected function createWay( $nodeIdAUInt, $changeSetAUInt, $userOsmIdAUInt, $versionAUInt, $visibleABol, $timeStampATstp, $idUserAUInt = 1, $idLoaderAUInt = 1 )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.ways ( id, id_sys_user, id_changesets, id_user, version, visible, timestamp, id_load ) VALUES
      ( {$nodeIdAUInt}, {$idUserAUInt}, {$changeSetAUInt}, {$userOsmIdAUInt}, {$versionAUInt}, {$visibleABol}, '{$timeStampATstp}', '{$idLoaderAUInt}' );";
      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createNodeTag( $xmlPreviousIdReferenceAUInt, $versionAUInt, $tagKAttributeAStr, $tagVAttributeAStr )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.node_tags ( id_sys_user, id_reference, version, k, v ) VALUES( {$this->idUserCUInt}, {$xmlPreviousIdReferenceAUInt}, {$versionAUInt}, '{$tagKAttributeAStr}', '{$tagVAttributeAStr}' );";
      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createWayTag( $xmlPreviousIdReferenceAUInt, $versionAUInt, $tagKAttributeAStr, $tagVAttributeAStr )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.way_tags ( id_sys_user, id_reference, version, k, v ) VALUES( {$this->idUserCUInt}, {$xmlPreviousIdReferenceAUInt}, {$versionAUInt}, '{$tagKAttributeAStr}', '{$tagVAttributeAStr}' );";
      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createWayNode( $xmlPreviousIdReferenceAUInt, $nodeIdAUInt )
    {
      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.way_nodes ( id_sys_user, id_way, id_node ) VALUES( {$this->idUserCUInt}, {$xmlPreviousIdReferenceAUInt}, {$nodeIdAUInt} );";
      $this->query( $queryLStr, __FILE__, __LINE__ );
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );
    }

    protected function createOsmUser( $idUserOsmAUInt, $nameUserOsmAStr, $idUserAUInt = 1 )
    {
      $_SESSION[ "lixo" ][ "f" ] = "createOsmUser( $idUserOsmAUInt, $nameUserOsmAStr, $idUserAUInt )";
      $queryLStr = "SELECT id FROM osm_user WHERE id = {$idUserOsmAUInt} LIMIT 1";
      $_SESSION[ "lixo" ][ "select1" ] = $queryLStr;
      $queryLObj = $this->query($queryLStr, __FILE__, __LINE__);
      if($queryLObj->num_rows == 1)
      {
        $returnLArr = $queryLObj->fetch_row();
        return $returnLArr[ 0 ];
      }

      $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.osm_user ( id, id_sys_user, name ) VALUES ( {$idUserOsmAUInt}, {$idUserAUInt}, '{$nameUserOsmAStr}' )";
      $_SESSION[ "lixo" ][ "select2" ] = $queryLStr;
      $this->query($queryLStr, __FILE__, __LINE__);
      $this->addToCompressedFile( $queryLStr . "\r\n", __FUNCTION__ );

      return $idUserOsmAUInt;
    }
  }
