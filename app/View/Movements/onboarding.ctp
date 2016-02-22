<!-- Movement Onboarding -->

<?php
echo $this->Html->css('movements/view', null, array('inline'=>false));
echo $this->Html->script('movements/onboarding');

?>


<div class="row banner-row">
	<div class="container">
		<h3><?php echo __('Invite Some Friends'); ?></h3>
	</div>
</div>

<style type="text/css">

.dialog {
	background-color: #fff;
	border: #DDD 1px solid;
	border-radius: 4px;
	overflow: hidden;
	margin: 40px 10px 20px 10px;
	padding: 20px 10px;
	text-align: center;
}

.dialog h3 {
	color: #4cae4c;
	margin: 0;
	margin: 10px 0px 20px 0px;
	padding: 0;
}

.dialog p {
	color: #666;
	margin: 0px 20px;
	padding: 0;
}

.dialog hr {
	margin: 20px 10px 0px 10px;
}

/* User Panel */

.col-sm-4 {
	margin: 0;
	padding: 0;
}

.user-panel {
	background-color: #f9f9f9;
	border: #DDD 1px solid;
	border-radius: 4px;
	overflow: hidden;
	margin: 20px 10px 0px 10px;
	padding: 0;
}

.user-panel input {
	background-color: #f9f9f9;
	border: none;
	font-size: 16px;
	padding: 10px 15px;
	width: 100%;
}

.user-panel input:first-child {
	background-color: #fcfcfc;
	border-bottom: #e1e1e1 1px solid;
}

/* Progress Bar */

.progress-bar {
	background-color: #5cb85c;
	box-shadow: none;
	color: #5cb85c;
	height: 22px;
}

.progress {
	background-color: transparent;
	border: #5cb85c 1px solid;
	box-shadow: none;
	border-radius: 4px;
	color: #5cb85c;
	display: none;
	height: 22px;
	margin: 25px auto 30px auto;
	max-width: 300px;
}

/* Button Container */

.onboarding-btn {
	margin: 0px 10px 20px 10px;
}

.btn-default {
	background-color: transparent;
	border: #e1e1e1 1px solid !important;
}

.btn-success {
    -webkit-transition: background-color, color 300ms linear;
    -moz-transition: background-color, color 300ms linear;
    -o-transition: background-color, color 300ms linear;
    -ms-transition: background-color, color 300ms linear;
    transition: background-color, color 300ms linear;
}
/*49B778*/

</style>

<div class="row">

	<div class="container">

		<div class="dialog">
			<h3><?php echo __('Congratulations'); ?></h3>
			<p><?php echo __('It is important to gain as much support as possible for your movement. To get started why not tell some friends and get the ball rolling. Once this step is complete your movement will be available for the world to see!'); ?></p>

			<div class="progress" id="progress-bar">
				<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>
			</div>

			<hr />

			<div id="panel-container">

				<div class="col-sm-4">
					<div class="user-panel" data-index="1">
						<input type="text" class="name-field" placeholder="<?php echo __('Name'); ?>">
						<input type="text" class="email-field" placeholder="<?php echo __('Email'); ?>">
					</div>
				</div>

				<div class="col-sm-4">
					<div class="user-panel" data-index="2">
						<input type="text" class="name-field" placeholder="<?php echo __('Name'); ?>">
						<input type="text" class="email-field" placeholder="<?php echo __('Email'); ?>">
					</div>
				</div>

				<div class="col-sm-4">
					<div class="user-panel" data-index="3">
						<input type="text" class="name-field" placeholder="<?php echo __('Name'); ?>">
						<input type="text" class="email-field" placeholder="<?php echo __('Email'); ?>">
					</div>
				</div>

			</div>

		</div>
		
		<?php echo $this->Html->link('<div class="btn btn-default pull-left onboarding-btn">' . __('Skip') . '</div>', array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"]), array('escape' => false)); ?>


			<?php echo $this->Form->create('Movement', array('id' => 'data-form', 'novalidate')); ?>

	    	<?php echo $this->Form->input('id', array('type' => 'hidden', 'value' => $movement["Movement"]["id"])); ?>

	    	<?php echo $this->Form->input('data', array('id' => 'form-data', 'type' => 'hidden')); ?>

	    	<?php
			$options = array(
			    'label' => __('Continue'),
			    'class' => 'btn btn-default pull-right onboarding-btn',
			    'id' => 'continue-button'
			);
			echo $this->Form->end($options);
			?>
	
		<div class="clearfloat"></div>

	</div>
</div>