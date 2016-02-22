<?php
class Like extends AppModel {

    public $specific = true;

    var $belongsTo = array(
    	'User' => array('counterCache' => true),
    	'Venue' => array('counterCache' => true)
    	);

}
?>