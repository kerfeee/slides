<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Admin Page</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <style>
      body {
        overflow: hidden;
      }

      #overlay {
        position: absolute;
        margin: auto;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        height: 100vh; /*covers the entire page*/
        width: 100vw;
        background-color: white;
        z-index: 1;
      }

      #options {
        position: absolute;
        margin: auto;
        right: 10px;
        float: right;
        border: 2px black solid;
        border-radius: 50%;
        font-size: 1.45em;
        padding-right: 7.5px;
        padding-left: 7.5px;
      }

      #photo_gallery {
        position: fixed;
        /*fixed positioning prevents website from being scrollable*/
        right: 0;
        left: 0;
        border: 2px black solid;
        padding: 20px;
        height: 50vh;
        width: 90vw;
        display: grid;
        grid-template-columns: auto auto auto;
        justify-content: center;
        column-gap: 50px;
        row-gap: 20px;
        overflow-y: scroll;
        overflow-x: hidden;
      }

      #pic_selector {
        position: absolute;
        margin: auto;
        bottom: 5vh;
        left: 0;
        right: 0;
        width: 95vw;
      }

      #element_label {
        border: 2px black solid;
        padding: 5px;
        font-size: 1.5em;
      }

      #selected_pic {
        display: none;
      }

      #submit {
        float: right;
        font-size: 1em;
        border: 2px black solid;
      }
    </style>
  </head>
<body>
  <div id="overlay">
     <!-- covers entire page until all elements have loaded so nothing is half loaded -->
  </div>

  <div style="text-align:center;">
    <h2 style="display:inline-block;">Admin Page</h2>
    <span id="options">&excl;</span>
  </div>

  <div id="photo_gallery">
  </div>

  <div id="pic_selector">
    <form action="admin.php" method="POST" enctype="multipart/form-data">
      <label id="element_label">
        <span id="pic_caption">Add Picture</span>
        <input type="file" multiple class="btns" name="selected_pics[]" id="selected_pic"/>
      </label>
      <input type="submit" class="btns" value="Submit" id="submit"/>
    </form>
  </div>

  <?php
    ini_set('display_errors', 1);
    $imgArray = array_slice(scandir('images'), 2); // removes '.' and '..'

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($_FILES['selected_pics']['name']) {
        for ($i = 0; $i < count($_FILES['selected_pics']['name']); $i++) {
          move_uploaded_file($_FILES['selected_pics']['tmp_name'][$i], "./images/{$_FILES['selected_pics']['name'][$i]}");
        }
      }
      header('Location: admin.php'); // prevents from resending information
    }
  ?>

  <form action="admin.php" method="POST" id="hidden_form" style="display:none;">
    <input type="text" name="timer_length" id="timer_length"/>
    <input type="text" name="delete_file" id="delete_file"/>
    <button type="submit" form="hidden_form" id="hidden_form_btn"></button>
  </form>

  <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      if ($_POST['timer_length']) {
        file_put_contents('timer-length.txt', $_POST['timer_length'], LOCK_EX);
        header('Location: admin.php'); // prevents from resending information
      } else if ($_POST['delete_file']) {
        unlink('./images/'.$_POST['delete_file']);
        header('Location: admin.php'); // prevents from resending information
      }
    }
  ?>
</body>
<script type="text/javascript">
  window.addEventListener("DOMContentLoaded", function() { // removes the overlay once DOM has loaded
    let opacity = 1;
    let fadeIn = setInterval(function() {
      opacity -= 0.1;
      document.getElementById("overlay").style.opacity = `${opacity}`; // is recursively updated
      if (opacity < 0.1) {
        document.getElementById("overlay").style.opacity = "0";
        // floating numbers set the opacity to negative
        document.getElementById("overlay").style.display = "none";
        clearInterval(fadeIn);
        console.log(`STOP ${document.getElementById("overlay").style.opacity}`);
      }
    }, 50);
  })

  // event listener for when an image file has been selected
  document.getElementById("selected_pic").addEventListener("change", function() {
    document.getElementById("pic_caption").innerText = "Pictures Selected!";
  })

  document.getElementById("options").addEventListener("click", function() {
    let timerLength = (parseFloat(window.prompt("Photo timer length (seconds):")) * 1000);
    // seconds to milliseconds, multiply entered value by 1000
    // turn response into float while still rejecting chars
    if (Number.isInteger(timerLength)) {
      document.getElementById("timer_length").value = timerLength;
      document.getElementById("hidden_form_btn").click();
    } else {
      alert("Sorry, incorrect information was given.");
    }
  })

  let imgArray = <?php echo json_encode($imgArray); ?>;
  const gallery = document.getElementById("photo_gallery");

  for (let i = 0; i < imgArray.length; i++) {
    let picNode = document.createElement("IMG");
    picNode.setAttribute("class", "pic_items");
    picNode.style.height = "100%";
    picNode.style.width = "100%";
    picNode.style.border = "2px black solid";
    picNode.setAttribute("src", "./images/"+imgArray[i]);
    gallery.appendChild(picNode);

    document.getElementsByClassName("pic_items")[i].addEventListener("dblclick", function(e) {
      let confirmation = confirm("Would you like to delete this photo?");
      let srcPath = e.target.getAttribute("src");
      let fileName = srcPath.slice(9, srcPath.length);
      // characters 0-8 are "./images/" so everything past that is the file name

      if (confirmation) {
        document.getElementById("delete_file").value = fileName;
        document.getElementById("hidden_form_btn").click();
      }
    })
  }
</script>
</html>
