<?php
class Supporter extends AppModel {
	
    public $belongsTo = array('Movement');

    public $actsAs = array('Containable');

    public function get_supporter_by_supporter_id_and_movement_id($user_id, $movement_id)
    {
    	return $this->findBySupporterAndMovementId($user_id, $movement_id);
    }
}
?>