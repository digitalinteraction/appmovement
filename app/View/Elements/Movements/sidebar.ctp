
<!-- Movement Sidebar -->

<?php echo $this->Html->css('movements/sidebar', null, array('inline'=>false)); ?>

<div class="movement-sidebar profile-container visible-md visible-lg">

	<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/' . $movement['User']['photo']; ?>)"></div>

	<h2><?php echo $movement['User']['fullname']; ?></h2>

	<p><?php echo __('Movement Creator'); ?></p>

	<?php echo $this->Element('Movements/action_button', array('movement' => $movement)); ?>

</div>

<div class="movement-sidebar" id="stats-sidebar">

	<div class="stats-section col-md-12 <?php if ($movement["Movement"]["phase"] != -1) { echo 'col-xs-4'; } else { echo 'col-xs-6'; } ?> supporter_count_wrapper" data-toggle="tooltip" data-placement="bottom" title="<?php echo $movement["Movement"]["supporters_count"] . " / " . $movement["Movement"]["target_supporters"] . " " . __('Supporters'); ?>">

		<h1 id="supporters-count" class="count-up"><?php echo number_format($movement["Movement"]["supporters_count"]); ?></h1>
		<h5 class="visible-md visible-lg"><?php echo $movement["Movement"]["supporters_count"] == 1 ? strtoupper(__('supporter')) : strtoupper(__('supporters')); ?></h5>
		<h5 class="visible-xs visible-sm"><?php echo $movement["Movement"]["supporters_count"] == 1 ? __('supporter') : __('supporters'); ?></h5>

	</div>

	<?php if ($movement["Movement"]["phase"] != 3) { ?>

		<div class="stats-section col-md-12 col-xs-4">
			
			<?php if ($movement["Movement"]["phase"] <= 0) { ?>
				
				<h1 class="count-up"><?php echo ($movement["Movement"]["design_start"] < 0) ? 0 : $movement["Movement"]["design_start"] ?></h1>
				<h5 class="visible-md visible-lg"><?php echo $movement["Movement"]["design_start"] == 1 ? strtoupper(__('day to go')) : strtoupper(__('days to go')); ?></h5>
				<h5 class="visible-xs visible-sm"><?php echo $movement["Movement"]["design_start"] == 1 ? __('day to go') : __('days to go'); ?></h5>

			<?php } else if ($movement["Movement"]["phase"] == 1) { ?>

				<h1 class="count-up"><?php echo ($movement["Movement"]["launch_start"] < 0) ? 0 : $movement["Movement"]["launch_start"] ?></h1>
				<h5 class="visible-md visible-lg"><?php echo $movement["Movement"]["launch_start"] == 1 ? strtoupper(__('day to go')) : strtoupper(__('days to go')); ?></h5>
				<h5 class="visible-xs visible-sm"><?php echo $movement["Movement"]["launch_start"] == 1 ? __('day to go') : __('days to go'); ?></h5>

			<?php } else if ($movement["Movement"]["phase"] == 2) { ?>

				<h1 class="count-up"><?php echo number_format($movement["Movement"]["launch_start"] >= 0) ? 0 : abs($movement["Movement"]["launch_start"]) ?></h1>
				<h5 class="visible-md visible-lg"><?php echo $movement["Movement"]["launch_start"] == 1 ? strtoupper(__('day launching')) : strtoupper(__('days launching')); ?></h5>
				<h5 class="visible-xs visible-sm"><?php echo $movement["Movement"]["launch_start"] == 1 ? __('day launching') : __('days launching'); ?></h5>

			<?php } ?>

		</div>
	
	<?php } ?>

	<div class="stats-section col-md-12 <?php if ($movement["Movement"]["phase"] != -1) { echo 'col-xs-4'; } else { echo 'col-xs-6'; } ?>">

		<?php
		$url = 'https://app-movement.com/view/' . $movement["Movement"]["id"];

		$fb_json_string = @file_get_contents('http://api.facebook.com/restserver.php?method=links.getStats&format=json&urls=' . $url);
		if ($fb_json_string === FALSE) {
			$fb_count = 0;
		} else {
			$fb_json = json_decode($fb_json_string, true);
			$fb_count = isset($fb_json[0]['share_count'])?(intval($fb_json[0]['share_count'])):0;
		}		

		$twitter_json_string = @file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url=' . $url);
		if ($twitter_json_string === FALSE) {
			$twitter_count = 0;
		} else {
			$twitter_json = json_decode($twitter_json_string, true);
			$twitter_count = isset($twitter_json['count'])?intval($twitter_json['count']):0;
		}
		

		$gplus_json_string = @file_get_contents('https://plus.google.com/ripple/details?url=' . $url);
		if ($gplus_json_string === FALSE) {
			$gplus_count = 0;
		} else {
			$gplus_json = preg_match('@([0-9]+) public shares@', $gplus_json_string, $matches);
			$gplus_count = isset($matches[1])? $matches[1] : 0;
		}
		
		$share_count = $fb_count + $twitter_count + $gplus_count;
		?>

		<h1 class="count-up"><?php echo number_format($share_count); ?></h1>
		<h5 class="visible-md visible-lg"><?php echo $share_count == 1 ? strtoupper(__('person shared')) : strtoupper(__('people shared')); ?></h5>
		<h5 class="visible-xs visible-sm"><?php echo $share_count == 1 ? __('person shared') : __('people shared'); ?></h5>

	</div>

	<div class="clearfix"></div>

	<hr class="visible-xs" />

	<ul class="sidebar-social-links">
		<li class="share-fb"><i class="fa fa-facebook-square"></i></li>
		<li class="share-twitter"><i class="fa fa-twitter-square"></i></li>
		<li class="share-googleplus"><i class="fa fa-google-plus-square"></i></li>
		<li class="share-email"><i class="fa fa-envelope-square"></i></li>
	</ul>

