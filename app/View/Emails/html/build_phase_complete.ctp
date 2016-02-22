<?php 
$data = json_decode($notification["Notification"]["data"]);

$username = $notification["User"]["username"];
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$android_store_listing_url = $data->android_download_url;
$ios_store_listing_url = $data->ios_download_url;
$supporter_count = $data->supporter_count;
$creator_name = $data->creator_name;
$creator_photo = $data->creator_photo;

if ($this->Session->check('Config.text_direction')) {
	$rtl = ($this->Session->read('Config.text_direction') == 'RTL') ? true : false;
} else {
	$rtl = false;
}
?>

<div class="email-container" style="background-color: #f9f9f9; margin: 10px 0px 0px 0px; width: 100%; max-width: 680px; <?php if ($rtl) { echo 'direction: rtl; text-align: right;'; } else { echo 'text-align: left;'; } ?>">

	<div class="email-banner" style="background-color: #5dd0d0; margin: 0; padding: 0; padding-top: 20px; text-align: center;">
		
		<img class="email-logo" style="height: 50px; width: 50px;" alt="" src="https://app-movement.com/img/logo_triangle.gif" />
		
		<h3 style="color: #fff; font-size: 1.8em; font-weight: 300; margin: 0; padding: 10px 10px 20px 10px;"><?php echo __('%s is now available to download!', $movement_title); ?></h3>

		<div class="banner-subtitle" style="background-color: #58c4c4;">

			<p style="color: #fff; font-size: 1.2em; font-weight: 300; margin: 0; padding: 20px 10px;"><?php echo __('After gathering support and design input from %s people %s is now available to be downloaded from the app store.', $supporter_count, $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#fff'))); ?></p>

		</div>

	</div>

	<div class="email-body" style="padding: 15px; text-align:center;">

		<h3><?php echo __('Congratulations, you have an app you can share with the world!'); ?></h3>
		<p>
			<?php echo __('<strong>Download the app now</strong> and start contributing your knowledge about the best places nearby, the success of the app depends on it. Your actions in these early days will make sure the app is successful, so it is very important that you add the best places nearby and leave reviews.'); ?>
		</p>

		<p><?php echo __('<strong>It\'s up to you to add places and leave reviews</strong> to encourage others to download the app and contribute to the community.'); ?></p>
		<br />
		<h3><?php echo __('Download your app by clicking below'); ?></h3>

		<div style="text-align:center; margin:auto;">
		<p style="font-size: 1em; padding: 5px;">
			<a href="<?php echo $android_store_listing_url; ?>"><img class="" style="width: 100%; max-width:240px" alt="" src="https://app-movement.com/img/android-store.jpg" /></a>
		</p>

		<p style="font-size: 1em; padding: 5px;">
			<a href="<?php echo $ios_store_listing_url; ?>"><img class="" style="width: 100%; max-width:240px" alt="" src="https://app-movement.com/img/apple-store.jpg" /></a>
		</p>
		</div>

		<h3><?php echo __('Thank You!'); ?></h3>
		<p>
			<?php echo __('Without you this app would never have been made so we would like to say thank you for contributing your time and efforts in supporting the idea, contributing your thoughts and sharing it with friends.'); ?>
		</p>

		<p><?php echo __('App Movement Team'); ?></p>
		
	</div>

	<div class="email-footer" style="background-color: #f9f9f9; border-top: #e6e6e6 1px solid; border-bottom: #e6e6e6 1px solid; color: #AAA; font-size: 1em; margin: 0; padding: 15px; <?php if ($rtl) { echo 'direction: rtl; text-align: right;'; } else { echo 'text-align: left;'; } ?>">
		
		<?php echo $this->Html->link(__('View this email in another language'), $this->Html->url(array('controller' => 'notifications', 'action' => 'view', $notification["Notification"]["id"]), true), array('style' => 'color:#58c4c4')); ?>

		<br />

		<?php echo __('Find out more at %s', $this->Html->link('app-movement.com', $this->Html->url(array('controller' => 'pages', 'action' => 'display', 'home'),true), array('style' => 'color:#58c4c4'))); ?>
		
		<br />
		
		<?php echo __('%s to change your notification settings', $this->Html->link(__('Click here'), $this->Html->url(array('controller' => 'users', 'action' => 'edit'),true), array('style' => 'color:#58c4c4'))); ?>
	
	</div>

</div>