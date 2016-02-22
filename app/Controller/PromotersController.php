<?php

App::import('Vendor','HashIds');

class PromotersController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
	public $uses = array('Movement', 'ReferralUrl', 'Supporter', 'Promoter');

    // Add a new promoter
    public function add($movement_id = null) {
        $this->autoRender = false;

        $movement = $this->Movement->findById($movement_id);
        $user = $this->Auth->user();
        $supporter = null;

		$response = array();
        $response['meta']['success'] = false;
        // $response['response'] = false;
        // check user is vailid and supporter of movement and that movement exists
        if($movement && $this->Auth->login())
        {	
        	$supporter = $this->Supporter->get_supporter_by_supporter_id_and_movement_id($user["id"], $movement["Movement"]["id"]);
	        if($supporter)
	        {
		        $promoter = $this->Promoter->findByPromoterAndMovementId($user["id"], $movement["Movement"]["id"]);

		        // find the stats for this promoter and display them
		        if(!$promoter)
		        {
		        	// add promoter record and display stats
		        	$this->Promoter->create();
		        	$this->Promoter->set('promoter', $user["id"]);
		        	$this->Promoter->set('movement_id', $movement["Movement"]["id"]);
		        	// $this->Promoter->set('', $user["id"]);
		        	if(!$this->Promoter->save())
		        	{
		        		return json_encode($response);
		        	}
		        }
		        
		        $ref_link = $this->ReferralUrl->get_referral_link($movement["Movement"]["id"], $user["id"]);

		        if($ref_link)
		        {
		        	// return ref link
		        	$response["meta"]['success'] = true;
		        	$response["response"]["ref_link"] = Router::url('/' . $ref_link, true);
		        }
		        else
		        {	
		        	$response["meta"] = false;
		        }

		        return json_encode($response);

	        }
	        else
	        {
	        	throw new NotFoundException();
	        }
        }
        else
        {
        	throw new NotFoundException();
        }
    }

    // Check if a vote has been confirmed
    public function check($id = null, $code = null) {
        $this->autoRender = false;

        if ($id && $code) {

            // Decode the code

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
        if(!$this->request->is('post'))
        {
            throw new NotFoundException();
        }
        
        $response = array();
        $response['meta']['success'] = false;
        $response['response'] = array();

        $user_id = null;

        $code = $this->request->data('code');

        if ($code) {
        	$supporter = $this->Supporter->findByCode($code);

        	if ($supporter) {
	        	$this->Supporter->read(null, $supporter["Supporter"]["id"]);
                $this->Supporter->set(array(
                    'confirmed' => 1,
                    'code' => null // uncomment for production!
                ));
                $this->Supporter->save();
		        
		        $response['meta']['success'] = true;
                $response['response'] = $supporter["Supporter"]["supporter"];
                $user_id = $supporter["Supporter"]["supporter"];

        	} else {
                $response['meta']['errors'][] = __('Please check that you have entered the correct code. This can be found at https://app-movement.com');
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
}
?>