</div>

<?php if ($movement["Movement"]["phase"] == 0) { ?>

	<?php if (($movement["Movement"]["target_supporters"] - $movement["Movement"]["supporters_count"]) <= 0) { ?>

		<div class="movement-sidebar notifications-sidebar">

			<h5><i class="fa fa-flag"></i><?php echo __('This Movement has been fully supported!'); ?></h5>

		</div>

	<?php } else { ?>

		<div class="movement-sidebar notifications-sidebar">

			<h5><i class="fa fa-flag"></i><span id="remaining-supporters-count"><?php echo ($movement["Movement"]["target_supporters"] - $movement["Movement"]["supporters_count"]); ?></span> <?php echo __('more supporters to hit the target.'); ?></h5>

		</div>

	<?php } ?>

<?php } ?>

<?php if (($movement["Movement"]["supporters_count"] >= 5) && ($this->Session->read('Auth.User.id') == $movement["Movement"]["user_id"])) { ?>

	<div class="movement-sidebar notifications-sidebar">

		<h5><i class="fa fa-flag"></i><?php echo __('Movements with 5 or more supporters cannot be edited.'); ?></h5>

	</div>

<?php } ?>

<?php if ($movement["Movement"]["phase"] == -1) { ?>

	<div class="movement-sidebar notifications-sidebar">

		<h5><i class="fa fa-flag"></i><?php echo __('This movement did not gather enough support to progress to the design phase.'); ?> (<?php echo $movement["Movement"]["supporters_count"]; ?> / <?php echo $movement["Movement"]["target_supporters"]; ?>)</h5>

	</div>

<?php } ?>

<?php if ($movement["Movement"]["flag"]) { echo '<div class="btn btn-danger btn-lg btn-block">' . __('Under Review') . '</div>'; } ?>

<?php if ($this->Session->read('Auth.User.id') == $movement["Movement"]["user_id"]) {
	echo '<div class="btn btn-warning btn-lg btn-block" id="update-button">' . __('Post Update') . '</div>';
} ?>

<?php if (($movement["Movement"]["supporters_count"] < 5) && ($this->Session->read('Auth.User.id') == $movement["Movement"]["user_id"]))
{
	echo $this->Html->link('<div class="btn btn-info btn-lg btn-block edit-button" id="edit-button">' . __('Edit Movement') . '</div>', 'edit/' . $movement["Movement"]["id"], array('escape' => false));
} ?>