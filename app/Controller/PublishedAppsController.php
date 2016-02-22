<?php
class PublishedAppsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Email', 'Session', 'RequestHandler');
    public $uses = array('User', 'Venue', 'Review', 'Photo', 'Movement', 'PublishedApp', 'SiteUsageLog');
    
    public $layout = 'mini';

    // Index
    public function index() {

        $this->layout = 'default';

        $published_apps = $this->PublishedApp->find('all', array(
            'conditions' => array(
                'hidden' => false
                )
            ));
        
        $this->set(array('published_apps' => $published_apps, 'title_for_layout' => 'Published Apps'));
    }

    // About
    public function about($app_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        $this->set(array('published_app' => $published_app, 'title_for_layout' => 'About'));
    }

    // Download
    public function download($app_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        $this->set(array('published_app' => $published_app, 'title_for_layout' => 'Download'));
    }

    // Review
    public function review($app_id, $review_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        $review = $this->Review->findById($review_id);
        $venue = $this->Venue->find('first', array(
            'conditions' => array(
                'Venue.id' => $review["Review"]["venue_id"]
                ),
            'contain' => array('Review' => array(
                'conditions' => array('Review.deleted' => 0),
                'User' => array('fullname')
                ))
            ));

        $user_id = $review["Review"]["user_id"];
        
        Configure::write('identifierDB', 'appmovement'); 
        $user = $this->User->findById($user_id);

        $this->Review->id = $review_id;

        $neighbours = $this->Review->find('neighbors', array(
            'order' => 'id DESC'
        ));

        $this->set(array('neighbours' => $neighbours, 'venue' => $venue, 'review' => $review, 'user' => $user, 'published_app' => $published_app, 'title_for_layout' => 'Review'));
    }

    // Venue
    public function venue($app_id, $venue_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        $venue = $this->Venue->find('first', array(
            'conditions' => array(
                'Venue.id' => $venue_id
                ),
            'contain' => array('Review' => array(
                'conditions' => array('Review.deleted' => 0),
                'User' => array('fullname'), 'Photo'))
            ));

        $this->Venue->id = $venue_id;

        $neighbours = $this->Venue->find('neighbors', array(
            'order' => 'id DESC',
            'contain' => array('Review' => array('User' => array('fullname'), 'Photo'))
        ));

        $this->set(array('neighbours' => $neighbours, 'venue' => $venue, 'published_app' => $published_app, 'title_for_layout' => 'Venue'));
    }

    // Map
    public function map($app_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        $reviews = $this->Review->find('all', array('conditions' => array()));

        $this->set(array('published_app' => $published_app, 'reviews' => $reviews, 'title_for_layout' => 'Map'));
    }

    // Stats
    public function stats($app_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        // Get active users

        $active_user_count = $this->SiteUsageLog->find('all', array(
            'conditions' => array(
                'SiteUsageLog.url LIKE' => '%' . $published_app["PublishedApp"]["app_identifier"] . '%'
                ),
            'group' => array('SiteUsageLog.user_ip')
            ));

        
        // Reviews

        $reviews = $this->Review->find('all', array( 'conditions' => array( "Review.created >" => date('Y-m-d', strtotime("-21 days")) ) ));

        $review_counts = $this->Review->find('all', array(
                    'conditions' => array(
                    ),
                    'fields' => array('COUNT(Review.id) as count', 'DATE(Review.created) as day'),
                    'group' => array('DATE(Review.created)')
                ));

        $review_counts = $this->get_data("Review", $reviews, $review_counts);

        $total_reviews_count = $this->Review->find('count');

        // Venues

        $venues = $this->Venue->find('all', array( 'conditions' => array( "Venue.created >" => date('Y-m-d', strtotime("-21 days")) ) ));

        $venue_counts = $this->Venue->find('all', array(
                    'conditions' => array(
                    ),
                    'fields' => array('COUNT(Venue.id) as count', 'DATE(Venue.created) as day'),
                    'group' => array('DATE(Venue.created)')
                ));

        $venue_counts = $this->get_data("Venue", $venues, $venue_counts);

        $total_venues_count = $this->Venue->find('count');

        // Users

        $users = $this->User->find('all', array( 'conditions' => array( "User.created >" => date('Y-m-d', strtotime("-21 days")) ) ));

        $user_counts = $this->User->find('all', array(
                    'conditions' => array(
                    ),
                    'fields' => array('COUNT(User.id) as count', 'DATE(User.created) as day'),
                    'group' => array('DATE(User.created)')
                ));

        $user_counts = $this->get_data("User", $users, $user_counts);

        $total_users_count = $this->SiteUsageLog->find('count', array(
            'conditions' => array(
                'action' => 'version',
                'url LIKE' => '%' . $published_app['PublishedApp']['app_identifier'] . '%'
                ),
            'group' => 'site_user_id'
            ));

        ini_set('memory_limit', '-1');

        $test_total_users = $this->SiteUsageLog->find('all', array(
            'conditions' => array(
                'url LIKE' => '%api/geolocation/' . $published_app['PublishedApp']['app_identifier'] . '/venues%',
                'action' => 'venues',
                'parameters LIKE' => '%user_id":"%'
                )
            ));

        $test_user_ids = [];

        foreach ($test_total_users as $index => $test_user) {
            $test_encoded = json_decode($test_user["SiteUsageLog"]["parameters"]);
            array_push($test_user_ids, $test_encoded->user_id);
        }

        $test_user_ids = array_unique($test_user_ids);

        $total_users_count = count($test_user_ids);

        $this->set(compact('active_user_count', 'venues', 'reviews', 'review_counts', 'venue_counts', 'user_counts', 'total_reviews_count', 'total_venues_count', 'total_users_count'));

        $this->set(array('published_app' => $published_app, 'title_for_layout' => 'Stats'));
    }

    // Photos
    public function photos($app_id) {

        $published_app = $this->PublishedApp->findById($app_id);
        Configure::write('identifierDB', $published_app["PublishedApp"]["app_identifier"]); 

        $photos = $this->Photo->find('all', array('conditions' => array(
            'Photo.flag' => 0
            )));

        $this->set(array('published_app' => $published_app, 'photos' => $photos, 'title_for_layout' => 'Photos'));

    }

    public function get_data($class = null, $items = null, $item_counts = null) {

        $period = new DatePeriod(
             new DateTime(date('Y-m-d H:i:s', strtotime($items[0][$class]["created"]))),
             new DateInterval('P1D'),
             new DateTime(date("Y-m-d H:i:s", strtotime($items[count($items) - 1][$class]["created"])))
        );

        $data = array();

        $items_per_day = array();

        foreach($item_counts as $item_count)
        {
            $items_per_day[$item_count[0]["day"]] = $item_count[0]["count"];
        }

        $day_count = 1;
        $item_count = 0;

        $data[0] = 0;

        foreach($period as $date)
        {
            if(array_key_exists($date->format('Y-m-d'), $items_per_day))
            {
                $data[$day_count] = $item_count + (int) $items_per_day[$date->format('Y-m-d')];

                $item_count = $data[$day_count];
            }
            else
            {
                $data[$day_count] = $item_count;
            }

            $day_count++;
        }

        return json_encode($data);
    }

    public function beforeFilter() {

        parent::beforeFilter();
        
        $this->Auth->allow('index', 'about', 'download', 'review', 'venue', 'map', 'stats', 'photos', 'get_data');
    }
}
?>