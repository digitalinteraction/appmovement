<?php

App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'Validation', 'Utility');

App::import('Vendor','HashIds');
App::import('Vendor', 'resizer');
App::import('Vendor', 'image_uploader');

class MovementsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler', 'Paginator');
    public $uses = array('Movement', 'MovementUpdate', 'MovementPhoto', 'Supporter', 'AppType', 'User', 'Transaction', 'AppTypeDesignTask', 'MovementDesignTask', 'Invite', 'Notification', 'TypeSuggestion', 'ContributionType', 'DesignTaskOption', 'Contribution', 'DesignTask', 'ShareLink');

    public $paginate = array(
        'limit' => 36,
        'conditions' => array('Movement.flag' => 0), //, 'Movement.phase !=' => -1
        'order' => 'Movement.created desc'
    );

    // Discover
    public function index() {
        $this->set('subpage', 'Discover');
        $this->set('title_for_layout', 'Discover');

        $this->Paginator->settings = $this->paginate;

        if (isset($this->request["url"]["query"])) {

            // Search parameter passed
            $query = $this->request["url"]["query"];

            $movements = $this->Paginator->paginate('Movement', array('OR' => array('Movement.title LIKE' => '%' . $query . '%', 'Movement.tags LIKE' => '%' . $query . '%')));

        } else {

            $query = '';
            
            $movements = $this->Paginator->paginate('Movement');
        
        }

        $this->set('query', $query);

        foreach ($movements as &$movement) {

            $conditions = array(
                'Supporter.supporter' => $this->Auth->user('id'),
                'Supporter.movement_id' => $movement["Movement"]["id"],
                'Supporter.confirmed' => true
            );

            $movement["Movement"]["supported"] = ($this->Supporter->hasAny($conditions)) ? true : false;
        }

        $this->set('movements', $movements);
    }

    // Submit an app type suggestion
    public function suggest_type($user_id = null, $suggestion = null) {

        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $response = array();
        $response['meta']['success'] = false;
        $response['response'] = array();

        // Save suggestion
        $this->TypeSuggestion->create();
        $data = array('user_id' => $user_id, 'suggestion' => $suggestion);
        $this->TypeSuggestion->save($data);

        $response['meta']['success'] = true;

        echo json_encode($response);
    }

    // Update movement phase
    public function updatePhase() {

        $this->autoRender = false;
        $this->layout = 'ajax';

        $movements = $this->Movement->find('all', array(
                                        'conditions' => array(
                                            'Movement.flag' => 0,
                                            'Movement.phase !=' => -1,
                                            'Movement.phase !=' => 2,
                                            'Movement.phase !=' => 3
                                            )
                                    ));

        foreach ($movements as $movement) {

            $this->Movement->read(null, $movement["Movement"]["id"]);

            $phase = 0; 

            // get now            
            $now = new DateTime();
            $now->format('Y-m-d H:i:s');

            // create date time object for now
            $movement_created = new DateTime($movement["Movement"]["created"]);
            // format to mysql datetime string
            // $movement_created = $movement_created->format('Y-m-d H:i:s');


            // create datetime for support phase end
            $movement_support_phase_end = new DateTime($movement["Movement"]["created"]);
            // add support duration in days to the created date
            $movement_support_phase_end = $movement_support_phase_end->add(new DateInterval('P' . $movement["Movement"]["support_duration"] . 'D'));
            // format to mysql datetime string
            // $movement_support_phase_end = $movement_support_phase_end->format('Y-m-d H:i:s');


            // create datetime for design phase end
            $movement_design_phase_end = new DateTime($movement_support_phase_end->format('Y-m-d H:i:s'));
            // add design duration in days to support phase datetime
            $movement_design_phase_end = $movement_design_phase_end->add(new DateInterval('P' . $movement["Movement"]["design_duration"] . 'D'));
            // format to mysql datetime string
            // $movement_design_phase_end = $movement_design_phase_end->format('Y-m-d H:i:s');

            // if now is after the created date
            // && now is after the support phase end date
            // now must be after the support phase
            //
            if(($now >= $movement_created) && ($now > $movement_support_phase_end))
            {

                // check if we have enough supporters for movement
                if ($movement["Movement"]["supporters_count"] >= $movement["Movement"]["target_supporters"])
                {
                    // if now is after support phase end date and before design phase end
                    // movement must be in design phase
                    // else movement must be after design phase
                    if(($now >= $movement_support_phase_end) && ($now <= $movement_design_phase_end))
                    {
                        // movement in design phase
                        $phase = 1;

                        // if the current phase is in the support phase (0)
                        // then notify supporters that the phase has changed
                        if($movement["Movement"]["phase"] == 0)
                        {
                            // create design tasks and associated design task options
                            $this->DesignTask->generate_design_tasks($movement["Movement"]["id"]);
                            
                            $this->Notification->notify_users_of_support_phase_complete($movement["Movement"]["id"], $movement["Supporters"]);
                        }
                    }
                    else
                    {
                        // movement is in build phase
                        $phase = 2;

                        // if the current phase in in design phase (1), phase has changed
                        // notify supporters that phase has changed to build
                        if($movement["Movement"]["phase"] == 1)
                        {   
                            $this->Notification->notify_users_of_design_phase_complete($movement["Movement"]["id"], $movement["Supporters"]);
                        }
                    }
                }
                else
                {
                    // Failed to progress to design phase because not enough supporters
                    $phase = -1;
                    $this->Notification->notify_users_of_movement_failed($movement["Movement"]["id"], $movement["Supporters"]);
                }
            }
            else
            {
                // must still be in support phase
                $phase = 0;
            }

            // print result
            // echo '<p>Phase changed from ' . $movement["Movement"]["phase"] . ' to ' . $phase . ' for movement ' . $movement["Movement"]["title"] . '</p>';
            // echo '<p>Now: ' . $now . ' - Movement created: ' . $movement_created . ' - Support end: ' . $movement_support_phase_end . ' - Design end: ' . $movement_design_phase_end . '</p>';

            // if the phase has changed then update it
            if($movement["Movement"]["phase"] != $phase)
            {
                $this->Movement->set('phase', $phase); // Set the new phase

                unset($this->Movement->validate); // Prevent movement from validating self

                $this->Movement->save();
            }
        }
    }

    // Overview
    public function overview() {
        $this->set('subpage', 'Overview');
        $this->set('title_for_layout', 'Overview');

        $users_movements = $this->Movement->get_users_movements($this->Auth->user('id'));

        $supported_movements = $this->Movement->get_supported_movements($this->Auth->user('id'));

        $movements = array_merge($users_movements, $supported_movements);

        $number_in_support = 0;
        $number_in_design = 0;
        $number_in_launch = 0;

        for ($row = 0; $row < count($movements); $row++) {
            if ($movements[$row]["Movement"]["phase"] == 0) {
                 $number_in_support++;
            }
            if ($movements[$row]["Movement"]["phase"] == 1) {
                 $number_in_design++;
            }
            if ($movements[$row]["Movement"]["phase"] == 2) {
                 $number_in_launch++;
            }
        }

        $this->set(array('movements' => $movements, 'number_in_support' => $number_in_support, 'number_in_design' => $number_in_design, 'number_in_launch' => $number_in_launch));
    }

    // Movement Onboarding
    public function onboarding($id = null) {

        if ($this->request->is('post')) {

            $this->autoRender = false;
            $this->layout = 'ajax';

            $movement_id = $this->request->data["Movement"]["id"];
            $data = $this->request->data["Movement"]["data"];

            $persons = json_decode($data);

            $user = $this->User->findById($this->Auth->user('id'));
            $movement = $this->Movement->findById($movement_id);

            $sender_name = $user["User"]["fullname"];

            $emailSent = 0;
            $emailLimit = 18; // Limit number of emails per request

            if ($this->Auth->login()) {
                $this->set('ref_link', $this->ShareLink->generate_movement_share_link($movement["Movement"]["id"], $this->Auth->user('id'), $this->Session->read('site_user.id'), false));
            }
            else
            {   
                // user not signed in so just share /view/movement_id
                $ref_link = 'view/' . $movement["Movement"]["id"];
            }

            $this->set('ref_link', $ref_link); 

            // Send emails
            foreach ($persons as $person)
            {
                if ($emailSent < $emailLimit) {
                    
                    $Email = new CakeEmail('custom');
                    $Email->to($person->email);
                    $Email->emailFormat('html');
                    $Email->template('invite_user_to_movement')->viewVars( array('target_name' => $person->name, 'sender_name' => $sender_name, 'movement_title' => $movement["Movement"]["title"], 'movement_link' => 'https://app-movement.com/' . $ref_link));
                    $Email->subject(__('%s needs your support', $sender_name));
                    
                    if ($Email->send()) {
                        $emailSent++;
                        $success = 1;
                    } else {
                        $success = 0;
                    }

                    // Add user to invite table
                    $this->Invite->create();
                    $data = array('fullname' => $person->name, 'email' => $person->email, 'invited_by' => $this->Auth->user('id'), 'success' => $success);
                    $this->Invite->save($data);

                }
            }

            $this->Session->setFlash(__('Your friends have been invited'), 'default', array(), 'good');
            return $this->redirect(array('controller' => 'movements', 'action' => 'view', $movement_id));
        
        } else {

            if ((!$id) || (!$this->Movement->hasAny(array( 'Movement.id' => $id )))) {
                // Movement does not exist
                $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
                return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
            }

            $movement = $this->Movement->get_movement($id);

            $this->set('subpage', __('%s | Onboarding', $movement['Movement']['title']));
            $this->set('title_for_layout', __('%s | Onboarding', $movement['Movement']['title']));

            $this->set('movement', $movement);

        }
    }

    // View a movement
    public function view($id = null) {

        if ((!$id) || (!$this->Movement->hasAny(array( 'Movement.id' => $id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($id);

        $this->set('movement', $movement);

        $show_support = $this->Session->read('User.recently_authenticated');

        if ($movement['Movement']['supported']) {
            $show_support = false;
        }

        $this->Session->write('User.recently_authenticated', false);

        $this->set(compact('show_support'));

        $this->set('ref_link', $this->ShareLink->generate_movement_share_link($movement["Movement"]["id"], $this->Auth->user('id'), $this->Session->read('site_user.id'), false));
        
        $this->set('subpage', $movement['Movement']['title']);
        $this->set('title_for_layout', $movement['Movement']['title']);
    }

    // Start a movement
    public function start() {

        $this->set('subpage', __('Start a Movement'));
        $this->set('title_for_layout', __('Start a Movement'));

        // get app types
        $app_types = $this->AppType->find('list');

        $this->set('app_types', $app_types);

        if ($this->request->is('post')) {

            if (!$this->Auth->loggedIn()) {
                $this->Session->setFlash(__('Please login or create an account to start a movement'), 'default', array(), 'bad');
                return $this->redirect(array('controller' => 'movements', 'action' => 'start'));
            }

            // Set current movement to form data
            $this->Movement->set($this->request->data);
            
            // Check the movement validates without saving
            if ($this->Movement->validates()) {

                $this->Movement->set($this->request->data);
                
                // Save the movement to the database
                if ($this->Movement->save($this->request->data)) 
                {
                    // Generate app identifier
                    $movement_id = $this->Movement->getLastInsertId();
                    $this->Movement->id = $movement_id;
                    $this->Movement->saveField('identifier', 'movement_' . $this->Auth->user('id') . '_' . $movement_id);
                    $this->Movement->saveField('user_id', $this->Auth->user('id'));

                    // Save movement photo
                    $this->MovementPhoto->create();
                    $data = array('movement_id' => $movement_id, 'primary' => 1, 'filename' => $this->request->data["Movement"]["photo"]);
                    $this->MovementPhoto->save($data);

                    $movement = $this->Movement->find('first', array('conditions' => array('Movement.id' => $this->Movement->id)));

                    // make creator a confirmed supporter
                    $supporter_code = $this->code(array('id' => $movement_id));
                    
                    $supporter = $this->Supporter->find('first', array(
                                                        'conditions' => array(
                                                            'Supporter.supporter' => $this->Auth->user('id'),
                                                            'movement_id' => $movement_id
                                                        )
                                                    ));

                    if($supporter)
                    {
                        $this->Supporter->read(null, $supporter["Supporter"]["id"]);
                        $this->Supporter->set(array(
                            'confirmed' => 1,
                            'confirmed_on' => date('Y-m-d H:i:s')
                        ));
                        $this->Supporter->save();
                    }

                    // Create a notification for the user who has started a movement
                    // NOTIFICATION TYPE ID might need changing if we update the notifcation types table
                    $this->Notification->create();
                    $this->Notification->set('user_id', $this->Auth->user('id'));
                    $this->Notification->set('movement_id', $this->Movement->id);
                    $this->Notification->set('notification_type_id', 1);
                    $this->Notification->set('data', json_encode(array('supporter_target' => (string) $movement["Movement"]["target_supporters"])));
                    $this->Notification->save();

                    return $this->redirect(array('controller' => 'movements', 'action' => 'onboarding', $this->Movement->id));
                }            
            }
            else
            {
                // Fields fail validation
                $this->Session->setFlash(__('There are some errors with your movement'), 'default', array(), 'bad');
                $errors = $this->Movement->validationErrors;
                $this->set('errors', $errors);
            }
        }
    }

    // Edit a movement
    public function edit($id = null) {

        $this->set('subpage', __('Edit Movement'));
        $this->set('title_for_layout', __('Edit Movement'));

        $app_types = $this->AppType->find('list');
        $this->set('app_types', $app_types);


        if ((!$id) || (!$this->Movement->hasAny(array( 'Movement.id' => $id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($id);

        $this->set('movement', $movement);

        if ((($movement["Movement"]["user_id"] != $this->Auth->user('id')) && ($this->Auth->user('role') != 'admin')) || (($movement["Movement"]["supporters_count"] >= 5) && ($this->Auth->user('role') != 'admin'))) {

            // User does not own movement
            $this->Session->setFlash(__('You do not have permission to do that'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));

        } else {

            if ($this->request->is('post')) {

                // Get movement id
                $this->Movement->id = $movement["Movement"]["id"];

                $this->Movement->set( $this->request->data );

                unset($this->Movement->validate['app_type_id']);
                unset($this->Movement->validate['photo']);
                
                if ($this->Movement->validates()) {

                    $this->Movement->set($this->request->data);
                        
                    if ($this->Movement->save($this->request->data)) 
                    {
                        // Delete old photos
                        $this->MovementPhoto->deleteAll(array('MovementPhoto.movement_id' => $movement["Movement"]["id"]), false);

                        // Save movement photo
                        $this->MovementPhoto->create();
                        $data = array('movement_id' => $movement["Movement"]["id"], 'primary' => 1, 'filename' => $this->request->data["Movement"]["photo"]);
                        $this->MovementPhoto->save($data);

                        $this->Session->setFlash(__('Movement was updated'), 'default', array(), 'good');

                        return $this->redirect(array('controller' => 'movements', 'action' => 'view', $this->Movement->id));
                    }
                }
                else
                {
                    // Fields fail validation
                    $this->Session->setFlash(__('Movement was not updated'), 'default', array(), 'bad');
                    
                    $errors = $this->Movement->validationErrors;
                }

                $movement['Movement']['overview'] = null;
                $response['movement'] = $movement['Movement'];
            }

        }
    }

    // Delete a movement
    public function delete($id = null) {

        if ((!$id) || (!$this->Movement->hasAny(array( 'Movement.id' => $id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($id);

        if ($movement["Movement"]["user_id"] != $this->Auth->user('id')) {
            // User does not own movement
            $this->Session->setFlash(__('You do not have permission to do that'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        } else {
            // Mark movement as deleted
            $this->Movement->id = $id;
            $this->Movement->saveField('deleted', 1);

            $this->Session->setFlash(__('Movement was deleted'), 'default', array(), 'good');
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }
    }

    // Add a movement update
    public function add_movement_update() {

        $this->autoRender = false;
        $this->layout = 'ajax';

        $this->request->data['MovementUpdate']['user_id'] = $this->Auth->user('id');
        
        $response["meta"]["success"] = false;
        $response["response"] = null;

        if($this->Auth->loggedIn())
        {
            $user = $this->Auth->user();
            
            $this->request->data["MovementUpdate"]["text"] = htmlspecialchars($this->request->data["MovementUpdate"]["text"]);
            $this->MovementUpdate->set($this->request->data);

            if($this->MovementUpdate->validates())
            {
                // save the MovementUpdate
                $movement_update = $this->MovementUpdate->save($this->request->data);
                $response["meta"]["success"] = true;
                $movement_update = $this->MovementUpdate->findById($movement_update["MovementUpdate"]["id"]);

                // get element output render (bit of a hack but it works)
                $View = new View();
                $response["response"] = $View->element('Updates/movement_update', array('movement_update' => $movement_update["MovementUpdate"]));

                // send notification to all users
                $movement = $this->Movement->get_movement($movement_update["MovementUpdate"]["movement_id"]);

                // creator_name
                $creator_name = explode(" ", $this->Auth->user('fullname'));     

                $this->Notification->notify_users_of_movement_update(
                                        $movement['Movement']['id'],
                                        $movement['Movement']['title'],
                                        htmlspecialchars($this->request->data["MovementUpdate"]["text"]), 
                                        $creator_name[0],
                                        $movement['User']['photo'],
                                        $movement['Supporters']
                                    );
            }
            else
            {
                // echo 'no validate';
                $response["errors"] = $this->MovementUpdate->validationErrors;
            }            
        }
        else
        {
            $response["errors"]["invalid_auth"] = __("You are required to be logged in for this transaction");
        }

        return json_encode($response);
    }

    // Delete a movement update
    public function delete_movement_update($id = null) {

        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $response["meta"]["success"] = false;
        $response["response"] = null;

        $movement_update = $this->MovementUpdate->findById($id);

        if ($movement_update["MovementUpdate"]["user_id"] != $this->Auth->user('id')) {

            // User does not own update
            $this->Session->setFlash(__('You do not have permission to do that'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        
        } else {
        
            // Mark update as deleted
            $this->MovementUpdate->id = $id;
            $this->MovementUpdate->saveField('deleted', 1);

            $response["meta"]["success"] = true;
        
        }

        return json_encode($response);
    }

    // Get movements owned by and recent
    // PLEASE DO ME PROPERLY
    public function recent() {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $response = array();
        $response['meta']['success'] = false;
        $response['meta']['errors'] = array();
        $response['response'] = array();

        if (!$this->request->is('get'))
        {
            throw new NotFoundException();
        }
        
        $id = null;
        $api_key = null;

        if (array_key_exists('id',$this->params['url']))
        {
            $id = $this->params['url']['id'];
        }


        if (array_key_exists('api_key',$this->params['url']))
        {
            $api_key = $this->params['url']['api_key'];
        }
        
        if ($id)
        {
            if ($api_key)
            {
                $movements = $this->Supporter->find('all', array(
                        'conditions' => array(
                            'Supporter.supporter' => $id,
                            'Supporter.confirmed' => true
                        ),
                        'contain' => array('Movement' => array('MovementPhoto')),
                        'order' => array('Supporter.created DESC'),
                        'limit' => 20,
                        'group' => 'Supporter.movement_id'
                    )
                );

                for ($i = 0; $i < count($movements); $i++)
                {
                    unset($movements[$i]['Supporter']);
                    unset($movements[$i]['Movement']['overview']);

                    $movements[$i]["Movement"]["time"] = ($movements[$i]["Movement"]["design_start"] <= 0) ? 0 : $movements[$i]["Movement"]["design_start"];
                
                    $movements[$i]["Movement"]["photo"] = $movements[$i]["Movement"]["MovementPhoto"][0]["filename"]; // Set photo
                }

                $response['meta']['success'] = true;
                $response['response'] = $movements;
            }
            else
            {
                $response['meta']['errors'][] = __("api_key parameter required");
            }
        }
        else
        {
            // missing id parameter
            $response['meta']['errors'][] = __("Id parameter required");
        }


        // log transaction
        $this->Transaction->create();
        $this->Transaction->set('endpoint', 'recent_get');
        $this->Transaction->set('data', json_encode($this->params->query));
        $this->Transaction->set('success', $response['meta']['success']);
        $this->Transaction->set('user_id', $id);
        $this->Transaction->set('user_ip', $this->request->clientIp());
        $this->Transaction->set('user_agent', $_SERVER['HTTP_USER_AGENT']);
        $this->Transaction->set('api_key', null);
        $this->Transaction->save();

        echo json_encode($response);
    }

    public function code($id = null){
        $this->autoRender = false;
        $this->layout = 'ajax';

        $userId = $this->Auth->user('id');
        $user = $this->User->findById($userId);

        $response = array();
        $response['meta'] = false;
        $response['code'] = null;

        // workaround - calling this function directly passing the $id works, however from ajax (view.js showModal()) could'nt 
        // get the parameter to pass into this function, therefore had to use $this->request. Sucks assballs.
        if(!$id){ $id = $this->request->query('id'); }

        // check they passed in movement id
        if($id)
        {
            $movement = $this->Movement->findById($id);

            if($movement)
            { 
                if($user)
                {   
                    $supportRecord = $this->Supporter->findBySupporterAndMovementId($user['User']['id'], $movement['Movement']['id']);
                        
                    $hasher = new HashIds(Configure::read('urlSalt'), Configure::read('codeHashMinLength'), Configure::read('hashAlphabet'));

                    // if support record exists then return the code to user else generate one
                    if($supportRecord)
                    {
                        $hash = $hasher->encrypt($supportRecord['Supporter']['id']);
                        $response['code'] = $hash;
                        $response['meta'] = true;
                    }
                    else
                    {
                        $this->Supporter->create();
                        $this->Supporter->set('supporter', $user['User']['id']);
                        $this->Supporter->set('movement_id', $movement['Movement']['id']);
                        $this->Supporter->save();
                        // // generate
                        $hash = $hasher->encrypt($this->Supporter->id);
                        $this->Supporter->set('code', $hash);
                        $this->Supporter->save();

                        $response['code'] = $hash;
                        $response['meta'] = true;
                    }
                }
            }  
        }

        return json_encode($response);
    }    

    // Movement launch status
    public function launch($id = null) {

        if ((!$id) || (!$this->Movement->hasAny(array( 'Movement.id' => $id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($id);

        $this->set('movement', $movement);

    }
    
    public function image_uploader() {

        $this->autoRender = false;
        $this->layout = 'ajax';

        App::import('Vendor','image_uploader');
        $image_uploader = new image_uploader;
        echo $image_uploader->initialize();
    }

    public function isAuthorized($user) {

        if (in_array($this->action, array('launch'))) {

            $movement_id = (int) $this->request->params['pass'][0];

            $movement = $this->Movement->findById($movement_id);

            if ($movement["Movement"]["phase"] < 2) {
                $this->Session->setFlash(__('This movement has not yet reached the launch phase'), 'default', array(), 'bad');
                return false;
            }

        }

        if (in_array($this->action, array('edit'))) {

            $movement_id = (int) $this->request->params['pass'][0];

            $movement = $this->Movement->findById($movement_id);

            if ((($movement["Movement"]["user_id"] != $this->Auth->user('id')) && ($this->Auth->user('role') != 'admin')) || (($movement["Movement"]["supporters_count"] >= 5) && ($this->Auth->user('role') != 'admin')))
            {
                $this->Session->setFlash(__('Movements with 5 supporters or more cannot be edited'), 'default', array(), 'bad');
                return false;
            }
        }
        
        return parent::isAuthorized($user);
    }

    public function beforeFilter() {

        parent::beforeFilter();

        $this->Auth->allow(array('start', 'launch'));

    }
}
?>
