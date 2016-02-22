<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'Validation', 'Utility');

class User extends AppModel {

    function __construct()
    {
        parent::__construct(); 
        $this->useDbConfig = 'default';
    }

    public $validate = array(
        'fullname' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter your full name'
            ),
            'isCorrectLength' => array(
                'rule' => array('minLength', '4'),
                'message' => 'Full name must be more than 4 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 40),
                'message' => 'Full name must be less than 40 characters'
            )
        ),
        'username' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a username'
            ),
            'isUnique' => array(
                    'rule' => 'isUnique',
                    'allowEmpty'=> false,
                    'required' => true,
                    'message' => 'That username has been taken, please choose another'
            ),
            'isCorrectFormat' => array(
                'rule'    => '/^[\p{Arabic}a-zA-Z0-9_-]*$/u',
                'message' => 'Only letters, numbers, underscores or dashes. Please enter a valid name'
            ),
            'isCorrectLength' => array(
                'rule' => array('minLength', '4'),
                'message' => 'Usernames must be more than 4 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 40),
                'message' => 'Usernames must be less than 40 characters'
            )
        ),
        'password' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a password'
            ),
            'isCorrectLength' => array(
                'rule' => array('minLength', '8'),
                'message' => 'Password must be more than 8 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 40),
                'message' => 'Password must be less than 40 characters'
            )
        ),
        'password_confirm' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please re-enter your password'
            ),
            'isMatching' => array(
                'rule' => array('equalToField', 'password'),
                'required' => true,
                'message' => 'Both password fields must match'
            ),
            'isCorrectLength' => array(
                'rule' => array('minLength', '8'),
                'message' => 'Password must be more than 8 characters'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 40),
                'message' => 'Password must be less than 40 characters'
            )
        ),
        'email'    => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false
            ),
            'isEmail' => array(
                'rule' => 'email',
                'message' => 'Please enter a valid email address'
            ),
            'isUnique' => array(
                    'rule' => 'isUnique',
                    'allowEmpty'=> false,
                    'required' => true,
                    'message' => 'An account registered with this email already exists, please enter another or login below'
            ),
            'isMaxLength' => array(
                'rule'    => array('maxLength', 240),
                'message' => 'Emails must be less than 240 characters'
            )
        ),
        'photo' => array(
            'isCorrectFileType' => array(
                'rule' => array('extension', array('jpg', 'jpeg', 'png')),
                'message' => 'Image must be of type: jpg, jpeg or png'
            ),
            'isNotTooSmall' => array(
                'rule' => array('fileSize', '>=', '5KB'),
                'message' => 'Image must be more than 5KB'
            ),
            'isNotTooBig' => array(
                'rule' => array('fileSize', '<=', '3MB'),
                'message' => 'Image must be less than 3MB'
            )
        )
    );

    public function getUser($user_id = null) {
    
        $user = $this->findById($user_id);

        return $user;
    }

    public function getUserByEmail($email = null) {
    
        $user = $this->findByEmail($email);

        return $user;
    }

    public function checkUsername($username = null) {

        if ($this->hasAny(array('username' => $username))) {
            
            // username exists

            return false;
        }

        return true;

    }

    public function checkEmail($email = null) {
    
        if ($this->hasAny(array('email' => $email))) {
            
            // Email exists

            return false;
        }

        if (Validation::email($email) == false) {
            
            // Email invalid

            return false;
        }

        return true;
    }

    public function beforeSave($options = array())
    {
        if (isset($this->data[$this->alias]['password']))
        {
            $passwordHasher = new SimplePasswordHasher();
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
        }
        return true;
    }

    function equalToField($array, $field)
    {
        return strcmp($this->data[$this->alias][key($array)], $this->data[$this->alias][$field]) == 0;
    }
}
?>