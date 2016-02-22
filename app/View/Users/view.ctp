<!-- Profile view -->

<?php
echo $this->Html->css('dashboard', null, array('inline'=>false));
?>

<style type="text/css">
#movements-container {
	padding: 25px 5px;
}

@media (max-width: 992px) {
	#movements-container {
		margin: 0 auto;
	}
}
</style>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('%s\'s Profile', strtok($user['User']['fullname'], " ")); ?></h3>

	</div>
</div>

<div class="row profile-row">
	<div class="container">

		<div class="col-md-3">

			<div class="profile-container">

				<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/' . $user['User']['photo']; ?>)"></div>

				<h2><?php echo $user['User']['fullname']; ?></h2>

				<?php echo '<h5>' . __('Joined') . ' ' . date("Y", strtotime($user['User']['created'])) . '</h5>'; ?>

			</div>

		</div>

		<div class="col-md-9">

			<?php if (count($movements) > 0) { ?>

				<div id="movements-container" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 265, "gutter": 20, "itemSelector": ".movement-tile" }'>

					<?php foreach ($movements as $movement) { ?>

						<?php echo $this->element('movement_tile', array('movement' => $movement)); ?>

					<?php } ?>

				</div>

			<?php } else { ?>


				<div class="no-results"><h3><?php echo __('%s has no movements', strtok($user['User']['fullname'], " ")); ?></h3></div>

			<?php } ?>

		</div>

	</div>
</div>