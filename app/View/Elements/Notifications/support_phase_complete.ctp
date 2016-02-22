<?php
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$supporter_target = $notification["Movement"]["target_supporters"];
?>

<div class="notification info-notification">

	<h2><?php echo __('Movement Supported'); ?></h2>

	<p><?php echo __('Congratulaions! %s has gathered over <strong>%s supporters.</strong> You can now access the <strong>design phase</strong> where you can contribute and vote on ideas. Click below to get started.', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true)), $supporter_target); ?></p>

	<div class="stat-notification">
		<h3><?php echo $this->Html->link(__('View Design Phase'), array('controller' => 'tasks', 'action' => 'landing', $movement_id)); ?></h3>
	</div>

</div>
