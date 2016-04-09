<?php

  include_once "../osmXmlToMongoDb.class.php";

  class simpleForm extends osmXmlToMongoDb
  {
    public $setupCollectionCObj;

    public function __construct()
    {

    }

    public function backup()
    {
      $this->setupCollectionCObj = $this->dataBaseCObj->setupFill;

      $resLObj = fopen( "./backup.fillSetup.json", "a" );

      $returnLArr = array();

      $cursorSetupFillLObj = $this->setupCollectionCObj->find();
      foreach( $cursorSetupFillLObj as $documentSetupFillLArr )
      {
        $returnLArr[] = $documentSetupFillLArr;
      }

      fwrite( $resLObj, json_encode( $returnLArr, 1 ) . "\r\n\r\n\r\n\r\n\r\n" );
      fclose( $resLObj );
    }

    public function make()
    {
      $this->setupCollectionCObj = $this->dataBaseCObj->setupFill;

      $cursorSetupFillLObj = $this->setupCollectionCObj->find(
        array(
          "zoomFactor" => ( int ) $_REQUEST[ "z" ]
        )
      );
      $cursorSetupFillLObj->sort(
        array(
          "pointKey" => 1,
          "pointValue" => 1
        )
      );
      foreach( $cursorSetupFillLObj as $documentSetupFillLArr )
      {
        $documentSetupFillLArr[ 'element' ] = str_replace( "src=\"/w/images", "src=\"http://wiki.openstreetmap.org/w/images", $documentSetupFillLArr[ 'element' ] );
        $documentSetupFillLArr[ 'element' ] = str_replace( "srcset=\"/w/images", "srcset=\"http://wiki.openstreetmap.org/w/images", $documentSetupFillLArr[ 'element' ] );
        $documentSetupFillLArr[ 'element' ] = str_replace( " /w/images/", " http://wiki.openstreetmap.org/w/images/", $documentSetupFillLArr[ 'element' ] );
        $documentSetupFillLArr[ 'element' ] = str_replace( "href=\"/wiki/", "href=\"http://wiki.openstreetmap.org/wiki/", $documentSetupFillLArr[ 'element' ] );

        $documentSetupFillLArr[ 'rendering' ] = str_replace( "src=\"/w/images", "src=\"http://wiki.openstreetmap.org/w/images", $documentSetupFillLArr[ 'rendering' ] );
        $documentSetupFillLArr[ 'rendering' ] = str_replace( "srcset=\"/w/images", "srcset=\"http://wiki.openstreetmap.org/w/images", $documentSetupFillLArr[ 'rendering' ] );
        $documentSetupFillLArr[ 'rendering' ] = str_replace( " /w/images/", " http://wiki.openstreetmap.org/w/images/", $documentSetupFillLArr[ 'rendering' ] );
        $documentSetupFillLArr[ 'rendering' ] = str_replace( "href=\"/wiki/", "href=\"http://wiki.openstreetmap.org/wiki/", $documentSetupFillLArr[ 'rendering' ] );
        print "
          {$documentSetupFillLArr[ 'element' ]}
          {$documentSetupFillLArr[ 'rendering' ]}
          {$documentSetupFillLArr[ 'photo' ]}
          <form id='{$documentSetupFillLArr[ '_id' ]}' name='{$documentSetupFillLArr[ '_id' ]}' action='./simpleForm.php' method='post' onchange=\"sendData( '{$documentSetupFillLArr[ '_id' ]}' );\">
            <input type='hidden' name='_id' id='{$documentSetupFillLArr[ '_id' ]}_id' value='{$documentSetupFillLArr[ '_id' ]}'>
            <table>
              <tr>
                <td>
                  key
                </td>
                <td>
                  {$documentSetupFillLArr[ 'pointKey' ]}.{$documentSetupFillLArr[ 'pointValue' ]}
                </td>
              </tr>
              <tr>
                <td>
                  zoom
                </td>
                <td>
                  {$documentSetupFillLArr[ 'zoomFactor' ]}
                </td>
              </tr>
              <tr>
                <td>
                  visible
                </td>
                <td>
                  <select name='visibleForm' id='{$documentSetupFillLArr[ '_id' ]}_visibleForm'>";

                    if( $documentSetupFillLArr[ 'visible' ] == false )
                    {
                      print "<option value=0 selected>false</option>
                             <option value=1>true</option>";
                    }
                    else
                    {
                      print "<option value=0>false</option>
                             <option value=1 selected>true</option>";
                    }
print "
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='thicknessForm'>thickness</label>
                </td>
                <td>
                  <input type='text' name='thicknessForm' id='{$documentSetupFillLArr[ '_id' ]}_thicknessForm' value='{$documentSetupFillLArr[ 'thickness' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='styleForm'>style</label>
                </td>
                <td>
                  <input type='text' name='styleForm' id='{$documentSetupFillLArr[ '_id' ]}_styleForm' value='{$documentSetupFillLArr[ 'style' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='imageTileForm'>image_tile</label>
                </td>
                <td>
                  <input type='text' name='imageTileForm' id='{$documentSetupFillLArr[ '_id' ]}_imageTileForm' value='{$documentSetupFillLArr[ 'imageTile' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='typeForm'>type</label>
                </td>
                <td>
                  <select name='typeForm' id='{$documentSetupFillLArr[ '_id' ]}_typeForm'>";

                  if( $documentSetupFillLArr[ 'type' ] == "line" )
                  {
                    print "<option value='none'>none</option>
                           <option value='line' selected>line</option>
                           <option value='style'>style</option>
                           <option value='filledPolygon'>filledPolygon</option>";
                  }
                  else if( $documentSetupFillLArr[ 'type' ] == "style" )
                  {
                    print "<option value='none'>none</option>
                           <option value='line'>line</option>
                           <option value='style' selected>style</option>
                           <option value='filledPolygon'>filledPolygon</option>";
                  }
                  else if( $documentSetupFillLArr[ 'type' ] == "filledPolygon" )
                  {
                    print "<option value='none'>none</option>
                           <option value='line'>line</option>
                           <option value='style'>style</option>
                           <option value='filledPolygon' selected>filledPolygon</option>";
                  }
                  else
                  {
                    print "<option value='none' selected>none</option>
                           <option value='line'>line</option>
                           <option value='style'>style</option>
                           <option value='filledPolygon'>filledPolygon</option>";
                  }

        //<input type='text' name='typeForm' id='{$documentSetupFillLArr[ '_id' ]}_typeForm' value='{$documentSetupFillLArr[ 'type' ]}'>
print "
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='colorRedForm'>color_red</label>
                </td>
                <td>
                  <input type='text' name='colorRedForm' id='{$documentSetupFillLArr[ '_id' ]}_colorRedForm' value='{$documentSetupFillLArr[ 'colorRed' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='colorGreenForm'>color_green</label>
                </td>
                <td>
                  <input type='text' name='colorGreenForm' id='{$documentSetupFillLArr[ '_id' ]}_colorGreenForm' value='{$documentSetupFillLArr[ 'colorGreen' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='colorBlueForm'>color_blue</label>
                </td>
                <td>
                  <input type='text' name='colorBlueForm' id='{$documentSetupFillLArr[ '_id' ]}_colorBlueForm' value='{$documentSetupFillLArr[ 'colorBlue' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  <label from='colorAlphaForm'>color_alpha</label>
                </td>
                <td>
                  <input type='text' name='colorAlphaForm' id='{$documentSetupFillLArr[ '_id' ]}_colorAlphaForm' value='{$documentSetupFillLArr[ 'colorAlpha' ]}'>
                </td>
              </tr>
              <tr>
                <td>
                  &nbsp;
                </td>
                <td>
                  <button type='button' name='send' value='send' onclick=\"sendData( '{$documentSetupFillLArr[ '_id' ]}' );\">send</button>
                </td>
              </tr>
            </table>
          </form>
          -----------------------------------------------------------------------------------------------------------------------------------------------------
          <br>
          <br>
          <br>
        ";
      }
    }

    public function updateData()
    {
      $this->setupCollectionCObj = $this->dataBaseCObj->setupFill;

      $resLObj = fopen( "./backup.{$_POST[ '_id' ]}.json", "a" );
      $cursorSetupFillLObj = $this->setupCollectionCObj->find(
        array(
          "_id" => new MongoId( $_POST[ "_id" ] )
        )
      );
      foreach( $cursorSetupFillLObj as $documentSetupFillLArr )
      {
        fwrite( $resLObj, json_encode( $documentSetupFillLArr, 1 ) );
      }
      fclose( $resLObj );

      $this->setupCollectionCObj->update(
        array(
          "_id" => new MongoId( $_POST[ "_id" ] )
        ),
        array(
          '$set' => array(
            "visible" => ( $_POST[ "visibleForm" ] == 1 ) ? true : false,
            "thickness" => $_POST[ "thicknessForm" ],
            "style" => $_POST[ "styleForm" ],
            "imageTile" => $_POST[ "imageTileForm" ],
            "type" => $_POST[ "typeForm" ],
            "colorRed" => new MongoInt64( $_POST[ "colorRedForm" ] ),
            "colorGreen" => new MongoInt64( $_POST[ "colorGreenForm" ] ),
            "colorBlue" => new MongoInt64( $_POST[ "colorBlueForm" ] ),
            "colorAlpha" => new MongoInt64( $_POST[ "colorAlphaForm" ] ),
          )
        )
      );
    }
  }