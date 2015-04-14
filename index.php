<?php
session_start();
if(isset($_SESSION['admin']) && $_SESSION['admin'] === TRUE)
	echo "ADMIN <br/>";
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
<link href="external/bootstrap/css/bootstrap.min.css" rel="stylesheet">

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
</head>
<body>
	<h1>Webradio Transco</h1>


	<div class="">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th>#</th>
					<th>Original URL</th>
					<th>transcoded format</th>
					<th>lien</th>
				</tr>
			</thead>
			<tbody>

<?php
require_once 'Stream.class.php';

// DB
Stream::prepare_db ();
Stream::create_structure ();

$streams = Stream::get_all ();
foreach ( $streams as $s ) {
	?>
	<tr>
		<td><?=$s->get_id()?></td>
		<td><?=$s->get_original_url()?></td>
		<td><?=' [' . $s->get_mux() . ' ' . $s->get_acodec() . ' ' . $s->get_ab() . 'kbps]'?></td>
		<td><a href="stream.php?id=<?= $s->get_id()?>">GO</a></td>
	</tr>
	<?php
}
?>
			</tbody>
		</table>
	</div>
  </body>
</html>
