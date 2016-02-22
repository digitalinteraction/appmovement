<?php
class CommentsController extends AppController {
    
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
    public $uses = array('Comment', 'CommentType', 'User', 'CommentVote');

	public function get($parent_id, $type_id) {
        
        $this->autoRender = false;
        $this->layout = 'ajax';

		if ($this->request->is('requested')) 
        {
            // if type id has been passed in then find comments by parent and type id
            // else find type id from comment types and use id to find comments by parent and comment type id
            if(is_numeric($type_id))
            {
                return $this->Comment->get_comments_by_parent_id_and_type_id($parent_id, $type_id);
                // return $this->Comment->find('all');   
            }
            else
            {
                // a type by name has been passed in e.g. 'design_task'
                // find id for this name
                $type = $this->CommentType->findByType($type_id);
                if($type)
                {
                    return $this->Comment->get_comments_by_parent_id_and_type_id($parent_id, $type["CommentType"]["id"]);
                }
                else
                {
                    return false;
                }
            }
        }

        $type = $this->CommentType->findByType($type_id);        

        // cant redirect to /design/:movement_id/task/:task_id because don't have movement id in this request :(
        if($type && $parent_id)
        {
            switch($type["CommentType"]["type"]){
                case "movement":
                    $this->redirect(array('controller' => 'movements', 'action' => 'view', $parent_id));
                    break;
                case "design_landing":
                    $this->redirect(array('controller' => 'tasks', 'landing' => 'index', $parent_id));
                    break;
                default:
                    $this->redirect(array('controller' => 'movements', 'action' => 'index'));
            }
        }
        else
        {
            $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }   
	}

    public function add() {

        $this->autoRender = false;
        $this->layout = 'ajax';

        $this->request->data['Comment']['user_id'] = $this->Auth->user('id');
        
        $response["meta"]["success"] = false;
        $response["response"] = null;

        if($this->Auth->loggedIn())
        {
            $user = $this->Auth->user();
            
            // need to check if they are allowed to comment on these things!
            $this->request->data["Comment"]["enabled"] = 1;
            $this->request->data["Comment"]["text"] = htmlspecialchars($this->request->data["Comment"]["text"]);
            $this->Comment->set($this->request->data);

            if($this->Comment->validates())
            {

                if($this->request->data["Comment"]["in_reply_to_comment_id"] > 0)
                {
                    $response["meta"]["is_reply"] = true;
                }
                else
                {
                    $response["meta"]["is_reply"] = false;
                    $this->request->data["Comment"]["in_reply_to_comment_id"] = NULL;
                }

                $response["meta"]["parent_id"] = $this->request->data["Comment"]["in_reply_to_comment_id"];

                // save the comment
                $comment = $this->Comment->save($this->request->data);
                $response["meta"]["success"] = true;
                $comment = $this->Comment->findById($comment["Comment"]["id"]);


                if($response['meta']['is_reply'])
                {
                    // get element output render (bit of a hack but it works)
                    $View = new View();
                    $response["response"] = $View->element('Comments/reply', array('reply' => $comment));
                }
                else
                {
                    // get element output render (bit of a hack but it works)
                    $View = new View();
                    $response["response"] = $View->element('Comments/comment', array('comment' => $comment));
                }
            }
            else
            {
                // echo 'no validate';
                $response["errors"] = $this->Comment->validationErrors;
            }            
        }
        else
        {
            $response["errors"]["invalid_auth"] = __("You are required to be logged in for this transaction");
        }

        return json_encode($response);
    }

    // Delete a comment
    public function delete($id = null) {

        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $response["meta"]["success"] = false;
        $response["response"] = null;

        $comment = $this->Comment->findById($id);

        if ($comment["Comment"]["user_id"] != $this->Auth->user('id')) {

            // User does not own comment
            $this->Session->setFlash(__('You do not have permission to do that'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        
        } else {
        
            // Mark comment as deleted
            $this->Comment->id = $id;
            $this->Comment->saveField('deleted', 1);

            $response["meta"]["success"] = true;
        
        }

        return json_encode($response);
    }

    public function vote($id = null) {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $response["meta"]["success"] = false;
        $response["response"] = null;

        if($this->Auth->loggedIn())
        {
            $user = $this->Auth->user();

            $this->request->data["CommentVote"]["user_id"] = $user['id'];

            $this->CommentVote->set($this->request->data);

            if($this->CommentVote->validates())
            {
                $this->CommentVote->save($this->request->data);
                $response["meta"]["success"] = true;
                $response["response"]["comment_id"] = $this->request->data["CommentVote"]["comment_id"];
                $response["response"]["up"] = $this->request->data["CommentVote"]["up"];
            }
            else
            {
                $response["errors"] = $this->CommentVote->validationErrors;
            }
        }


        return json_encode($response);
    }

    public function beforeFilter() {
        
        parent::beforeFilter();

        if ($this->request->is('requested') && $this->request->params['action'] == 'get') {
            $this->Auth->allow(array('get'));
        }
    }
}

?>