	<!-- Name Task -->

<?php
echo $this->Html->css('tasks/landing', null, array('inline'=>false));
echo $this->Html->css('tasks/view', null, array('inline'=>false));
echo $this->Html->script('tasks/view');
echo $this->Html->script('contributions/contributions');
echo $this->Html->script('votes/votes');
?>

<div class="row banner-row">
	<h3><?php echo __($design_task["AppTypeDesignTask"]["DesignTask"]["name"]); ?></h3>
</div>

<div class="row">
	<div class="container">
		<ul class="breadcrumbs pull-left">
			
			<?php echo $this->Html->link('<li><i class="fa fa-wrench"></i>' . __('Design Area') . '</li>', array('controller' => 'tasks', 'action' => 'landing', $movement["Movement"]["id"]), array('escape' => false)); ?>

			<?php echo '<li class="active">' . __($design_task["AppTypeDesignTask"]["DesignTask"]["name"]) . '</li>'; ?>
		</ul>
	</div>
</div>

<div class="row">

	<div class="container">
		
		<div class="design-header col-md-12">

			<div class="row" id="design-header-row">

				<div class="col-md-12" style="padding-left:0px;">

					<?php echo $this->Html->link('<div class="movement-photo" style="background-image:url(' . $this->webroot . 'img/movements/large/' . $movement["MovementPhoto"][0]["filename"] . ')"></div>', array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"]), array('escape' => false)); ?>

					<div class="task-info">
						<h2><?php echo $this->Html->link($movement["Movement"]["title"], array('controller' => 'movements', 'action' => 'view', $movement["Movement"]["id"])); ?></h2>
						<?php if ($movement["Movement"]["phase"] == 1) { ?>
							<h4><i class="fa fa-circle-o"></i><?php echo __('Design Phase Active'); ?></h4>
						<?php } else { ?>
							<h4><i class="fa fa-check-circle"></i><?php echo __('Design Phase Complete'); ?></h4>
						<?php } ?>
					</div>

				</div>

				<div class="clearfix"></div>

			</div>

			<div class="row">
				
				<?php if ($movement["Movement"]["phase"] == 1) { ?>
					<?php if ($is_supporter) { ?>
						<div class="info-banner info-banner-info">
							<h1><?php echo __('Instructions'); ?></h1>
							<p><?php echo __($design_task["AppTypeDesignTask"]["DesignTask"]["instruction"]); ?></p>
						</div>
					<?php } else { ?>
						<div class="info-banner info-banner-default">
							<p><?php echo __('You have not supported this movement so you are not eligible to vote or contribute in the design phase. You are however free to browse what others have submitted.'); ?></p>
						</div>
					<?php } ?>
				<?php } else if ($movement["Movement"]["phase"] >= 2) { ?>
						<div class="info-banner info-banner-success">
							<h1><?php echo __('Design Complete'); ?></h1>
							<p><?php echo __('The design phase is now complete, you can view the contributions below. The winning contributions are highlighted in green.'); ?></p>
						</div>
				<?php } ?>
			
			</div>

		</div>

	</div>

</div>


<?php echo $this->element('Modals/contribution_flag_modal'); ?>

<style type="text/css">

<?php if ($movement["Movement"]["phase"] >= 2) { ?>

	.tile h3 {
		color: #BBB;
	}

	.tile .vote-count {
		color: #BBB;
	}

	.tile:first-child {
		border-color: #5BCA5B;
	}

	.tile:first-child .vote-count {
		background-color: #EEF5EC;
		color: #5BCA5B;
	}

	.tile:first-child .padding {
		background-color: #EEF5EC;
		color: #CCC;
		border-left: #5BCA5B 1px solid;
	}

	.tile:first-child h3 {
		color: #5BCA5B;
	}

	.winning-contribution {
		background-color: #EEF5EC;
		border-color: #5BCA5B;
		color: #5BCA5B;
		border-left: #5BCA5B 1px solid;
	}

	.winning-contribution h3 {
		color: #5BCA5B;
	}

	.winning-contribution .padding {
		border-left: #5BCA5B 1px solid;
	}

	.winning-contribution .vote-count {
		color: #5BCA5B;
	}

<?php } ?>

</style>

<?php echo $this->element('Tasks/' . $design_task["AppTypeDesignTask"]["DesignTask"]["element"], array('contributions' => $contributions, 'movement' => $movement, 'is_supporter' => $is_supporter)); ?>

<div id="temp" style="display:none"></div><!-- Used to escape input -->

<div class="row">

	<div class="container">

		<?php echo $this->element('discussion', array('type' => 'design_task', 'parent_id' => $design_task["MovementDesignTask"]["id"])); ?>	

	</div>

</div>

<script type="text/javascript">

	var $container = $('#contributions_wrapper');

	$container.imagesLoaded( function() {
	
		$container.masonry({"isFitWidth": true});

		$container.masonry('on', 'layoutComplete', function(msnryInstance, laidOutItems) {

			// Layout complete

		});
	
	});

</script>