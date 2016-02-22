
<!-- Phase Bar -->

<?php echo $this->Html->css($this->Session->read('Config.text_direction') . '/phasebar', null, array('inline'=>false)); ?>

<script type="text/javascript">

$(document).ready(function() {

	var phase = <?php echo $movement["Movement"]["phase"]; ?>;

	switch (phase) {
		case 0:
			$('.phase-bar').addClass('support-phase');
			break;
		case 1:
			$('.phase-bar').addClass('design-phase');
			break;
		case 2:
			$('.phase-bar').addClass('launch-phase');
			break;
		case 3:
			$('.phase-bar').addClass('complete-phase');
			break;
		case 4:
			$('.phase-bar').addClass('complete-phase');
			break;
	}
});

</script>

<ul class="phase-bar">
	<li id="launch-phase">
		<span>
			<h3><?php echo __('Launch'); ?></h3>
		</span>
	</li>
	<li id="design-phase">
		<span>
			<h3><?php echo __('Design'); ?></h3>
		</span>
	</li>
	<li id="support-phase">
		<span>
			<h3><?php echo __('Support'); ?></h3>
		</span>
	</li>
</ul>

<div class="row process-banner" id="process-banner">

	<div class="collapse-button"><i class="fa fa-times fa-lg"></i></div>

	<div class="col-sm-4">
		<div class="process-content">
			<?php echo $this->Html->image('process/1.png', array('class' => 'img-responsive', 'alt' => 'First Stage')); ?>
			<p><?php echo __('Help %s by supporting this movement.', strtok($movement["User"]["fullname"], " ")); ?></p>

		</div>
	</div>

	<div class="col-sm-4">
		<div class="process-content">
			<?php echo $this->Html->image('process/2.png', array('class' => 'img-responsive', 'alt' => 'Second Stage')); ?>
			<p><?php echo __('Work with %s and other supporters to design the app.', strtok($movement["User"]["fullname"], " ")); ?></p>
		</div>
	</div>

	<div class="col-sm-4">
		<div class="process-content">
			<?php echo $this->Html->image('process/3.png', array('class' => 'img-responsive', 'alt' => 'Third Stage')); ?>
			<p><?php echo __('Download the app from the App Store for free!'); ?></p>
		</div>
	</div>

</div>

<script type="text/javascript">

	$(document).ready(function() {

		if (getCookie("movement_process_banner") == "") { $('.process-banner').show(); }

		$('.collapse-button').click(function() {

			setCookie('movement_process_banner', '0', 365);
			$('.process-banner').slideUp(300);

		});

		$('#support-phase').click(function() {
			document.location = "<?php echo $this->webroot . 'view/' . $movement['Movement']['id']; ?>";
		});

		<?php if ($movement["Movement"]["phase"] >= 1) { ?>

			$('#design-phase').click(function() {
				document.location = "<?php echo $this->webroot . 'design/' . $movement['Movement']['id']; ?>";
			});

		<?php } ?>

		<?php if ($movement["Movement"]["phase"] >= 2) { ?>

			$('#launch-phase').click(function() {
				document.location = "<?php echo $this->webroot . 'launch/' . $movement['Movement']['id']; ?>";
			});

		<?php } ?>

	});

</script>