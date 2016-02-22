<?php
$username = $notification["User"]["username"];
$movement_title = $notification["Movement"]["title"];
$movement_id = $notification["Movement"]["id"];
$supporter_target = $notification["Movement"]["target_supporters"];

if ($this->Session->check('Config.text_direction')) {
	$rtl = ($this->Session->read('Config.text_direction') == 'RTL') ? true : false;
} else {
	$rtl = false;
}
?>

<div class="email-container" style="background-color: #f9f9f9; margin: 10px 0px 0px 0px; width: 100%; max-width: 680px; <?php if ($rtl) { echo 'direction: rtl; text-align: right;'; } else { echo 'text-align: left;'; } ?>">

	<div class="email-banner" style="background-color: #5dd0d0; margin: 0; padding: 0; padding-top: 20px; text-align: center;">
		
		<img class="email-logo" style="height: 50px; width: 50px;" alt="" src="https://app-movement.com/img/logo_triangle.gif" />
		
		<h3 style="color: #fff; font-size: 1.8em; font-weight: 300; margin: 0; padding: 10px 10px 20px 10px;"><?php echo __('You Supported a Movement'); ?></h3>

		<div class="banner-subtitle" style="background-color: #58c4c4;">

			<p style="color: #fff; font-size: 1.2em; font-weight: 300; margin: 0; padding: 20px 10px;"><?php echo __('You have successfully supported the App Movement for %s', $this->Html->link($movement_title, $this->Html->url(array('controller' => 'movements', 'action' => 'view', $movement_id),true), array('style' => 'color:#fff'))); ?></p>

		</div>
	
	</div>

	<div class="email-body" style="padding: 15px;">

		<h4 style="font-size: 1.4em; margin: 15px 0px; padding: 0;"><?php echo __('Next steps...'); ?></h4>
		
		<p style="font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('The Movement needs at least %s people to reach its support target. By sharing the Movement page on social networks you are increasing the chances of its success. If the Movement reaches its target then yourself and other supporters will be exclusively invited to the design phase where you can share ideas and vote on the best features/contributions for the app.', $supporter_target); ?></p>
		
		<p style="font-size: 1em; margin: 15px 0px; padding: 0;"><?php echo __('Once the design phase is complete the app will be automatically generated and available to download for free!'); ?></p>

		<p><?php echo __('Visit our %s for more information.', $this->Html->link(__('Frequently Asked Questions'), $this->Html->url(array('controller' => 'pages', 'action' => 'display', 'faq'),true), array('style' => 'color:#58c4c4'))); ?></p>
	
	</div>

	<div class="email-footer" style="background-color: #f9f9f9; border-top: #e6e6e6 1px solid; border-bottom: #e6e6e6 1px solid; color: #AAA; font-size: 1em; margin: 0; padding: 15px; <?php if ($rtl) { echo 'direction: rtl; text-align: right;'; } else { echo 'text-align: left;'; } ?>">

		<?php echo $this->Html->link(__('View this email in another language'), $this->Html->url(array('controller' => 'notifications', 'action' => 'view', $notification["Notification"]["id"]), true), array('style' => 'color:#58c4c4')); ?>
		
		<br />

		<?php echo __('Find out more at %s', $this->Html->link('app-movement.com', $this->Html->url(array('controller' => 'pages', 'action' => 'display', 'home'),true), array('style' => 'color:#58c4c4'))); ?>
		
		<br />
		
		<?php echo __('%s to change your notification settings', $this->Html->link(__('Click here'), $this->Html->url(array('controller' => 'users', 'action' => 'edit'),true), array('style' => 'color:#58c4c4'))); ?>
	
	</div>

</div>