<?php
// required to check user session variables
App::uses('CakeSession', 'Model/Datasource');
App::import('Vendor', 'ContributionValidator');

class Contribution extends AppModel {

    public $validationDomain = 'validation_errors';

    var $virtualFields = array(
        'up_votes' => 'SELECT COUNT(*) FROM votes as Vote WHERE Vote.contribution_id = Contribution.id AND Vote.vote_up = 1 AND Vote.flag = 0',
        'down_votes' => 'SELECT COUNT(*) FROM votes as Vote WHERE Vote.contribution_id = Contribution.id AND Vote.vote_up = 0 AND Vote.flag = 0',
        'votes' => 'SELECT ((SELECT COUNT(*) FROM votes as Vote WHERE Vote.contribution_id = Contribution.id AND Vote.vote_up = 1 AND Vote.flag = 0) - (SELECT COUNT(*) FROM votes as Vote WHERE Vote.contribution_id = Contribution.id AND Vote.vote_up = 0 AND Vote.flag = 0))'
    );

    public $belongsTo = array('ContributionType');

    public $uses = array('ContributionType');

    public $validate = array(
        'user_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'on'         => 'create',
                'message' => 'Please include user_id'
            ),
            'isNumeric' => array(
            	'rule'    => 'numeric',
                'message' => 'Please only enter a number for the user_id'
            ),
            'isAllowedToContribute' => array(
            	'rule' => array('isAllowedToContribute', 'user_id'),
            	'required' => true,
                'on'         => 'create',
                'message' => 'You cannot contribute to this movement as you are not a supporter or promoter of this movement'
            )
        ),
        'movement_design_task_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'on'         => 'create',
                'message' => 'Please enter movement_design_task_id'
            ),
            'isNumeric' => array(
            	'rule'    => 'numeric',
            	'message' => 'Please only enter movement_design_task_id as numeric value'
            ),
            'isValidMovementDesignTaskId' => array(
                'rule' => array('isValidMovementDesignTaskId', 'movement_design_task_id'),
                'required' => true,
                'on'         => 'create',
                'message' => 'Movement_design_task_id incorrect'
            ),
        ),
        'contribution_type_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'on'         => 'create',
                'message' => 'Please enter contribution_type_id'
            ),
            'isValidType' => array(
                'rule' => array('isValidType', 'contribution_type_id'),
                'required' => true,
                'on'         => 'create',
                'message' => 'Contribution_type_id incorrect'
            )
        ),
        'data' => array(
        	'isEmpty' => array(
	        	'rule' => 'notEmpty',
	        	'required' => true,
	        	'on'         => 'create',
	        	'message'  => 'Please enter a contribution',
        	),
        	'isValidFormat' => array(
                'rule' => array('isValidFormat', 'data'),
                'required' => true,
                'on'         => 'create',
                'message' => 'Please ensure that you have entered your contribution correctly'
            ),
            'isUnique' => array(
                'rule' => array('checkUnique', array('data', 'user_id', 'movement_design_task_id'), false), 
                'message' => 'You have already submitted a contribution with those details'
            )
        )
    );

	public function __construct($id = false, $table = null, $ds = null) {
		App::import('model', 'ContributionType');
		App::import('model', 'MovementDesignTask');
		App::import('model', 'Supporter');

	    parent::__construct($id, $table, $ds);
	}

    public function checkUnique($ignoredData, $fields, $or = true) {
        return $this->isUnique($fields, $or);
    }

	function isValidFormat($array, $field) {

        // $contribution_validator = new ContributionValidator();
        // $contribution_validator->load();

		// get contribution structure from contribution_types_table
		if(!array_key_exists("contribution_type_id", $this->data[$this->alias]))
		{	
			return false;
		}

    	$ContributionType = new ContributionType();
    	$contribution_type = $ContributionType->get_contribution_type_by_id_or_name($this->data[$this->alias]["contribution_type_id"]);

    	if(!$contribution_type)
    	{
    		return false;
    	}

    	if(!array_key_exists("data", $this->data[$this->alias]))
    	{
    		return false;
    	}

  //   	// take the contribution_type structure array and compare it with the contribution.data json array 
  //   	// being submitted. The count will be 0 if there are no differences in keys
    	if(count(array_diff_key(json_decode($this->data[$this->alias]["data"], true), json_decode($contribution_type["ContributionType"]["structure"], true)) == 0))
    	{

  //   		// keys match
    		// check if the value of the contribution.data is empty
            if(count(json_decode($this->data[$this->alias]["data"], true)) == 0)
            {
                return false;
            }

    		foreach(json_decode($this->data[$this->alias]["data"], true) as $data_element)
    		{
    			// if any of data is empty then fail
    			if(empty($data_element))
    			{
    				return false;
    			}
    		}

            // check valid hex structure
            if($contribution_type["ContributionType"]["type"] == 'colour')
            {
                foreach(json_decode($this->data[$this->alias]["data"], true) as $data_element)
                {
                    // go through each element and check that it is correctly structure
                    // otherwise fail on validation
                    if(!preg_match('/^#[a-f0-9]{6}$/i', $data_element))
                    {
                        return false;  
                    }
                }
            }
    		return true;
    	}
    	else
    	{
    		return false;
    	}
	}
	
	// make sure that the contribution type is valid for this movement_design_Task
	function isValidType($array, $field) {
		$ContributionType = new ContributionType();
		$MovementDesignTask = new MovementDesignTask();
		$contribution_type = $ContributionType->get_contribution_type_by_id_or_name($this->data[$this->alias][key($array)]);

		$MovementDesignTask->recursive = 2;
		$movement_design_task = $MovementDesignTask->find('first', array(
			'conditions' => array(
				'MovementDesignTask.id' => $this->data[$this->alias]["movement_design_task_id"]
			),
			'limit' => 1
		));

		// check that the form has correct contribution type
		if($contribution_type)
		{
			// check that movement design task has been found by movement design task id
			if($movement_design_task)
			{	
				// check that the design task is using the contribution_type_id supplied in the form
				if($movement_design_task["AppTypeDesignTask"]["DesignTask"]["contribution_type_id"] == $this->data[$this->alias][key($array)])
				{
					return true;
				}
			}
		}

		return false;
    }


    // Check that the movement design task exists
    // PUT IN AUTH ACCESS CONTROL LIST check in here to make sure users can contribute to this task
    function isValidMovementDesignTaskId($array, $field) {
		$MovementDesignTask = new MovementDesignTask();

		// recursive so we pull back MovementDesignTask -> AppTypeDesignTask -> DesignTask
		$MovementDesignTask->recursive = 2;
		$movement_design_task = $MovementDesignTask->find('first', array(
			'conditions' => array(
				'MovementDesignTask.id' => $this->data[$this->alias]["movement_design_task_id"]
			),
			'limit' => 1
		));

		if($movement_design_task)
		{
			return true;
		}
		else
		{
			return false;
		}
    }

    // For the contribution type check that the form has been submitted with the correct structure
    // function isValidStructure($array, $field) {
    	
    // }

    // Check if the current user is allowed to contribute to this movement
    function isAllowedToContribute($array, $field) {
    	$Supporter = new Supporter();
		$MovementDesignTask = new MovementDesignTask();

		if(!array_key_exists("movement_design_task_id", $this->data[$this->alias]))
		{
			return false;
		}

		$movement_design_task = $MovementDesignTask->find('first', array(
			'conditions' => array(
				'MovementDesignTask.id' => $this->data[$this->alias]["movement_design_task_id"]
			),
			'limit' => 1
		));

		$user_id = CakeSession::read("Auth.User.id");

		//if we have a user 
		if($user_id)
		{
			// check we have a design task associated with the form data
			if($movement_design_task)
			{
				// check if the current signed in user 
				// is a supporter of this movement
				$supporter = $Supporter->find('first', array(
								'conditions' => array(
									'Supporter.supporter' => $user_id,
									'Supporter.movement_id' => $movement_design_task["MovementDesignTask"]["movement_id"]
								), 
								'limit' => 1
							));
				
				if($supporter)
				{
					return true;
				}
			}
		}	
		
		return false;
    }

    public function beforeFind($queryData) {

        parent::beforeFind($queryData);
        $defaultConditions = array('Contribution.deleted' => 0);
        $queryData['conditions'] = array_merge($queryData['conditions'], $defaultConditions);
        return $queryData;
    }
}
?>