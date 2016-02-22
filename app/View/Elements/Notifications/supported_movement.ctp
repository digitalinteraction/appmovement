<?php
$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
$movement_photo = $notification["Movement"]["MovementPhoto"][0]["filename"];
$supporter_target = $notification["Movement"]["target_supporters"];
$support_duration = $notification["Movement"]["support_duration"];
?>

<div class="notification info-notification">

	<h2><?php echo __('You supported a Movement'); ?></h2>

	<p><?php echo __('You have supported %s. It is now up to you to gather <strong>%s people</strong> in the next <strong>%s days</strong>.', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true)), $supporter_target, $support_duration); ?></p>
	
	<h5><?php echo __('What happens next?'); ?></h5>
	
	<p><?php echo __('You will <strong>receive updates</strong> about the progress of this movement via email. If the Movement is successful you will be exclusively invited to the design phase where you can help shape the design of the app.'); ?></p>

	<p><?php echo __('Take a look at our %s section for more', $this->Html->link(__('FAQ'), $this->Html->url(array('controller' => 'pages', 'action' => 'faq'),true))); ?></p>
	
	<div class="share-btns">
		<ul>
			<li class="share-button share-fb" data-movement="<?php echo $movement_id; ?>" data-start="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=" data-end="&p[images][0]=http://app-movement.com/img/movements/large/<?php echo $movement_photo; ?>&p[title]=<?php echo $movement_title; ?>&p[summary]=Support this app movement!"><i class="fa fa-facebook" data-reflink=""></i></li>
			<li class="share-button share-twitter" data-movement="<?php echo $movement_id; ?>" data-start="http://twitter.com/home?status=Support this movement! <?php echo $movement_title; ?> " data-end=""><i class="fa fa-twitter"></i></li>
			<li class="share-button share-googleplus" data-movement="<?php echo $movement_id; ?>" data-start="https://plus.google.com/share?url=Support this movement! <?php echo $movement_title; ?> " data-end=""><i class="fa fa-google-plus"></i></li>
		</ul>
	</div>

</div>