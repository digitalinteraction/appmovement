<?php

App::import('Vendor', 'resizer');
App::uses('L10n', 'I18n');

class NotificationsController extends AppController {    
    public $uses = array('Notification', 'Movement', 'Supporter', 'Promoter' ,'NotificationType', 'Comment', 'CommentType', 'User');

    public function send() 
    {
        $this->autoRender = false;
        $this->layout = false;
        $this->render(false);

        // Send main notifications
        $this->send_main_notifications();

        // Send grouped notifications
        $this->send_grouped_notifications();

    }
    
    public function attempted_delivery($notification_id, $delivery_status)
    {
        $this->Notification->id = $notification_id;
        $this->Notification->saveField('delivered', $delivery_status);
        $this->Notification->saveField('attempted_delivery_on', date("Y-m-d H:i:s"));
    }

    public function send_main_notifications()
    {
        // Get notifications that will be sent as it's own email (none repeating)
        // e.g. Notifications about build phase complete sent to 100 people
        // all the same but a user's name might change
        $notifications = $this->Notification->find('all', array(
                            'conditions' => array(
                                'delivered' => 0,
                                'NotificationType.repeating' => '0',
                                'User.receives_email_updates' => '1'
                            ),
                            'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                            'order' => array('NotificationType.priority' => 'desc')
                        ));

        $x_smtp_api_headers = array();

        // // loop through notifications and email people
        foreach($notifications as $notification) {

            // email people!
            $Email = new CakeEmail('custom');
            $Email->to($notification["User"]["email"]);
            $Email->emailFormat('html');

            $x_smtpapi_headers = array('X-SMTPAPI' => json_encode(array(
                                        'category' => $notification["NotificationType"]["name"],
                                        'filters' => array(
                                            'clicktrack' => array(
                                                'settings' => array(
                                                    'enable' => array('1')
                                                )
                                            ),
                                            'opentrack' => array(
                                                'settings' => array(
                                                    'enable' => array('1')
                                                )
                                            )
                                        )
                                    )
            ));

            // Default to english
            $this->Session->write('Config.language', 'en');

            // Update the locale of the email message if user has locale
            if($notification["User"]["locale"])
            {
                $this->Session->write('Config.language', $notification["User"]["locale"]);

                // Check for RTL language
                $l10n = new L10n();
                $l10n_language = $l10n->catalog($this->Session->read('Config.language'));

                $this->Session->write('Config.text_direction', strtoupper($l10n_language['direction']));
            }

            // check if the notification subject has parameters {parameter} in the data of the notifcation
            $subject = __($notification["NotificationType"]["subject"]);
            $data = json_decode($notification["Notification"]["data"]);

            // see if the subject contains parameters surrounded by {} curly braces
            preg_match_all('/{(.*?)}/', $subject, $results);

            // if there are encoded vars then modify the subject
            if($results)
            {
                // loop through the inner results (e.g. creator_name rather than {creator_name})
                foreach($results[1] as $result)
                {
                    // if the parameter in the subject is contained within the data part of the notification
                    // then add it to the subject
                    if(array_key_exists($result, $data))
                    {
                        $subject = str_replace('{' . $result . '}', $data->$result, $subject);    
                    }
                }
            }

            $Email->addHeaders($x_smtpapi_headers);
            $Email->template($notification["NotificationType"]["view_name"], 'main_notification')->viewVars(array('notification' => $notification));
            $Email->subject($subject);

            if($Email->send())
            {
                // update delivered to true
                $delivery_status = 1;
            }   
            else
            {
                // update delivered to false
                $delivery_status = -1;
            }

            // // $delivery_status = 0; // Comment for production
            $this->attempted_delivery($notification["Notification"]["id"], $delivery_status);

        }
        
    }

