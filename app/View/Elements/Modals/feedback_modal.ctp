<!-- Feedback Modal -->

<?php echo $this->Html->css('modals/feedback_modal', null, array('inline'=>false)); ?>

<div class="modal fade feedback-modal" id="feedback-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<h3><?php echo __('Submit Feedback'); ?></h3>
				<hr />
				<p><?php echo __('Got some feedback for us? Is something not working? We’d love to hear your suggestions. Please leave us a message in the box below.'); ?></p>
				<br />
				<div class="form-group input-section">
					<input class="input-field" id="feedback-email" placeholder="<?php echo __('Email Address'); ?>" maxlength="70" type="text">
				</div>
				<div class="form-group input-section">
					<textarea id="feedback-comment" rows="6" placeholder="<?php echo __('Enter your comment here'); ?>"></textarea>
				</div>
            </div>
			<div class="modal-footer">
				<div class="btn btn-danger btn-sm pull-left" data-dismiss="modal"><?php echo __('Cancel'); ?></div>
				<div class="btn btn-success pull-right" id="feedback-modal-submit"><?php echo __('Submit'); ?></div>
				<div class="clearfix"></div>
			</div>
		</div>
	</div>
</div>