<!-- Geolocation Map Preview -->

<?php
echo $this->Html->css('previews/colour', null, array('inline'=>false));
?>

<style type="text/css">

	.primary-colour {
		color: <?php echo $primary_colour; ?>;
	}

	.pin-colour {
		color: <?php echo $pin_colour; ?>;
	}

	.star-colour {
		color: <?php echo $star_colour; ?>;
	}

</style>

<style type="text/css">

	.map-container {
		background-size: cover;
		background-image: url("<?php echo Router::url('/', true) . 'img/ios_map.png'; ?>");
		background-repeat: no-repeat;

		height: 480px;
		width: 100%;
	}

	.map-container .pin {
		height: 36px;
		position: relative;
		width: 32px;
	}

	.map-container .pin img {
		height: 36px;
		width: 32px;
	}

	.map-container .pin.pin-a {
		left: 80px;
		top: 40px;
	}

	.map-container .pin.pin-b {
		left: 140px;
		top: 240px;
	}

	.map-container .pin.pin-c {
		left: 120px;
		top: 60px;
	}

	.map-container .pin.pin-d {
		left: 130px;
		top: 220px;
	}

	.map-container .pin.pin-e {
		left: 90px;
		top: 170px;
	}

</style>

<div class="preview">

	<div class="status-bar">

		<span class="carrier">Carrier</span>
		<span class="time">15:18</span>
		<span class="battery">97%</span>

		<div class="clearfix"></div>

	</div>

	<div class="nav-bar">

		<div class="nav-button-left nav-item primary-colour">

			<i class="fa fa-angle-left primary-colour"></i>

			<?php echo __('Home'); ?>

		</div>

		<div class="nav-title nav-item">

			<?php echo __('Discover'); ?>

		</div>

		<div class="nav-button-right nav-item primary-colour">

			<?php echo __('Add'); ?>

		</div>

	</div>

	<div class="map-container">

		<div class="pin pin-a">
			<img src="<?php echo Router::url(array('controller' => 'generate', 'action' => 'marker', 'a', 0), true) . '?colour=' . str_replace('#', '', $pin_colour); ?>">
		</div>

		<div class="pin pin-b">
			<img src="<?php echo Router::url(array('controller' => 'generate', 'action' => 'marker', 'a', 0), true) . '?colour=' . str_replace('#', '', $pin_colour); ?>">
		</div>

		<div class="pin pin-c">
			<img src="<?php echo Router::url(array('controller' => 'generate', 'action' => 'marker', 'a', 0), true) . '?colour=' . str_replace('#', '', $pin_colour); ?>">
		</div>

		<div class="pin pin-d">
			<img src="<?php echo Router::url(array('controller' => 'generate', 'action' => 'marker', 'a', 0), true) . '?colour=' . str_replace('#', '', $pin_colour); ?>">
		</div>

		<div class="pin pin-e">
			<img src="<?php echo Router::url(array('controller' => 'generate', 'action' => 'marker', 'a', 0), true) . '?colour=' . str_replace('#', '', $pin_colour); ?>">
		</div>

	</div>

</div>