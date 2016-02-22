
<!-- Promote Modal -->

<?php echo $this->Html->css('modals/promote_modal', null, array('inline'=>false)); ?>

<div class="modal fade promoter-modal" id="promoterModal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body text-center">
				<h3><?php echo __('Start promoting this movement'); ?></h3>
				<p>
					<?php echo __('Share the link below anywhere you like to start promoting this app movement and earning influence for the design phase!'); ?>
				</p>
				<hr />
				<div class="reflink-container">
					<input type="text" id="reflink"></input>
				</div>
            	<hr />
				<ul>
					<li class="share-fb"><i class="fa fa-facebook"></i></li>
					<li class="share-twitter"><i class="fa fa-twitter"></i></li>
					<li class="share-googleplus"><i class="fa fa-google-plus"></i></li>
					<li class="share-linkedin"><i class="fa fa-linkedin"></i></li>
				</ul>
            </div>
			<div class="modal-footer">
				<div class="btn btn-warning btn-sm" data-dismiss="modal"><?php echo __('Done'); ?></div>
			</div>
		</div>
	</div>
</div>