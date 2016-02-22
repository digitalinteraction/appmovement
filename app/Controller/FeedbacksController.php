<?php

App::import('Vendor', 'resizer');

class FeedbacksController extends AppController {    
    public $uses = array('Feedback');

    public function submit(){
    	$this->autoRender = false;
        $this->layout = 'ajax';

        $response["meta"]["success"] = true;
        $response["response"] = true;

        $user_id = null;
        $url = null;

        if($this->Auth->user('id'))
        {
        	$user = $this->Auth->user();
        	$user_id = $user['id'];
        }

        if(!array_key_exists('email', $this->request->data) ? $email = null : $email = $this->request->data['email']);
        if(!array_key_exists('comment', $this->request->data) ? $comment = null : $comment = $this->request->data['comment']);
        if(!array_key_exists('url', $this->request->data) ? $url = null : $url = $this->request->data['url']);

        if($comment)
        {
        	$this->Feedback->create();
	        $this->Feedback->set('email', $email);
	        $this->Feedback->set('comment', $comment);
	        $this->Feedback->set('user_id', $user_id);
            $this->Feedback->set('url', $url);
	        $this->Feedback->save();
        }

        return json_encode($response);
    }

    public function beforeFilter() {
        $this->Auth->allow();
    }
}
?>
