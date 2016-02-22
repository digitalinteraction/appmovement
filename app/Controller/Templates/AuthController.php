<?php
App::import('Vendor', 'ResponseObject');
App::uses('SimplePasswordHasher', 'Controller/Component/Auth', 'Validation', 'Utility');

class AuthController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Email', 'Session', 'RequestHandler', 'ApiSession');
    public $uses = array('User', 'Movement', 'PublishedAppSurvey', 'Survey', 'IssuedSurvey', 'ShareLink', 'ShareLinkType', 'PublishedApp', 'SiteUsageLogs');

    public $autoRender = false;
    public $layout = 'json';

    private $api_key = Configure::read('geolocation.api_key');

    public $response; // Response object

    // TEST AREA

    public function test()
    {
    }

    // DUPLICATED FROM GeolocationController
    public function checkSessionKey($session_key, $user_id)
    {
        if (!$this->ApiSession->checkSessionKey($session_key, $user_id))
        {
            array_push($this->response->errors, 'Session Key Invalid');
            $this->response->meta['session_valid'] = false;
            $this->response->output();
        }
    }

    public function checkKey($api_key)
    {
        if ($api_key != $this->api_key)
        {
            $this->response->errors = 'API Key Invalid';
            $this->response->output();
        }
    }

    /**
     * @api {post} /api/auth/register /register - Register New User
     * @apiName Register
     * @apiGroup Auth
     * @apiDescription This endpoint will register a new user in the App Movement system. If a user calls this register endpoint with the same details as the initial request the user object of the currently registered user will be returned.
     * @apiSampleRequest https://app-movement.com/api/auth/register
     *
     * @apiParam    {String}    username         Username of the user
     * @apiParam    {String}    [fullname]       Fullname of the user
     * @apiParam    {String}    email            Email address of the user
     * @apiParam    {String}    api_key          API Key
     *
     * @apiSuccess {User}   User                            User object
     * @apiSuccess {String} User.id                         Users id
     * @apiSuccess {String} User.fullname                   Full name of user
     * @apiSuccess {String} User.password                   Hashed Password
     * @apiSuccess {String} User.email                      User Email
     * @apiSuccess {String} User.photo                      User photo of file (url needs to be constructed to request this image)
     * @apiSuccess {String} User.locale                     Language of user (when registering)
     * @apiSuccess {String} User.role                       User role on site (will always be "user" unless specified otherwise)
     * @apiSuccess {String} User.code                       Forgotten password code that is currently active when user performed forgotten password request
     * @apiSuccess {String} User.receives_email_updates     Boolean to show if user receives email updates
     * @apiSuccess {String} User.created                    User account creation datetime
     * @apiSuccess {String} User.modified                   User account modified datetime
     * @apiSuccess {String} User.session_key                Latest active session key for given user
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "User": {
     *            "photo": "default.png",
     *            "role": "user",
     *            "receives_email_updates": "1",
     *            "username": "kapow123321",
     *            "fullname": "mr boop",
     *            "email": "kapow@boopbong.com",
     *            "password": "c4c0902f3646624266eaafdf2c453c205a639e1b",
     *            "modified": "2015-07-28 11:19:00",
     *            "created": "2015-07-28 11:19:00",
     *            "id": "4183",
     *            "session_key": "Wy7Ooao7q7dOpPdT9ass8BbEmT87BdoFa"
     *        }
     *    }
     * }
     *
     * @apiError   username         Please provide username
     * @apiError   username taken   Username has been taken. Please sign in with your App Movement account if you have one.
     * @apiError   email            Please provide email
     * @apiError   duplicate email  Email address already registered. Please sign in with your App Movement account if you have one.
     * @apiError   password         Please provide password
     * @apiError   api_key          API key required
     * @apiError   Save failed      Account could not be created
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Email address already registered. Please sign in with your App Movement account if you have one.",
     *        "Username has been taken. Please sign in with your App Movement account if you have one."
     *    ],
     *    "data": []
     *}
     */
    public function register()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Get parameters
        $username = $this->request->data('username');
        $fullname = $this->request->data('fullname');
        $email = $this->request->data('email');
        $password = $this->request->data('password');

        // Check parameters
        if (!$username) { array_push($this->response->errors, __('Please provide username')); }
        if (!$email) { array_push($this->response->errors, __('Please provide email')); }
        if (!$password) { array_push($this->response->errors, __('Please provide password')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Attempt login with credentials
        $user = $this->loginUser($email, $password);

        if ($user) {
            $user['User']['session_key'] = $this->ApiSession->generateSessionKey($user['User']['id']);
        } else {
            // Perform functions
            // !! Check user does not exist
            // !! Check vailid email/username/password
            if ($this->User->checkEmail($email) == false) { array_push($this->response->errors, __('Email address already registered. Please sign in with your App Movement account if you have one.')); }
            if ($this->User->checkUsername($username) == false) { array_push($this->response->errors, __('Username has been taken. Please sign in with your App Movement account if you have one.')); }

            // Insert user
            $this->User->create();

            $data = array();

            if(!$fullname) { $fullname = $username; }

            $data['User']['username'] = $username;
            $data['User']['fullname'] = $fullname;
            $data['User']['email'] = $email;
            $data['User']['password'] = $password;

            unset($this->User->validate);

            // Check for errors
            if (count($this->response->errors) > 0) {
                $this->response->output();
            }
            else
            {
                $user = $this->User->save($data);

                if (!$user) {
                    array_push($this->response->errors, __('Account could not be created'));
                }
                else
                {
                    // Generate session key
                    $user['User']['session_key'] = $this->ApiSession->generateSessionKey($user['User']['id']);
                }
            }
        }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $user;
        $this->response->output();
    }

    /**
     * @api {post} /api/auth/login /login - Authenticate User
     * @apiName Login
     * @apiGroup Auth
     * @apiDescription This endpoint will authenticate a user in the App Movement system. After successful authentication the endpoint will return a session_key which should be used on subsequent requests when interacting with the API.
     * @apiSampleRequest https://app-movement.com/api/auth/login
     *
     * @apiParam    {String}    email            Email address of the user
     * @apiParam    {String}    password         Password of the user (pre hashed and salted with appropriate salt)
     * @apiParam    {String}    api_key          API Key
     *
     * @apiSuccess {User}   User                            User object
     * @apiSuccess {String} User.id                         Users id
     * @apiSuccess {String} User.fullname                   Full name of user
     * @apiSuccess {String} User.password                   Hashed Password
     * @apiSuccess {String} User.email                      User Email
     * @apiSuccess {String} User.photo                      User photo of file (url needs to be constructed to request this image)
     * @apiSuccess {String} User.locale                     Language of user (when registering)
     * @apiSuccess {String} User.role                       User role on site (will always be "user" unless specified otherwise)
     * @apiSuccess {String} User.code                       Forgotten password code that is currently active when user performed forgotten password request
     * @apiSuccess {String} User.receives_email_updates     Boolean to show if user receives email updates
     * @apiSuccess {String} User.created                    User account creation datetime
     * @apiSuccess {String} User.modified                   User account modified datetime
     * @apiSuccess {String} User.session_key                Latest active session key for given user
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "User": {
     *            "photo": "default.png",
     *            "role": "user",
     *            "receives_email_updates": "1",
     *            "username": "kapow123321",
     *            "fullname": "mr boop",
     *            "email": "kapow@boopbong.com",
     *            "password": "c4c0902f3646624266eaafdf2c453c205a639e1b",
     *            "modified": "2015-07-28 11:19:00",
     *            "created": "2015-07-28 11:19:00",
     *            "id": "4183",
     *            "session_key": "Wy7Ooao7q7dOpPdT9ass8BbEmT87BdoFa"
     *        }
     *    }
     * }
     *
     * @apiError   username         Please provide username
     * @apiError   username taken   Username has been taken. Please sign in with your App Movement account if you have one.
     * @apiError   email            Please provide email
     * @apiError   duplicate email  Email address already registered. Please sign in with your App Movement account if you have one.
     * @apiError   password         Please provide password
     * @apiError   api_key          API key required
     * @apiError   Save failed      Account could not be created
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Email address already registered. Please sign in with your App Movement account if you have one.",
     *        "Username has been taken. Please sign in with your App Movement account if you have one."
     *    ],
     *    "data": []
     *}
     */
    // Attempt Login
    public function login()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Get parameters
        $email = $this->request->data('email');
        $password = $this->request->data('password');

        // Check parameters
        if (!$email) { array_push($this->response->errors, __('Please provide email')); }
        if (!$password) { array_push($this->response->errors, __('Please provide password')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        $user = $this->loginUser($email, $password);

        if (!$user) { array_push($this->response->errors, __('Authentication Failed')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Generate session key
        $user['User']['session_key'] = $this->ApiSession->generateSessionKey($user['User']['id']);

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $user;
        $this->response->output();
    }

    private function loginUser($usernameOrEmail, $password)
    {
        $passwordHasher = new SimplePasswordHasher();
        $password = AuthComponent::password($password);

        return $this->User->find('first', array(
            'conditions' => array(
                'OR' => array(
                    'User.email' => $usernameOrEmail,
                    'User.username' => $usernameOrEmail
                ),
                'User.password' => $password
            ),
            'recursive' => -1
        ));
    }

    /**
     * @api {post} /api/auth/reset /reset - Reset Password
     * @apiName Reset
     * @apiGroup Auth
     * @apiDescription This endpoint will reset a user's password using the App Movement system. After posting, the user will receive an email with a unique link which they need to click and follow onto the App Movement forgotten password page. From here they can reset their password. The meta.success will tell you if the email has been sent successfully. There is no response object in the data object
     * @apiSampleRequest https://app-movement.com/api/auth/reset
     *
     * @apiParam    {String}    email            Email address of the user
     * @apiParam    {String}    app_name         App name of the app requesting the forgotten password
     * @apiParam    {String}    api_key          API Key
     *
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": []
     * }
     *
     * @apiError   email            Please provide email
     * @apiError   app_name         Please provide an app name
     * @apiError   user             User not found
     * @apiError   api_key          API key required
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide email",
     *        "Please provide an app name"
     *    ],
     *    "data": []
     *}
     *
     */
    // Send Reset Code
    public function reset()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Get parameters
        $email = $this->request->data('email');
        $app_name = $this->request->data('app_name');

        // Check parameters
        if (!$email) { array_push($this->response->errors, __('Please provide email')); }
        if (!$app_name) { array_push($this->response->errors, __('Please provide an app name')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        $random_code = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        $reset_link = 'https://app-movement.com/reset?email=' . $email . '&code=' . $random_code;

        $user = $this->User->findByEmail($email);

        // Check for errors
        if (!$user) { array_push($this->response->errors, __('User not found')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        $this->User->id = $user['User']['id'];
        $this->User->set('code', $random_code);
        $this->User->save($this->request->data, false);

        $Email = new CakeEmail('custom');
        $Email->to($email);
        $Email->emailFormat('html');
        $Email->template('reset', 'main_notification');
        $Email->viewVars( array('reset_link' => $reset_link, 'app_name' => $app_name));
        $Email->subject('Reset Your ' . $app_name . ' Password');
        $Email->from(array('admin@app-movement.com' => $app_name));

        if ($Email->send()) {
            // The email was sent
        } else {
            array_push($this->response->errors, __('Email failed to send'));
        }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->output();
    }

    /**
     * @api {get} /api/:identifier/version /version - Version check
     * @apiName Version Check - Legacy
     * @apiGroup Auth
     * @apiDescription This endpoint will display the latest version numbers for both the iOS and Android apps for the geolocation template. <br/><br/><strong>This has been replaced by the /setup endpoint</strong>. <br/><br/>However it is required for legacy support. <br/>The :identifier should be the movement identifier e.g. movement_42_156 found in the movement table. No api key required.
     * @apiSampleRequest https://app-movement.com/api/:identifier/version
     *
     *
     * @apiSuccess {String} ios_latest_version          Latest ios version number
     * @apiSuccess {String} android_latest_version      Latest Android version number
     * @apiSuccess {String} ios_download_link           iOS app store download link
     * @apiSuccess {String} android_download_link       Android Google Play Store download link
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "ios_latest_version": "1.1",
     *        "android_latest_version": "1.0",
     *        "ios_download_link": "",
     *        "android_download_link": ""
     *    }
     * }
     *
     */
    public function version($identifier)
    {
        $this->response = new ResponseObject();

        $movement = $this->Movement->find('first', array(
                                        'conditions' => array(
                                            'identifier' => $identifier
                                        )
                                    ));

        if($movement)
        {
            $return = array();
            $return['ios_latest_version'] = $movement['Movement']['ios_latest_version'];
            $return['android_latest_version'] = $movement['Movement']['android_latest_version'];
            $return['ios_download_link'] = $movement['Movement']['ios_download_link'];
            $return['android_download_link'] = $movement['Movement']['android_download_link'];

            $this->response->data = $return;
            $this->response->meta['success'] = true;
        }
        else
        {
            array_push($this->response->errors, 'Incorrect identifier specified');
        }

        $this->response->output();


    }

    /**
     * @api {get} /api/:identifier/setup /setup - Get Setup Params
     * @apiName Setup
     * @apiGroup Auth
     * @apiDescription This is called each time an app is loaded and will return the latest version numbers for both iOS and Android as well as any survey urls that should be presented to the user
     * @apiSampleRequest https://app-movement.com/api/movement_98_59/setup
     *
     * @apiParam    {Number}    user_id         user id of current logged in user
     * @apiParam    {String}    session_key     session id of authenticated user
     * @apiParam    {String}    api_key         API Key
     *
     * @apiSuccess {String} share_hashtags Dynamic hashtags to be injected in share text
     * @apiSuccess {String} ios_min_supported_version Min supporterd version number for iOS
     * @apiSuccess {String} android_min_supported_version Min supporterd version number for Android
     * @apiSuccess {String} ios_latest_version Latest version number for iOS
     * @apiSuccess {String} android_latest_version Latest version number for Android
     * @apiSuccess {String} ios_download_link Download link for iOS
     * @apiSuccess {String} android_download_link Download link for Android
     * @apiSuccess {String} survey_url Survey URL of a Google Forms survey
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "share_hashtags": "#AppMovement #AppName",
     *        "ios_min_supported_version": "1.0",
     *        "android_min_supported_version": "1.0",
     *        "ios_latest_version": "1.1",
     *        "android_latest_version": "1.0",
     *        "ios_download_link": "",
     *        "android_download_link": "",
     *        "survey_url": "https://docs.google.com/forms/d/10y1axg5gc02t2T9jyG3rTxoQDafNziLuoJSwCk1W7NI"
     *    }
     * }
     *
     * @apiError   identifier     Incorrect identifier specified
     * @apiError   user_id        Please provide user_id
     * @apiError   api_key        API key required
     * @apiError   session_key    Incorrect identifier specified
     *
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "API key required",
     *        "Please provide user_id",
     *        "Incorrect identifier specified"
     *    ],
     *    "data": []
     *}
     */
    public function setup($identifier)
    {
        $this->response = new ResponseObject();

        if(!$this->request->query('api_key')){
            array_push($this->response->errors, 'API key required');
        }
        else
        {
            $this->checkKey($this->request->query('api_key'));
        }

        $user_id = $this->request->query('user_id');
        
        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }

        // Check session key
        if ($this->request->query('session_key')) {
            $this->checkSessionKey($this->request->query('session_key'), $this->request->query('user_id'));
        }

        $published_app = $this->PublishedApp->find('first', array(
                                                        'conditions' => array(
                                                            'app_identifier' => $identifier
                                                        )
                                                    ));
        $return = array();

        if ($published_app)
        {
            $return['share_hashtags'] = $published_app["PublishedApp"]["share_hashtags"];
            $return['ios_min_supported_version'] = $published_app["PublishedApp"]['ios_min_supported_version'];
            $return['android_min_supported_version'] = $published_app["PublishedApp"]['android_min_supported_version'];
            $return['ios_latest_version'] = $published_app["PublishedApp"]['ios_latest_version'];
            $return['android_latest_version'] = $published_app["PublishedApp"]['android_latest_version'];
            $return['ios_download_link'] = $published_app["PublishedApp"]['ios_download_link'];
            $return['android_download_link'] = $published_app["PublishedApp"]['android_download_link'];
        }
        else
        {
            array_push($this->response->errors, __('Incorrect identifier specified'));
        }

        // Check for errors
        if (count($this->response->errors) > 0) {
            $this->response->output();
        }
        else
        {
            // check locale
            if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
                $this->User->id = $user_id;
                $this->User->saveField('locale', $locale);
            }

            $completed_surveys = $this->IssuedSurvey->find('list', array(
                                                            'conditions' => array(
                                                               'user_id' => $user_id
                                                            ),
                                                            'fields' => array(
                                                               'published_app_survey_id'
                                                            )
                                                       ));

            // check to see if this published app has any surveys available for the user
            $uncompleted_surveys = $this->PublishedAppSurvey->find('all', array(
                                                                        'conditions' => array(
                                                                            'NOT' => array(
                                                                                'PublishedAppSurvey.id' => $completed_surveys
                                                                             ),
                                                                            'published_app_id' => $published_app["PublishedApp"]["id"],
                                                                            'active' => 1
                                                                        ),
                                                                        'order' => array(
                                                                                'PublishedAppSurvey.id ASC'
                                                                        )
                                                                      ));

            // flag to identify if survey has been issued
            $mark_survey_as_issued = false;
            $issued_survey = NULL;
            // loop through surveys to detect types available
            foreach($uncompleted_surveys as $survey)
            {
                if($survey['SurveyType']['type'] == 'nth_app_use')
                {
                    // the survey is intended to be available on Nth open of the app
                    // find all the /api/:identifier/setup calls for this user_id
                    // and count how many times this has been called.
                    // show if this count is >= survey threshold
                    //
                    // horrible like added for setup because url sometimes has trailing slash
                    $app_launch_count = $this->SiteUsageLogs->find('count', array(
                                                                    'conditions' => array(
                                                                       'action' => 'setup',
                                                                       'url LIKE' => 'api/' . $published_app['PublishedApp']['app_identifier'] . '/setup%',
                                                                       'parameters LIKE' => '%user_id":"' . $user_id . '"%'
                                                                    )
                                                                ));

                    if($app_launch_count >= $survey['PublishedAppSurvey']['app_launch_threshold'])
                    {
                            $mark_survey_as_issued = true;
                            $issued_survey = $survey;
                            break;
                    }
                }
                else if($survey['SurveyType']['type'] == 'fixed_period')
                {
                    // the survey is intended to be available between a given period only
                    // check if the period between start date of survey and end date (start date + survey duration)
                    $start = date($survey['PublishedAppSurvey']['start']);
                    $now = date("Y-m-d H:i:s");
                    $end = date("Y-m-d H:i:s", strtotime($survey['PublishedAppSurvey']['start'] . ' + ' . $survey['PublishedAppSurvey']['duration_in_days'] . ' days'));

                    if($start <= $now && $now <= $end)
                    {
                            $mark_survey_as_issued = true;
                            $issued_survey = $survey;
                            break;
                    }
                }
                else if($survey['SurveyType']['type'] == 'usage_offset')
                {
                    // get the first time that the user called /setup
                    // horrible like added for setup because url sometimes has trailing slash
                    $first_app_launch = $this->SiteUsageLogs->find('first', array(
                                                                    'conditions' => array(
                                                                       'action' => 'setup',
                                                                       'url LIKE' => 'api/' . $published_app['PublishedApp']['app_identifier'] . '/setup%',
                                                                       'parameters LIKE' => '%user_id":"' . $user_id . '"%'
                                                                    ),
                                                                    'order' => array(
                                                                       'SiteUsageLogs.created ASC'
                                                                    )
                                                                ));

                    // check they've actually used the app at least once
                    if($first_app_launch)
                    {
                        // check if date survey should be presented >= first use + days after first use
                        $now = date("Y-m-d H:i:s");
                        // minimum date to display survey takes the first use
                        // and adds the days_after_first_use (from the published app survey table)
                        // in order to calculate the earliest opportunity to show the survey
                        $minimum_date_to_display_survey = date("Y-m-d H:i:s", strtotime($first_app_launch['SiteUsageLogs']['created'] . ' + ' . $survey['PublishedAppSurvey']['days_after_first_use'] . ' days'));
                        $maximum_date_to_display_survey = date("Y-m-d H:i:s", strtotime($minimum_date_to_display_survey . ' + ' . $survey['PublishedAppSurvey']['days_available_for'] . ' days'));

                        // check if now is between the earliest date and last date that the survey should be available for
                        if($now >= $minimum_date_to_display_survey && $now <= $maximum_date_to_display_survey)
                        {
                            $mark_survey_as_issued = true;
                            $issued_survey = $survey;
                            break;
                        }
                    }
                }
            }

            // if the conditions on the survey types are met then check if we should issue the survey
            if($mark_survey_as_issued == true)
            {
                $this->IssuedSurvey->create();
                $this->IssuedSurvey->set('published_app_survey_id', $survey['PublishedAppSurvey']['id']);
                $this->IssuedSurvey->set('user_id', $user_id);
                $this->IssuedSurvey->save();

                $return['survey_url'] = $survey['Survey']['url'];

                // check if this survey needs variables appending to the GET parameters in the request
                // i.e. for adding user_id into the url
                if($issued_survey['PublishedAppSurvey']['append_variables'])
                {
                    $return['survey_url'] = str_replace("{user_id}", $user_id, $return['survey_url']);
                }
            }
        }

        // Send response
        $this->response->data = $return;
        $this->response->meta['success'] = true;
        $this->response->output();
    }

    /**
     * @api {get} /api/share /share - Generate a share link
     * @apiName Share
     * @apiGroup Auth
     * @apiDescription This endpoint generates unique share links for any given content.
     * @apiSampleRequest https://app-movement.com/api/share
     *
     * @apiParam    {Number}    user_id         user id of current logged in user
     * @apiParam    {String}    content_id      Unique id of the content you're sharing (e.g. review id)
     * @apiParam    {String}    app_identifier  App Identifier (e.g. movement_56_254)
     * @apiParam    {String}    share_type      The type of share action you are performing. (e.g. review, movement, venue). Pass in the string representation of the share_link_type_id
     * @apiParam    {String}    api_key         API Key
     *
     * @apiSuccess {String} share_link  Share code that needs to be appended to the url (e.g. http://app-movement.com/share/CODE)
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *  "meta": {
     *    "success": true,
     *    "min_version": 1,
     *    "session_valid": true
     *  },
     *  "errors": [],
     *  "data": {
     *    "share_link": "https://app-movement.com/RN6"
     *  }
     * }
     *
     * @apiError   identifier     Please provide content_id
     * @apiError   user_id        Please provide app_identifier
     * @apiError   session_key    Please provide share_type
     * @apiError   share_type     Please provide valid app_identifier
     * @apiError   api_key        API key required
     *
     *
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *  "meta": {
     *    "success": false,
     *    "min_version": 1,
     *    "session_valid": true
     *  },
     *  "errors": [
     *    "Please provide content_id",
     *    "Please provide app_identifier",
     *    "Please provide share_type",
     *    "Please provide valid app_identifier",
     *    "Please provide a valid share_type"
     *  ],
     *  "data": []
     * }
     */
    public function share()
    {
        $this->response = new ResponseObject();

        if(!$this->request->query('api_key')){
            array_push($this->response->errors, __('API key required'));
        }
        else
        {
            $this->checkKey($this->request->query('api_key'));
        }

        $user_id = $this->request->query('user_id');
        $content_id = $this->request->query('content_id');
        $app_identifier = $this->request->query('app_identifier');
        $share_type = $this->request->query('share_type');

        // Check parameters
        if (!$content_id) { array_push($this->response->errors, __('Please provide content_id')); }
        if (!$app_identifier) { array_push($this->response->errors, __('Please provide app_identifier')); }
        if (!$share_type) { array_push($this->response->errors, __('Please provide share_type')); }

        // check app identifier
        $published_app = $this->PublishedApp->find('first', array(
                                                    'conditions' => array(
                                                        'app_identifier' => $app_identifier
                                                    )
                                                ));


        if (!$published_app) { array_push($this->response->errors, __('Please provide valid app_identifier')); }

        $share_type_id = $this->ShareLinkType->find('first', array(
                                                    'conditions' => array(
                                                        'name' => $share_type
                                                    )
                                                ));

        if(!$share_type_id) { array_push($this->response->errors, __('Please provide a valid share_type')); }

        if (count($this->response->errors) > 0) {
            $this->response->output();
        }
        else
        {
            // public function generate_share_link($share_link_type_id, $parent_id, $published_app_id, $user_id, $site_user_id, $return_full_url = true)
            $share_link = $this->ShareLink->generate_share_link($share_type_id['ShareLinkType']['id'], $content_id, $published_app['PublishedApp']['id'], $user_id, NULL);

            $return = array();
            $return['share_link'] = $share_link;
        }


        // Send response
        $this->response->data = $return;
        $this->response->meta['success'] = true;
        $this->response->output();
    }



    function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }
}
?>
