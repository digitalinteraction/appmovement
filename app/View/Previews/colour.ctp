<!-- Colour Preview -->

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

			<?php echo __('Discover'); ?>

		</div>

		<div class="nav-title nav-item">

			<?php echo __('Place'); ?>

		</div>

		<div class="nav-button-right nav-item primary-colour">

			<?php echo __('Add Review'); ?>

		</div>

	</div>

	<div class="venue-photo">

		<div class="venue-name">

			<?php echo __('Name of Place'); ?>

		</div>

	</div>

	<div class="ratings-container">

		<div class="rating">

			<div class="rating-title">

				<?php echo __('Rating Option A'); ?>

			</div>

			<div class="rating-stars">

				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star"></i>

			</div>

		</div>

		<div class="rating">

			<div class="rating-title">

				<?php echo __('Rating Option B'); ?>

			</div>

			<div class="rating-stars">

				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star"></i>

			</div>

		</div>

		<div class="rating">

			<div class="rating-title">

				<?php echo __('Rating Option C'); ?>

			</div>

			<div class="rating-stars">

				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star"></i>
				<i class="fa fa-star"></i>

			</div>

		</div>

		<div class="rating">

			<div class="rating-title">

				<?php echo __('Rating Option D'); ?>

			</div>

			<div class="rating-stars">

				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star star-colour"></i>
				<i class="fa fa-star"></i>

			</div>

		</div>

	</div>

</div>