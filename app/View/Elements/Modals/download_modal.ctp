
<!-- Download Modal -->

<?php echo $this->Html->css('modals/download_modal', null, array('inline'=>false)); ?>

<div class="modal fade download-modal" id="download-modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo __('Close'); ?></span></button>
			<div class="modal-body">

				<h2><?php echo __('Download'); ?></h2>

				<p><?php echo __('This app has been successfully supported and designed by the community and is now available on the app stores.'); ?></p>

				<p><?php echo __('Please click one of the buttons below to download this app for free.'); ?></p>

				<div class="download-links">
					<?php echo $this->Html->link($this->Html->image('apple-store.jpg', array('class' => 'store-image')), $movement["Movement"]["ios_download_link"], array('escape' => false)); ?>

					<?php echo $this->Html->link($this->Html->image('android-store.jpg', array('class' => 'store-image')), $movement["Movement"]["android_download_link"], array('escape' => false)); ?>
				</div>

            </div>
		</div>
	</div>
</div>