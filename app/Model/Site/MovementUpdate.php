<?php
class MovementUpdate extends AppModel {
    
    public $validationDomain = 'validation_errors';

	public $name = 'MovementUpdate';

    public $validate = array(
        'text' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter an update'
            ),
            'isMinLength' => array(
                'rule'    => array('minLength', 10),
                'message' => 'Your update must be at least 10 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 4000),
                'message' => 'Your update must be less than 4000 characters'
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
            'isValidUser' => array(
                'rule'    => 'isValidUser',
                'require' => true,
                'message' => 'You are not the creator of this movement'
            ),
            'isNotSpamming' => array(
                'rule' => 'isNotSpamming',
                'require' => true,
                'message' => 'You can only post 2 updates per day. Please try again in a short while.'
            )
        )
    );

    public function isNotSpamming() {
        App::Import('Model', 'Movement');
        $this->Movement = new Movement;

        $user_id = $this->data["MovementUpdate"]["user_id"];

        $movement_id = $this->data["MovementUpdate"]["movement_id"];

        $movement = $this->Movement->find('first', array(
                            'conditions' => array(
                                'Movement.id' => $movement_id,
                                'Movement.user_id' => $user_id
                            )
                        ));

        if(!$movement)
        {
            return false;
        }

        $previous_update = $this->find('all', array(
                        'conditions' => array(
                            'MovementUpdate.user_id' => $user_id,
                            'MovementUpdate.movement_id' => $movement_id,
                            'MovementUpdate.created >=' => date('Y-m-d H:i:s', strtotime('-24 hour'))
                        )
                ));


        if($previous_update)
        {
            if(count($previous_update) >= 2)
            {
                return false;
            }
        }

        return true;
    }

    public function isValidUser() {
        App::Import('Model', 'Movement');
        $this->Movement = new Movement;

        $user_id = $this->data["MovementUpdate"]["user_id"];
        
        if(!CakeSession::read('Auth.User.id'))
        {
            return false;
        }

        if(CakeSession::read('Auth.User.id') != $user_id)
        {
            return false;
        }

        $movement_id = $this->data["MovementUpdate"]["movement_id"];

        $movement = $this->Movement->find('first', array(
                            'conditions' => array(
                                'Movement.id' => $movement_id,
                                'Movement.user_id' => $user_id
                            )
                        ));

        if($movement)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function beforeFind($queryData) {

        parent::beforeFind($queryData);
        $defaultConditions = array('MovementUpdate.deleted' => 0);
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }
}
?>