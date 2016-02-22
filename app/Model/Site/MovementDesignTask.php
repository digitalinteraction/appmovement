<?php
class MovementDesignTask extends AppModel {
    
    public $actsAs = array('Containable');

    public $hasMany = array('Contributions');

    public $belongsTo = array(
        'AppTypeDesignTask' => array(
            'className' => 'AppTypeDesignTask',
            'foreignKey' => 'app_type_design_task_id'
        )
    );

    public function get_design_task_by_id($id)
    {
        $contain = array(
            'AppTypeDesignTask' => array(
                'DesignTask'
            )
        );

        return $this->find('first', array(
            'conditions' => array(
                'MovementDesignTask.id' => $id
            ),
            'contain' => $contain
        ));
    }

    public function get_design_tasks_by_movement_id($id)
    {
        $contain = array(
            'AppTypeDesignTask' => array(
                'DesignTask'
            )
        );

        $design_tasks = $this->find('all', array(
            'conditions' => array(
                'movement_id' => $id
            ),
            'contain' => $contain
        ));

        return $design_tasks;
    }
}
?>