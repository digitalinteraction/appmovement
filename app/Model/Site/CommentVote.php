<?php
class CommentVote extends AppModel {
    
    public $validationDomain = 'validation_errors';

	public $name = 'CommentVote';

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Comment' => array(
			'className' => 'Comment',
			'foreignKey' => 'comment_id'
		)
	);

	public $validate = array(
        'up' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please vote up or down'
            )
        ),
        'user_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please include user_id'
            ),
            'isNumeric' => array(
            	'rule'    => 'numeric'
            ),
            'isUniqueVote' => array(
                'rule' => 'isUniqueVote',
                'required' => true,
                'message' => 'You may only cast your vote once'
            )
        ),
        'comment_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter comment_id'
            )
        )
    );

    function isUniqueVote($array, $field) {

        $votes = $this->find('all', array(
                    'conditions' => array(
                        'CommentVote.user_id' => $this->data['CommentVote']['user_id'],
                        'CommentVote.comment_id' => $this->data['CommentVote']['comment_id'],
                        'CommentVote.up' => $this->data['CommentVote']['up']
                )));
        if($votes) {
            return false;
        }
        
        return true;
    }
}
?>