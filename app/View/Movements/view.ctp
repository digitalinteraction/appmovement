<!-- Movement view -->

<?php
echo $this->Html->css('movements/view', null, array('inline'=>false));
echo $this->Html->script('movements/view');
echo $this->Html->script('bootstrap-tour');
echo $this->Html->script('jquery.counterup.min');

echo $this->Html->meta(array('name' => 'og:title', 'content' => 'Support ' . $movement["User"]["fullname"] . '\'s app idea - ' . $movement["Movement"]["title"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:type', 'content' => 'website'), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:image', 'content' => $this->webroot . 'img/movements/large/' . $movement["MovementPhoto"][0]["filename"]), NULL, array('inline' => false));
echo $this->Html->meta(array('name' => 'og:url', 'content' => $this->webroot . 'view/' . $movement["Movement"]["id"]), NULL, array('inline' => false));
?>

<script type="text/javascript">

	var ref_link = '<?php echo $ref_link; ?>';
	var movement_id = '<?php echo $movement["Movement"]["id"]; ?>';
	var movement_image = '<?php echo $movement["MovementPhoto"][0]["filename"]; ?>';
	var movement_title = <?php echo json_encode($movement["Movement"]["title"]); ?>;
	var movement_user_fullname = <?php echo json_encode($movement["User"]["fullname"]); ?>;
	var is_creator = <?php if ($this->Session->read('Auth.User.id') == $movement["Movement"]["user_id"]) { echo 'true'; } else { echo 'false'; } ?>;
	var has_supported = <?php if ($movement['Movement']['supported']) { echo 'true'; } else { echo 'false'; } ?>;
	var show_tour = <?php echo isset($_GET['tour']) ? '1' : '0'; ?>;
	var movement_phase = '<?php echo $movement["Movement"]["phase"]; ?>';
	
	$(document).ready(function() {

		<?php if ($show_support) { ?>

			showModal(movement_id);

		<?php } else { ?>

			window.history.replaceState('promoter', 'Promote', '<?php echo Router::url('/', false) . $ref_link; ?>');

		<?php } ?>

		window.fbAsyncInit = function() {
			FB.init({
				appId      : '1623248021226561',
				xfbml      : true,
				version    : 'v2.2'
			});
		};

		(function(d, s, id){
			var js, fjs = d.getElementsByTagName(s)[0];
			if (d.getElementById(id)) {return;}
			js = d.createElement(s); js.id = id;
			js.src = "//connect.facebook.net/en_US/sdk.js";
			fjs.parentNode.insertBefore(js, fjs);
		}(document, 'script', 'facebook-jssdk'));

	});

</script>

<?php
echo $this->Html->css('movements/view', null, array('inline'=>false));
echo $this->Html->script('movements/view');
echo $this->Html->script('bootstrap-tour');
echo $this->Html->script('jquery.counterup.min');
?>

<div class="row banner-row">
	<div class="container">
		<h3 id="movement-title"><?php echo $movement["Movement"]["title"]; ?></h3>
	</div>
</div>

<div class="row movement-row">
	<div class="container">
		
		<div class="col-md-9">

			<div class="movement-container">

				<?php if ($movement["Movement"]["supported"]) { ?>
					<div class="supported-strip"><?php echo __('SUPPORTED'); ?></div>
				<?php } ?>

				<div class="supported-strip" id="temp-banner"><?php echo __('SUPPORTED'); ?></div>
				
				<div id="movement-banner" style="background-image:url('<?php echo $this->webroot . 'img/movements/large/' . $movement["MovementPhoto"][0]["filename"]; ?>')"></div>

				<?php if ($movement["Movement"]["phase"] == -1) { ?>

					<?php echo $this->element('Movements/notification_banner', array('movement' => $movement)); ?>

				<?php } else { ?>
					
					<?php echo $this->element($this->Session->read('Config.text_direction') . '/phasebar', array('movement' => $movement)); ?>
				
				<?php } ?>
				
				<h5><?php echo $movement["Movement"]["title"]; ?></h5>
				<p class="visible-xs visible-sm text-muted">by <?php echo $movement["User"]["fullname"]; ?></p>
				
				<div class="movement-overview">

					<div class="short-description">
						<div class="movement-description">
							<?php echo nl2br(strip_tags($movement["Movement"]["description"])); ?>
						</div>
						<div class="text-muted" id="read-more-button"><i class="fa fa-chevron-down"></i></div>
						<br />
					</div>

					<div class="full-description">
						<div class="movement-description">
							<?php echo nl2br(strip_tags($movement["Movement"]["overview"], '<br>,<b>,<center>,<a>')); ?>
						</div>
					</div>

					<?php if($movement["Movement"]["launch_status"] == 6):?>
						<div class="movement-download-links">
							<a href="<?php echo $movement['Movement']['ios_download_link']; ?>"><img class="app-download-link" alt="" src="<?php echo $this->webroot . 'img/apple-store.jpg'; ?>"/></a>
							<a href="<?php echo $movement['Movement']['android_download_link']; ?>"><img class="app-download-link" alt="" src="<?php echo $this->webroot . 'img/android-store.jpg'; ?>" /></a>
						</div>
					<?php endif; ?>

					<div class="visible-xs visible-sm">

						<hr />

						<?php echo $this->Element('Movements/action_button', array('movement' => $movement)); ?>

					</div>


				</div>

				<div class="movement-updates">
					
					<?php foreach ($movement["MovementUpdate"] as $movement_update) {

						echo $this->element('Updates/movement_update', array('movement_update' => $movement_update));

					} ?>

				</div>

				<?php if ($this->Session->read('Auth.User.id') == $movement["Movement"]["user_id"])
				{ ?>

					<div id="update-container">
						<div class="update-info">
							<h4><?php echo __('Keep your supporters informed'); ?></h4>
							<p><?php echo __('Send your supporters updates to keep them informed and get them involved. Your update will be sent directly to their email inbox. Please note, you can only send a maximum of 2 updates per day.'); ?></p>
						</div>
						<div class="alert alert-warning" id="update-form-errors">
							<ul id="update-form-errors-list">
							</ul>
						</div>
						<?php
						echo $this->Form->create();
						echo $this->Form->input('update', array('class' => 'input-field', 'id' => 'input-update', 'label' => false, 'placeholder' => __('Keep your supporters in the loop by posting an update.'), 'rows' => '4', 'type' => 'textarea'));
						echo $this->Form->end();
						?>

						<div class="btn btn-warning" id="post-update-button"><?php echo __('Post Update'); ?></div>
					
					</div>

				<?php } ?>

				
				<?php if ($movement["Movement"]["phase"] > 0) { ?>
					
					<hr class="visible-md visible-lg" />

					<?php echo $this->Element('Movements/supporter_graph'); ?>
					
				<?php } ?>

				<?php if (count($movement["MovementPhoto"]) > 0) { echo $this->Element('photo_gallery', $movement); } ?>

				<?php if ($movement["Movement"]["tags"] != '') { ?>
					
					<?php
					echo $this->Form->create();
					echo $this->Form->input('tags', array('style' => 'display:none', 'label' => 'Tags', 'type' => 'hidden', 'value' => $movement['Movement']['tags']));
					echo $this->Form->end();
					?>

				<?php } ?>

			</div>

			<div class="visible-md visible-lg">
				<?php echo $this->element('discussion', array('type' => 'movement', 'parent_id' => $movement["Movement"]["id"])); ?>
			</div>
		</div>

		<div class="col-md-3">

			<!-- Movement Sidebar -->
			<?php echo $this->element('Movements/sidebar', array('movement' => $movement)); ?>

		</div>

	</div>

</div>

<!-- Login Modal -->
<?php echo $this->element('Modals/auth_modal', array('movement' => $movement, 'auto_show' => false)); ?>

<!-- Support Modal -->
<?php echo $this->element('Modals/support_modal', array('movement' => $movement)); ?>

<!-- Download Modal -->
<?php echo $this->element('Modals/download_modal', array('movement' => $movement)); ?>
