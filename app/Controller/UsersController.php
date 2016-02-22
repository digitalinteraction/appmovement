<?php
App::import('Vendor', 'resizer');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'Validation', 'Utility');

class UsersController extends AppController {
    public $helpers = array('Html', 'Form', 'Session', 'Resize');
    public $components = array('Session', 'RequestHandler', 'Paginator', 'Email');
    public $uses = array('User', 'Supporter', 'Movement', 'Transaction', 'HandsetInformation', 'Notification', 'ShareLink', 'SiteUsageLog', 'ShareLinkClick');

    public $paginate = array(
        'limit' => 4
    );

    public function register() {
        $this->set('subpage', 'Register');
        $this->set('title_for_layout', 'Register');

        if ($this->Auth->loggedIn()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }
        
        if ($this->request->is('get')) {
            if (($this->referer() != '/login') && ($this->referer() != '/register') && ($this->referer() != '/reset')) {
                $this->Session->write('pre_register_route', $this->referer());
            }
        }

        if ($this->request->is('post')) {

            if (isset($this->request->data['User']['quick'])) {
                if ($this->request->data['User']['quick']) {
                    if (($this->referer() != '/login') && ($this->referer() != '/register') && ($this->referer() != '/reset')) {
                        $this->Session->write('pre_register_route', $this->referer());
                    }
                    // Quick registration so set some parameters here
                    $fullname_array = explode(' ',trim($this->request->data['User']['fullname']));
                    $username = '';
                    foreach ($fullname_array as $index => $word) {
                        if ($index == 0) {
                            $username = $word;    
                        } else {
                            $username .= '_' . $word;
                        }
                    }
                    $this->request->data['User']['username'] = strtolower(trim($username));

                    $this->request->data['User']['password_confirm'] = $this->request->data['User']['password'];
                }
            }

            $password = $this->request->data['User']['password'];
            $password_confirm = $this->request->data['User']['password_confirm'];

            $this->request->data['User']['password'] = Security::hash($password . Configure::read('Security.salt'), 'sha1', false);
            $this->request->data['User']['password_confirm'] = Security::hash($password_confirm . Configure::read('Security.salt'), 'sha1', false);
            $this->request->data['User']['locale'] = $this->Session->read('Config.language');

            if ($this->User->save($this->request->data)) {

                // Let's grab their Gravatar image
                $this->set_gravatar($this->User->getLastInsertId());

                // The user has been saved
                $this->Auth->login();

                $this->Session->setFlash(__('You have created an account'), 'default', array(), 'good');

                $this->Session->write('User.recently_authenticated', true);

                if (($this->Session->read('pre_register_route') == '/register') || ($this->Session->read('pre_register_route') == '/login')) {
                    return $this->redirect($this->Auth->redirect());
                } else {
                    return $this->redirect($this->Session->read('pre_register_route'));
                }

            } else {

                // Fields fail validation
                $errors = $this->User->validationErrors;

                $this->request->data['User']['password'] = $password;
                $this->request->data['User']['password_confirm'] = $password_confirm;

            }
        }
    }

