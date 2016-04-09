<?php

  $conn = new MongoClient();
  $db   = $conn->qconsp;

  $coll1 = $db->setupFill;
  $cur1 = $coll1->find();

  $coll2 = $db->setupMap;
  $cur2 = $coll2->find();

  $a = array();

  foreach( $cur1 as $l )
  {
    $a[] = $l;
  }

  $res = fopen( "setupFill.backup.json", "w" );
  fwrite( $res, json_encode( $a, 1 ) );
  fclose( $res );

  $a = array();

  foreach( $cur2 as $l )
  {
    $a[] = $l;
  }

  $res = fopen( "setupMap.backup.json", "w" );
  fwrite( $res, json_encode( $a, 1 ) );
  fclose( $res );