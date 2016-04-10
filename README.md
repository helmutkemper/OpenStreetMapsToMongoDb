#Open Street Map XML

Este é um trabalho preliminar e sujeito a mudanças. Use por sua conta e risco.

```PHP
<?php

  session_start();

  include_once "./size.class.php";

  include_once "./genericMysql.class.php";
  include_once "./db.class.php";

  include_once "./userMySqlSupport.class.php";
  include_once "./user.class.php";


  include_once "./osmXmlMySqlSupport.class.php";
  include_once "./osmXml.class.php";

  $userLObj = new user();

  // true - salva um arquivo com o SQL do banco no disco rígido
  // size::KByte( 10 ) - Lê apenas 10KB do arquivo .osm por vês, bom para computadores lentos
  // size::MByte( 10 ) - Espera o arquivo SQL ocupar 10MB na memória para compactar ( este é o
  //                     tamanho máximo do arquivo para a instalação básica do XAMPP )
  $parserXmlLObj = new osmXml( true, size::KByte( 10 ), size::MByte( 10 ) );
  $parserXmlLObj->connect( "127.0.0.1", "user", "password" );
  
  // Apenas deixe isto, por enquanto
  $parserXmlLObj->addIdUser( 1 );
  
  // Apenas deixe isto, por enquanto
  $parserXmlLObj->addIdLoader( 1 );
  
  // Nome da nova base de dados
  $parserXmlLObj->createDataBaseAndSelect( "gis" );
  
  // Cria a base de dados completa
  $parserXmlLObj->createTables();
  
  // Processa o arquivo XML
  $parserXmlLObj->processOsmFile( "./brazil-latest.osm" );

```
