<?php
class MobileController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler', 'Paginator');
    public $uses = array('Movement', 'Survey', 'SurveyResult');

    public function about($movement_id = NULL) {

        $this->layout = 'stripped';

        if ((!$movement_id) || (!$this->Movement->hasAny(array( 'Movement.id' => $movement_id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($movement_id);

        $this->set(array('movement' => $movement));
        
    }

    public function terms($movement_id = NULL) {

        $this->layout = 'stripped';

        if ((!$movement_id) || (!$this->Movement->hasAny(array( 'Movement.id' => $movement_id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($movement_id);

        $this->set(array('movement' => $movement));
        
    }

    public function survey($movement_id = NULL) {
        
        
        if ($this->request->is('get')) {

            $this->layout = 'stripped';

            if ((!$movement_id) || (!$this->Movement->hasAny(array( 'Movement.id' => $movement_id )))) {
                // Movement does not exist
                $this->Session->setFlash('Could not find movement', 'default', array(), 'bad');
                return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
            }

            $movement = $this->Movement->get_movement($movement_id);

            $survey = $this->Survey->findByMovementId($movement_id);

            $this->set(array('movement' => $movement, 'survey' => $survey));
        }

        if ($this->request->is('post')) {

            // Get survey
            $survey = $this->Survey->findByMovementId($movement_id);

            // Save survey
            $this->SurveyResult->create();
            $this->request->data["SurveyResult"]["movement_id"] = $movement_id;
            $this->request->data["SurveyResult"]["survey_id"] = $survey["Survey"]["id"];
            $survey_result = $this->SurveyResult->save($this->request->data);

            return $this->redirect(array('controller' => 'mobile', 'action' => 'survey_complete'));
        }
    }

    public function survey_complete() {

        $this->layout = 'stripped';
        
    }

    public function beforeFilter() {

        parent::beforeFilter();
        
        $this->Auth->allow('about', 'terms', 'survey', 'survey_complete');

    }

}
?>