<?php
$data = json_decode($notification["Notification"]["data"]);

$username = $notification["User"]["username"];
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$supporter_count = $data->supporter_count;
$supporter_target = $notification["Movement"]["target_supporters"];
?>

<div class="notification info-notification">

	<h2><?php echo __('Movement was Unsuccessful'); ?></h2>

	<p><?php echo __('<strong>%s people</strong> supported the idea of creating an app for %s. Unfortunately this time you missed our target of %s and will not be taken forward into the design phase.', $supporter_count, $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true)), $supporter_target); ?><p>

</div>