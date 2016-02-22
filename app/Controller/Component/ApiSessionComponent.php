<?php
App::uses('Component', 'Controller');

class ApiSessionComponent extends Component {

    public $components = array('Session');

    public function generateSessionKey($user_id)
    {
    	$api_session = ClassRegistry::init('ApiSession');

        $session_key = str_shuffle(Configure::read('Security.salt'));

        // Add session key to database
        $api_session->create();

        $data = array();

        $data['user_id'] = $user_id;
        $data['session_key'] = $session_key;

        $user = $api_session->save($data);

        return $session_key;
    }

    public function checkSessionKey($session_key, $user_id)
    {
	    if ($user_id == "") { return false; } // Session is not valid if there is no user_id
        if ($session_key == "") { return false; } // Session is not valid if there is no user_id
	
    	$api_session = ClassRegistry::init('ApiSession');
    	
        $valid_date = date('Y-m-d H:i:s', strtotime('-' . Configure::read('geolocation.session_timeout'), time()));

        $conditions = array(
            'ApiSession.user_id' => $user_id,
            'ApiSession.session_key' => $session_key,
            'ApiSession.created >' => $valid_date
            );

        if ($api_session->hasAny($conditions)) {
            // Session is valid
            return true;
        } else {
            // Session has expired / does not exist
            return false;
        }
    }
}
?>
