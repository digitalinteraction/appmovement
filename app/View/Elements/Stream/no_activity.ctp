<?php
$data = json_decode($notification["Notification"]["data"]);
$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
$movement_photo = $notification["Movement"]["MovementPhoto"][0]["filename"];
$supporter_target = $notification["Movement"]["target_supporters"];
?>

<div class="notification stat-notification" style="text-align: center; border-bottom: #DDD 1px dashed; padding: 20px; position: relative;">

	<h2 style="color: #999; font-size: 24px; font-weight: 200; line-height: 60px; margin: 0px; padding: 0px;"><?php echo __('Keep Sharing!'); ?></h2>
	
	<p style="color: #999; font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('It is really important that you continue to share this Movement in order to reach the goal of %s supporters!', $supporter_target); ?></p>
	
	<h3 style="color: #666; font-size: 16px; line-height: 30px; margin: 0px; padding: 0px;"><?php echo $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#58c4c4')); ?></h3>
	
</div>	
