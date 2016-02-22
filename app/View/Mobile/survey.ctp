<!-- Mobile Survey view -->

<?php
echo $this->Html->css('mobile/global', null, array('inline'=>false));
echo $this->Html->css('mobile/survey', null, array('inline'=>false));
?>

<div class="row">

	<div class="container">

		<div class="survey-container">
			
			<p><?php echo $survey["Survey"]["content"]; ?></p>
			
			<?php echo $this->Form->create('Survey', array('class' => 'survey-form', 'style' => 'display:none')); ?>

				<label>Question A</label>
				<?php echo $this->Form->input('questionA', array('type' => 'text', 'class' => 'input-field', 'label' => false)); ?>
				
				<label>Question B</label>
				<?php echo $this->Form->input('questionB', array('type' => 'text', 'class' => 'input-field', 'label' => false)); ?>

				<?php
				$options = array(
				    'label' => 'Submit Survey',
				    'class' => 'btn btn-success btn-lg',
				);
				?>

			<?php echo $this->Form->end($options); ?>

		</div>
	
	</div>

</div>