<?php
$data = json_decode($notification["Notification"]["data"]);
$supporter_count = $data->supporter_count;
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_support_phase');
$days = floor($hours / 24);
?>

<div class="notification stat-notification">

	<div class="time-stamp" data-toggle="tooltip" data-placement="left" title="Activity in the past <?php echo $days; ?> days"><i class="fa fa-clock-o"></i><?php echo __('%s days', $days); ?></div>

	<h2><span>+<?php echo $supporter_count; ?></span><?php echo __(' New Supporters'); ?></span></h2>
	
	<h3><?php echo $this->Html->link(substr($movement_title, 0, 30), $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true)); ?></h3>

</div>
