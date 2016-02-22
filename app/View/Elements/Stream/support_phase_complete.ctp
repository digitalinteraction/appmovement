<?php
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$supporter_target = $notification["Movement"]["target_supporters"];
?>

<div class="notification info-notification" style="border-bottom: #DDD 1px dashed; padding: 20px; position: relative;">

	<h2 style="color: #999; font-size: 24px; font-weight: 200; line-height: 60px; margin: 0px; padding: 0px;"><?php echo __('Movement Supported'); ?></h2>

	<p style="color: #999; font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('Congratulaions! %s has gathered over <strong>%s supporters.</strong> You can now access the <strong>design phase</strong> where you can contribute and vote on ideas. Click below to get started.', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#58c4c4')), $supporter_target); ?></p>

	<div class="stat-notification">
		<h3 style="color: #666; font-size: 16px; line-height: 30px; margin: 0px; padding: 0px;"><?php echo $this->Html->link(__('View Design Phase'), array('controller' => 'tasks', 'action' => 'landing', $movement_id)); ?></h3>
	</div>

</div>
