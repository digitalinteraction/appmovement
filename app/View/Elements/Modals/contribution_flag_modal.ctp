
<!-- Report Modal -->

<div class="modal fade login-modal" id="contribution-flag-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<h3><?php echo __('Report Contribution'); ?></h3>
				<hr />
				<p><?php echo __('Check the option below and click submit if you think this contribution is:'); ?></p>
				<ul class="contribution_report_radio_button_list">
					<li><input type="radio" name="contribution_report_radio" value="1"><?php echo __('Abusive / Offensive'); ?></li>
					<li><input type="radio" name="contribution_report_radio" value="2"><?php echo __('Copyright Infringement'); ?></li>
					<li><input type="radio" name="contribution_report_radio" value="3"><?php echo __('Spam'); ?></li>
				</ul>
				<div id="contribution-flag-modal-errors"><?php echo __('Please select an option above'); ?></div>
            </div>
			<div class="modal-footer">
				<div class="btn btn-success" id="contribution-flag-modal-submit"><?php echo __('Submit'); ?></div>
				<div class="btn btn-danger btn-sm" data-dismiss="modal"><?php echo __('Cancel'); ?></div>
			</div>
		</div>
	</div>
</div>