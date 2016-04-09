<?php
  /**
   * Created by PhpStorm.
   * User: kemper
   * Date: 24/01/16
   * Time: 08:35
   */

  $conn = new MongoClient();
  $db   = $conn->qconsp;
  $coll = $db->ways;

  $cur  = $coll->find();

  foreach( $cur as $data )
  {
    if( count( $data[ "nodes" ] ) > 2 )
    {
      foreach( $data[ "nodes" ] as $nodeKey => $nodeValue )
      {
        if( $nodeKey == 0 )
        {
          $middle = $nodeValue;
        }
        else
        {
          $middle[ 0 ] = ( $middle[ 0 ] + $nodeValue[ 0 ] ) / 2;
          $middle[ 1 ] = ( $middle[ 1 ] + $nodeValue[ 1 ] ) / 2;
        }
      }

      $data[ "middle" ] = $middle;

      print "<pre>";
      print_r(
        array(
          array(
            "_id" => $data[ "_id" ]
          ),
          $nodeValue
        )
      );
      die();

      $coll->update(
        array(
          "_id" => $data[ "_id" ]
        ),
        $nodeValue
      );
    }
  }