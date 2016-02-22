<!-- Mobile About view -->

<?php
echo $this->Html->css('mobile/global', null, array('inline'=>false));
?>

<!-- Header -->
<div class="row banner-row">
	<div class="container">
		<h3><?php echo __('About'); ?></h3>
	</div>
</div>

<div class="row">

	<div class="container">

		<div class="mobile-page-panel">

			<?php
			if ($movement["Movement"]["about"] != '')
			{
				echo $movement["Movement"]["about"];
			}
			else
			{
			?>

			<p><?php echo __('This app allows anyone to rate and review locations on the map.'); ?></p>

			<p><?php echo __('Anyone can share a review and post an image for others to see.'); ?></p>

			<p><?php echo __('You can view your reviews on your profile page.'); ?><p>

			<?php } ?>

		</div>

	</div>

</div>