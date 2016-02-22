<!-- Launch View -->

<?php
echo $this->Html->css('launch', null, array('inline'=>false));
echo $this->Html->css('movements/view', null, array('inline'=>false));
?>

<?php
$app_states = array(__('Building App'), __('Submitted to App Store'), __('Awaiting Review'), __('Review in Progress'), __('Processing for App Store'), __('Available to Download'));
?>

<div class="row banner-row">
	<h3><?php echo __('Launch Phase'); ?></h3>
</div>

<div class="row">
	<div class="container">

		<div class="col-md-7 col-sm-6">

			<div class="info-container">

				<div class="status-banner" style="background-image:url('<?php echo $this->webroot . 'img/movements/large/' . $movement["MovementPhoto"][0]["filename"]; ?>')"></div>

				<h2><?php echo $movement["Movement"]["title"]; ?></h2>

				<p>
					<?php echo __('This application has made it to the launch phase, don\'t worry we will take it from here.'); ?>
				<br /><br />
					<?php echo __('Our engineers will use all of the communities contributions from the design phase to build a bespoke application.'); ?>
				<br /><br />
					<?php echo __('We will then release the application to the relevant App Stores and notify you when it is available to download.'); ?>
				</p>

			</div>

			<?php echo $this->Html->link('<div class="link-container">' . __('View Contributions') . '</div>', array('controller' => 'tasks', 'action' => 'landing', $movement["Movement"]["id"]), array('escape' => false)); ?>

		</div>

		<div class="col-md-5 col-sm-6">

			<div class="status-container">

				<h2><?php echo __('App Status'); ?></h2>

				<ul>

					<?php
					$count = 0;
					foreach ($app_states as $state) {
						
						if ($count < $movement["Movement"]["launch_status"]) {
							echo '<li><h4><i class="fa fa-check"></i>' . __($state) . '</h4></li>';
						} else {
							echo '<li><h4><i class="fa fa-circle-o"></i>' . __($state) . '</h4></li>';
						}
						$count++;
					}
					?>

				</ul>

			</div>

			<?php if ($movement["Movement"]["launch_status"] > count($app_states)) { ?>

				<div class="info-container download-container">

					<h2><?php echo __('Download App'); ?></h2>

					<div class="col-xs-6">

						<a href="<?php echo $movement["Movement"]["ios_download_link"]; ?>" target="_blank">
							<?php echo $this->Html->image('apple-store.jpg', array('class' => 'img-responsive')); ?>
						</a>

					</div>

					<div class="col-xs-6">

						<a href="<?php echo $movement["Movement"]["android_download_link"]; ?>" target="_blank">
							<?php echo $this->Html->image('android-store.jpg', array('class' => 'img-responsive')); ?>
						</a>

					</div>

					<div class="clearfloat"></div>

				</div>

			<?php } ?>

		</div>

	</div>
</div>