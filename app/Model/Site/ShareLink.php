<?php
App::import('Vendor','HashIds');

class ShareLink extends AppModel {

	public $name = 'ShareLink';

    public $belongsTo = 'ShareLinkType';

    public $hasMany = array('ShareLinkClicks');

    public function generate_review_share_link($review_id, $published_app_id, $user_id, $return_full_url = true)
    {
        return $this->generate_share_link(3, $review_id, $published_app_id, $user_id, $return_full_url);
    }

    public function generate_movement_share_link($movement_id, $user_id = NULL, $site_user_id, $return_full_url = false)
    {
        return $this->generate_share_link(1, $movement_id, NULL, $user_id, $site_user_id, $return_full_url);
    }

    public function generate_share_link($share_link_type_id, $parent_id, $published_app_id, $user_id, $site_user_id, $return_full_url = true)
    {
    	// User can be in 4 states:
    	// Unauthenticated, viewing first time
    	// Unauthenticated, viewing nth time
    	// Recently Authenticated, viewing 2nd time
    	// Authenticated, viewing first time
    	// Authenticated, viewing nth time


    	// Check if user has Recently Authenticated (user_id + site_user_id)
        // check we haven't already generated a share url for this dataset
        $share_link = $this->find('first', array(
                                'conditions' => array(
                                    'user_id' => $user_id,
                                    'site_user_id' => $site_user_id,
                                    'share_link_type_id' => $share_link_type_id,
                                    'parent_id' => $parent_id,
                                    'published_app_id' => $published_app_id
                                )
                            ));

        // user might have generated the share link before authenticating. Need to find the share_link record by the site_user_id
        if(!$share_link)
        {
        	// Check if we have a site_user_id (Unauthenticated)
        	// check if this user has a site_user_id cookie, if it does use that to find an unauthenticated auth url
        	if($site_user_id)
        	{
        		$share_link = $this->find('first', array(
        		                    'conditions' => array(
        		                        'site_user_id' => $site_user_id,
        		                        'share_link_type_id' => $share_link_type_id,
        		                        'parent_id' => $parent_id,
        		                        'published_app_id' => $published_app_id
        		                    )
        		                ));
        	}

            // if we have a share link without a user_id but this site_user_id has a share link associated with it
            // link them both together
            if($share_link && $user_id)
            {
                $this->id = $share_link['ShareLink']['id'];
                $this->set('user_id', $user_id);
                $this->save();
            }

        }

        // // return if we already have record return it. Check if the the $return_full_url was set to true
        if($share_link)
        {
            if($return_full_url)
            {
                return Configure::read('short_url_path') . $share_link['ShareLink']['code'];
            }
            else
            {
                return $share_link['ShareLink']['code'];
            }
        }

        // create new share link using these parameters
        $this->create();
        $this->set('code', null);
        $this->set('share_link_type_id', $share_link_type_id);
        $this->set('user_id', $user_id);
        $this->set('site_user_id', $site_user_id);
        $this->set('parent_id', $parent_id);
        $this->set('published_app_id', $published_app_id);
        $this->save();

        $hashids = new HashIds(Configure::read('urlSalt'), Configure::read('urlHashMinLength'), Configure::read('hashAlphabet'));
        $hash = $hashids->encrypt($this->id);

        $this->set('code', $hash);
        $this->save();

        if ($return_full_url) {
            return Configure::read('short_url_path') . $hash;
        } else {
            return $hash;
        }
    }
}
?>
