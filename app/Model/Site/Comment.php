<?php
class Comment extends AppModel {
    
    public $validationDomain = 'validation_errors';

	public $name = 'Comment';

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'CommentType' => array(
			'className' => 'CommentType',
			'foreignKey' => 'comment_type_id'
		)
	);

    public $hasMany = array('CommentVotes');

    var $virtualFields = array(
        'up_votes' => 'SELECT COUNT(CommentVote.id) FROM comment_votes as CommentVote WHERE CommentVote.comment_id = Comment.id AND CommentVote.up = 1',
        'down_votes' => 'SELECT COUNT(CommentVote.id) FROM comment_votes as CommentVote WHERE CommentVote.comment_id = Comment.id AND CommentVote.up = 0',
    );

	public $validate = array(
        'text' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a comment'
            ),
            'isMinLength' => array(
                'rule'    => array('minLength', 3),
                'message' => 'Your comment must be at least 3 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 500),
                'message' => 'Your comment must be less than 500 characters'
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
            )
        ),
        'parent_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter parent_id'
            ),
            'isNumeric' => array(
            	'rule'    => 'numeric',
            	'message' => 'Please only enter parent_id as numeric value'
            ),
            'isValidParentId' => array(
                'rule' => array('isValidParentId', 'parent_id'),
                'required' => true,
                'on'         => 'create',
                'message' => 'Parent_id incorrect'
            ),
        ),
        'comment_type_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter comment_type_id'
            ),
            'isValidType' => array(
                'rule' => array('isValidType', 'comment_type_id'),
                'required' => true,
                'on'         => 'create',
                'message' => 'Comment_type_id incorrect'
            )
        )
    );

	public function get_comments_by_parent_id_and_type_id($parent_id, $type_id){
        
        $comments = $this->find('all', array(
                'conditions' => array(
                    'parent_id' => $parent_id,
                    'comment_type_id' => $type_id,
                    'in_reply_to_comment_id' => NULL,
                ),
                'fields' => array(
                    'Comment.*',
                    'User.id',
                    'User.username',
                    'User.photo',
                ),
                'order' => array(
                    'Comment.created DESC'
                )
            ));

        foreach($comments as $key => $comment)
        {
            // fetch any replies
            $comments[$key]["replies"] = $this->find('all', array(
                    'conditions' => array(
                        'in_reply_to_comment_id' => $comment["Comment"]["id"]
                    )
                ));
        }

        return $comments;
	}

	// This is a custom validator for the comment_type_id
	// you can pass in either the type string name or the comment_type_id
	// when adding a comment it will check the type exists in the database
	// and validate the form
	function isValidType($array, $field) {
		return $this->CommentType->get_comment_type_by_id_or_name($this->data[$this->alias][key($array)]);
    }

    function isValidParentId($array, $field) {
    	$comment_type = $this->CommentType->get_comment_type_by_id_or_name($this->data["Comment"]["comment_type_id"]);

    	// make sure we have a valid type first
    	if($comment_type)
    	{
    		App::import('Model', $comment_type["CommentType"]["parent_model_name"]);

    		$parent_model = new $comment_type["CommentType"]["parent_model_name"]();

    		$parent_id = $parent_model->find('count', array(
				'conditions' => array(
					$comment_type["CommentType"]["parent_model_name"] . '.id' => $this->data["Comment"]["parent_id"]
				),
				'limit' => 1
			));
    		
    		if($parent_id == 1)
    		{
    			return true;
    		}
    		else
    		{
    			return false;
    		}
    	}
    }

    function beforeSave($options = array()) {

        if (array_key_exists("comment_type_id", $this->data["Comment"])) {
            
            $comment_type = $this->CommentType->get_comment_type_by_id_or_name($this->data["Comment"]["comment_type_id"]);
            $this->data["Comment"]["comment_type_id"] = $comment_type["CommentType"]["id"];

        }

    	// CHECK HERE IF COMMENT IS BEING SPAMMED - use timeout or something
    }

    public function beforeFind($queryData) {

        parent::beforeFind($queryData);
        $defaultConditions = array('Comment.deleted' => 0);
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }
}
?>