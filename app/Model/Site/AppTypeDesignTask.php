<?php
class AppTypeDesignTask extends AppModel {

	public $belongsTo = array(
        'AppType' => array(
            'className' => 'AppType',
            'foreignKey' => 'app_type_id'
        ),
        'DesignTask' => array(
            'className' => 'DesignTask',
            'foreignKey' => 'design_task_id'
        )
    );

    public function get_design_tasks_by_app_type_id($id)
    {
        return $this->find('all', array(
                    'condition' => array(
                        'app_type_id' => $id
                    ),
                    'fields' => array(
                        'DesignTask.id',
                        'DesignTask.name',
                        'DesignTask.description',
                        'DesignTask.contribution_type_id'
                    )
                ));
    }
}
?>