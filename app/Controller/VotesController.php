<?php
class VotesController extends AppController {

    public $uses = array('Vote', 'Contribution', 'User');

    public function vote() {
        $this->autoRender = false;
        $this->layout = 'ajax';

        $response["meta"]["success"] = false;
        $response["response"] = null;

        if ($this->Auth->loggedIn())
        {
            $user = $this->Auth->user();

            $user_id = $this->Auth->user('id');

            $vote_up = $this->request->data('vote_up');

            $contribution_id = $this->request->data('contribution_id');

            $contribution = $this->Contribution->findById($contribution_id);
            
            if ($contribution["Contribution"]["user_id"] == $user_id) {

                $response["meta"]["success"] = false;
                $response["errors"] = __("You cannot vote on your own contribution");
                return json_encode($response);
                
            }

            if ($this->Vote->hasAny(array( 'Vote.contribution_id' => $contribution_id, 'Vote.user_id' => $user_id, 'Vote.vote_up' => $vote_up, 'Vote.flag' => 0 ))) {

                $response["meta"]["success"] = false;
                $response["errors"] = __("You cannot vote more than once");
                return json_encode($response);

            }

                        // $user_votes = $this->Vote->findByUserIdAndContributionId(array('user_id' => $user_id, 'contribution_id' => $contribution_id));
            $previous_user_vote = $this->Vote->find('first', array(
                                                'conditions' => array(
                                                    'user_id' => $user_id,
                                                    'contribution_id' => $contribution_id,
                                                    'flag' => 0
                                                ),
                                                'order' => array('created desc')
                                            ));

            // Save vote
            $this->Vote->updateAll(array('flag' => 1), array('Vote.user_id' => $user_id, 'Vote.contribution_id' => $contribution_id));

            $flag = 0;

            // if the user has previously voted, check if we need to discard this vote so that it zeros the user's votes
            // by setting all vote flags to 1
            if($previous_user_vote)
            {
                if($previous_user_vote['Vote']['vote_up'] != $vote_up)
                {
                    $flag = 1;
                }
            }

            $data = array('user_id' => $user_id, 'vote_up' => $vote_up, 'contribution_id' => $contribution_id, 'flag' => $flag);
            $this->Vote->save($data);

            $vote = $this->Vote->save($this->request->data);
            $contribution = $this->Contribution->findById($vote["Vote"]["contribution_id"]);

            $response["meta"]["success"] = true;
            $response["response"] = ($contribution["Contribution"]["up_votes"] - $contribution["Contribution"]["down_votes"]);
            return json_encode($response);
        }
        else
        {
            $response["meta"]["success"] = false;
            $response["errors"] = __("You must be logged in to vote");
            return json_encode($response);
        }
    }

}
?>