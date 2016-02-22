<!-- Register view -->

<?php
echo $this->Html->css('register', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Create an Account'); ?></h3>

	</div>
</div>

<div class="row registration-row">
	<div class="container form-container">

		<?php echo $this->Form->create('User', array('class' => 'registration-form')); ?>

			<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/default.png'; ?>)"></div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('fullname', array('type' => 'text' ,'class' => 'input-field', 'placeholder' => __('Your Name'), 'label' => __('Full Name'))); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('username', array('type' => 'text' ,'class' => 'input-field', 'placeholder' => __('Username'))); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'input-field', 'placeholder' => __('Email Address'))); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'input-field', 'placeholder' => __('Password'))); ?>
			</div>

			<div class="form-group input-section">
		    	<?php echo $this->Form->input('password_confirm', array('type' => 'password', 'class' => 'input-field', 'placeholder' => __('Confirm Password'), 'label' => __('Confirm Password'))); ?>
			</div>

			<?php
			$options = array(
			    'label' => __('Register'),
			    'class' => 'btn btn-success btn-lg',
			);
			?>

			<div class="form-group input-section">
				<p><?php echo __('By creating an account means you agree to App Movement\'s <br /> %s and %s', $this->Html->link('Terms of Use', array('controller' => 'pages', 'action' => 'display', 'terms'), array('escape' => false)), $this->Html->link('Privacy Policy', array('controller' => 'pages', 'action' => 'display', 'privacy'), array('escape' => false))); ?>
			</div>

		<?php echo $this->Form->end($options); ?>

		<?php echo $this->Html->link(__('I already have an account'), array('controller' => 'users', 'action' => 'login'), array('escape' => false) ); ?>

	</div>
</div>

<script type="text/javascript">

	$('#register-tab li').addClass('active');

</script>