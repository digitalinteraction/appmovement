<?php
class Movement extends AppModel {

    public $validationDomain = 'validation_errors';

    var $virtualFields = array(
        'supporters_count' => 'SELECT COUNT(*) FROM supporters as Supporter WHERE Supporter.movement_id = Movement.id AND Supporter.confirmed = 1',
        'promoters_count' => 'SELECT COUNT(*) FROM promoters as Promoter WHERE Promoter.movement_id = Movement.id',
        'design_start' => 'SELECT DATEDIFF(DATE_ADD(Movement.created, INTERVAL Movement.support_duration DAY), NOW())',
        'launch_start' => 'SELECT DATEDIFF(DATE_ADD(Movement.created, INTERVAL (Movement.support_duration + Movement.design_duration) DAY), NOW())',
        'progress' => '100 / (target_supporters / (SELECT COUNT(*) FROM supporters as Supporter WHERE Supporter.movement_id = Movement.id AND Supporter.confirmed = 1))'
    );

    public $belongsTo = array('AppType', 'User');
    
    public $hasMany = array(
        'MovementPhoto',
        'MovementUpdate' => array(
            'conditions' => array('deleted' => 0),
        ),
        'Supporters',
        'Promoters'
    );
   
    public $validate = array(
        'title' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a title for your movement'
            ),
            'isMinLength' => array(
                'rule'    => array('minLength', 10),
                'message' => 'The title must be more than 10 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 50),
                'message' => 'The title must be less than 50 characters'
            )
        ),
        'description' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a short paragraph that describes your movement'
            ),
            'isMinLength' => array(
                'rule'    => array('minLength', 20),
                'message' => 'The description must be more than 20 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 140),
                'message' => 'The description must be less than 140 characters'
            )
        ),
        'overview' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a detailed overview of your movement'
            ),
            'isMinLength' => array(
                'rule'    => array('minLength', 100),
                'message' => 'The overview must be more than 100 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 4000),
                'message' => 'The overview must be less than 4000 characters'
            )
        ),
        'app_type_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please select the type of app you wish to create'
            )
        ),
        'photo' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please select a photo for your movement'
            )
        )
    );

    public function get_movement($id = null) {
    
        $movements = $this->find('all', array(
            'conditions' => array(
                'Movement.id' => $id
                )
        ));

        $movements = $this->check_supported($movements);

        return $movements[0];
    }

    public function get_all_movements() {
    
        $movements = $this->find('all', array(
            'limit' => 40           
        ));

        $movements = $this->check_supported($movements);

        return $movements;
    }

    public function search_movements($query = null) {
    
        $movements = $this->find('all', array(
            'conditions' => array(
                'Movement.title LIKE ' => $query . '%',
                'Movement.flag' => 0
                ),
            'group' => 'Movement.id'
        ));

        $movements = $this->check_supported($movements);

        return $movements;
    }

    public function get_featured_movements() {

        $movements = $this->find('all', array(
                'limit' => 8,
                'order' => 'rand()',
                'conditions' => array('Movement.flag' => 0, 'Movement.phase' => array(0, 1, 2)),
                'order' => 'Movement.phase ASC',
                'group' => 'Movement.id'
            ));

        $movements = $this->check_supported($movements);

        return $movements;
    }

    public function get_users_movements($user_id) {

        $movements = $this->find('all', array(
                'conditions' => array('Movement.user_id' => $user_id),
                'group' => 'Movement.id',
                'order' => 'Movement.created DESC'
            ));

        $movements = $this->check_supported($movements);

        return $movements;
    }

    public function get_supported_movements($user_id) {
        
        $movements = $this->find('all', array(
                'joins' => array(
                    array(
                        'table' => 'supporters',
                        'alias' => 'SupportersJoin',
                        'type' => 'INNER',
                        'conditions' => array(
                            'SupportersJoin.movement_id = Movement.id'
                        )
                    )
                ),
                'conditions' => array(
                    'Movement.user_id !=' => $user_id, 
                    'SupportersJoin.supporter' => $user_id,
                    'SupportersJoin.confirmed' => 1
                ),
                'order' => 'Movement.created DESC'
            ));

        $movements = $this->check_supported($movements);

        return $movements;
    }

    public function check_supported($movements) {

        $user_id = CakeSession::read("Auth.User.id");

        $supporter = ClassRegistry::init('Supporter');

        foreach ($movements as &$movement) {

            $conditions = array(
                'Supporter.supporter' => $user_id,
                'Supporter.movement_id' => $movement["Movement"]["id"],
                'Supporter.confirmed' => 1
            );

            $movement["Movement"]["supported"] = ($supporter->hasAny($conditions)) ? true : false;

        }

        return $movements;

    }

    public function beforeFind($queryData) {

        parent::beforeFind($queryData);
        $defaultConditions = array('Movement.deleted' => 0);
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }
}
?>