    public function login() {
        $this->set('subpage', 'Login');
        $this->set('title_for_layout', 'Login');

        if ($this->Auth->loggedIn()) {
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }            

        if ($this->request->is('post')) {

            if (isset($this->request->data['User']['quick'])) {
                if ($this->request->data['User']['quick']) {
                    if (($this->referer() != '/login') && ($this->referer() != '/register') && ($this->referer() != '/reset')) {
                        $this->Session->write('pre_login_route', $this->referer());
                    }
                }
            }
        
            $password = $this->request->data['User']['password'];

            $this->request->data['User']['password'] = Security::hash($password . Configure::read('Security.salt'), 'sha1', false);

            $success = false;

            // Attempt to login using standard AM authentication
            if ($this->Auth->login()) {

                $success = true;

            } else {

                // If that fails, do it the FF way and update it to AM style
                // Legacy request for supporting a previous application
                $this->request->data['User']['password'] = Security::hash($password . Configure::read('feedfinder_salt'), 'sha1', false);

                if ($this->Auth->login()) {

                    $success = true;

                    // Update password to AM style
                    $updated_password = Security::hash($password . Configure::read('Security.salt'), 'sha1', false);

                    $this->User->read(null, $this->Auth->user('id'));
                    $this->User->set('password', $updated_password);
                    $this->User->set('feedfinder_password_updated', true);
                    $this->User->validate = array();
                    $this->User->save();
                }   
            }

            if ($success) {
                
                // associate all instances of anon site_user_id with this user_id
                // share_links
                if($this->Session->read('site_user.id'))
                {   
                    // update share links
                    $share_links = $this->ShareLink->find('all', array(
                                    'conditions' => array(
                                        'user_id' => NULL,
                                        'site_user_id' => $this->Session->read('site_user.id')
                                    )
                                ));

                    foreach($share_links as $share_link)
                    {
                        $this->ShareLink->id = $share_link['ShareLink']['id'];
                        $this->ShareLink->set('user_id', $this->Auth->user('id'));
                        $this->ShareLink->save();
                    }

                    // update share link clicks
                    $share_link_clicks = $this->ShareLinkClick->find('all', array(
                                    'conditions' => array(
                                        'user_id' => NULL,
                                        'site_user_id' => $this->Session->read('site_user.id')
                                    )
                                ));

                    foreach($share_link_clicks as $share_link_click)
                    {
                        $this->ShareLinkClick->id = $share_link_click['ShareLinkClick']['id'];
                        $this->ShareLinkClick->set('user_id', $this->Auth->user('id'));
                        $this->ShareLinkClick->save();
                    }

                    // update site_usage_logs
                    $site_usage_logs = $this->SiteUsageLog->find('all', array(
                                                            'conditions' => array(
                                                                'user_id' => NULL,
                                                                'site_user_id' => $this->Session->read('site_user.id')
                                                            )
                                                        ));

                    foreach($site_usage_logs as $site_usage_log)
                    {
                        $this->SiteUsageLog->id = $site_usage_log['SiteUsageLog']['id'];
                        $this->SiteUsageLog->set('user_id', $this->Auth->user('id'));
                        $this->SiteUsageLog->save();
                    }
                }

                $this->Session->setFlash(__('You have logged in'), 'default', array(), 'good');

                $this->Session->write('User.recently_authenticated', true);
                
                return $this->redirect($this->Session->read('pre_login_route'));

            } else {

                // The user was not logged in
                $this->Session->setFlash(__('Your username or password was incorrect.'), 'default', array(), 'bad');
            }

            // The user was not logged in
            $this->Session->setFlash(__('Your username or password was incorrect.'), 'default', array(), 'bad');
        }
    }

    public function logout() {
        return $this->redirect($this->Auth->logout());
    }

    public function forgot() {
        $this->set('subpage', 'Forgot');
        $this->set('title_for_layout', 'Forgot');
        
        if ($this->request->is('post')) {

            // User is attempting to send a code

            $app_name = $this->data['User']['app_name'];
            $email = $this->data['User']['email'];

            $random_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

            $reset_link = 'https://app-movement.com/reset?email=' . $email . '&code=' . $random_code;

            $user = $this->User->findByEmail($email);

            if ($user) {
                $this->User->id = $user['User']['id'];
                $this->User->set('code', $random_code);
                $this->User->save($this->request->data, false);    

                $Email = new CakeEmail('custom');
                $Email->to($this->data['User']['email']);
                $Email->emailFormat('html');
                $Email->template('reset', 'main_notification');
                $Email->viewVars( array('reset_link' => $reset_link, 'app_name' => 'App Movement'));
                $Email->subject(__('Reset Your App Movement Password'));

                if ($Email->send()) {
                    // The email was sent
                    $this->Session->setFlash(__('A reset link will be emailed to you.'), 'default', array(), 'good');
                    return $this->redirect('/login');

                } else {
                    // The email failed to send
                    $this->Session->setFlash(__('The reset email could not be sent'), 'default', array(), 'bad');
                }

            } else {
                // The email failed to send
                $this->Session->setFlash(__('An account does not exist with the given email address'), 'default', array(), 'bad');
            }
        }
    }

