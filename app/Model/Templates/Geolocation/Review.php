<?php
class Review extends AppModel {

    public $specific = true;

    var $hasMany = array('Photo', 'Like', 'Report');

    var $belongsTo = array(
    	'User' => array('counterCache' => true),
    	'Venue' => array('counterCache' => true)
    	);

    var $virtualFields = array(
        'photo_count' => 'SELECT COUNT(*) FROM photos as Photo WHERE Photo.review_id = Review.user_id AND Photo.flag = 0',
        'contribution_count' => 'SELECT COUNT(*) FROM reviews as Review WHERE Review.user_id = Review.user_id AND Review.flag = 0',
        'like_count' => 'SELECT COUNT(*) FROM likes as p WHERE p.review_id = Review.id'
    );



    public $validate = array(
        'review_text' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter some review text'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 800),
                'message' => 'Review text must be less than 800 characters'
            )
        ),
        // 'photo' => array(
        //     'isCorrectFileType' => array(
        //         'rule' => array('extension', array('jpg', 'jpeg', 'png')),
        //         'message' => 'Image must be of type: jpg, jpeg or png'
        //     ),
        //     'isNotTooSmall' => array(
        //         'rule' => array('fileSize', '>=', '5KB'),
        //         'message' => 'Image must be more than 5KB'
        //     ),
        //     'isNotTooBig' => array(
        //         'rule' => array('fileSize', '<=', '5MB'),
        //         'message' => 'Image must be less than 5MB'
        //     )
        // )
    );

    public function beforeFind($queryData) {

        parent::beforeFind($queryData);
        $defaultConditions = array('Review.deleted' => 0);
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }
}
?>