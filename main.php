<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Photos</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
      body {
        background-color: white;
        touch-action: none;
      }

      #slides_container {
        position: fixed;
        margin: auto;
        right: 0;
        left: 0;
        top: 0;
        bottom: 0;
        height: 90vh;
        width: 95vw;
      }

      #pic_container {
        position: absolute;
        margin: auto;
        right: 0;
        left: 0;
        top: 0;
        bottom: 0;
        border: 5px black solid;
        max-height: 85%;
        max-width: 90%;
      }
    </style>
  </head>
<body>
  <?php
    $imgArray = array_slice(scandir('images'), 2); // removes '.' and '..'
  ?>

  <div id="slides_container">
    <img id="pic_container"/>
  </div>
</body>
<script type="text/javascript">
  let imgArray = <?php echo json_encode($imgArray); ?>;
  let timerLength = <?php echo file_get_contents('timer-length.txt'); ?>;
  // the timer length is stored in a .txt file
  let i = 0;

  function changePic() {
    document.getElementById("pic_container").setAttribute("src", "./images/"+imgArray[i]);
    if (i == (imgArray.length - 1)) {
      i = 0;
      setTimeout(function() {
        window.location.reload(); // refreshes the page to add in any newly deleted or added pics
      }, timerLength); // after the last pic's normal show time elapses
    } else {
      i++;
    }
  } changePic(); // shows the first pic instantly without having to wait the x amount of seconds

  setInterval(function() {
    changePic(); // shows every pic after the first one
  }, timerLength);
</script>
</html>
