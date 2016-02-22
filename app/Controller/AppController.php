<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('Controller', 'Controller');
App::uses('CakeEmail', 'Network/Email');
App::uses('L10n', 'I18n');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'Validation', 'Utility');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

    public $uses = array('SiteUsageLog', 'SiteUser', 'User');

    function constructClasses() {
        // Save company name when we already know it but before models are created
        Configure::write('identifierDB',
            !empty($this->params['identifier']) ? $this->params['identifier'] : false
        );
        parent::constructClasses();
    }

    public $components = array(
        'Cookie',
        'Session',
        'Auth' => array(
            'loginRedirect' => array(
                'controller' => 'users',
                'action' => 'dashboard'
            ),
            'logoutRedirect' => array(
                'controller' => 'pages',
                'action' => 'display',
                'home'
            ),
            'authorize' => array('Controller')
        )
    );

    public function isAuthorized($user) {
        // Default allow
        return true;
    }

    public function beforeFilter() {

	    parent::beforeFilter();

        $this->Auth->authenticate = array('Custom'); // Set custom authenticate

        // log request
        $this->SiteUsageLog->create();

        // check if we have a site user cookie - this allows us to log unauthenticated users through the site
        if(!isset($_COOKIE['uuid']))
        {
            // new session has started, create cookie
            // generate unique, random unauthenticated user id (uuid)
            $uuid = $this->Session->userAgent() . time();

            // add site session id to db
            $this->SiteUser->create();
            $this->SiteUser->set('uuid', $uuid);
            if($this->Auth->user('id'))
            {
                $this->SiteUser->set('user_id', $this->Auth->user('id'));
            }
            $this->SiteUser->save();

            // write the site_sessions.id (not the ssid, the id in the table) to session storage
            $this->Session->write('site_user.id', $this->SiteUser->id);

            // create cookie
            $cookie_name = "uuid";
            $cookie_value = $uuid;
            setcookie($cookie_name, $cookie_value, time() + (86400 * 365), "/"); // 86400 = 1 day
        }
        else
        {
            // check if we have assigned the current signed in user with the site_user_id
            $site_user = $this->SiteUser->find('first', array(
                                    'conditions' => array(
                                        array(
                                            'uuid' => $_COOKIE['uuid']
                                        )
                                    )
                            ));

            if($site_user)
            {
                if($this->Auth->user('id') && $site_user['SiteUser']['user_id'] == null)
                {
                    $this->SiteUser->id = $site_user['SiteUser']['id'];
                    $this->SiteUser->set('user_id', $this->Auth->user('id'));
                    $this->SiteUser->save();
                }

                $this->Session->write('site_user.id', $site_user['SiteUser']['id']);
            }
        }

        $this->SiteUsageLog->set('site_user_id', $this->Session->read('site_user.id'));


        if($this->Auth->user('id'))
        {
            $this->SiteUsageLog->set('user_id', $this->Auth->user('id'));
        }

        $this->SiteUsageLog->set('user_agent', $this->request->header('User-Agent'));
        $this->SiteUsageLog->set('user_ip', $this->request->clientIp());
        $this->SiteUsageLog->set('request_type', $_SERVER['REQUEST_METHOD']);
        $this->SiteUsageLog->set('controller', $this->request->params['controller']);
        $this->SiteUsageLog->set('action', $this->request->params['action']);
        $this->SiteUsageLog->set('parameters', json_encode($_GET));

        // exclude post variables from storing in the site usage logs
        // otherwise this will record passwords
        if(($this->request->params['action'] == 'login') || ($this->request->params['action'] == 'register') || ($this->request->params['action'] == 'reset') || ($this->request->params['action'] == 'edit'))
        {
            $this->SiteUsageLog->set('post_data', 'sensitive');

            if($_SERVER['REQUEST_METHOD'] == 'POST')
            {
                if(array_key_exists('User', $this->request->data))
                {
                    if(array_key_exists('username', $this->request->data['User']))
                    {
                        $this->SiteUsageLog->set('post_data', json_encode(array('User' => array('username' => $this->request->data['User']['username']))));
                    }
                }
            }
        }
        else
        {
    		json_encode(file_get_contents('php://input'));
    		if (json_last_error() == JSON_ERROR_NONE) {
    			$this->SiteUsageLog->set('post_data', file_get_contents('php://input'));
    		} else {
    			$this->SiteUsageLog->set('post_data', json_encode($_POST));
    		}
	    }

	    // extract user_id from request if directed at app APIs
	    if($this->request->params['controller'] == 'auth' || $this->request->params['controller'] == 'geolocation')
	    {
	    	$user_id = NULL;

	    	if(array_key_exists('user_id', $_GET))
	    	{
	    		$user_id = $_GET['user_id'];
	    	}

	    	if(array_key_exists('user_id', $_POST))
	    	{
	    		$user_id = $_POST['user_id'];
	    	}

	    	if(strlen($user_id) > 0) $this->SiteUsageLog->set('user_id', $user_id);
	    }

        $this->SiteUsageLog->set('url', $this->request->url);
        $this->SiteUsageLog->set('referer', $this->request->referer());

        $this->SiteUsageLog->save();


        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
        {
            $this->_setLanguage();
        }

        $this->Auth->deny();


    	$this->Auth->allow(array('share_link_redirect', 'display', 'index', 'view', 'confirm', 'recent', 'info', 'preview', 'generate', 'updatePhase', 'supporter_stats', 'design', 'feedback', 'sharelinkredirect'));
        
        /* AJAX check  */
        $ajax_request = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') ? true : false;

        if (($this->request->url != 'login') && ($this->request->url != 'register') && ($this->request->url != '/') && (!$ajax_request)) {

            $this->Session->write('pre_login_route', '/' . $this->request->url);

        }
    }

    private function _setLanguage() {

        $did_change_language = true; // Default

        $browser_language = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

        // if l - language param is passed then set app language
        if($this->request->query('l'))
        {
            $this->Session->write('Config.language', $this->request->query('l'));
            $this->Cookie->write('lang', $this->request->query('l'), false, '30 days');

            if($this->Auth->user('id'))
            {
                $this->User->id = $this->Auth->user('id');
                $this->User->saveField('locale', $this->request->query('l'));
            }
        }
        // else check if the user has a cookie with a language already set and set the page to this
        else if($this->Cookie->read('lang'))
        {
            if(strlen($this->Cookie->read('lang')) > 2)
            {
                $this->Cookie->write('lang', $browser_language, false, '30 days');
                $this->Session->write('Config.language', $browser_language);
            }
            else
            {
                $this->Session->write('Config.language', $this->Cookie->read('lang'));
            }
        }
        // else if user has no cookie and browser has a language, set it
        else
        {
            $this->Cookie->write('lang', $browser_language, false, '30 days');
            $this->Session->write('Config.language', $browser_language);
        }

        // Check for RTL language
        $l10n = new L10n();
        $l10n_language = $l10n->catalog($this->Session->read('Config.language'));

        $this->Session->write('Config.text_direction', strtoupper($l10n_language['direction']));
    }

}

