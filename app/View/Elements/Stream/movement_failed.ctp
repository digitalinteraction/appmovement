<?php
$data = json_decode($notification["Notification"]["data"]);

$username = $notification["User"]["username"];
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$supporter_count = $data->supporter_count;
$supporter_target = $notification["Movement"]["target_supporters"];
?>

<div class="notification info-notification" style="border-bottom: #DDD 1px dashed; padding: 20px; position: relative;">

	<h2 style="color: #999; font-size: 24px; font-weight: 200; line-height: 60px; margin: 0px; padding: 0px;"><?php echo __('Your Movement was unsuccessful'); ?></h2>

	<p style="color: #999; font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('<strong>%s people</strong> supported the idea of creating an app for %s. Unfortunately this time you missed our target of and will not be taken forward into the design phase.', $supporter_count, $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#58c4c4')), $supporter_target); ?></p>

</div>