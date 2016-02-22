<!-- Contributions landing view -->

<?php
echo $this->Html->css('tasks/landing', null, array('inline'=>false));
echo $this->Html->script('tasks/landing');
echo $this->Html->script('bootstrap-tour');

echo $this->Html->script('jquery-1.10.2.min');
?>

<script type="text/javascript">

	var show_tour = <?php if (isset($_GET['tour'])) { echo 'true'; } else { echo 'false'; } ?>;

</script>

<div class="row banner-row">
	<h3><?php echo __('Design Area'); ?></h3>
</div>

<div class="row">

	<div class="container">
		
		<div class="design-header col-md-12">

			<div class="row" id="design-header-row">

					<?php echo $this->Html->link('<div class="movement-photo" style="background-image:url(' . $this->webroot . 'img/movements/large/' . $movement["MovementPhoto"][0]["filename"] . ')"></div>', array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"]), array('escape' => false)); ?>

					<div class="task-info">
						<h2><?php echo $this->Html->link($movement["Movement"]["title"], array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"])); ?></h2>
						<?php if ($movement["Movement"]["phase"] < 1) { ?>
							<h4><i class="fa fa-warning"></i><?php echo __('Design Phase Not Available'); ?></h4>
						<?php } else if ($movement["Movement"]["phase"] == 1) { ?>
							<h4><i class="fa fa-circle-o"></i><?php echo __('Design Phase Active'); ?></h4>
						<?php } else { ?>
							<h4><i class="fa fa-check-circle"></i><?php echo __('Design Phase Complete'); ?></h4>
						<?php } ?>
					</div>

					<div class="time-remaining">
						<h1><?php echo ($movement["Movement"]["launch_start"] < 0) ? 0 : $movement["Movement"]["launch_start"] ?></h1>
						<h5><?php echo $movement["Movement"]["launch_start"] == 1 ? __('Day Remaining') : __('Days Remaining'); ?></h5>
					</div>


				<div class="clearfix"></div>
			</div>

			<div class="row">
				
				<?php if ($movement["Movement"]["phase"] < 1) { ?>
					<div class="info-banner info-banner-warning">
						<p><?php echo __('The design phase is not yet available, please come back later!'); ?></p>
					</div>
				<?php } else if ($movement["Movement"]["phase"] == 1) { ?>
					<?php if ($is_supporter) { ?>
						<div class="info-banner info-banner-info">
							<h1><?php echo __('Congratulations'); ?></h1>
							<p><?php echo __('This movement has made it to the design phase. Yourself and other supporters can now design the application together. Just click one of the following design tasks to explore, vote and contribute ideas.'); ?></p>
						</div>
					<?php } else { ?>
						<div class="info-banner info-banner-default">
							<p><?php echo __('You have not supported this movement so you are not eligible to vote or contribute in the design phase. You are however free to browse what others have submitted.'); ?></p>
						</div>
					<?php } ?>
				<?php } else if ($movement["Movement"]["phase"] >= 2) { ?>
					<div class="info-banner info-banner-success">
						<h1><?php echo __('Design Complete'); ?></h1>
						<p><?php echo __('The design phase is now complete, we will select the highest voted contributions to tailor the application. You can view the community\'s contributions below.'); ?></p>
					</div>
				<?php } ?>

			</div>

		</div>

	</div>

</div>

<div class="row">
	<div class="container">

		<div class="movement-tasks-container">

			<div class="movement-tasks">

				<?php

				$design_tasks_completed = 0;

				$index = 0;

				foreach($design_tasks as $design_task): ?>

					<a href="<?php echo $movement["Movement"]["id"]; ?>/task/<?php echo $design_task["MovementDesignTask"]["id"]; ?>">
						<div class="movement-task" id="movement-task-<?php echo $index; ?>">
							<table>
								<tr>
									<?php if ($is_supporter) { ?>
										<td class="status-container" valign="middle" rowspan="3">
											<?php
											if ((($design_task['MovementDesignTask']['user_contribution_count'] > 0) && (!$design_task['AppTypeDesignTask']['DesignTask']['auto_generated'])) || (($design_task['MovementDesignTask']['user_vote_count'] > 0) && ($design_task['AppTypeDesignTask']['DesignTask']['auto_generated']))) { ?>
												
												<?php $design_tasks_completed++; ?>
												<i class="fa fa-check"></i>
											
											<?php } else { ?>

												<i class="fa fa-circle-o"></i>
											
											<?php } ?>
										</td>
									<?php } else { ?>
										<td class="status-container" valign="middle" rowspan="3">
											
											<i class="fa fa-lock"></i>
											
										</td>
									<?php } ?>

									<td valign="middle" style="width:100%"><h3 class="list-group-item-heading"><?php echo __($design_task["AppTypeDesignTask"]["DesignTask"]["name"]); ?></h3></td>
									<td class="stats-container" valign="middle" rowspan="3"><i class="fa fa-chevron-<?php if ($this->Session->read('Config.text_direction') == 'RTL') { echo 'left'; } else { echo 'right'; } ?>"></i></td>
								</tr>
								<tr>
									<td valign="middle" style="width:100%"><p class="list-group-item-text"><?php echo __($design_task["AppTypeDesignTask"]["DesignTask"]["description"]); ?></p></td>
								</tr>
								<tr>
									<td valign="middle" style="width:100%">
										<div class="recent-contributions">

											<h3>
												<?php
												if ($design_task['AppTypeDesignTask']['DesignTask']['auto_generated']) {
													echo $design_task['MovementDesignTask']['new_contribution_count']; ?> <?php echo __('Fixed Option'); ?><?php echo ($design_task['MovementDesignTask']['new_contribution_count'] == 1) ? '' : 's';
												} else {
													echo $design_task['MovementDesignTask']['new_contribution_count']; ?> <?php echo __('User Contribution'); ?><?php echo ($design_task['MovementDesignTask']['new_contribution_count'] == 1) ? '' : 's';
												}
												?>
											</h3>

										</div>
									</td>
								</tr>
							</table>
						</div>
					</a>

				<?php
				$index++;
				endforeach; ?>

				<?php if ($design_tasks_completed == count($design_tasks)): ?>
					<div class="row">
						<div class="col-md-12 design-header">

							<div class="info-banner info-banner-success" style="text-align:center">
								<h1><?php echo __('Awesome!'); ?></h1>
								<p><?php echo __('You\'ve submitted contributions to all of the design tasks, thank you!'); ?><br /><?php echo __('Don\'t forget to come back and vote on contributions from other members!'); ?></p>
							</div>

						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="row">

	<div class="container">

		<?php echo $this->element('discussion', array('type' => 'design_landing', 'parent_id' => $movement["Movement"]["id"])); ?>	

	</div>

</div>

<script type="text/javascript">
	$( document ).ready(function() {
		$('#design_phase_days_remaining').tooltip('show').tooltip('hide');
	});
</script>