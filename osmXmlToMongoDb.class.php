<?php

  class osmXmlToMongoDb
  {
    protected $connectionCObj;
    protected $dataBaseCObj;

    public function __construct()
    {

    }

    public function connect( $connectionAStr = null )
    {
      $this->connectionCObj = new MongoClient( $connectionAStr );
    }

    public function setDataBase( $dataBaseAStr )
    {
      $this->dataBaseCObj        = $this->connectionCObj->$dataBaseAStr;
    }
  }