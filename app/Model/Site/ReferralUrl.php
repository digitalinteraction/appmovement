<?php
class ReferralUrl extends AppModel {

	public $name = 'ReferralUrl';

	public $hasMany = array('ReferralUrlClicks');

	// returns referral link for a given movement_id for the current authed user 
	// if ref link does not exist a new link will be created and returned
	public function get_referral_link($movementId, $userId)
	{
		App::Import('Model', 'User');
		$this->User = new User;

		$user = $this->User->findById($userId);

        if($user)
        {
            $response['meta'] = true;
            $referral = $this->findByMovementIdAndUserId($movementId, $user['User']['id']);

            if($referral)
            {
                return $referral['ReferralUrl']['short_url'];
            }
            else
			{
                $this->create();
                $this->set('short_url', null);
                $this->set('movement_id', $movementId);
                $this->set('user_id', $userId);
                $this->save();
                
                $hashids = new HashIds(Configure::read('urlSalt'), Configure::read('urlHashMinLength'), Configure::read('hashAlphabet'));
                $hash = $hashids->encrypt($this->id);
                
                $this->set('short_url', $hash);
                $this->save();

                return $hash;
            }
        }
        else
        {
        	return false;
        }
	}
}
?>