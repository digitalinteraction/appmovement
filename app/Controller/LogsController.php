<?php

App::import('Vendor','HashIds');
App::import('Vendor', 'resizer');
App::import('Vendor', 'image_uploader');

class LogsController extends AppController {
    public $helpers = array('Html', 'Session');
    public $components = array('Session', 'RequestHandler');
    public $uses = array('ShareLinkButtonClick', 'ShareLink');

    // http://localhost/movement/logs/share_button/77/65vx/facebook

    // logs when a share button is clicked
    public function share_button($movement_id, $code, $type)
    {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $user_id = $this->Auth->user('id');
        
        // fetch the url
        $share_link_record = $this->ShareLink->find('first', array(
                                'conditions' => array(
                                    array('parent_id' => $movement_id),
                                    array('code' => $code)
                                )
                            ));

        // if the user is authenticated and has a ref link record, record it otherwise record the click unauthenticated without related ref_link record
        
        $this->ShareLinkButtonClick->create();

        if($share_link_record)
        {
            // log click    
            $this->ShareLinkButtonClick->set('share_link_id', $share_link_record['ShareLink']['id']);
        }   

        $this->ShareLinkButtonClick->set('movement_id', $movement_id);
        $this->ShareLinkButtonClick->set('user_id', $user_id);
        $this->ShareLinkButtonClick->set('site_user_id', $this->Session->read('site_user.id'));
        $this->ShareLinkButtonClick->set('type', $type);
        $this->ShareLinkButtonClick->save();
    }

    function beforeFilter(){       
        $this->Auth->allow(array('share_button'));
    }

}
?>
