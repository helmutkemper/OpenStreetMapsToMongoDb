<?php

  class imageText
  {
    public static $textCArr;
    public static $counterCUInt;
    public static $fontFileCStr;
    public static $fontSizeCUInt;
    public static $textLengthUFlt;

    static public function setText( $textAStr )
    {
      self::$fontFileCStr = "arial.ttf";
      self::$fontSizeCUInt = 10;
      self::$textCArr = /*strrev*/($textAStr);
      self::$counterCUInt = 0;//strlen( $textAStr );
      self::$textLengthUFlt = imagettfbbox( self::$fontSizeCUInt, 0, self::$fontFileCStr, self::$textCArr );
    }

    static public function textTo( $img, $x1, $y1, $x2, $y2, $color = null )
    {return;
      $mul = 1;
      if( $x1 < $x2 )
      {
        $mul = -1;
        self::$textCArr = strtoupper(self::$textCArr);
        $_x1 = $x1;
        $_y1 = $y1;

        $x2 = $x1;
        $y2 = $y1;

        $x1 = $_x1;
        $y1 = $_y1;
      }

      $dot = $x1*$x2 + $y1*$y2;
      $det = $x1*$y2 - $y1*$y2;

      $angleLSFlt = atan2( ( $y2 - $y1 ), ( $x2 - $x1 ) );

      self::$textCArr = self::$textCArr . " - " . rad2deg($angleLSFlt);

      $textToImageLStr = "";

      $angleRealLSFlt = $angleLSFlt;

      $xTextLSFlt = ( float ) $x1;
      $yTextLSFlt = ( float ) $y1;

      $white = imagecolorallocate( $img, 255, 55, 55 );

      $distanceLUInt = 0;
      $distanceTotalLUInt = self::distance( $x1, $y1, $x2, $y2 );

      do
      {
        $fontBoxLArr = imagettfbbox( self::$fontSizeCUInt, rad2deg($angleLSFlt), self::$fontFileCStr, self::$textCArr[ self::$counterCUInt ] );
        $distanceLastLUInt = self::distance( $fontBoxLArr[ 4 ], $fontBoxLArr[ 5 ], $fontBoxLArr[ 6 ], $fontBoxLArr[ 7 ] ) + 5;

        if( $distanceLUInt + $distanceLastLUInt >= $distanceTotalLUInt )
        {
          //self::$counterCUInt = 0;
          break;
        }

        if($angleLSFlt > deg2rad(0))
        {
          $angleLSFlt *= -1;

          //$_xTextLSFlt = ( $xTextLSFlt - ( 5 * sin( deg2rad( 90 ) ) ) );
          //$_yTextLSFlt = ( $yTextLSFlt - ( 5 * cos( deg2rad( 90 ) ) ) );
        }
        else
        {
          //$_xTextLSFlt = ( $xTextLSFlt - ( 1 * sin( deg2rad( 90 ) ) ) );
          //$_yTextLSFlt = ( $yTextLSFlt - ( 1 * cos( deg2rad( 90 ) ) ) );
        }

        //imagettftext ( $img, self::$fontSizeCUInt, rad2deg($angleLSFlt), $xTextLSFlt, $yTextLSFlt, $white, self::$fontFileCStr, floor(rad2deg($angleLSFlt)) );break;
        imagettftext ( $img, self::$fontSizeCUInt, rad2deg($angleLSFlt), $xTextLSFlt, $yTextLSFlt, $white, self::$fontFileCStr, self::$textCArr[ self::$counterCUInt ] );
        //imagefilledellipse($img, $xTextLSFlt, $yTextLSFlt, 5, 5, $white );
        //imagettftext ( $img, self::$fontSizeCUInt, 0, $xTextLSFlt, $yTextLSFlt, $white, self::$fontFileCStr, self::$textCArr[ self::$counterCUInt ] );
        //return;

        if( strlen( self::$textCArr ) <= self::$counterCUInt + 1 )
        {
          self::$counterCUInt = 0;
          break;
        }

        //$textToImageLStr .= self::$textCArr[ self::$counterCUInt ];
        self::$counterCUInt += 1;
        //$distanceLUInt += $distanceLastLUInt;



        $xTextLSFlt = $xTextLSFlt + $distanceLastLUInt * cos( $angleRealLSFlt ) * $mul;
        $yTextLSFlt = $yTextLSFlt + $distanceLastLUInt * sin( $angleRealLSFlt ) * $mul;
      }
      while( true );
    }

    static public function distance( $x1, $y1, $x2, $y2 )
    {
      return sqrt( pow( ( $x2 - $x1 ), 2 ) + pow( ( $y2 - $y1 ), 2 ) );
    }
  }