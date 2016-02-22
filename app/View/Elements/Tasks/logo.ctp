
<!-- Logo Task Element -->

<?php
echo $this->Html->css('tasks/logo', null, array('inline'=>false));?>

<div class="row">
	<div class="container contributions-container">

		<div id="contributions_wrapper" class="js-masonry" data-masonry-options='{ "isFitWidth": true, "columnWidth": 270, "gutter": 20, "itemSelector": ".tile" }'>
			<?php
			foreach ($contributions as $item) {
				echo $this->element('Contributions/' . $design_task["AppTypeDesignTask"]["DesignTask"]["element"], array('item' => $item, 'movement' => $movement, 'is_supporter' => $is_supporter));
			} 
			?>
		</div>
		<div class="clearfloat"></div>
	</div>
</div>

<?php if (($movement["Movement"]["phase"] == 1) && $is_supporter) { ?>

	<div class="row">
		<div class="container contribute-area">
			<div class="contribute-container">
				<h2><?php echo __('Suggest %s', __($design_task["AppTypeDesignTask"]["DesignTask"]["name"])); ?></h2>
				<div class="alert alert-warning" id="contribution-form-errors">
					<ul id="contribution-form-errors-list">
					</ul>
				</div>
				<?php echo $this->Form->create('Contribution', array('type' => 'file', 'action' => '/add/file', 'id' => 'contributon-add-form')); ?>
				<?php echo $this->Form->input('file', array( 'type' => 'file', 'label' => false)); ?>
				<?php echo $this->Form->input('contribution_type_id', array('class' => 'input-field', 'id' => 'contribution_type_id', 'type' => 'hidden', 'value' => $design_task["AppTypeDesignTask"]["DesignTask"]["contribution_type_id"])); ?>
		    	<?php echo $this->Form->input('movement_design_task_id', array('class' => 'input-field', 'id' => 'movement_design_task_id', 'type' => 'hidden', 'value' => $design_task["MovementDesignTask"]["id"])); ?>
		    	<?php echo $this->Form->input('movement_id', array('class' => 'input-field', 'id' => 'movement_id', 'type' => 'hidden', 'value' => $movement["Movement"]["id"])); ?>
		    	<?php echo $this->Form->input('app_type_design_task_element', array('class' => 'input-field', 'id' => 'app_type_design_task_element', 'type' => 'hidden', 'value' => $design_task["AppTypeDesignTask"]["DesignTask"]["element"])); ?>
				<br />

				<button type="submit" class="btn btn-lg btn-success"><?php echo __('Submit Idea'); ?></button>
				<?php echo $this->Form->end(); ?>
			</div>
		</div>
	</div>

<?php } ?>