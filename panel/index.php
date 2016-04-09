<html>
  <header>
    <title>Map test</title>
    <script src="../js/jquery-2.2.0.min.js" language="JavaScript"></script>
    <script language="JavaScript">
      function sendData( id )
      {
        var data = {
          _id: $( '#' + id + '_id' ).val(),
          thicknessForm: $( '#' + id + '_thicknessForm' ).val(),
          visibleForm: $( '#' + id + '_visibleForm' ).val(),
          styleForm: $( '#' + id + '_styleForm' ).val(),
          imageTileForm: $( '#' + id + '_imageTileForm' ).val(),
          typeForm: $( '#' + id + '_typeForm' ).val(),
          colorRedForm: $( '#' + id + '_colorRedForm' ).val(),
          colorGreenForm: $( '#' + id + '_colorGreenForm' ).val(),
          colorBlueForm: $( '#' + id + '_colorBlueForm' ).val(),
          colorAlphaForm: $( '#' + id + '_colorAlphaForm' ).val()
        };

        $.post({
          url: './simpleFormPost.php',
          data: data
        });
      }
    </script>
  </header>
  <body>
    <?php

      include_once "./simpleForm.class.php";

      $formLObj = new simpleForm();
      $formLObj->connect();
      $formLObj->setDataBase( "qconsp" );
      //$formLObj->backup();

      if( isset( $_POST[ "_id" ] ) )
      {
        //$formLObj->updateData();
      }

      $formLObj->make();

    ?>
  </body>
</html>