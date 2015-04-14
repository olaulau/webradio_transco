<?php
session_start();

require_once __DIR__.'/admin.class.php';

Admin::restrict();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
<title>Webradio Transco</title>

<!-- Bootstrap -->
<link href="../external/bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link href="../external/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="external/jquery.min.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="external/bootstrap/js/bootstrap.min.js"></script>

<script src="index.js"></script>
</head>
<body>

<div class="container">
	<h1>Add a stream</h1>
	<form action="add.post.php" method="post">
	  <div class="form-group form-group-lg">
	    <label for="original_url">Original URL</label>
	    <input type="text" class="form-control" id="original_url" name="original_url" placeholder="original URL" required>
	  </div>
	  
	  <div class="form-group form-group-lg">
	    <label for="acodec">Audio codec</label>
	    <select class="form-control" id="acodec" name="acodec" required>
	    	<option value="" disabled selected>audio codec</option>
		  	<option value="vorb">vorg</option>
		</select>
	  </div>
	  
	   <div class="form-group form-group-lg">
	    <label for="ab">Audio bitrate</label>
	    <input type="number" class="form-control" id="ab" name="ab" placeholder="audio bitrate" min="32" max="320" required>
	  </div>
	  
	  <div class="form-group form-group-lg">
	    <label for="mux">Mux</label>
	    <select class="form-control" id="mux" name="mux" required>
	    	<option value=""disabled selected>mux</option>
		  	<option value="ogg">ogg</option>
		</select>
	  </div>

	  <button type="submit" class="btn btn-lg btn-default">Add</button>
	</form>
</div>


  </body>
</html>
