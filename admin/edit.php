<?php
session_start();

require_once __DIR__.'/../includes/Stream.class.php';
require_once __DIR__.'/../includes/VLC_capabilities.class.php';
require_once __DIR__.'/admin.class.php';

Admin::restrict();

if(!empty($_GET['id'])) {
	$id = $_GET['id'];
	if(ctype_digit($id) && $id > 0) {
		$id = (int)$id;
		Stream::prepare_db();
		$stream = Stream::find_stream($id);
		if(isset($stream)) {
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
		<form action="edit.post.php" method="post">
			<input type="hidden" name="id" id="id" value="<?= $stream->get_id() ?>" />
		
			<div class="form-group form-group-lg">
				<label for="name">Name</label> <input type="text" class="form-control" id="name"
					name="name" placeholder="Name" value="<?= $stream->get_name() ?>" required>
			</div>

			<div class="form-group form-group-lg">
				<label for="original_url">Original URL</label> <input type="text" class="form-control" id="original_url"
					name="original_url" placeholder="original URL" value="<?= $stream->get_original_url() ?>" required>
			</div>

			<div class="form-group form-group-lg">
				<label for="acodec">Audio codec</label> <select class="form-control" id="acodec" name="acodec" required>
				<option value="" disabled>audio codec</option>
				<?php
				foreach (VLC_capabilities::$acodecs as $acodec => $label) {
					$selected = $stream->get_acodec() == $acodec ? 'selected' : '';
					echo '<option value="'.$acodec.'" '.$selected.'>'.$label.'</option>' . PHP_EOL;
				}
				?>
				</select>
			</div>

			<div class="form-group form-group-lg">
				<label for="ab">Audio bitrate</label> <input type="number" class="form-control" id="ab" name="ab"
					placeholder="audio bitrate" min="32" max="320" value="<?= $stream->get_ab() ?>" required>
			</div>

			<div class="form-group form-group-lg">
				<label for="mux">Mux</label> <select class="form-control" id="mux" name="mux" required>
					<option value="" disabled selected>mux</option>
					<?php
					foreach (VLC_capabilities::$muxers as $mux => $label) {
						$selected = $stream->get_mux() == $mux ? 'selected' : '';
						echo '<option value="'.$mux.'" '.$selected.'>'.$label.'</option>' . PHP_EOL;
					}
					?>
				</select>
			</div>

			<button type="submit" class="btn btn-lg btn-default">Add</button>
		</form>
	</div>

</body>
</html>
<?php
		}
		else {
			die("id doesn't exist");
		}
	}
	else {
		die("id is not a positive integer");
	}
}
else {
	die("no stream id specified");
}