    public function send_grouped_notifications()
    {
        // Send out grouped notifications to users
        $users = $this->Notification->find('all', array(
                    'conditions' => array(
                        'User.receives_email_updates' => 1
                    ),
                    'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                    'group' => array('Notification.user_id')
                ));

        // loop through users and find notifications
        foreach($users as $user)
        {

            // logic to send emails every X number of days

            // select records within the last 3 days - if records exist then do not notify user with new updates
            // select notifications from notifications 
            // where priority is not 3 (i.e. stream updates only)
            // created > X hours ago
            // attempted_delivery_on is not 0 (i.e. has been attempted to be delivered)
            // order by created limit 1

            // check what phase we are currently in
            $notification_timeout_duration_in_hours = 168;

            switch($user["Movement"]["phase"])
            {
                // support phase
                case 0:
                    $notification_timeout_duration_in_hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_support_phase');
                    break;
                // design phase
                case 1:
                    $notification_timeout_duration_in_hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_design_phase');
                    break;
                // build phase
                case 2:
                    $notification_timeout_duration_in_hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_build_phase');
                    break;

            }

            $notifications_in_past_x_hours = $this->Notification->find('all', array(
                                                'conditions' => array(
                                                    'Notification.user_id' => $user["Notification"]["user_id"],
                                                    'Notification.delivered' => 1,
                                                    'NotificationType.repeating' => '1',
                                                    'NotificationType.movement_phase' => $user["Movement"]["phase"],
                                                    // 'Notification.attempted_delivery_on >= DATE_ADD(CURDATE(), INTERVAL -' . $notification_timeout_duration_in_hours . ' HOUR)'
                                                    'Notification.attempted_delivery_on >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL -' . $notification_timeout_duration_in_hours . ' HOUR)'
                                                ),
                                                'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                                                'order' => array('Notification.attempted_delivery_on' => 'desc'),
                                                'limit' => 1
                                            ));


            // if notifications_in_past_x_days > 0, do not send any more updates!
            if(count($notifications_in_past_x_hours) == 0)
            // if(true)
            {
                $user_notifications = $this->Notification->find('all', array(
                        'conditions' => array(
                            'delivered' => 0,
                            'NotificationType.repeating' => '1',
                            'Notification.user_id' => $user["Notification"]["user_id"],
                        ),
                        'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                        'order' => array('NotificationType.priority' => 'desc')
                    ));
            
                // check we have some notifications to send them
                if($user_notifications)
                {

                    // Default to english
                    $this->Session->write('Config.language', 'en');

                    // Update the locale of the email message if user has locale
                    if($user_notifications[0]["User"]["locale"])
                    {
                        $this->Session->write('Config.language', $user_notifications[0]["User"]["locale"]);

                        // Check for RTL language
                        $l10n = new L10n();
                        $l10n_language = $l10n->catalog($this->Session->read('Config.language'));

                        $this->Session->write('Config.text_direction', strtoupper($l10n_language['direction']));
                    }

                    $email_notification_stream = "";
                    
                    // loop through and render the views
                    foreach($user_notifications as $user_notification)
                    {
                        $view = new View();
                        $view->layout = false;
                        // set json variables to element
                        $view->set('notification', $user_notification);
                        $view->viewPath = 'Elements/Stream';
                        $email_notification_stream .= $view->render($user_notification["NotificationType"]["view_name"]);
                    }

                    // generate email and inject notification stream into body
                    $Email = new CakeEmail('custom');
                    $Email->to($user_notifications[0]["User"]["email"]);
                    $Email->emailFormat('html');

                    $x_smtpapi_headers = array('X-SMTPAPI' => json_encode(array(
                            'category' => 'grouped_movement_notifications',
                            'filters' => array(
                                'clicktrack' => array(
                                    'settings' => array(
                                        'enable' => array('1')
                                    )
                                ),
                                'opentrack' => array(
                                    'settings' => array(
                                        'enable' => array('1')
                                    )
                                )
                            )
                        )
                    ));

                    $Email->addHeaders($x_smtpapi_headers);

                    $Email->template('notification_stream', 'grouped_notification')->viewVars(array('stream' => $email_notification_stream, 'username' => $user_notifications[0]["User"]["username"]));
                    $Email->subject(__("We have new updates for you!"));
                    
                    // send email
                    // update notification as sent
                    $delivery_status = 0;

                    if($Email->send())
                    {   
                        // successfully delivered
                        $delivery_status = 1;
                    }   
                    else
                    {   
                        // not successfully delivered
                        $delivery_status = -1;
                    }


                    foreach($user_notifications as $user_notification)
                    {
                        // $delivery_status = 0; // Comment for production
                        $this->attempted_delivery($user_notification["Notification"]["id"], $delivery_status);
                    }

                }
            }

        }
    }

