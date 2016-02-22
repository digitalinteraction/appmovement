<!-- Missing Controller view -->

<?php
echo $this->Html->css('error', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="container">

		<h3>500 <?php echo __('Error'); ?></h3>

	</div>
</div>

<div class="row error-row">
	<div class="container">

		<h3><?php echo __('Something went wrong!'); ?></h3>
		<h3><?php echo __('We couldn\'t find what you were looking for!'); ?></h3>
		
		<br />
		
		<?php echo $this->Html->link('<p>' . __('Take me home') . ' <i class="fa fa-chevron-circle-right"></i></p>', array('controller' => 'pages', 'action' => 'display', 'home'), array('escape' => false)); ?>

	</div>
</div>