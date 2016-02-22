<!-- Overview view -->

<?php
echo $this->Html->css('discover', null, array('inline'=>false));
echo $this->Html->script('movements/overview');
?>

<div class="row banner-row">
	<div class="custom-container">

		<h3><?php echo __('Overview'); ?></h3>

	</div>
</div>

<div class="row sort-row">
	<div class="container">

		<ul>
	        <?php if (count($movements) > 0 ) { echo '<li class="filter-button active" id="filter-everything"><a>' . __('All') . '</a></li>'; } ?>
	        <?php if ($number_in_support != 0) { echo '<li class="filter-button" id="filter-support"><a>' . __('Support') . '</a></li>'; } ?>
	        <?php if ($number_in_design != 0) { echo '<li class="filter-button" id="filter-design"><a>' . __('Design') . '</a></li>'; } ?>
	        <?php if ($number_in_launch != 0) { echo '<li class="filter-button" id="filter-launch"><a>' . __('Launch') . '</a></li>'; } ?>
    	</ul>

	</div>
</div>

<div class="row movements-row">
	<div class="custom-container">

		<?php if (count($movements) == 0) {
			echo '<div class="no-results"><h3>' . __('No Movements Found') . '</h3><h5>' . __('You have not started or supported any yet!') . '</h5></div>';
		} ?>

		<?php if ($movements) { ?>

			<div id="movements-container" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 265, "gutter": 20, "itemSelector": ".movement-tile" }'>

				<?php foreach ($movements as &$movement) {

					echo $this->element('movement_tile', array('movement' => $movement));

				} ?>

			</div>

		<?php } ?>

	</div>
</div>

<script type="text/javascript">

	$('#activity-tab li').addClass('active');

</script>