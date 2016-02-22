<?php
$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
?>

<div class="notification info-notification">

	<h2><?php echo __('Design Phase Complete'); ?></h2>

	<p><?php echo __('%s has now completed the design phase.', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true))); ?></p>

	<p><?php echo __('The next stage for your Movement is the launch phase where we will generate your app based on the results from the design process. We will submit it to the app store and notify you and other supporters when it is ready to download. You can track the progress of the app through the various stages of its launch by clicking the link below.'); ?></p>

	<div class="stat-notification">
		<h3><?php echo $this->Html->link('View Launch Phase', array('controller' => 'movements', 'action' => 'launch', $movement_id)); ?></h3>
	</div>
	
</div>