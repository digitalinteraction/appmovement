<?php

App::import('Vendor', 'resizer');

class TasksController extends AppController {
    public $helpers = array('Html', 'Form', 'Session', 'GoogleMap');
    public $components = array('Session', 'RequestHandler', 'Paginator');
    public $uses = array('Movement', 'Contribution', 'DesignTask', 'AppTypeDesignTask', 'MovementDesignTask', 'Comment', 'Vote', 'Supporter');

    public function landing($movement_id = NULL) {

        if ((!$movement_id) || (!$this->Movement->hasAny(array( 'Movement.id' => $movement_id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($movement_id);

        $design_tasks = $this->MovementDesignTask->get_design_tasks_by_movement_id($movement["Movement"]["id"]);

        $user_id = $this->Auth->user('id');

        foreach ($design_tasks as &$design_task) {
            
            $design_task['MovementDesignTask']['user_contribution_count'] = $this->Contribution->find('count', array(
                'conditions' => array(
                    'Contribution.user_id' => $user_id,
                    'Contribution.movement_design_task_id' => $design_task['MovementDesignTask']['id']
                    )
                ));
            
            $user_votes = $this->Vote->find('all', array(
                'conditions' => array(
                    'Vote.user_id' => $user_id
                    )
                ));

            $user_votes_array = array();

            foreach ($user_votes as $index => $vote)
            {
                array_push($user_votes_array, $vote["Vote"]["contribution_id"]);
            }
            
            $design_task['MovementDesignTask']['user_vote_count'] = $this->Vote->find('count', array(
                'conditions' => array(
                    'Vote.contribution_id' => $user_votes_array
                    )
                ));
            
            $design_task['MovementDesignTask']['new_contribution_count'] = $this->Contribution->find('count', array(
                'conditions' => array(
                    'Contribution.movement_design_task_id' => $design_task['MovementDesignTask']['id']
                    )
                ));
            
            $design_task['MovementDesignTask']['active_user_count'] = $this->Contribution->find('count', array(
                'conditions' => array(
                    'Contribution.movement_design_task_id' => $design_task['MovementDesignTask']['id']
                    ),
                'group' => array('Contribution.user_id')
                ));
        }

        $conditions = array(
            'Supporter.supporter' => $user_id,
            'Supporter.movement_id' => $movement_id,
            'Supporter.confirmed' => true
        );

        $is_supporter = ($this->Supporter->hasAny($conditions)) ? true : false;

        $this->set(array('movement' => $movement, 'design_tasks' => $design_tasks, 'is_supporter' => $is_supporter));
        
    } 

    public function view($movement_id = NULL, $task_id = NULL) {

        if ((!$movement_id) || (!$this->Movement->hasAny(array( 'Movement.id' => $movement_id )))) {
            // Movement does not exist
            $this->Session->setFlash(__('Could not find movement'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'index'));
        }

        $movement = $this->Movement->get_movement($movement_id);

        if ($movement["Movement"]["phase"] <= 0) {
            // Movement has not reached the design phase
            $this->Session->setFlash(__('Movement has not yet reached the design phase'), 'default', array(), 'bad');
            return $this->redirect(array('controller' => 'movements', 'action' => 'view', $movement_id));
        }

        $design_task = $this->MovementDesignTask->get_design_task_by_id($task_id);

        if ($movement["Movement"]["phase"] >= 2) {
            $contributions = $this->Contribution->find('all', array(
            'conditions' => array(
                'Contribution.movement_design_task_id' => $task_id
                ),
            'order' => 'Contribution.votes DESC'
            ));
        } else {
            $contributions = $this->Contribution->find('all', array(
            'conditions' => array(
                'Contribution.movement_design_task_id' => $task_id
                )
            ));
        }

        foreach ($contributions as &$contribution) {
            
            if ($this->Vote->hasAny(array('Vote.user_id' => $this->Auth->user('id'), 'Vote.contribution_id' => $contribution["Contribution"]["id"], 'Vote.flag' => 0))) {
              
                if ($this->Vote->hasAny(array('Vote.user_id' => $this->Auth->user('id'), 'Vote.contribution_id' => $contribution["Contribution"]["id"], 'Vote.vote_up' => 1, 'Vote.flag' => 0))) {
                    
                    $contribution["Contribution"]["user_vote"] = 1;
                    
                } else {

                    $contribution["Contribution"]["user_vote"] = -1;

                }

            } else {

                    $contribution["Contribution"]["user_vote"] = 0;

            }
        }

        $user_id = $this->Auth->user('id');

        $conditions = array(
            'Supporter.supporter' => $user_id,
            'Supporter.movement_id' => $movement_id,
            'Supporter.confirmed' => true
        );

        $is_supporter = ($this->Supporter->hasAny($conditions)) ? true : false;

        $this->set(array('movement' => $movement, 'parent_id' => 1, 'design_task' => $design_task, 'contributions' => $contributions, 'is_supporter' => $is_supporter));
    }

    public function isAuthorized($user) {

        // Check if user is a supporter

        $movement_id = (int) $this->request->params['pass'][0];

        $movement = $this->Movement->findById($movement_id);
        
        if($movement)
        {
            if ($movement["Movement"]["phase"] == 0) {
                $this->Session->setFlash(__('Movement has not yet reached the design phase'), 'default', array(), 'bad');
                return $this->redirect(array('controller' => 'movements', 'action' => 'view', $movement_id));
            } else if ($movement["Movement"]["phase"] == -1) {
                return $this->redirect(array('controller' => 'movements', 'action' => 'view', $movement_id));
            }
        }

        return parent::isAuthorized($user);
    }

    public function beforeFilter() {

        parent::beforeFilter();
        
        $this->Auth->allow('landing', 'view');

    }

}
?>