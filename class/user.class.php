<?php

  class user extends userMySqlSupport
  {
    public function addNewUser ( $userNameAStr, $nickNameAStr, $passwordAStr, $emailAStr, $levelAEnm )
    {
      $passwordAStr = md5( $passwordAStr ); // todo: mudar esta m...a
      $this->supportAddNewUser( $userNameAStr, $nickNameAStr, $passwordAStr, $emailAStr, $levelAEnm );
    }
  }