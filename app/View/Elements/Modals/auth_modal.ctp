
<!-- Auth Modal -->

<?php if ($auto_show) { ?>
	<style type="text/css">
	.navigation {
		z-index: 1041;
	}
	.modal {
		top: 80px;
	}
	.modal-backdrop {
		top: 80px;
	}
	</style>
<?php } ?>
<?php echo $this->Html->css('modals/auth_modal', null, array('inline'=>false)); ?>

<div class="modal auth-modal fade" id="auth-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true"<?php if ($auto_show) { echo ' data-backdrop="static"'; } ?>>
	<div class="modal-dialog">
		<div class="modal-content">

			<?php if (!$auto_show) { ?>
				<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __('Close'); ?></span></button>
			<?php } ?>

			<div class="modal-body">

				<div role="tabpanel">
					<!-- Nav tabs -->
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active"><a href="#register" aria-controls="register" role="tab" data-toggle="tab"><?php echo __('Register'); ?></a></li>
						<li role="presentation"><a href="#login" aria-controls="login" role="tab" data-toggle="tab"><?php echo __('Login'); ?></a></li>
					</ul>

					<!-- Tab panes -->
					<div class="tab-content">
						<div role="tabpanel" class="tab-pane active" id="register">

							<?php echo $this->Form->create('User', array('class' => 'register-form', 'url' => array('controller' => 'users', 'action' => 'register'))); ?>

								<?php echo $this->Form->input('quick', array('type' => 'hidden', 'value' => true)); ?>

								<div class="form-group input-section">
							    	<?php echo $this->Form->input('fullname', array('type' => 'text' ,'class' => 'input-field', 'placeholder' => __('Your Name'), 'label' => __('Full Name'))); ?>
								</div>

								<div class="form-group input-section">
							    	<?php echo $this->Form->input('email', array('type' => 'email', 'class' => 'input-field', 'placeholder' => __('Email Address'))); ?>
								</div>

								<div class="form-group input-section">
							    	<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'input-field', 'placeholder' => __('Password'))); ?>
								</div>

								<div class="form-group input-section">
									<p class="text-muted"><?php echo __('By creating an account means you agree to our %s and %s', $this->Html->link( 'Terms of Use', array('controller' => 'pages', 'action' => 'display', 'terms'), array('escape' => false)), $this->Html->link( 'Privacy Policy', array('controller' => 'pages', 'action' => 'display', 'privacy'), array('escape' => false))); ?></p>
								</div>
								
								<br />

								<?php
								$options = array(
								    'label' => __('Create Account'),
								    'class' => 'btn btn-lg btn-success',
								);
								?>

							<?php echo $this->Form->end($options); ?>

						</div>
						<div role="tabpanel" class="tab-pane" id="login">
		
							<?php echo $this->Form->create('User', array('class' => 'login-form', 'url' => array('controller' => 'users', 'action' => 'login'))); ?>

								<?php echo $this->Form->input('quick', array('type' => 'hidden', 'value' => true)); ?>
								
								<div class="form-group input-section">
									<?php echo $this->Form->input('username', array('type' => 'text', 'class' => 'input-field', 'label' => __('Username / Email'), 'placeholder' => __('Username'))); ?>
								</div>

								<div class="form-group input-section">
								<?php echo $this->Form->input('password', array('type' => 'password', 'class' => 'input-field', 'label' => __('Password'), 'placeholder' => __('Password'), 'value' => ''), array('autocomplete' => 'false')); ?>
								</div>

								<div class="form-group input-section">
									<p class="text-muted"><?php echo __('Forgotten your password? %s to reset it.', $this->Html->link(__('Click here'), array('controller' => 'users', 'action' => 'forgot'), array('escape' => false))); ?></p>
								</div>
								
								<br />

								<?php
								$options = array(
								    'label' => __('Login'),
								    'class' => 'btn btn-lg btn-success',
								);
								?>

							<?php echo $this->Form->end($options); ?>

						</div>
					</div>

				</div>
				
            </div>
		</div>
	</div>
</div>

<?php if ($auto_show) { ?>
	
	<script type="text/javascript">

		$(document).ready(function() {
			$('.auth-modal').modal('show');
		});

	</script>

<?php } ?>