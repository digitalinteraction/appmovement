<?php 

$current_user_has_voted_up = false;
$current_user_has_voted_down = false;

foreach ($comment["CommentVotes"] as $votes) {
	if($votes["user_id"] == CakeSession::read("Auth.User.id")) {
		if($votes["up"]){
			$current_user_has_voted_up = true;
			$current_user_has_voted_down = false;	
		}
		else
		{
			$current_user_has_voted_down = true;
			$current_user_has_voted_up = false;
		}
	}
}

?>
<div class="row comment" id="comment-<?php echo $comment["Comment"]["id"]; ?>">
	<div class="col-md-12">
		<div class="comment-container">
			<div class="comment-profile">
				<div class="comment-profile-photo" style="background-image:url('<?php echo $this->webroot; ?>img/users/thumb/<?php echo $comment["User"]["photo"]; ?>')"></div>
			</div>
			<div class="comment-body">
				<p class="comment-details"><?php echo $comment["User"]["username"]; ?> - <span class="comment-created-ago" title="<?php echo $comment["Comment"]["created"]; ?>"></span> <?php if ($comment["User"]["id"] == $this->Session->read('Auth.User.id')) { echo '<i class="comment-delete-button fa fa-trash-o" style="margin-left:8px" data-commentid="' . $comment["Comment"]["id"] . '"></i>'; } ?></p>
				<p><?php echo $comment["Comment"]["text"]; ?></p>
				<div class="comment-votes">
					<?php $comment_score = ($comment['Comment']['up_votes'] - $comment['Comment']['down_votes']); ?>
					<span id="comment-score-<?php echo $comment["Comment"]["id"]; ?>" class="<?php if($comment_score > 0): echo 'comment-vote-positive'; elseif($comment_score < 0): echo 'comment-vote-negative'; else: echo "neutral"; endif ?>"><?php echo $comment_score; ?></span> <i class="fa fa-arrow-up <?php if($current_user_has_voted_up): echo 'comment-vote-positive'; endif?>" id="vote-up-<?php echo $comment["Comment"]["id"]; ?>" data-commentid="<?php echo $comment["Comment"]["id"]; ?>"></i></i> <i class="fa fa-arrow-down <?php if($current_user_has_voted_down): echo 'comment-vote-negative'; endif?>" id="vote-down-<?php echo $comment["Comment"]["id"]; ?>" data-commentid="<?php echo $comment["Comment"]["id"]; ?>"></i>
				</div>
			</div>
			<div id="comment-replies-<?php echo $comment["Comment"]["id"]; ?>" class="comment-replies">
				<?php if(array_key_exists("replies", $comment)): ?>
					<?php foreach($comment["replies"] as $reply): ?>
						<?php echo $this->element('Comments/reply', array('reply' => $reply)); ?>
					<?php endforeach; ?>
				<?php endif;?> 
			</div>
			<div class="comment-reply">
				<?php if($comment["Comment"]["in_reply_to_comment_id"] == NULL): ?>
					<span class="comment-reply-btn"><i class="fa fa-reply"></i> <?php echo __('Reply'); ?></span>
				<?php endif; ?>
				<div class="row comment-reply-box">
					<div class="col-md-12">
						<?php echo $this->Form->create('Comment', array('class' => 'comment-form reply-form', 'action' => 'add')); ?>
							
							<div class="input-section">
								<div class="alert alert-warning reply-form-errors">
									<ul class="reply-form-errors-list">
									</ul>
								</div>

								<input type="text" class="input-field reply-form-input" id="reply-form-textarea-<?php echo $comment["Comment"]["id"]; ?>" placeholder="<?php echo __('Your comment'); ?>" data-in-reply-to-comment-id="<?php echo $comment["Comment"]["id"]; ?>"></input>
								<div class="btn comment-reply-submit" data-in-reply-to-comment-id="<?php echo $comment["Comment"]["id"]; ?>"><?php echo __('Reply'); ?></div>
								
							</div>

						<?php echo $this->Form->end(); ?>
					</div>
				</div>
			</div>
			<div class="clearfloat"></div>
		</div>
	</div>
</div>