    public function reset() {
        $this->set('subpage', 'Reset');
        $this->set('title_for_layout', 'Reset');

        $this->set('code', $this->request->query('code'));
        $this->set('email', $this->request->query('email'));

        if ($this->request->is('post')) {

            $user = $this->User->find('first', array(
                'conditions' => array(
                    'email' => $this->request->data['User']['email'],
                    'code' => $this->request->data['User']['code']
                    )
                ));

            if ($user) {
                $password = $this->request->data['User']['password'];
                $password_confirm = $this->request->data['User']['password_confirm'];

                $hashed_password = Security::hash($password . Configure::read('Security.salt'), 'sha1', false);
                $hashed_password_confirm = Security::hash($password_confirm . Configure::read('Security.salt'), 'sha1', false);

                $this->User->id = $user['User']['id'];
                $this->User->set('password', $hashed_password);
                $this->User->set('password_confirm', $hashed_password_confirm);
                if($this->User->save($this->User, array('fieldList' => array('password', 'password_confirm', 'code'))))
                {
                    // The password was updated
                    $this->Session->setFlash(__('Your password has been updated'), 'default', array(), 'good');

                    $this->User->id = $user['User']['id'];
                    $this->User->set('code', NULL);
                    $this->User->save($this->User, array('fieldList' => array('code')));

                    if ($this->Auth->login($user["User"])) {
                        
                        return $this->redirect($this->Auth->redirect());

                    }
                    
                }
                else
                {
                    $this->Session->setFlash(__('Password could not be updated'), 'default', array(), 'bad');
                    $errors = $this->User->validationErrors;
                }
            } else {
            
                $this->Session->setFlash(__('Password could not be updated'), 'default', array(), 'bad');
            
            }
        }
    }

    public function dashboard() {

        $this->set('subpage', 'Dashboard');
        $this->set('title_for_layout', 'Dashboard');

        $this->Paginator->settings = $this->paginate;

        $user_id = $this->Auth->user('id');
        $user = $this->User->findById($user_id);
        $this->set('user', $user);

        $movements = $this->Movement->find('all', array(
            'conditions' => array('Movement.user_id' => $user_id),
            'limit' => 20,
            'order' => 'Movement.created desc'
            ));

        $notifications = $this->Notification->find('all', array(
                                                'conditions' => array(
                                                    'Notification.user_id' => $user_id,
                                                    'NotificationType.show_in_dashboard' => true
                                                ),
                                                'contain' => array('NotificationType', 'Movement' => array('MovementPhoto'), 'User'),
                                                'order' => array('Notification.created' => 'desc'),
                                                'limit' => 50
                                            ));

        $this->set('movements', $movements);
        $this->set('notifications', $notifications);
    }

    public function view($user_id) {

        if ($user_id == $this->Auth->user('id')) {
            
            // Attempting to access own profile so redirect to dashboard
            return $this->redirect('/dashboard');

        }

        $user = $this->User->findById($user_id);

        $movements = $this->Movement->find('all', array(
            'conditions' => array('Movement.user_id' => $user_id),
            'limit' => 20,
            'order' => 'Movement.created desc'
            ));

        foreach ($movements as &$movement) {

            $conditions = array(
                'Supporter.supporter' => $this->Auth->user('id'),
                'Supporter.movement_id' => $movement["Movement"]["id"],
                'Supporter.confirmed' => true
            );

            $movement["Movement"]["supported"] = ($this->Supporter->hasAny($conditions)) ? true : false;
        }

        $this->set(compact('movements', 'user'));

        $this->set('subpage', $user["User"]["fullname"]);
        $this->set('title_for_layout', $user["User"]["fullname"]);
    }

