<?php

  class userMySqlSupport extends db
  {
    protected function supportAddNewUser( $userNameAStr, $nickNameAStr, $passwordAStr, $emailAStr, $levelAEnm )
    {
      $queryLStr = "SELECT id FROM sys_user WHERE EMAIL = '$emailAStr' LIMIT 1";
      $queryLObj = $this->query( $queryLStr, __FILE__, __LINE__ );

      var_dump( $queryLObj );
      die();

      $queryLStr = "INSERT INTO sys_user( `id`, `name`, `nickname`, `password`, `email`, `level` ) VALUE ( null, $userNameAStr, $nickNameAStr, $passwordAStr, $emailAStr, $levelAEnm );";
      $this->query( $queryLStr, __FILE__, __LINE__ );
    }
  }