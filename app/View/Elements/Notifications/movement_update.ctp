<?php
$data = json_decode($notification["Notification"]["data"]);
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_support_phase');
$days = floor($hours / 24);
$creator_name = $data->creator_name;
$update_text = $data->msg;
$creator_photo = $data->creator_photo;
?>
<div class="notification stat-notification">

	<div class="time-stamp" data-toggle="tooltip" data-placement="left" title="Activity in the past <?php echo $days; ?> days"><i class="fa fa-clock-o"></i><?php echo __('%s days', $days); ?></div>
	<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/' . $creator_photo; ?>)"></div>
	<h3><?php echo __('%s has posted an update', $creator_name); ?></h3>
	<p><?php echo $data->msg; ?></p>
	
	<h3><?php echo $this->Html->link(substr($movement_title, 0, 30), $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true)); ?></h3>

</div>
