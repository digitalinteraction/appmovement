<?php
$data = json_decode($notification["Notification"]["data"]);
$supporter_count = $data->supporter_count;
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_support_phase');
$days = floor($hours / 24);
?>

<div class="notification stat-notification" style="text-align: center; border-bottom: #DDD 1px dashed; padding: 20px; position: relative;">

	<div class="time-stamp" style="color: #999; cursor: pointer; position: absolute; top: 10px; right: 5px;"  data-toggle="tooltip" data-placement="left" title="Activity in the past <?php echo $days; ?> days"><i class="fa fa-clock-o" style="margin-right: 5px;"></i><?php echo __('%s days', $days); ?></div>
	
	<h2 style="color: #999; font-size: 24px; font-weight: 200; line-height: 60px; margin: 0px; padding: 0px;"><span style="color: #5cb85c; font-weight: normal; padding-right: 5px">+<?php echo __('%s</span> New Supporters</span>', $supporter_count); ?></h2>
	
	<h3 style="color: #666; font-size: 16px; line-height: 30px; margin: 0px; padding: 0px;"><?php echo $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#58c4c4')); ?></h3>

</div>