    // generate daily updates
    public function generate()
    {
        $this->autoRender = false;
        // generate a daily set of notifications for each person in a movement
        // get all movements with notification enabled
        $this->Movement->recursive = -1;
        $movements = $this->Movement->find('all', array(
                            'conditions' => array(
                                'Movement.notifications_enabled' => 1
                            ),
                            'fields' => array('id', 'title', 'phase')
                        ));
        
        // $comment_types = $this->CommentType->find('all'); 

        // loop through each movement and see if there are any notifications 
        // that have been sent in the last 24hrs
        foreach($movements as $movement)
        {
            // depending on the phase send emails at different durations
            // support phase - every 3 days
            // design phase - every 1 days
            // build phase - do not generate any, other than when we release the app

            // fetch any notifications that have been delivered
            // in the last 24hrs that are stream messages only (i.e. new supporters, promoters and NOT phase changes)

            $notification_timeout_duration_in_hours = 24;

            switch($movement["Movement"]["phase"])
            {
                // support phase
                case 0:
                    $notification_timeout_duration_in_hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_support_phase');
                    break;
                // design phase
                case 1:
                    $notification_timeout_duration_in_hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_design_phase');
                    break;
                // build phase
                case 2:
                    $notification_timeout_duration_in_hours = Configure::read('notification_timeout_duration_in_hours_for_movements_in_build_phase');
                    break;

            }

            // get different notification types for current movement's phase
            // only fetch the notification types that are repeating - i.e. only included in the daily/tri-daily updates
            $notification_types = $this->NotificationType->find('all', array(
                                                            'conditions' => array(
                                                                'repeating' => '1',
                                                                'NotificationType.movement_phase' => $movement["Movement"]["phase"]
                                                            )
                                                        ));

            // get notifications for the current phase that the movement is in, 
            // checking that for this current phase, have we contacted them within the time period allotted for a phase
            // only check notifications that are repeating notifications! 
            $notifications_in_past_x_hours = $this->Notification->find('all', array(
                                                'conditions' => array(
                                                    'Notification.movement_id' => $movement["Movement"]["id"],
                                                    'NotificationType.repeating' => '1',
                                                    // 'Notification.created >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL -' . $notification_timeout_duration_in_hours . ' HOUR)',
                                                    'Notification.created >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL -' . $notification_timeout_duration_in_hours . ' HOUR)',
                                                    'NotificationType.movement_phase' => $movement["Movement"]["phase"]
                                                ),
                                                'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                                                'order' => array('Notification.created' => 'desc'),
                                                'limit' => 1
                                            ));


            // if no user has been notified about new updates
            // generate some to put on the notification stack
            if(count($notifications_in_past_x_hours) == 0)
            // if(true)
            {
                // we need to send off some notifications
                // get all supporters for this movement
                $supporters = $this->Supporter->find('all', array(
                                                    'conditions' => array(
                                                        'movement_id' => $movement["Movement"]["id"],
                                                        'confirmed' => 1
                                                    ),
                                                    'fields' => array('id', 'supporter')
                                                ));

                // generate some different stats to populate our notifications
                // get supporter count over the last 24 hours
                // NEEDS 24 logic
                $supporter_count = $this->Supporter->find('count', array(
                                                        'conditions' => array(
                                                            'movement_id' => $movement["Movement"]["id"],
                                                            'confirmed' => 1,
                                                            'Supporter.created >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL -' . $notification_timeout_duration_in_hours . ' HOUR)'
                                                        )
                                                    ));

                // get promoter count over last 24 hours
                // needs 24 hour logic
                $promoter_count = $this->Promoter->find('count', array(
                                                        'conditions' => array(
                                                            'movement_id' => $movement["Movement"]["id"],
                                                            'Promoter.created >= DATE_ADD(DATE_ADD(CURDATE(), INTERVAL 1 DAY), INTERVAL -' . $notification_timeout_duration_in_hours . ' HOUR)'
                                                        )
                                                    ));

                // $movement_view_comments_count = 0;
                // $movement_design_landing_comments_count = 0;
                // $movement_design_task_comments_count = 0;

                // // get movement comment count
                // $movement_comments_count = $this->Comment->find('count', array(
                //                                 'conditions' => array(
                //                                     'comment_type_id' => 2
                //                                 )
                //                             ));

                // for each notification_type generate some notifications
                foreach($notification_types as $notification_type)
                {   
                    // check if we have any data for this notification to display
                    // if we are not accessing counts (i.e. support count or promoter count)
                    // then set flag to true, we need to send this off
                    $generate_notification_flag = true;

                    // create our json_data element based on the type
                    $notification_structure = json_decode($notification_type["NotificationType"]["structure"], true);
                    $notification_data = array();

                    // see if the notification structure requires the support or promoter count
                    // if it does, then add it to our data
                    if($notification_structure)
                    {
                        if(array_key_exists("supporter_count", $notification_structure))
                        {
                            // if we don't have any new supporters don't generate a notification about this
                            // perhaps we should generate a different one to make people aware that they need more supporters
                            // if($supporter_count == 0)
                            // {
                            //     $generate_notification_flag = false;
                            // }


                            $notification_data["supporter_count"] = (string) $supporter_count;
                        }

                        if(array_key_exists("promoter_count", $notification_structure))
                        {
                            // if we don't have any new supporters don't generate a notification about this
                            // perhaps we should generate a different one to make people aware that they need more supporters
                            // if($promoter_count == 0)
                            // {
                            //     $generate_notification_flag = false;
                            // }

                            $notification_data["promoter_count"] = (string) $promoter_count;
                        }
                    }
                    else
                    {
                        $notification_structure = null;
                    }

                    // check that if this form of notification requires counts (i.e. new supporter_count)
                    // then send off a notification about this, otherwise do not if supporter/promoter count = 0
                    // if($generate_notification_flag == true)
                    // {
                        // generate a new notification for all users, for this type of notification 
                        // loop through each supporter and create a notification for them
                        // for this notification type

                        foreach($supporters as $supporter)
                        {
                            // generate a notification for this type
                            $this->Notification->create();
                            $this->Notification->set('user_id', $supporter["Supporter"]["supporter"]);
                            $this->Notification->set('notification_type_id', $notification_type["NotificationType"]["id"]);
                            $this->Notification->set('data', json_encode($notification_data));
                            $this->Notification->set('movement_id', $movement["Movement"]["id"]);
                            $this->Notification->set('delivered', 0);
                            
                            if($this->Notification->validates())
                            {
                                $this->Notification->save();
                            }
                            else
                            {
                                echo '<pre>';
                                print_r($this->Notification->data);
                                echo '</pre>';
                                echo '<pre>';
                                print_r($this->Notification->validationErrors);
                                echo '</pre>';
                            }

                        }
                    // }

                }
            }
        }


    }

