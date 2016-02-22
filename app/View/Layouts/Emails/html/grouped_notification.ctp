<!-- Template for grouped notifications -->

<?php
if ($this->Session->check('Config.text_direction')) {
	$rtl = ($this->Session->read('Config.text_direction') == 'RTL') ? true : false;
} else {
	$rtl = false;
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">
<html style="background-color: #fff; margin: 0; padding: 0; width: 100%;">
<head>
	<title>App Movement Notification</title>
</head>

<body style="background-color: #fff; margin: 0; padding: 0; width: 100%;">

	<div class="email-container" style="background-color: #f9f9f9; margin: 10px 0px 0px 0px; width: 100%; max-width: 680px; text-align: left; <?php if ($rtl) { echo 'direction: rtl; text-align: right;'; } else { echo 'text-align: left;'; } ?>">

		<div class="email-banner" style="background-color: #5dd0d0; margin: 0; padding: 0; padding-top: 20px; text-align: center;">
			
			<img class="email-logo" style="height: 50px; width: 50px;" alt="App Movement" src="https://app-movement.com/img/logo_triangle.png" />
			
			<h3 style="color: #fff; font-size: 1.8em; font-weight: 300; margin: 0; padding: 10px 10px 20px 10px;"><?php echo __('Movement Updates'); ?></h3>

			<div class="banner-subtitle" style="background-color: #58c4c4;">

				<p style="color: #fff; font-size: 1.2em; font-weight: 100; margin: 0; padding: 20px 10px;"><?php echo __('Here are the latest updates for the movements you currently support.'); ?></p>

			</div>
		
		</div>

		<div class="email-body" style="padding: 15px;">

			<table style="width: 100%">
				<tr>
					<td align="center">

						<div class="stream-container" style="max-width: 840px;">

							<?php echo $this->fetch('content'); ?>

						</div>

					</td>
				</tr>
			</table>
		
		</div>

		<div class="email-footer" style="background-color: #f9f9f9; border-top: #e6e6e6 1px solid; border-bottom: #e6e6e6 1px solid; color: #AAA; font-size: 1em; margin: 0; padding: 15px; <?php if ($rtl) { echo 'direction: rtl; text-align: right;'; } else { echo 'text-align: left;'; } ?>">

			<?php echo __('Find out more at %s', $this->Html->link(__('app-movement.com'), $this->Html->url(array('controller' => 'pages', 'action' => 'display', 'home'),true), array('style' => 'color:#58c4c4'))); ?>
			
			<br />
			
			<?php echo __('%s to change your notification settings', $this->Html->link(__('Click here'), $this->Html->url(array('controller' => 'users', 'action' => 'edit'),true), array('style' => 'color:#58c4c4'))); ?>
		
		</div>

	</div>

</body>
</html>