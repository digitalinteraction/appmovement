<?php
class AppType extends AppModel {

	public $hasMany = array(
		'AppTypeDesignTasks' => array(
            'className' => 'AppTypeDesignTasks'
        )
	);
}
?>