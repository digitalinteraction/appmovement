<?php

class NotificationType extends AppModel {
	public $name = 'NotificationType';

	public function get_notification_type_by_id($value){
		
		$notification_type = $this->find('first', array(
				'conditions' => array(
					'id' => $value
				),
				'limit' => 1
			));

		if(count($notification_type))
		{
			return $notification_type;
		}
		else
		{
			return false;
		}
	}
}

?>