<?php
echo $this->Html->css('notifications', null, array('inline'=>false));

$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
?>

<div class="notification info-notification" style="border-bottom: #DDD 1px dashed; padding: 20px; position: relative;">

	<h2 style="color: #999; font-size: 24px; font-weight: 200; line-height: 60px; margin: 0px; padding: 0px;"><?php echo __('Congratulations! Your App is Now Available!'); ?></h2>
	
	<p style="color: #999; font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('%s has now been fully designed by the supporting members of the community and has been generated! Click below to download the app.', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#58c4c4'));); ?></p>

	<div class="stat-notification">
		<h3 style="color: #666; font-size: 16px; line-height: 30px; margin: 0px; padding: 0px;"><?php echo $this->Html->link(__('View Launch Phase'), array('controller' => 'movements', 'action' => 'launch', $movement_id)); ?></h3>
	</div>
	
</div>