    // Remove the notifications for deleted movements
    public function clear() {

        $this->autoRender = false;
        // $this->layout = 'ajax';

        $notifications = $this->Notification->find('all', array(
            'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
            ));

        foreach ($notifications as &$notification) {
            
            $conditions = array('Movement.id' => $notification['Notification']['movement_id']);

            if ($this->Movement->hasAny($conditions)) {
                // echo json_encode($notification);
                // echo '<p>No Movement</p>';
                echo '<p>Movement</p>';
            } else {
                // No related movement
                echo '<p>No Movement</p>';
                $this->Notification->delete($notification['Notification']['id']);
            }

        }
    }

    public function built() {

        $response = new stdClass();
        $response->meta = new stdClass();
        $response->meta->success = false;

        $movement_id = $_POST['movement_id'];

        $ios_download_link = $_POST['ios_download_link'];
        $android_download_link = $_POST['android_download_link'];

        $movement = $this->Movement->find('first', array(
                                        'conditions' => array(
                                            'Movement.id' => $movement_id
                                        )
                                    ));


        $user = $this->Auth->user();

        if($user && $movement_id && $ios_download_link && $android_download_link)
        {
            if($user['role'] == 'admin')
            {
                $this->Notification->notify_users_of_build_phase_complete($movement, $movement["Supporters"]);

                $this->Movement->id = $movement_id;
                $this->Movement->saveField('phase', 3);
                $this->Movement->saveField('launch_status', 6);
                $this->Movement->saveField('ios_download_link', $ios_download_link);
                $this->Movement->saveField('android_download_link', $android_download_link);

                $response->meta->success = true;
            }   
        }

        echo json_encode($response);
    }

    public function new_supporter() {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $response = new stdClass();
        $response->meta = new stdClass();
        $response->meta->success = true;

        echo json_encode($response);
                
    }

    public function view($notification_id) {

        // $this->autoRender = false;
        $this->layout = 'Emails/html/view_main_notification';

        $notification = $this->Notification->find('first', array(
                            'conditions' => array(
                                'Notification.id' => $notification_id,
                                'NotificationType.repeating' => '0'
                            ),
                            'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                            'order' => array('NotificationType.priority' => 'desc')
                        ));

        if (!$notification)
        {
            return $this->redirect(array('controller' => 'pages', 'action' => 'display', 'home'));
        }

        $this->set(compact('notification'));

        return $this->render('/Emails/html/' . $notification["NotificationType"]["view_name"]);
    }

    public function isAuthorized($user) {
        $this->autoRender = false;
        $this->layout = false;
        $this->render(false);

        // Restricted to user
        if (in_array($this->action, array('generate_built_notification'))) {
            if (!isset($user['role']) || ($user['role'] != 'admin')) {
                return false;
            }
        }

        return parent::isAuthorized($user);
    }


    public function beforeFilter() {

        parent::beforeFilter();
    
        $this->Auth->allow(array('send', 'generate', 'view'));
    }
}
?>
