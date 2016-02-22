 <?php
class DesignTask extends AppModel {

	public $belongsTo = 'ContributionType';

	public function __construct($id = false, $table = null, $ds = null) {
		App::import('model', 'Movement');
		App::import('model', 'MovementDesignTask');
		App::import('model', 'AppTypeDesignTask');
		App::import('model', 'Contribution');
		App::import('model', 'DesignTaskOption');

	    parent::__construct($id, $table, $ds);
	}

	public function generate_design_tasks($movement_id) {

		$Movement = new Movement();

        $movement = $Movement->find('first', array(
                                    'conditions' => array(
                                        'Movement.id' => $movement_id
                                    )
                                ));

        if(!$movement)
            return false;

        $AppTypeDesignTask = new AppTypeDesignTask();
        $MovementDesignTask = new MovementDesignTask();
        $DesignTaskOption = new DesignTaskOption();
        $Contribution = new Contribution();

        // // get the design tasks for this type of app
        $AppTypeDesignTask->recursive = 2;
        $app_type_design_tasks = $AppTypeDesignTask->find('all', array(
                                                            'conditions' => array(
                                                                'app_type_id' => $movement['Movement']['app_type_id']
                                                            )
                                                        ));


        // fetch all the current design tasks for this movement
        // loop through each design task and see if the current movement has been allocated that task
        foreach($app_type_design_tasks as $app_type_design_task)
        {
              $movement_design_task = $MovementDesignTask->find('first', array(
                                                            'conditions' => array(
                                                                'movement_id' => $movement['Movement']['id'],
                                                                'app_type_design_task_id' => $app_type_design_task['AppTypeDesignTask']['id']
                                                            )
                                                        ));

              $movement_design_task_id;
              // if the movement design task doesn't exist, create it
              if(!$movement_design_task)
              {
                    $MovementDesignTask->create();
                    $MovementDesignTask->set('movement_id', $movement['Movement']['id']);
                    $MovementDesignTask->set('app_type_design_task_id', $app_type_design_task['AppTypeDesignTask']['id']);
                    $MovementDesignTask->save();

                    $movement_design_task_id = $MovementDesignTask->id;
              }
              else
              {
                    $movement_design_task_id = $movement_design_task['MovementDesignTask']['id'];
              }

              // check if the design task has a contribution type that is auto generated (i.e. an option to vote on)
              if($app_type_design_task['DesignTask']['ContributionType']['is_auto_generated'] == 1)
              {

                    // fetch each design task option and see if it exists in the contributions table

                    $design_task_options = $DesignTaskOption->find('all', array(
                                                                    'conditions' => array(
                                                                        'design_task_id' => $app_type_design_task['DesignTask']['id']
                                                                    )
                                                                ));

                    // loop through each design task option and see if we have a contribution already created for the design task
                    foreach($design_task_options as $design_task_option)
                    {
                        // check if the auto generated contribution exists
                        $contribution = $Contribution->find('first', array(
                                                'conditions' => array(
                                                    'movement_design_task_id' => $movement_design_task_id,
                                                    'data' => $design_task_option['DesignTaskOption']['body']
                                                )
                                            ));

                        // check if the contrbution exists
                        if(!$contribution)
                        {
                            // create the design task option aka "contribution"
                            unset($Contribution->validate);
                            $Contribution->create();
                            $Contribution->set('movement_design_task_id', $movement_design_task_id);
                            $Contribution->set('contribution_type_id', $app_type_design_task['DesignTask']['contribution_type_id']);
                            $Contribution->set('data', $design_task_option['DesignTaskOption']['body']);
                            $Contribution->save();
                        }
                    }


              }

        }

    }
}
?>