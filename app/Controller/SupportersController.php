<?php

App::import('Vendor','HashIds');
App::uses('HttpSocket', 'Network/Http');

class SupportersController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
    public $uses = array('Supporter', 'Promoter', 'Movement', 'Notification');

    // Add a new vote
    public function add($id = null, $code = null) {
        $this->autoRender = false;

        if ($id && $code) {
            $conditions = array(
                'Supporter.supporter' => $this->Auth->user('id'),
                'Supporter.movement_id' => $id
            );

            if (!$this->Supporter->hasAny($conditions)){
    	        $data = array('supporter' => $this->Auth->user('id'), 'movement_id' => $id, 'code' => $code);
    			$this->Supporter->save($data);
            }
        }
    }

    // Check if a vote has been confirmed
    public function check($id = null, $code = null) {

        $this->autoRender = false;

        if ($id && $code) {

            // Decode the code
            $code = strtolower($code);

            $hasher = new HashIds(Configure::read('urlSalt'), Configure::read('codeHashMinLength'), Configure::read('hashAlphabet'));
            $hash = $hasher->decrypt($code);
            
        	$supporter = $this->Supporter->findById($hash);
        	if ($supporter["Supporter"]["confirmed"]) {
        		echo 'true';
        	} else {
        		echo 'false';
        	}
        }
    }

    // Confirm a vote
    // $_POST api endpoint
    public function confirm() {
        $this->autoRender = false;
        $this->layout = 'ajax';

        // check post
        // if(!$this->request->is('post'))
        // {
        //     throw new NotFoundException();
        // }
        
        $response = array();
        $response['meta']['success'] = false;
        $response['response'] = array();

        $user_id = null;

        $code = $this->request->data('code');
        
        $code = strtolower($code);

        if ($code) {
        	$supporter = $this->Supporter->findByCode($code);

        	if ($supporter) {
	        	$this->Supporter->read(null, $supporter["Supporter"]["id"]);
                $this->Supporter->set(array(
                    'confirmed' => 1,
                    //'code' => null // uncomment for production!
                    'confirmed_on' => date('Y-m-d H:i:s')
                ));
                $this->Supporter->save();
		        
		        $response['meta']['success'] = true;
                $response['response'] = $supporter["Supporter"]["supporter"];
                $user_id = $supporter["Supporter"]["supporter"];

        	} else {
                $response['meta']['errors'][] = __('Please check that you have entered the correct code. This can be found at http://app-movement.com');
		    }
        }
        else
        {
            $response['meta']['errors'][] = __("Code parameter missing");
        }

        // log transaction
        $this->loadModel('Transaction');
        $this->Transaction->set('endpoint', 'confirm_post');
        $this->Transaction->set('data', json_encode($this->request->data));
        $this->Transaction->set('success', $response['meta']['success']);
        $this->Transaction->set('user_id', $user_id);
        $this->Transaction->set('user_ip', $this->request->clientIp());
        $this->Transaction->set('user_agent', $_SERVER['HTTP_USER_AGENT']);
        $this->Transaction->set('api_key', $this->request->data('api_key'));
        $this->Transaction->save();


        echo json_encode($response);
    }

    public function confirm_support()
    {
        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $response = array();
        $response['meta']['success'] = false;
        $response['response'] = array();

        if($this->Auth->loggedIn())
        {
            $user = $this->Auth->user();


            $grecaptcha_code = $_GET['grecaptcha_response'];
            
            
            $movement = $this->Movement->find('first', array(
                                        'conditions' => array(
                                            'Movement.id' => $_GET['movement_id']
                                        )
                                    ));

            if($movement){
                if($grecaptcha_code){
                    // check if human
                    $HttpSocket = new HttpSocket();
                    $results = $HttpSocket->get(Configure::read('google_recaptcha_url'), array('response' => $grecaptcha_code, 'secret' => Configure::read('google_recaptcha_secret')));
                    $result = json_decode($results->body);

                    if($result->success)
                    {
                        // confirm support
                        $response['meta']['success'] = true;

                        $supporter = $this->Supporter->find('first', array(
                                                        'conditions' => array(
                                                            'Supporter.supporter' => $this->Auth->user('id'),
                                                            'movement_id' => $movement['Movement']['id']
                                                        )
                                                    ));

                        $this->Supporter->read(null, $supporter["Supporter"]["id"]);
                        $this->Supporter->set(array(
                            'confirmed' => 1,
                            'confirmed_on' => date('Y-m-d H:i:s')
                        ));
                        $this->Supporter->save();

                        // send off supporter notification
                        $this->Notification->notify_new_supporter($movement['Movement']['id'], $supporter["Supporter"]["supporter"]);
                    }
                    else
                    {
                        $response['meta']['errors'][] = __("Please verify you are a real person by clicking the button above");
                    }
                }
                else
                {
                    $response['meta']['errors'][] = __("Please verify you are a real person by clicking the button above");
                }
            }
            else
            {
                $response['meta']['errors'][] = __("Invalid movement_id");
            }
        }
        else
        {
            $response['meta']['errors'][] = __("You must be logged in to perform this action");
        }

        echo json_encode($response);
    }

    public function supporter_stats($movement_id)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        // find movement
        $movement = $this->Movement->find('first', array(
                                        'conditions' => array(
                                            'Movement.id' => $movement_id
                                        )
                                    ));


        if(!$movement)
        {
            return false;
        }

        // create a day period array between when movement started and now
        $period = new DatePeriod(
             new DateTime(date('Y-m-d H:i:s', strtotime($movement["Movement"]["created"]))),
             new DateInterval('P1D'),
             new DateTime(date("Y-m-d H:i:s", strtotime("+" . ($movement["Movement"]["support_duration"]) . " days", strtotime($movement["Movement"]["created"]))))
             // 
        );

        // Last 60 days of support duration
        // $period = new DatePeriod(
        //      new DateTime(date("Y-m-d H:i:s", strtotime("+" . ($movement["Movement"]["support_duration"] - 60) . " days", strtotime($movement["Movement"]["created"])))),
        //      new DateInterval('P1D'),
        //      new DateTime(date("Y-m-d H:i:s", strtotime("+" . ($movement["Movement"]["support_duration"]) . " days", strtotime($movement["Movement"]["created"]))))
        // );

        $data = array();

        // get all supporters for this movement, group by day
        $supporter_counts = $this->Supporter->find('all', array(
                    'conditions' => array(
                        'Supporter.movement_id' => $movement_id,
                        'Supporter.confirmed' => 1
                    ),
                    'fields' => array('COUNT(Supporter.id) as supporter_count', 'DATE(Supporter.created) as day'),
                    'group' => array('DATE(Supporter.created)')
                ));


        $supporters_per_day = array();

        // collapse cakephp result so that it becomes date -> count i.e. [2014-07-10] => 2
        // we do this so that in the period loop we can use array_key_exists and then set that array 
        // element with the supporter count
        foreach($supporter_counts as $supporter_count)
        {
            $supporters_per_day[$supporter_count[0]["day"]] = $supporter_count[0]["supporter_count"];
        }

        // echo '<pre>';
        // print_r($supporters_per_day);
        // echo '</pre>';

        // loop through each period and populate $data
        // with the given day and the supporter count
        $day_count = 1;
        $supporter_count = 0;

        $data[0] = 0;
        // echo '<pre>';
        // print_r($supporters_per_day);
        // echo '</pre>';
        foreach($period as $date)
        {
            if(array_key_exists($date->format('Y-m-d'), $supporters_per_day))
            {
                // we have a supporter record 
                // populate data with it, casting the count as an int 
                // so that the json_encode methods uses ints rather than strings   
                // $data[$day_count] = $supporter_count . "," . (int) $supporters_per_day[$date->format('Y-m-d')];
                $data[$day_count] = $supporter_count + (int) $supporters_per_day[$date->format('Y-m-d')];

                $supporter_count = $data[$day_count];
                // echo '<pre>';
                // print_r($supporter_count);
                // echo '</pre>';
            }
            else
            {
                // echo '<pre>';
                // print_r($supporter_count);
                // echo '</pre>';
                $data[$day_count] = $supporter_count;
            }

            $day_count++;
        } 

        return json_encode($data);
    }
}
?>