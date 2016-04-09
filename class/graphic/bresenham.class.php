<?php

  class bresenham// extends Graph
  {

    //$angle = atan2( $y0 - $y1, $x0 - $x1 ) * 180 / pi();

    public function __construct()
    {

    }

    public static function circle( $img, $x1, $y1, $r, $color )
    {
      // a e b são o centro
      //( px - a )^2 + ( py - b )^2 = r^2

      $pXMinLSInt = ( int ) $x1 - ( $r / 2 );
      $pXMaxLSInt = ( int ) $x1 + ( $r / 2 );

      $pYMinLSInt = ( int ) $y1 - ( $r / 2 );
      $pYMaxLSInt = ( int ) $y1 + ( $r / 2 );

      for( $pXSInt = $pXMinLSInt; $pXSInt <= $pXMaxLSInt; $pXSInt += 1 )
      {
        for( $pYSInt = $pYMinLSInt; $pYSInt <= $pYMaxLSInt; $pYSInt += 1 )
        {
          if( ( $pXSInt - $x1 )^2 + ( $pYSInt - $y1 )^2 == $r^2 )
          {
            imagesetpixel( $img, $pXSInt, $pYSInt, $color );
          }
        }
      }
    }

    public static function __lineTo( $img, $x1, $y1, $color, $r = 1 )
    {


      for($l=0;$l<$r;$l+=1)
      {
        for( $i = 89.95; $i < 90.05; $i += 0.01 )
        {
          $_x1 = $x1 + $r * sin( $i );
          $_y1 = $y1 + $r * cos( $i );
          imagesetpixel( $img, $_x1, $_y1, $color );
        }

        $x1 = $x1 + 1 * sin( 90 );
        $y1 = $y1 + 1 * cos( 90 );
      }


/*
      for($i=269.95;$i<270.05;$i+=0.01)
      {
        $_x2 = $x1 + $r * sin( $i );
        $_y2 = $y1 + $r * cos( $i );
        imagesetpixel( $img, $_x2, $_y2, $color );
      }
*/
    }

    public static function lineTo( $img, $x1, $y1, $x2, $y2, $color, $r = 1 )
    {
      $dy = $y2 - $y1;
      $dx = $x2 - $x1;

      if ( $dy < 0 )
      {
        $dy = $dy * -1;
        $stepy = -1;
      }
      else
      {
        $stepy = 1;
      }

      if ( $dx < 0 )
      {
        $dx = $dx * -1;
        $stepx = -1;
      }
      else
      {
        $stepx = 1;
      }

      $dy *= 2;
      $dx *= 2;

      self::__lineTo( $img, $x1, $y1, $color, $r );

      if( $dx > $dy )
      {
        $fraction = $dy - ( $dx / 2 );
        while ( $x1 != $x2 )
        {
          if ( $fraction >= 0 )
          {
            $y1 += $stepy;
            $fraction -= $dx;
          }

          $x1 += $stepx;
          $fraction += $dy;

          self::__lineTo( $img, $x1, $y1, $color, $r );
        }
      }
      else
      {
        $fraction = $dx - ( $dy / 2 );
        while ( $y1 != $y2 )
        {
          if( $fraction >= 0 )
          {
            $x1 += $stepx;
            $fraction -= $dy;
          }
          $y1 += $stepy;
          $fraction += $dx;

          self::__lineTo( $img, $x1, $y1, $color, $r );
        }
      }
    }
  }