
<!-- Action button -->

<?php if (($this->Session->read('Auth.User')) && (!$movement["Movement"]["flag"])) { ?> <!-- Signed in -->

	<!-- Support phase -->
	<?php if ($movement["Movement"]["phase"] == 0) { ?>

		<?php if ($movement["Movement"]["supported"]) { ?>

			<div class="btn btn-success btn-lg btn-block action-btn support-btn"><i class="fa fa-check-circle"></i><?php echo __('Supported'); ?></div>

		<?php } else { ?>

			<div class="btn btn-success btn-lg btn-block action-btn support-btn" id="support-btn" onClick="showModal(<?php echo $movement["Movement"]["id"]; ?>)"><?php echo __('Support Idea'); ?></div>

			<div class="btn btn-success btn-lg btn-block action-btn support-btn temp-supported-btn" id="temp-supported-btn"><i class="fa fa-check-circle"></i><?php echo __('Supported'); ?></div>
		
		<?php } ?>

	<?php } ?>

	<!-- Design phase -->
	<?php if ($movement["Movement"]["phase"] == 1) { ?>

		<?php if ($movement["Movement"]["supported"]) { ?>

			<?php echo $this->Html->link('<div class="btn btn-success btn-lg btn-block action-btn design-btn" id="design-btn"><i class="fa fa-wrench"></i>' . __('Help Design') . '</div>', array('controller' => 'tasks', 'action' => 'landing', $movement["Movement"]["id"]), array('escape' => false)); ?>
		
		<?php } else { ?>

			<!-- User hasn't supported yet -->

			<div class="btn btn-success btn-lg btn-block action-btn support-btn" id="support-btn" onClick="showModal(<?php echo $movement["Movement"]["id"]; ?>)"><?php echo __('Support Idea'); ?></div>

			<?php echo $this->Html->link('<div class="btn btn-success btn-lg btn-block action-btn design-btn temp-design-btn" id="temp-design-btn"><i class="fa fa-wrench"></i>' . __('Help Design') . '</div>', array('controller' => 'tasks', 'action' => 'landing', $movement["Movement"]["id"]), array('escape' => false)); ?>

		<?php } ?>

	<?php } ?>

	<!-- Launch phase -->
	<?php if ($movement["Movement"]["phase"] == 2) { ?>

		<?php echo $this->Html->link('<div class="btn btn-success btn-lg btn-block action-btn launch-btn" id="launch-btn">' . __('View Progress') . '</div>', array('controller' => 'movements', 'action' => 'launch', $movement["Movement"]["id"]), array('escape' => false)); ?>

	<?php } ?>

	<!-- App Launched -->
	<?php if ($movement["Movement"]["phase"] == 3) { ?>

		<div class="btn btn-success btn-lg btn-block action-btn download-btn" id="download-btn" onClick="showDownloadModal()"><?php echo __('Download App'); ?></div>

	<?php } ?>

<?php } else if (!$movement["Movement"]["flag"]) { ?>  <!-- Signed out -->

	<!-- Support phase -->
	<?php if ($movement["Movement"]["phase"] == 0) { ?>
		
		<div class="btn btn-success btn-lg btn-block action-btn support-btn" id="support-btn" onClick="showAuthModal(<?php echo $movement["Movement"]["id"]; ?>)"><?php echo __('Support Idea'); ?></div>

	<?php } ?>

	<!-- Design phase -->
	<?php if ($movement["Movement"]["phase"] == 1) { ?>

		<div class="btn btn-success btn-lg btn-block action-btn support-btn" id="support-btn" onClick="showAuthModal(<?php echo $movement["Movement"]["id"]; ?>)"><?php echo __('Support Idea'); ?></div>
		<?php // echo $this->Html->link('<div class="btn btn-success btn-lg btn-block action-btn design-btn" id="design-btn">' . __('View Design') . '</div>', array('controller' => 'tasks', 'action' => 'landing', $movement["Movement"]["id"]), array('escape' => false)); ?>

	<?php } ?>

	<!-- Launch phase -->
	<?php if ($movement["Movement"]["phase"] == 2) { ?>

		<?php echo $this->Html->link('<div class="btn btn-success btn-lg btn-block action-btn launch-btn" id="launch-btn">' . __('View Progress') . '</div>', array('controller' => 'movements', 'action' => 'launch', $movement["Movement"]["id"]), array('escape' => false)); ?>

	<?php } ?>

	<!-- App Launched -->
	<?php if ($movement["Movement"]["phase"] == 3) { ?>

		<div class="btn btn-success btn-lg btn-block action-btn download-btn" id="download-btn" onClick="showDownloadModal()"><?php echo __('Download App'); ?></div>

	<?php } ?>

<?php } ?>