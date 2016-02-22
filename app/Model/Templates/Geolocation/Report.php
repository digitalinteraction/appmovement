<?php
class Report extends AppModel {

    public $specific = true;

    var $belongsTo = array(
    	'Review' => array('counterCache' => true)
    	);

}
?>