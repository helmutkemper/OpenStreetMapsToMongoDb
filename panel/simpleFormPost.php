<?php

  include_once "./simpleForm.class.php";

  $formLObj = new simpleForm();
  $formLObj->connect();
  $formLObj->setDataBase( "qconsp" );
  //$formLObj->backup();

  if( isset( $_POST[ "_id" ] ) )
  {
    $formLObj->updateData();
  }


