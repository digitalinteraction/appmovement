<!-- Login view -->

<?php
echo $this->Html->css('login', null, array('inline'=>false));
?>

<div class="row banner-row">
	<div class="container">

		<h3><?php echo __('Login'); ?></h3>

	</div>
</div>

<div class="row login-row">
	<div class="container form-container">
		
		<?php echo $this->Form->create('User', array('class' => 'login-form')); ?>
		
			<div class="profile-photo" style="background-image: url(<?php echo $this->webroot . 'img/users/small/default.png'; ?>)"></div>

			<?php echo __($this->Session->flash('auth')); ?>

			<?php echo $this->Form->input('username', array('type' => 'text', 'class' => 'input-field', 'label' => __('Username / Email'), 'placeholder' => __('Username'))); ?>
			
			<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'input-field', 'label' => __('Password'), 'placeholder' => __('Password'), 'value' => ''), array('autocomplete' => 'false')); ?>
			
			<?php
			$options = array(
			    'label' => __('Login'),
			    'class' => 'btn btn-success btn-lg',
			);
			?>

		<?php echo $this->Form->end($options); ?>

		<?php echo $this->Html->link(__('Forgotten your password?'), array('controller' => 'users', 'action' => 'forgot'), array('escape' => false) ); ?>

	</div>
</div>

<script type="text/javascript">

	$('#login-tab li').addClass('active');

</script>