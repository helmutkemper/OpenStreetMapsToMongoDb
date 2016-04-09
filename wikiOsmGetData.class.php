<?php

  class wikiOsmGetData
  {
    public function __construct ( $wikiUrlAStr )
    {
      $fileHtmlLX = file( $wikiUrlAStr );
      $fileHtmlLX = implode( "", $fileHtmlLX );

      preg_match_all( "%(<table.*?/table>)%si", $fileHtmlLX, $matchAllTablesLArr );
      preg_match_all( "%(<h3><span.*?</span></h3>)%si", $fileHtmlLX, $matchAllH3SpanLArr );

      foreach( $matchAllTablesLArr[ 1 ] as $matchAllTablesKeyLUInt => $matchAllTablesValueLX )
      {
        $type = strip_tags( $matchAllH3SpanLArr[ $matchAllTablesKeyLUInt ][ 0 ] );

        preg_match_all( "%(<tr.*?/tr>)%si", $matchAllTablesValueLX, $matchAllTrLArr );
        foreach( $matchAllTrLArr[ 1 ] as $matchAllTrKeyLUIny => $matchAllTrValueLStr )
        {
          preg_match_all( "%<t[d].*?>(.*?)</t[d]>%si", $matchAllTrValueLStr, $matchAllTdLArr );
          if( count( $matchAllTdLArr[ 1 ] ) == 0 )
          {
            continue;
          }

          foreach( $matchAllTdLArr[ 1 ] as $matchAllTdKryLUInt => $ignoredValueLX )
          {
            if ( in_array( $matchAllTdKryLUInt, array( 0, 1 ) ) )
            {
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = strip_tags( $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = trim( $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = addslashes( $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
            }
            else
            {
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = preg_replace( "%^(.*?src=['\"])(.*?)(['\"].*)$%", "$1http://wiki.openstreetmap.org$2$3", $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = preg_replace( "%^(.*?href=['\"])(.*?)(['\"].*)$%", "$1http://wiki.openstreetmap.org$2$3", $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = strip_tags( $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ], "<a><img>" );
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = trim( $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
              $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] = addslashes( $matchAllTdLArr[ 1 ][ $matchAllTdKryLUInt ] );
            }
          }
          for( $i = 0; $i != 6; $i++ )
          {
            if( !isset( $matchAllTdLArr[ 1 ][ $i ] ) )
            {
              $matchAllTdLArr[ 1 ][ $i ] = "null";
            }
            else
            {
              $matchAllTdLArr[ 1 ][ $i ] = "'{$matchAllTdLArr[ 1 ][ $i ]}'";
            }
          }
          print "INSERT INTO `map_fill`(`id`, `humam_type`, `chave`, `tag`, `element`, `comment`, `rendering`, `photo`, `thickness`, `color_red`, `color_green`, `color_blue`, `color_alpha`) VALUES ( null, '{$type}', {$matchAllTdLArr[ 1 ][ 0 ]}, {$matchAllTdLArr[ 1 ][ 1 ]}, {$matchAllTdLArr[ 1 ][ 2 ]}, {$matchAllTdLArr[ 1 ][ 3 ]}, {$matchAllTdLArr[ 1 ][ 4 ]}, null, 1, 0, 0, 0, 0 );\r\n";
        }
      }

      //print "<pre>";
      //print_r( $matchAllH3SpanLArr );
      //print_r( $matchAllTablesLArr );
      //print "</pre>";
      die();
    }
  }
