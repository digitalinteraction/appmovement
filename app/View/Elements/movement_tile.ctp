
<!-- Movement Tile Element -->

<?php
if (!isset($show_description)) { $show_description = true; }
?>

<div class="movement-tile phase-<?php echo $movement["Movement"]["phase"]; ?>">

	<div class="inner-container" onClick="document.location='/view/<?php echo $movement["Movement"]["id"]; ?>'">

		<?php if ($movement["Movement"]["supported"]) { ?>
		
			<div class="supported-strip"><?php echo __('SUPPORTED'); ?></div>

		<?php } ?>

		<?php echo $this->Html->link('<div class="movement-photo" style="background-image:url(' . Router::url('/', true) . 'img/movements/medium/' . $movement["MovementPhoto"][0]["filename"] . ')"></div>', array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"]), array('escape' => false)); ?>

		<div class="tile-content">

			<div class="tile-description">
				<h3 class="movement-title"><?php echo $this->Html->link($movement["Movement"]["title"], array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"]), array('escape' => false)); ?></h3>

				<p class="movement-creator"> <?php echo __('by') . ' ' . $this->Html->link($movement["User"]["fullname"] . '</h3>', array('controller' => 'users', 'action' => 'view', $movement["User"]["id"]), array('escape' => false)); ?></p>

				<?php if ($show_description) {
					
					echo '<p class="movement-description">';
					echo $this->Text->truncate(
					    $movement["Movement"]["description"],
					    120,
					    array(
					        'ellipsis' => '...',
					        'exact' => false
					    )
					);
		 			echo '</p>';

				} ?>
			</div>

			<div class="tile-footer">

				<?php if ($movement["Movement"]["flag"] == 1) { ?>

						<hr />

						<h5><i class="fa fa-eye"></i><?php echo __('Under Review'); ?></h5>

				<?php } else { ?>

					<?php if ($movement["Movement"]["phase"] == -1): ?>

						<hr />

						<h5><i class="fa fa-thumbs-down"></i><?php echo __('Unsuccessful'); ?></h5>
						
					<?php endif ?>
					
					<?php if ($movement["Movement"]["phase"] == 0): ?>

						<div class="progress">
							<div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="<?php echo $movement["Movement"]["progress"]; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $movement["Movement"]["progress"]; ?>%">
							</div>
						</div>

						<div class="movement-supporters">
							
							<?php
							$percent_complete = ($movement["Movement"]["supporters_count"] > 0) ? round((100 / $movement["Movement"]["target_supporters"]) * $movement["Movement"]["supporters_count"]) : 0;
							?>
							<p><strong><?php echo $percent_complete . __('%'); ?></strong></p>
							<p><?php echo __('SUPPORTED'); ?></p>
						</div>
						<div class="movement-time">
							<p><strong><?php echo $movement["Movement"]["design_start"]; ?></strong></p>
							<p><?php echo $movement["Movement"]["design_start"] == 1 ? __('DAY TO GO') : __('DAYS TO GO'); ?></p>
						</div>
						
					<?php endif ?>

					<?php if ($movement["Movement"]["phase"] == 1): ?>

						<hr />

						<h5><i class="fa fa-wrench"></i><?php echo __('Design Phase'); ?></h5>
						
					<?php endif ?>

					<?php if ($movement["Movement"]["phase"] == 2): ?>

						<hr />

						<h5><i class="fa fa-rocket"></i><?php echo __('Launch Phase'); ?></h5>
						
					<?php endif ?>

					<?php if ($movement["Movement"]["phase"] >= 3): ?>

						<hr />

						<h5><i class="fa fa-download"></i><?php echo __('Available to Download'); ?></h5>
						
					<?php endif ?>

				<?php } ?>

				<div class="clearfix"></div>

			</div>

		</div>

	</div>

</div>