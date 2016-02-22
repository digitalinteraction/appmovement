<?php
App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'Validation', 'Utility');

class Feedback extends AppModel {

    function __construct()
    {
        parent::__construct(); 
        $this->useDbConfig = 'default';
    }

}
?>