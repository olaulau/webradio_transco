<?php
require_once __DIR__.'/includes/ALL.inc.php';

// DB
Stream::prepare_db ();
Stream::create_structure ();
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
<link href="external/bootstrap/css/bootstrap-theme.min.css" rel="stylesheet">

<link href="index.css" rel="stylesheet">



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

<div class="container" role="main">
<?php
session_start();
if(!empty($_SESSION['messages'])) {
	foreach ($_SESSION['messages'] as $message) {
		?>
		<div class="alert alert-success " role="alert">
			<?=$message?>
		</div>
		<?php
	}
	unset($_SESSION['messages']);
}
session_write_close();
?>
</div>


	<?php
	if(Admin::is_admin()) {
		?>
		<a href="admin/auth/signout.php"><button type="button" class="btn btn-lg btn-primary pull-right">Log out</button></a>
		<?php
	}
	else {
		?>
		<a href="admin/auth/signin.php"><button type="button" class="btn btn-lg btn-primary pull-right">Log in</button></a>
		<?php
	}
	?>
	
	<h1>Webradio Transco</h1>


	<div class="">
		<table class="table table-bordered table-hover">
			<thead>
				<tr>
					<th rowspan=2>#</th>
					<th rowspan=2>Name</th>
					<th colspan=3>listeners</th>
					<th rowspan=2>transcoded format</th>
					<?php
					if(Admin::is_admin()) {
						?>
						<th colspan=2>Original stream</th>
						<th colspan=2>internal VLC server</th>
						<?php
					}
					?>
					<th rowspan=2>lien</th>
					<?php if(Admin::is_admin()) {?> <th rowspan=2>actions</th> <?php } ?>
				</tr>
				<tr>
					<th>actual</th>
					<th>peak</th>
					<th>total</th>
					<?php
					if(Admin::is_admin()) {
						?>
						<th>URL</th>
						<th>track id</th>

						<th>HTTP port</th>
						<th>PID</th>
						<?php
					}
					?>
				</tr>
			</thead>
			<tbody>

<?php
$streams = Stream::get_all ();
foreach ( $streams as $s ) {
	?>
	<tr>
		<td><?=$s->get_id()?></td>
		<td><?=$s->get_name()?></td>
		<td><?=$s->get_actual_viewers()?></td>
		<td><?=$s->get_peak_viewers()?></td>
		<td><?=$s->get_total_viewers()?></td>
		<td><?=$s->get_mux() . ' ' . $s->get_acodec() . ' ' . $s->get_ab() . ' kbps'?></td>
		<?php
		if(Admin::is_admin()) {
			?>
			<td><?=$s->get_original_url()?></td>
			<td><?=$s->get_original_track_id()?></td>
			<td><?=$s->get_dest_port()?></td>
			<td><?=$s->get_pid()?></td>
			<?php
		}
		?>
		<td><a href="stream.php?id=<?= $s->get_id()?>"><span class="glyphicon glyphicon-play" aria-hidden="true" title="play"></span></a></td>
		<?php
		if(Admin::is_admin()) {
		?>
			<td>
				<a href="admin/force_stop.php?id=<?= $s->get_id()?>" alt="force stop" ><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>
				<a href="admin/edit.php?id=<?= $s->get_id()?>"><span class="glyphicon glyphicon-edit" aria-hidden="true"></span></a>
				<a href="admin/delete.php?id=<?= $s->get_id()?>"><span class="glyphicon glyphicon-remove" aria-hidden="true" title="remove"></span></a>
			</td>
		<?php
		}	
		?>
	</tr>
	<?php
}
?>
			</tbody>
		</table>
	</div>
	<?php
	if(Admin::is_admin()) {
		?>
		<a href="admin/edit.php">
		<button type="button" class="btn btn-primary btn-lg">
			<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add
		</button>
		</a>
		<?php
	}
	?>
  </body>
</html>
