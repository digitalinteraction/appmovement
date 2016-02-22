<?php

class Notification extends AppModel {
	
    public $name = 'Notification';

    public $actsAs = array('Containable');
    
	public $belongsTo = array(
		'NotificationType' => array(
			'className' => 'NotificationType',
			'foreignKey' => 'notification_type_id'
		),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        ),
        'Movement' => array(
            'className' => 'Movement',
            'foreignKey' => 'movement_id'
        )
	);

	public $validate = array(
        'user_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please include user_id'
            ),
            'isNumeric' => array(
            	'rule'    => 'numeric'
            )
        ),
        'data' => array(
            'isValidFormat' => array(
                'rule' => array('isValidFormat', 'data'),
                'required' => true,
                'allowEmpty' => false,
                'on'         => 'create',
                'message' => 'data is incorrect format for this notification type, or you have not specified a notification type'
            )
        ),
        'notification_type_id' => array(
            'isEmpty' => array(
                'rule' => 'notEmpty',
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Please enter a notification_type_id'
            ),
            'isNumeric' => array(
            	'rule'    => 'numeric',
            	'message' => 'Please only enter notification_type_id as numeric value'
            ),
            'isValidNotificationTypeId' => array(
                'rule' => array('isValidNotificationTypeId', 'notification_type_id'),
                'required' => true,
                'on'         => 'create',
                'message' => 'Notification_type_id incorrect'
            ),
        )
    );

	public function __construct($id = false, $table = null, $ds = null) {
		App::import('model', 'NotificationType');
	    parent::__construct($id, $table, $ds);
	}

	public function isValidFormat($array, $field) {

		// get notification structure from notification_types table
		if(!array_key_exists("notification_type_id", $this->data[$this->alias]))
		{	
			return false;
		}

    	$NotificationType = new NotificationType();
    	$notification_type = $NotificationType->get_notification_type_by_id($this->data[$this->alias]["notification_type_id"]);

    	if(!$notification_type)
    	{
    		return false;
    	}

    	if(!array_key_exists("data", $this->data[$this->alias]))
    	{
    		return false;
    	}

    	if(!is_array(json_decode($this->data[$this->alias]["data"], true)))
    	{
    		return false;
    	}

        
        if($notification_type["NotificationType"]["structure"] == null)
        {
            return true;
        }

  //   	// take the notification_type structure array and compare it with the notification.data json array 
  //   	// being submitted. The count will be 0 if there are no differences in keys
    	if(count(array_diff_key(json_decode($this->data[$this->alias]["data"], true), json_decode($notification_type["NotificationType"]["structure"], true)) == 0))
    	{

  //   		// keys match
    		foreach(json_decode($this->data[$this->alias]["data"], true) as $data_element)
    		{
    			// if any of data is empty then fail
    			if(empty($data_element))
    			{
    				return false;
    			}
    		}

    		return true;
    	}
    	else
    	{
    		return false;
    	}
	}

	public function isValidNotificationTypeId($array, $field) {
		$NotificationType = new NotificationType();
    	$notification_type = $NotificationType->get_notification_type_by_id($this->data[$this->alias]["notification_type_id"]);

    	if(!$notification_type)
    	{
    		return false;
    	}
    	else
    	{
    		return true;
    	}
	}
	
	public function get_notifications($user_id)
	{
		return $this->find('all', array(
				'conditions' => array('user_id' => $user_id)
		));
	}

    public function notify_new_supporter($movement_id, $supporter_id)
    {
        $previous_notifications = $this->find('count', array(
                                            'conditions' => array(
                                                'Notification.movement_id' => $movement_id,
                                                'Notification.user_id' => $supporter_id,
                                                'Notification.notification_type_id' => 9
                                            )
                                        ));

        if($previous_notifications == 0)
        {
            $this->save(array(
            'user_id' => $supporter_id,
            'movement_id' => $movement_id,
            'notification_type_id' => 9,
            'data' => '{}'
            ));
        }
    }

    public function notify_users_of_support_phase_complete($movement_id, $supporters)
    {
        $notifications = array();
        $notification_data = json_encode(array('supporter_count' => (string) count($supporters)));

        foreach($supporters as $supporter)
        {
            $row = array(
                    'user_id' => $supporter["supporter"],
                    'movement_id' => $movement_id,
                    'notification_type_id' => 4,
                    'data' => $notification_data
                );

            $notifications[] = $row;
        }

        $this->saveMany($notifications);
    }

    public function notify_users_of_design_phase_complete($movement_id, $supporters)
    {
        $notifications = array();

        $notifications_sent_previously = $this->find('count', array(
                                                'conditions' => array(
                                                    'movement_id' => $movement_id,
                                                    'notification_type_id' => 5
                                                )
                                            ));

        if($notifications_sent_previously == 0)
        {
            foreach($supporters as $supporter)
            {
                $row = array(
                        'user_id' => $supporter["supporter"],
                        'movement_id' => $movement_id,
                        'notification_type_id' => 5,
                        'data' => "{}"
                    );

                $notifications[] = $row;
            }

            $this->saveMany($notifications);
        }  
    }

    public function notify_users_of_build_phase_complete($movement, $supporters)
    {
        $notifications = array();

        $notifications_sent_previously = $this->find('count', array(
                                                'conditions' => array(
                                                    'movement_id' => $movement['Movement']['id'],
                                                    'notification_type_id' => 6
                                                )
                                            ));

        $ios_download_url = $movement['Movement']['ios_download_link'];
        $android_download_url = $movement['Movement']['android_download_link'];
        $movement_title = $movement['Movement']['title'];
        $creator_name = $movement['User']['fullname'];
        $creator_photo = $movement['User']['photo'];
        $supporter_count = count($supporters);

        $data = array(
                    'ios_download_url' => $ios_download_url,
                    'android_download_url' => $android_download_url,
                    'movement_title' => $movement_title,
                    'creator_name' => $creator_name,
                    'creator_photo' => $creator_photo,
                    'supporter_count' => $supporter_count
                );

        if($notifications_sent_previously == 0)
        {
            foreach($supporters as $supporter)
            {
                $row = array(
                        'user_id' => $supporter["supporter"],
                        'movement_id' => $movement['Movement']['id'],
                        'notification_type_id' => 6,
                        'data' => json_encode($data)
                    );

                $notifications[] = $row;
            }

            if (!$this->saveMany($notifications)) {
                echo '<pre>';
                print($this->validationErrors);   
                echo '</pre>';
            }
        }
    }

    public function notify_users_of_movement_failed($movement_id, $supporters)
    {
        $notifications = array();

        $data = array('supporter_count' => (string) count($supporters));

        $notifications_sent_previously = $this->find('count', array(
                                                'conditions' => array(
                                                    'movement_id' => $movement_id,
                                                    'notification_type_id' => 7
                                                )
                                            ));

        // check if we have sent this already
        if($notifications_sent_previously == 0)
        {
            foreach($supporters as $supporter)
            {
                $row = array(
                        'user_id' => $supporter["supporter"],
                        'movement_id' => $movement_id,
                        'notification_type_id' => 7,
                        'data' => json_encode($data)
                    );

                $notifications[] = $row;
            }

            $this->saveMany($notifications);
        }
    }

    public function notify_users_of_movement_update($movement_id, $movement_title, $msg, $creator_name, $creator_photo, $supporters)
    {
        $notifications = array();

        $data = array('msg' => $msg, 'creator_name' => $creator_name, 'movement_title' => $movement_title, 'creator_photo' => $creator_photo);

        // check here if the user has posted an update recently
        foreach($supporters as $supporter)
        {
            $row = array(
                    'user_id' => $supporter["supporter"],
                    'movement_id' => $movement_id,
                    'notification_type_id' => 10,
                    'data' => json_encode($data)
                );

            $notifications[] = $row;
        }

        $this->saveMany($notifications);
    }
}

?>