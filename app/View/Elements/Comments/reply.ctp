<?php 

$current_user_has_voted_up = false;
$current_user_has_voted_down = false;

foreach ($reply["CommentVotes"] as $votes) {
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
<div class="reply-container">
	<div class="comment-profile">
		<div class="comment-profile-photo" style="background-image:url('<?php echo $this->webroot; ?>img/users/thumb/<?php echo $reply["User"]["photo"]; ?>')"></div>
	</div>

	<div class="reply-body" id="comment-<?php echo $reply["Comment"]["id"]; ?>">
		<p class="reply-details"><?php echo $reply["User"]["username"]; ?> - <span class="comment-created-ago" title="<?php echo $reply["Comment"]["created"]; ?>"></span> <?php if ($reply["User"]["id"] == $this->Session->read('Auth.User.id')) { echo '<i class="comment-delete-button fa fa-trash-o" style="margin-left:8px" data-commentid="' . $reply["Comment"]["id"] . '"></i>'; } ?>
		</p>
		<p><?php echo $reply["Comment"]["text"]; ?></p>
		<div class="reply-votes">
			<?php $comment_score = ($reply['Comment']['up_votes'] - $reply['Comment']['down_votes']); ?>
					<span id="comment-score-<?php echo $reply["Comment"]["id"]; ?>" class="<?php if($comment_score > 0): echo 'comment-vote-positive'; elseif($comment_score < 0): echo 'comment-vote-negative'; else: echo "neutral"; endif ?>"><?php echo $comment_score; ?></span> <i class="fa fa-arrow-up <?php if($current_user_has_voted_up): echo 'comment-vote-positive'; endif?>" id="vote-up-<?php echo $reply["Comment"]["id"]; ?>" data-commentid="<?php echo $reply["Comment"]["id"]; ?>"></i></i> <i class="fa fa-arrow-down <?php if($current_user_has_voted_down): echo 'comment-vote-negative'; endif?>" id="vote-down-<?php echo $reply["Comment"]["id"]; ?>" data-commentid="<?php echo $reply["Comment"]["id"]; ?>"></i>
		</div>
	</div>
</div>