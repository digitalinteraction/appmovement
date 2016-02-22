<?php
echo $this->Html->css('notifications', null, array('inline'=>false));

$movement_id = $notification["Movement"]["id"];
$movement_title = $notification["Movement"]["title"];
$ios_store_listing_url = $notification["Movement"]['ios_download_link'];
$android_store_listing_url = $notification["Movement"]['android_download_link'];

?>

<div class="notification info-notification">

	<h2><?php echo __('Congratulations! Your App is Now Available!'); ?></h2>

	<p><?php echo __('%s has now been fully designed by the %s supporting members of the community and has been generated! Click below to download the app.', $this->Html->link("'" . $movement_title . "'", '/view/' . $movement_id)); ?></p>

	<a href="<?php echo $ios_store_listing_url; ?>"><img class="app-download-link" alt="" src="https://app-movement.com/img/apple-store.jpg" /></a>
	<a href="<?php echo $android_store_listing_url; ?>"><img class="app-download-link" alt="" src="https://app-movement.com/img/android-store.jpg" /></a>

</div>