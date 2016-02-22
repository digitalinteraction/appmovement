<?php
$data = json_decode($notification["Notification"]["data"]);
$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
$movement_photo = $notification["Movement"]["MovementPhoto"][0]["filename"];
$supporter_target = $notification["Movement"]["target_supporters"];
?>

<div class="notification stat-notification">

	<h2><?php echo __('Keep Sharing!'); ?></h2>

	<p><?php echo __('It is really important that you continue to share this Movement in order to reach the goal of %s supporters!', $supporter_target); ?></p>

	<h3><?php echo $this->Html->link(substr($movement_title, 0, 30), $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true)); ?></h3>

	<div class="share-btns">
		<ul>
			<li class="share-button share-fb" data-movement="<?php echo $movement_id; ?>" data-start="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=" data-end="&p[images][0]=http://app-movement.com/img/movements/large/<?php echo $movement_photo; ?>&p[title]=<?php echo $movement_title; ?>&p[summary]=Support this app movement!"><i class="fa fa-facebook" data-reflink=""></i></li>
			<li class="share-button share-twitter" data-movement="<?php echo $movement_id; ?>" data-start="http://twitter.com/home?status=Support this movement! <?php echo $movement_title; ?> " data-end=""><i class="fa fa-twitter"></i></li>
			<li class="share-button share-googleplus" data-movement="<?php echo $movement_id; ?>" data-start="https://plus.google.com/share?url=Support this movement! <?php echo $movement_title; ?> " data-end=""><i class="fa fa-google-plus"></i></li>
		</ul>
	</div>
	
</div>	
