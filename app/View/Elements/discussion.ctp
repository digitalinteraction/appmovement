<?php
echo $this->Html->css('comments', null, array('inline'=>false));
echo $this->Html->script('comments/comments');
echo $this->Html->script('jquery.timeago');
?>

<script type="text/javascript">
	
	var parent_id = '<?php echo $parent_id; ?>';
	var comment_type_id = '<?php echo $type; ?>';
	
</script>

<!-- Discussion Element -->

<div class="discussion-area">
	<div class="discussion-wrapper">

		<div class="row">
			<div class="col-md-12">
				<h2><?php echo __('Discussion'); ?></h2>
			</div>
		</div>

		<div class="row comment">
			<div class="col-md-12">
				<?php echo $this->Form->create('Comment', array('class' => 'comment-form', 'action' => 'add', 'id' => 'comment_add_form')); ?>
					
					<div class="input-section">
						<div class="alert alert-warning" id="comment-form-errors">
							<ul id="comment-form-errors-list">
							</ul>
						</div>

						<input type="text" class="input-field" id="comment-form-textarea" placeholder="<?php echo __('Your comment'); ?>"></input>
						<div class="btn" id="discussion_comment_submit_btn"><?php echo __('Comment'); ?></div>
						
					</div>

				<?php echo $this->Form->end(); ?>
			</div>
			<div class="col-md-12">
				<div id="comment-sort"><span id="comment-sort-newest"><?php echo __('Order by date'); ?> <i class ="fa fa-caret-up"></i></span><span style="display:none" id="comment-sort-oldest"><?php echo __('Order by date'); ?> <i class ="fa fa-caret-down"></i></span></div>
			</div>
		</div>

		<div id="comments-container" class="row">
		
			<?php
			$comments = $this->requestAction(array('controller' => 'comments', 'action' => 'get'), array('pass' => array('parent_id' => $parent_id, 'type_id' => $type)));
			
			if($comments):
				foreach($comments as $comment):
					echo $this->element('Comments/comment', array('comment' => $comment));
				endforeach;
			endif;
			?>

		</div>

	</div>
</div>