    public function edit() {

        $this->set('subpage', 'Edit Profile');
        $this->set('title_for_layout', 'Edit Profile');
        
        $user_id = $this->Auth->user('id');
        $user = $this->User->findById($this->Auth->user('id'));
        $this->set('user', $user);

        if ($this->request->is('post') || $this->request->is('put')) {

            // Get user id
            $this->User->id = $user["User"]["id"];

            $this->User->set( $this->request->data );

            $nophoto = false;

            if ($this->request->data['User']['photo']['size'] == 0) {
                // Photo not selected
                unset($this->User->validate['photo']);
                unset($this->request->data['User']['photo']);
                $this->request->data['User']['photo'] = $user["User"]["photo"];
                $nophoto = true;
            }
            
            unset($this->User->validate['password_confirm']);

            if ($this->User->validates()) {

                if (!$nophoto) {

                    // Get the file extension
                    $filename = $this->request->data['User']['photo']['name'];
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);

                    // Generate url and append extension
                    $photo_url = $this->Auth->user('id') . '_' . substr(str_shuffle('0123456789'), 0, 4) . '.' . $extension;
                    $temp_url =  $this->request->data['User']['photo']['tmp_name'];

                    // Unset the photo array before saving object
                    unset($this->User->validate['photo']);
                    unset($this->request->data['User']['photo']);

                    if (move_uploaded_file($temp_url, 'img/users/' . $photo_url))
                    {
                        // File uploaded - proceed to resize
    
                        $image = new ResizeImage();
                        
                        $target_path = 'img/users/' . $photo_url;
                        
                        // Large Profile
                        $image->load($target_path);
                        $image->makeThumb(960);
                        $image->save("img/users/large/" . $photo_url);
                        
                        // Medium Profile
                        $image->makeThumb(420);
                        $image->save("img/users/medium/" . $photo_url);
                        
                        // Small Profile
                        $image->makeThumb(240);
                        $image->save("img/users/small/" . $photo_url);

                        // Thumb Profile
                        $image->makeThumb(80);
                        $image->save("img/users/thumb/" . $photo_url);
                        
                        // Delete original upload
                        unlink("img/users/" . $photo_url);

                        // Save link to database

                        $this->request->data['User']['photo'] = $photo_url;
                    }
                }

                $this->User->set( $this->request->data );
            
                unset($this->User->validate['password_confirm']);

                // Hash password
                $this->request->data['User']['password'] = Security::hash($this->request->data['User']['password'] . Configure::read('Security.salt'), 'sha1', false);

                if ($this->User->save($this->request->data)) 
                {
                    $this->Session->setFlash(__('Your profile was updated'), 'default', array(), 'good');

                    $this->Auth->login();
                    
                    return $this->redirect('/dashboard');

                } else {
                    // Fields fail validation
                    $this->Session->setFlash(__('Your profile was not updated'), 'default', array(), 'bad');
                    $errors = $this->User->validationErrors;
                }

            } else {
                // Fields fail validation
                $this->Session->setFlash(__('Your profile was not updated'), 'default', array(), 'bad');
                $errors = $this->User->validationErrors;
            }
        }
    }

    public function admin() {

        $this->set('subpage', 'Admin Section');
        $this->set('title_for_layout', 'Admin Section');

        $movements = $this->Movement->find('all', array(
            'conditions' => array(
                'Movement.phase >=' => 2
                )
            ));

        $this->set('movements', $movements);
    }

    public function info() {
        $this->autoRender = false;
        $this->layout = 'ajax';

                // check post
        if(!$this->request->is('post'))
        {
            // throw new NotFoundException();
        }
        
        $response = array();
        $response['meta']['success'] = false;
        $response['response'] = array();
        
        $user_id = null;
        $api_key = null;
        $errors = array();
        // $response['response'] = json_encode($this->request->);
        $response['response'] = json_encode($_POST);

        if(isset($_POST['os']))
        {
            $this->HandsetInformation->set('os', $_POST['os']);    
        }
        if(isset($_POST['version']))
        {
            $this->HandsetInformation->set('version', $_POST['version']);    
        }
        if(isset($_POST['manufacturer']))
        {
            $this->HandsetInformation->set('manufacturer', $_POST['manufacturer']);    
        }
        if(isset($_POST['brand']))
        {
            $this->HandsetInformation->set('brand', $_POST['brand']);    
        }
        if(isset($_POST['device']))
        {
            $this->HandsetInformation->set('device', $_POST['device']);    
        }
        if(isset($_POST['model']))
        {
            $this->HandsetInformation->set('model', $_POST['model']);    
        }
        if(isset($_POST['product']))
        {
            $this->HandsetInformation->set('product', $_POST['product']);    
        }
        if(isset($_POST['screen_width']))
        {
            $this->HandsetInformation->set('screen_width', $_POST['screen_width']);    
        }
        if(isset($_POST['screen_height']))
        {
            $this->HandsetInformation->set('screen_height', $_POST['screen_height']);    
        }
        if(isset($_POST['screen_density']))
        {
            $this->HandsetInformation->set('screen_density', $_POST['screen_density']);    
        }
        if(isset($_POST['regid']))
        {
            $this->HandsetInformation->set('regid', $_POST['regid']);    
        }
        if(isset($POST['api_key']))
        {
            $api_key = $_POST['api_key'];
        }

        $this->HandsetInformation->set('created', date('Y-m-d H:i:s'));
        $this->HandsetInformation->save();

        $response['meta']['success'] = true;

        // log transaction
        $this->Transaction->set('endpoint', 'user_info_post');
        $this->Transaction->set('data', json_encode($_POST));
        $this->Transaction->set('success', $response['meta']['success']);
        $this->Transaction->set('user_id', $user_id);
        $this->Transaction->set('user_ip', $this->request->clientIp());
        $this->Transaction->set('user_agent', $_SERVER['HTTP_USER_AGENT']);
        $this->Transaction->set('api_key', $api_key);
        $this->Transaction->save();


        echo json_encode($response);
    }
    
    public function display_users() {

        $users = $this->User->find('all');

        $this->set(array('users' => $users));
    }

    public function set_gravatars() {

        $this->autoRender = false;
        $this->layout = 'ajax';

        $users = $this->User->find('all', array(
            'conditions' => array(
                'photo' => 'default.png'
                )
            ));

        foreach ($users as $index => $user) {
            
            $this->set_gravatar($user["User"]["id"]);

        }

        echo 'Processing complete!';
    }

    public function set_gravatar($user_id) {

        $user = $this->User->findById($user_id);
        $url = $this->get_gravatar($user["User"]["email"]);
        $extension = 'png';
        $photo_url = $user["User"]["id"] . '_' . substr(str_shuffle('0123456789'), 0, 4) . '.' . $extension;
        $target_path = 'img/users/' . $photo_url;

        if (@copy($url, $target_path)) {
        
            // File uploaded - proceed to resize

            $image = new ResizeImage();
            
            $target_path = 'img/users/' . $photo_url;
            
            // Large Profile
            $image->load($target_path);
            $image->makeThumb(960);
            $image->save("img/users/large/" . $photo_url);
            
            // Medium Profile
            $image->makeThumb(420);
            $image->save("img/users/medium/" . $photo_url);
            
            // Small Profile
            $image->makeThumb(240);
            $image->save("img/users/small/" . $photo_url);

            // Thumb Profile
            $image->makeThumb(80);
            $image->save("img/users/thumb/" . $photo_url);
            
            // Delete original upload
            unlink("img/users/" . $photo_url);

            // Save link to database

            $this->User->id = $user["User"]["id"];
            $this->User->set('photo', $photo_url);
            unset($this->User->validate['photo']);
                
            $this->User->save($this->User, array('fieldList' => array('photo')));
        }
    }

    public function get_gravatar($email) {
        $url = 'http://www.gravatar.com/avatar/';

        $url .= md5( strtolower(trim($email)));
        $s = 960;
        $r = 'g';
        $img = false;
        $atts = array();
        $d = '404';

        $url .= "?s=$s&d=$d&r=$r";

        return $url;
    }

    public function isAuthorized($user) {

        // Restricted to user
        if (in_array($this->action, array('admin', 'display_users', 'set_gravatars'))) {
            if (!isset($user['role']) || ($user['role'] != 'admin')) {
                return false;
            }
        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter() {

        parent::beforeFilter();
        
        $this->Auth->allow('forgot', 'reset', 'register', 'logout');

    }
}
?>