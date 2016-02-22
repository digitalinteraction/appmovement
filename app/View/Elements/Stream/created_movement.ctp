<?php
$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
$movement_photo = $notification["Movement"]["MovementPhoto"][0]["filename"];
$supporter_target = $notification["Movement"]["target_supporters"];
$support_duration = $notification["Movement"]["support_duration"];
?>

<div class="notification info-notification" style="border-bottom: #DDD 1px dashed; padding: 20px; position: relative;">

	<h2 style="color: #999; font-size: 24px; font-weight: 200; line-height: 60px; margin: 0px; padding: 0px;"><?php echo __('You started a Movement'); ?></h2>

	<p style="color: #999; font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('You have successfully created your movement - \'%s\' - and is now visible to the public. It is now up to you to gather <strong>%s people</strong> in the next <strong>%s days.</strong>', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#58c4c4')), $supporter_target, $support_duration); ?></p>

</div>