<?php

  class genericMysql
  {
    protected $mySqlConnectionCObj;
    protected $mySqlDataBaseCStr;
    protected $mySqlConnectedSuccessfulCBol;

    public function connect ( $mySqlHostAStr = "127.0.0.1", $mySqlUserAStr = "root", $mySqlPasswordAStr = "" )
    {
      $this->mySqlConnectionCObj = new mysqli( $mySqlHostAStr, $mySqlUserAStr, $mySqlPasswordAStr );
      if ( $this->mySqlConnectionCObj->connect_errno > 0 )
      {
        die ( "Connection error / Erro de conexÃ£o: {$this->mySqlConnectionCObj->connect_error}" );
      }
      $this->mySqlConnectedSuccessfulCBol = true;
    }

    public function database ( $dataBaseNameAStr )
    {
      $this->mySqlDataBaseCStr = $dataBaseNameAStr;
      $this->mySqlConnectionCObj->select_db( $this->mySqlDataBaseCStr );

      $this->mySqlConnectedSuccessfulCBol = true;
    }

    public function query ( $queryAStr, $fileAStr, $lineAUInt )
    {
      $queryLObj = $this->mySqlConnectionCObj->query( $queryAStr );

      if ( $this->mySqlConnectionCObj->errno > 0 )
      {
        $queryLStr = "INSERT INTO {$this->mySqlDataBaseCStr}.error_event ( file_name, func_line, error, query_text ) VALUES ( '" . basename( $fileAStr ) . "', " . __LINE__ . ", '" . addslashes( $this->mySqlConnectionCObj->error ) . "', '" . addslashes( $queryAStr ) . "' );";
        $this->mySqlConnectionCObj->query( $queryLStr );
        //throw new Exception( "Error [ file: " . basename( $fileAStr ) . " line: " . $lineAUInt . " ]: " . $this->mySqlConnectionCObj->error );
      }

      return $queryLObj;
    }
  }
