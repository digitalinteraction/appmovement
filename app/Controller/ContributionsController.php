<?php

App::import('Vendor', 'resizer');

class ContributionsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler', 'Paginator');
    public $uses = array('Movement', 'Contribution', 'ContributionType', 'ReportType', 'ContributionReport');

    public function add() {

        $this->autoRender = false;
        $this->layout = 'ajax';

        $response["meta"]["success"] = false;
        $response["response"] = null;

    	if ($this->request->is('post')) 
    	{
    		$this->request->data['Contribution']['user_id'] = $this->Auth->user('id');
    		if($this->Auth->loggedIn())
        	{	
        		$this->Contribution->create();

                if($this->Contribution->save($this->request->data))
	    		{
                    $response["meta"]["success"] = true;

                    $view = new View($this, false);
                    $view->viewPath = 'Elements';

                    $contribution = $this->Contribution->find('first', array(
                        'conditions' => array('Contribution.id' => $this->Contribution->getLastInsertId())
                    ));

                    $view->set('item', $contribution);
                    
                    $contribution_type = $this->ContributionType->get_contribution_type_by_id_or_name($this->request->data["Contribution"]["contribution_type_id"]);

                    $view_output = $view->render('Contributions/' . $contribution_type["ContributionType"]["type"]);
                    $response["response"] = $view_output;
	    		}
	    		else
	    		{
	    			$response["errors"] = $this->Contribution->validationErrors;
	    		}
        	}
    	}

        return json_encode($response);
    }

    public function add_file() {
        if (!$this->request->is('post')) {
            throw new NotFoundException();      
        }

        $errors = "";

        if(!$this->Auth->loggedIn())
        {
            $this->redirect('login');
        }

        if(!array_key_exists('file', $this->request->data['Contribution']))
        {
            $errors = __("No file has been submitted");
        }
        else
        {
            if(!array_key_exists('file', $this->request->data['Contribution']))
            {
                $errors = __("Error in file submission");
            }
            else
            {
                $allowedExts = array("gif", "jpeg", "jpg", "png");
                $filename = $this->request->data['Contribution']['file']['name'];
                $extension = pathinfo($filename, PATHINFO_EXTENSION);

                if ((($this->request->data['Contribution']['file']["type"] == "image/gif")
                || ($this->request->data['Contribution']['file']["type"] == "image/jpeg")
                || ($this->request->data['Contribution']['file']["type"] == "image/jpg")
                || ($this->request->data['Contribution']['file']["type"] == "image/png"))
                && ($this->request->data['Contribution']['file']["size"] <= 3000000)
                && ($this->request->data['Contribution']['file']["size"] >= 2000)
                && in_array($extension, $allowedExts))
                {

                    // Generate url and append extension
                    $photo_url = $this->Auth->user('id') . '_' . substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8) . '.' . $extension;
                    $temp_url =  $this->request->data['Contribution']['file']['tmp_name'];
                    $img_details = getimagesize($this->request->data['Contribution']['file']['tmp_name']);

                    // correct mime type and 30MB or less
                    if (move_uploaded_file($temp_url, 'img/contributions/' . $photo_url))
                    {
                        // File uploaded - proceed to resize
                        $image = new ResizeImage();
                        
                        $target_path = 'img/contributions/' . $photo_url;

                        // Thumb Profile
                        $image->load($target_path);
                        $image->resizeToWidth(80);
                        $image->save("img/contributions/thumb/" . $photo_url);
                        
                        // Small Profile
                        $image->load($target_path);
                        $image->resizeToWidth(240);
                        $image->save("img/contributions/small/" . $photo_url);
                        
                        // Medium Profile
                        $image->load($target_path);
                        $image->resizeToWidth(420);
                        $image->save("img/contributions/medium/" . $photo_url);
                        
                        // Large Profile
                        $image->load($target_path);
                        $image->resizeToWidth(960);

                        $image->save("img/contributions/large/" . $photo_url);

                        // Delete original upload
                        unlink("img/contributions/" . $photo_url);

                        $this->Contribution->create();
                        $this->Contribution->set('user_id', $this->Auth->user('id'));
                        $this->Contribution->set('contribution_type_id', $this->request->data["Contribution"]["contribution_type_id"]);
                        $this->Contribution->set('movement_design_task_id', $this->request->data["Contribution"]["movement_design_task_id"]);
                        $this->Contribution->set('data', json_encode(array('url' => $photo_url)));
                        
                        if ($this->Contribution->validates()) {
                            $this->Contribution->save();
                            $this->Session->setFlash(__("Contribution has been submitted"), 'default', array(), 'good');
                        }
                        else
                        {
                            $errors = $this->Contribution->validationErrors;
                        }
                    }
                }
                else
                {
                    $errors = __('Please ensure that your image is in the correct format (jpg, jpeg, png) and is between 2KB - 3MB');
                }
            }
        }

        if ($errors) {
            $this->Session->setFlash($errors, 'default', array(), 'bad');
        }   

        if(array_key_exists("movement_id", $this->request->data["Contribution"]))
        {
            if(array_key_exists("movement_design_task_id", $this->request->data["Contribution"]))
            {
                return $this->redirect('/design/' .  $this->request->data["Contribution"]["movement_id"] . '/task/' . $this->request->data["Contribution"]["movement_design_task_id"]);
            }
        }
        else
        {
            $this->redirect('/');
        }
    }

    // Delete a contribution
    public function delete($id = null) {

        $this->autoRender = false;
        $this->layout = 'ajax';
        
        $response["meta"]["success"] = false;
        $response["response"] = null;

        $contribution = $this->Contribution->findById($id);

        if ($contribution["Contribution"]["user_id"] != $this->Auth->user('id')) {

            // User does not own contribution
            $this->Session->setFlash(__('You do not have permission to do that'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        
        } else {
        
            // Mark contribution as deleted
            $this->Contribution->id = $id;
            $this->Contribution->saveField('deleted', 1);

            $response["meta"]["success"] = true;
        
        }

        return json_encode($response);
    }

    public function report() {
        $this->autoRender = false;
        $this->layout = 'ajax';
        $response["meta"]["success"] = false;
        $response["response"] = null;

        $contribution_id = $this->request->data('id');
        $report_type_id = $this->request->data('report_type_id');

        $response['errors'] = array();

        if(!$contribution_id) { $response['errors'][] = __("Contribution id required"); }
        if($report_type_id == null) { $response['errors'][] = __("No Report Type id"); }


        if ($this->Auth->user('id')) 
        {
            if(count($response['errors']) == 0)
            {
                $contribution = $this->Contribution->findById($contribution_id);

                if($contribution)
                {
                    $report_type = $this->ReportType->findById($report_type_id);

                    if($report_type)
                    {
                        $this->ContributionReport->create();
                        $this->ContributionReport->set('contribution_id', $contribution_id);
                        $this->ContributionReport->set('user_id', $this->Auth->user('id'));
                        $this->ContributionReport->set('report_type_id', $report_type_id);
                        $this->ContributionReport->save();

                        $response['meta']['success'] = true;
                        $response['response'] = $this->ContributionReport->id;
                    }
                    else
                    {
                        $response['errors'][] = __("Report type id incorrect");
                    }
                }
            }
        }

        return json_encode($response);
    }        
}
?>