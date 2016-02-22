<?php
ini_set('max_execution_time', 600);

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

App::import('Vendor', 'resizer');
App::import('Vendor', 'S3');
App::import('Vendor', 'ResponseObject');
App::uses('HttpSocket', 'Network/Http');
 
// AWS access info
if (!defined('awsAccessKey')) define('awsAccessKey', Configure::read('aws.awsAccessKey'));
if (!defined('awsSecretKey')) define('awsSecretKey', Configure::read('aws.awsSecretKey'));

class GeolocationController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Email', 'Session', 'RequestHandler', 'ApiSession');
    public $uses = array('User', 'ApiSession', 'Venue', 'Review', 'Transaction', 'Like', 'Report', 'Photo', 'Movement', 'PublishedApp', 'ShareLink');

    public $autoRender = false;
    public $layout = 'json';

    private $api_key = Configure::read('geolocation.api_key');

    public $response; // Response object

    private $foursquare_client_id = Configure::read('geolocation.foursquare_client_id');
    private $foursquare_client_secret = Configure::read('geolocation.foursquare_client_secret');
    private $foursquare_version = Configure::read('geolocation.foursquare_version');

    // TEST AREA

    public function test()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check session key
        $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
    }

    // TEST AREA

    public function checkKey($api_key)
    {
        if ($api_key != $this->api_key)
        {
            array_push($this->response->errors, 'API Key Invalid');
            $this->response->output();
        }
    }

    // DUPLICATED FROM AuthController
    public function checkSessionKey($session_key, $user_id)
    {
        if (!$this->ApiSession->checkSessionKey($session_key, $user_id))
        {
            array_push($this->response->errors, 'Session Key Invalid');
            $this->response->meta['session_valid'] = false;
            $this->response->output();
        }
    }
    
    /**
     * @api {get} /api/geolocation/:identifier/venues /venues - Get nearby venues
     * @apiName Venues
     * @apiGroup Geolocation
     * @apiDescription This endpoint provides an array of venue objects for a given latitude and longitude. <br/><br/><strong>It does not provide a complete venue object and therefore you must call /venue to get the full information.</strong>. <br/><br/>The venue photos are not included in this request and will be an empty array.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/venues
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    latitude         Latitude of location (e.g. 54)
     * @apiParam    {String}    longitude        Longitude of location (e.g. -1.3)
     * @apiParam    {String}    bounding_box     Bounding box of map on screen (e.g. top_left_latitude, top_left_longitude, top_right_latitude, top_right_longitude, bottom_right_latitude, bottom_right_longitude, bottom_left_latitude, bottom_left_longitude)
     * @apiParam    {String}    zoom_level       Zoom level of the map on screen (e.g. 17)
     * @apiParam    {String}    session_key      Session key of authenticated user
     * @apiParam    {String}    api_key          API Key
     *
     * @apiSuccess {Venue}  Venue                            Venue object
     * @apiSuccess {String} Venue.id                         Venue id in db
     * @apiSuccess {String} Venue.user_id                    User id of user who added the venue
     * @apiSuccess {String} Venue.foursquare_id              Foursquare id
     * @apiSuccess {String} Venue.name                       Venue name (max 300 chars)
     * @apiSuccess {String} Venue.latitude                   Latitude of place
     * @apiSuccess {String} Venue.longitude                  Longitude of place
     * @apiSuccess {String} Venue.address                    Address line 1 (Generally street number and if possible, house number)
     * @apiSuccess {String} Venue.city                       Address line 2, city name
     * @apiSuccess {String} Venue.state                      Address line 3, State, District of Region (e.g. Tyne and Wear)
     * @apiSuccess {String} Venue.postcode                   Address line 4, Postcode, Zip code
     * @apiSuccess {String} Venue.country                    Address line 5, Country of location
     * @apiSuccess {String} Venue.flag                       Hide/Unhide venue from search result. This is to "remove" the venue from the map.
     * @apiSuccess {String} Venue.created                    Created datetime
     * @apiSuccess {String} Venue.modified                   Modified datetime
     * @apiSuccess {String} Venue.review_count               Count of reviews for the given venue
     * @apiSuccess {String} Venue.average_review             Average rating of all the reviews
     * @apiSuccess {String} Venue.q1_average                 Q1 Average rating from all the reviews
     * @apiSuccess {String} Venue.q2_average                 Q2 Average rating from all the reviews
     * @apiSuccess {String} Venue.q3_average                 Q3 Average rating from all the reviews
     * @apiSuccess {String} Venue.q4_average                 Q4 Average rating from all the reviews
     * @apiSuccess {String} Venue.distance                   Distance from requested location
     * @apiSuccess {Review[]}  Venue.Review                  Array of review objects for the place (see below for the object structure)
     *
     * @apiSuccess {Review} Review                           Review Object
     * @apiSuccess {String} Review.id                        Review id in db
     * @apiSuccess {String} Review.venue_id                  Venue id of the reviewed venue
     * @apiSuccess {String} Review.user_id                   User id who left the review
     * @apiSuccess {String} Review.q1                        Q1 rating by user
     * @apiSuccess {String} Review.q2                        Q2 rating by user
     * @apiSuccess {String} Review.q3                        Q3 rating by user
     * @apiSuccess {String} Review.q4                        Q4 rating by user
     * @apiSuccess {String} Review.review_text               Review text of the review left by the user
     * @apiSuccess {String} Review.photo                     NOT SURE
     * @apiSuccess {String} Review.contribution_count        NOT SURE
     * @apiSuccess {String} Review.like_count                Number of likes that the review has received
     *
     * @apiSuccess {User}   User Object                      User object of the reviewing user
     * @apiSuccess {String} User.id                          Id of user leaving the review
     * @apiSuccess {String} User.username                    Username of reviewing user
     * @apiSuccess {String} User.fullname                    Full  name of reviewing user
     * @apiSuccess {String} User.photo                       Profile photo of user (i.e. andy.png). Needs a path to be constructed in order to access this image
     *
     * @apiSuccess {Photo[]}  Photo Object Array             Array of Photo objects in the review
     * @apiSuccess {Photo}    Photo                          Photo object
     * @apiSuccess {String}   Photo.id                       Id of photo in the db
     * @apiSuccess {String}   Photo.review_id                Review photo associated with
     * @apiSuccess {String}   Photo.venue_id                 Venue id associated with the review
     * @apiSuccess {String}   Photo.user_id                  User id that posted the photo
     * @apiSuccess {String}   Photo.filename                 Name of the photo (i.e. 1283123iu.jpg) Needs a path to be constructed in order to access this image
     * @apiSuccess {String}   Photo.created                  Created datetime of photo
     * @apiSuccess {String}   Photo.modified                 Modified datetime of photo
     * @apiSuccess {String}   Photo.foursquare               Boolean to show if the photo has been requested from the Foursquare API
     *
     * @apiSuccess {url}      Url                            Url object containing the different sized photos available
     * @apiSuccess {String}   Url.large                      Large photo (640x480)
     * @apiSuccess {String}   Url.medium                     Medium photo (320x240)
     * @apiSuccess {String}   Url.small                      Small photo (160x120)
     * @apiSuccess {String}   Url.thumb                      Thumb photo (80x60)
     *
     * @apiSuccess {String}   liked                          Shows if the current user making the request has "Liked" this specific review
     * 
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     *{
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": [
     *        {
     *            "Venue": {
     *                "id": "162",
     *                "user_id": "1490",
     *                "foursquare_id": "",
     *                "name": "Elba Park",
     *                "latitude": "54.8570940000",
     *                "longitude": "-1.4968190000",
     *                "address": "Blind Lane",
     *                "city": "Houghton le Spring",
     *                "state": "Tyne and Wear",
     *                "postcode": "DH4 5JN",
     *                "country": "United Kingdom",
     *                "flag": "0",
     *                "created": "2015-06-07 20:32:36",
     *                "modified": "2015-06-07 20:32:36",
     *                "review_count": "1",
     *                "average_review": "3.5",
     *                "q1_average": "4.0",
     *                "q2_average": "5.0",
     *                "q3_average": "3.0",
     *                "q4_average": "2.0",
     *                "distance": "9.046"
     *            },
     *            "Review": [
     *                {
     *                    "id": "122",
     *                    "venue_id": "162",
     *                    "user_id": "1490",
     *                    "q1": "4",
     *                    "q2": "5",
     *                    "q3": "3",
     *                    "q4": "2",
     *                    "review_text": "Set on the old Lambton Coke Works, Elba Park is reclaimed land offering wetlands and wildlife around a huge walkable area. There is a housing development but there is plenty of space away from this for some great air time. Lovely views of Penshaw Monument and you have amazing 360degree panormas",
     *                    "photo": null,
     *                    "flag": false,
     *                    "deleted": "0",
     *                    "created": "2015-06-07 20:36:32",
     *                    "modified": "2015-06-07 20:36:32",
     *                    "photo_count": "0",
     *                    "contribution_count": "1133",
     *                    "like_count": "3",
     *                    "User": {
     *                        "id": "1490",
     *                        "username": "richmix",
     *                        "fullname": "richmix",
     *                        "photo": "default.png"
     *                    },
     *                    "Photo": [
     *                        {
     *                            "id": "32",
     *                            "review_id": "122",
     *                            "venue_id": "162",
     *                            "user_id": "1490",
     *                            "filename": "1490_75f5a17d9fa7f318d1381f8d194523683794ed3ee057.jpg",
     *                            "flag": "0",
     *                            "created": "2015-06-07 20:36:34",
     *                            "modified": "2015-06-07 20:36:34",
     *                            "foursquare": 0,
     *                            "url": {
     *                                "large": "http://cdn.app-movement.com/apps/geolocation/uploads/large/1490_75f5a17d9fa7f318d1381f8d194523683794ed3ee057.jpg",
     *                                "medium": "http://cdn.app-movement.com/apps/geolocation/uploads/medium/1490_75f5a17d9fa7f318d1381f8d194523683794ed3ee057.jpg",
     *                                 "small": "http://cdn.app-movement.com/apps/geolocation/uploads/small/1490_75f5a17d9fa7f318d1381f8d194523683794ed3ee057.jpg",
     *                                "thumb": "http://cdn.app-movement.com/apps/geolocation/uploads/thumb/1490_75f5a17d9fa7f318d1381f8d194523683794ed3ee057.jpg"
     *                            }
     *                        }
     *                    ],
     *                    "liked": false
     *                }
     *            ]
     *        }
     *    ]
     *}
     * 
     * @apiError   user_id          NEEDS TO BE CHECKED
     * @apiError   latitude         Please provide latitude
     * @apiError   longitude        Please provide longitude
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     * THIS NEEDS TO BE REWRITTEN ONCE THE ERROR CHECKING IS THERE
     *}
     */
    // Get venues
    public function venues()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->query('api_key'));

        // Check session key
        if ($this->request->query('session_key')) {
            $this->checkSessionKey($this->request->query('session_key'), $this->request->query('user_id'));
        }

        // Get parameters
        $user_id = $this->request->query('user_id');
        $latitude = $this->request->query('latitude');
        $longitude = $this->request->query('longitude');

        // Check parameters
        if (!$latitude) { array_push($this->response->errors, __('Please provide latitude')); }
        if (!$longitude) { array_push($this->response->errors, __('Please provide longitude')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        $venues = $this->Venue->get_venues_from_coordinates($latitude, $longitude);

        // if (!$venues) { array_push($this->response->errors, 'No places found'); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Log transaction


        // Loop through venues and add liked and review images
        foreach ($venues as $venue_key => $venue) {

            foreach ($venues[$venue_key]["Review"] as $review_key => $review) {

                $review_id = $venues[$venue_key]["Review"][$review_key]["id"];
                $venues[$venue_key]["Review"][$review_key]["liked"] = $this->Like->hasAny(array('Like.user_id' => $user_id, 'Like.review_id' => $review_id));

                foreach ($venues[$venue_key]["Review"][$review_key]["Photo"] as $photo_key => $photo) {
                   
                    $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["foursquare"] = 0;
                    $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["url"]["large"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/large/' . $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["filename"];
                    $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["url"]["medium"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/medium/' . $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["filename"];
                    $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["url"]["small"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/small/' . $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["filename"];
                    $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["url"]["thumb"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/thumb/' . $venues[$venue_key]["Review"][$review_key]["Photo"][$photo_key]["filename"];
                    
                }

            }

        }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $venues;
        $this->response->output();
    }

    /**
     * @api {get} /api/geolocation/:identifier/venue /venue - Get specific venue
     * @apiName Venue
     * @apiGroup Geolocation
     * @apiDescription This endpoint returns a venue object for a given venue_id.<br/><br/>The venue photos are not included in this request and will be an empty array.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/venue
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the venue you wish to retrieve
     * @apiParam    {String}    session_key      Session key of authenticated user
     * @apiParam    {String}    api_key          API Key
     *
     * @apiSuccess {Venue}  Venue                            Venue object
     * @apiSuccess {String} Venue.id                         Venue id in db
     * @apiSuccess {String} Venue.user_id                    User id of user who added the venue
     * @apiSuccess {String} Venue.foursquare_id              Foursquare id
     * @apiSuccess {String} Venue.name                       Venue name (max 300 chars)
     * @apiSuccess {String} Venue.latitude                   Latitude of place
     * @apiSuccess {String} Venue.longitude                  Longitude of place
     * @apiSuccess {String} Venue.address                    Address line 1 (Generally street number and if possible, house number)
     * @apiSuccess {String} Venue.city                       Address line 2, city name
     * @apiSuccess {String} Venue.state                      Address line 3, State, District of Region (e.g. Tyne and Wear)
     * @apiSuccess {String} Venue.postcode                   Address line 4, Postcode, Zip code
     * @apiSuccess {String} Venue.country                    Address line 5, Country of location
     * @apiSuccess {String} Venue.flag                       Hide/Unhide venue from search result. This is to "remove" the venue from the map.
     * @apiSuccess {String} Venue.created                    Created datetime
     * @apiSuccess {String} Venue.modified                   Modified datetime
     * @apiSuccess {String} Venue.review_count               Count of reviews for the given venue
     * @apiSuccess {String} Venue.average_review             Average rating of all the reviews
     * @apiSuccess {String} Venue.q1_average                 Q1 Average rating from all the reviews
     * @apiSuccess {String} Venue.q2_average                 Q2 Average rating from all the reviews
     * @apiSuccess {String} Venue.q3_average                 Q3 Average rating from all the reviews
     * @apiSuccess {String} Venue.q4_average                 Q4 Average rating from all the reviews
     * @apiSuccess {Review[]}  Venue.Review                  Array of review objects for the place (see below for the object structure)
     *
     * @apiSuccess {Review} Review                           Review Object
     * @apiSuccess {String} Review.id                        Review id in db
     * @apiSuccess {String} Review.venue_id                  Venue id of the reviewed venue
     * @apiSuccess {String} Review.user_id                   User id who left the review
     * @apiSuccess {String} Review.q1                        Q1 rating by user
     * @apiSuccess {String} Review.q2                        Q2 rating by user
     * @apiSuccess {String} Review.q3                        Q3 rating by user
     * @apiSuccess {String} Review.q4                        Q4 rating by user
     * @apiSuccess {String} Review.review_text               Review text of the review left by the user
     * @apiSuccess {Boolean}Review.flag                      Boolean to "hide" the review from being returned
     * @apiSuccess {String} Review.deleted                   "0" or "1" to mark as deleted or not
     * @apiSuccess {String} Review.contribution_count        NOT SURE
     * @apiSuccess {String} Review.like_count                Number of likes that the review has received
     *
     * @apiSuccess {User}   User Object                      User object of the reviewing user
     * @apiSuccess {String} User.id                          Id of user leaving the review
     * @apiSuccess {String} User.username                    Username of reviewing user
     * @apiSuccess {String} User.fullname                    Full  name of reviewing user
     * @apiSuccess {String} User.photo                       Profile photo of user (i.e. andy.png). Needs a path to be constructed in order to access this image
     *
     * @apiSuccess {Photo[]}  Photo Object Array             Array of Photo objects in the review
     * @apiSuccess {Photo}    Photo                          Photo object
     * @apiSuccess {String}   Photo.id                       Id of photo in the db
     * @apiSuccess {String}   Photo.review_id                Review photo associated with
     * @apiSuccess {String}   Photo.venue_id                 Venue id associated with the review
     * @apiSuccess {String}   Photo.user_id                  User id that posted the photo
     * @apiSuccess {String}   Photo.filename                 Name of the photo (i.e. 1283123iu.jpg) Needs a path to be constructed in order to access this image
     * @apiSuccess {String}   Photo.created                  Created datetime of photo
     * @apiSuccess {String}   Photo.modified                 Modified datetime of photo
     * @apiSuccess {String}   Photo.foursquare               Boolean to show if the photo has been requested from the Foursquare API
     *
     * @apiSuccess {url}      Url                            Url object containing the different sized photos available
     * @apiSuccess {String}   Url.large                      Large photo (640x480)
     * @apiSuccess {String}   Url.medium                     Medium photo (320x240)
     * @apiSuccess {String}   Url.small                      Small photo (160x120)
     * @apiSuccess {String}   Url.thumb                      Thumb photo (80x60)
     *
     * @apiSuccess {String}   liked                          Shows if the current user making the request has "Liked" this specific review
     * 
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "Venue": {
     *            "id": "10",
     *            "user_id": "1078",
     *            "foursquare_id": "4dcbcaae7d8b84bcd55b5712",
     *            "name": "Hashøjskolen",
     *            "latitude": "55.3726968859",
     *            "longitude": "11.3328860368",
     *            "address": null,
     *            "city": "Slagelse",
     *            "state": "Region Sjælland",
     *            "postcode": null,
     *            "country": "Danmark",
     *            "flag": "0",
     *            "created": "2015-05-23 19:43:40",
     *            "modified": "2015-05-23 19:43:40",
     *            "review_count": "1",
     *            "average_review": "0.0",
     *            "q1_average": "0.0",
     *            "q2_average": "0.0",
     *            "q3_average": "0.0",
     *            "q4_average": "0.0"
     *        },
     *        "Review": [
     *            {
     *                "id": "8",
     *                "venue_id": "10",
     *                "user_id": "1078",
     *                "q1": "0",
     *                "q2": "0",
     *                "q3": "0",
     *                "q4": "0",
     *                "review_text": "WHO is et",
     *                "photo": null,
     *                "flag": false,
     *                "deleted": "0",
     *                "created": "2015-05-23 19:44:17",
     *                "modified": "2015-05-23 19:44:17",
     *                "photo_count": "0",
     *                "contribution_count": "1133",
     *                "like_count": "0",
     *                "User": {
     *                    "id": "1078",
     *                    "username": "Sutmig",
     *                    "fullname": "Sutmig",
     *                    "photo": "default.png"
     *                },
     *                "Photo": [
     *                    {
     *                        "id": "2",
     *                        "review_id": "8",
     *                        "venue_id": "10",
     *                        "user_id": "1078",
     *                        "filename": "1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                        "flag": "0",
     *                        "created": "2015-05-23 19:44:19",
     *                        "modified": "2015-05-23 19:44:19",
     *                        "foursquare": 0,
     *                        "url": {
     *                            "large": "http://cdn.app-movement.com/apps/geolocation/uploads/large/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                            "medium": "http://cdn.app-movement.com/apps/geolocation/uploads/medium/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                            "small": "http://cdn.app-movement.com/apps/geolocation/uploads/small/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                            "thumb": "http://cdn.app-movement.com/apps/geolocation/uploads/thumb/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg"
     *                        }
     *                    }
     *                ],
     *                "liked": false
     *            }
     *        ]
     *    }
     * }
     * 
     * @apiError   user_id          NEEDS TO BE CHECKED
     * @apiError   venue_id         Please provide venue_id
     * @apiError   venue_id         No venue found
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     * THIS NEEDS TO BE REWRITTEN ONCE THE ERROR CHECKING IS THERE
     *}
     */
    // Get a specific venue
    public function venue()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->query('api_key'));

        // Check session key
        if ($this->request->query('session_key')) {
            $this->checkSessionKey($this->request->query('session_key'), $this->request->query('user_id'));
        }
        
        // Get parameters
        $user_id = $this->request->query('user_id');
        $venue_id = $this->request->query('venue_id');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        $venue = $this->Venue->get_venue_with_id($venue_id);

        $published_app = $this->PublishedApp->find('first', array('conditions' => array(
            'PublishedApp.app_identifier' => $this->params['identifier']
        )));

        if (!$venue) { array_push($this->response->errors, __('No venue found')); }

        foreach ($venue["Review"] as $review_key => $review) {

            $review_id = $venue["Review"][$review_key]["id"];

            $venue["Review"][$review_key]["liked"] = $this->Like->hasAny(array('Like.user_id' => $user_id, 'Like.review_id' => $review_id));
            
            foreach ($venue["Review"][$review_key]["Photo"] as $photo_key => $photo) {
                
                $venue["Review"][$review_key]["Photo"][$photo_key]["foursquare"] = 0;
                $venue["Review"][$review_key]["Photo"][$photo_key]["url"]["large"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/large/' . $venue["Review"][$review_key]["Photo"][$photo_key]["filename"];
                $venue["Review"][$review_key]["Photo"][$photo_key]["url"]["medium"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/medium/' . $venue["Review"][$review_key]["Photo"][$photo_key]["filename"];
                $venue["Review"][$review_key]["Photo"][$photo_key]["url"]["small"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/small/' . $venue["Review"][$review_key]["Photo"][$photo_key]["filename"];
                $venue["Review"][$review_key]["Photo"][$photo_key]["url"]["thumb"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/thumb/' . $venue["Review"][$review_key]["Photo"][$photo_key]["filename"];
                
            }

        }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $venue;
        $this->response->output();
    }


    /**
     * @api {get} /api/geolocation/:identifier/venue_photos /venue_photos - Get venue photos
     * @apiName Venue_Photos
     * @apiGroup Geolocation
     * @apiDescription This endpoint returns an array of photos (with attached reviews).
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/venue_photos
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the venue you wish to retrieve
     * @apiParam    {String}    session_key      Session key of authenticated user
     * @apiParam    {String}    api_key          API Key
     *
     * @apiSuccess {Photo[]}  Photo Object Array             Array of Photo objects in the review
     * @apiSuccess {Photo}    Photo                          Photo object
     * @apiSuccess {String}   Photo.id                       Id of photo in the db
     * @apiSuccess {String}   Photo.review_id                Review photo associated with
     * @apiSuccess {String}   Photo.venue_id                 Venue id associated with the review
     * @apiSuccess {String}   Photo.user_id                  User id that posted the photo
     * @apiSuccess {String}   Photo.filename                 Name of the photo (i.e. 1283123iu.jpg) Needs a path to be constructed in order to access this image
     * @apiSuccess {String}   Photo.created                  Created datetime of photo
     * @apiSuccess {String}   Photo.modified                 Modified datetime of photo
     *
     * @apiSuccess {url}      Url                            Url object containing the different sized photos available
     * @apiSuccess {String}   Url.large                      Large photo (640x480)
     * @apiSuccess {String}   Url.medium                     Medium photo (320x240)
     * @apiSuccess {String}   Url.small                      Small photo (160x120)
     * @apiSuccess {String}   Url.thumb                      Thumb photo (80x60)
     * 
     * @apiSuccess {Review} Review                           Review Object within the photo object
     * @apiSuccess {String} Review.id                        Review id in db
     * @apiSuccess {String} Review.venue_id                  Venue id of the reviewed venue
     * @apiSuccess {String} Review.user_id                   User id who left the review
     * @apiSuccess {String} Review.q1                        Q1 rating by user
     * @apiSuccess {String} Review.q2                        Q2 rating by user
     * @apiSuccess {String} Review.q3                        Q3 rating by user
     * @apiSuccess {String} Review.q4                        Q4 rating by user
     * @apiSuccess {String} Review.review_text               Review text of the review left by the user
     * @apiSuccess {Boolean}Review.flag                      Boolean to "hide" the review from being returned (Performed by admins only)
     * @apiSuccess {String} Review.deleted                   "0" or "1" to mark as deleted or not
     * @apiSuccess {String} Review.created                   Created datetime
     * @apiSuccess {String} Review.modified                  Modified datetime
     * @apiSuccess {String} Review.photo_count               Count of photos contributed by user NEEDS LOOKING AT
     * @apiSuccess {String} Review.contribution_count        NOT SURE
     * @apiSuccess {String} Review.like_count                Number of likes that the review has received 
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": [
     *        {
     *            "Photo": {
     *                "id": "2",
     *                "review_id": "8",
     *                "venue_id": "10",
     *                "user_id": "1078",
     *                "filename": "1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                "flag": "0",
     *                "created": "2015-05-23 19:44:19",
     *                "modified": "2015-05-23 19:44:19",
     *                "url": {
     *                    "large": "http://cdn.app-movement.com/apps/geolocation/uploads/large/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                    "medium": "http://cdn.app-movement.com/apps/geolocation/uploads/medium/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                    "small": "http://cdn.app-movement.com/apps/geolocation/uploads/small/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg",
     *                    "thumb": "http://cdn.app-movement.com/apps/geolocation/uploads/thumb/1078_447e2e4abd1a6f797f3cceb264951232c905ce11d79e.jpg"
     *                }
     *            },
     *            "Review": {
     *                "id": "8",
     *                "venue_id": "10",
     *                "user_id": "1078",
     *                "q1": "0",
     *                "q2": "0",
     *                "q3": "0",
     *                "q4": "0",
     *                "review_text": "WHO is et",
     *                "photo": null,
     *                "flag": false,
     *                "deleted": "0",
     *                "created": "2015-05-23 19:44:17",
     *                "modified": "2015-05-23 19:44:17",
     *                "photo_count": "0",
     *                "contribution_count": "1133",
     *                "like_count": "0"
     *            }
     *        }
     *    ]
     * }
     * 
     * @apiError   user_id          NEEDS TO BE CHECKED
     * @apiError   venue_id         Please provide venue_id
     * @apiError   venue_id         No venue found
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     *{
     * THIS NEEDS TO BE REWRITTEN ONCE THE ERROR CHECKING IS THERE
     *}
     */
    // Get a photos for a specific venue
    public function venue_photos()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->query('api_key'));

        // Check session key
        if ($this->request->query('session_key')) {
            $this->checkSessionKey($this->request->query('session_key'), $this->request->query('user_id'));
        }
        
        // Get parameters
        $user_id = $this->request->query('user_id');
        $venue_id = $this->request->query('venue_id');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        $venue = $this->Venue->get_venue_with_id($venue_id);

        if (!$venue) { array_push($this->response->errors, __('No venue found')); }


        // Get venue photos 
        $photo_limit = 20;

        // Get user uploaded photos

        $user_photos = $this->Photo->find('all', array(
            'conditions' => array(
                'Photo.venue_id' => $venue_id,
                'Photo.flag' => 0
                ),
            'limit' => $photo_limit,
            'order' => 'Photo.created DESC'
            ));

        foreach ($user_photos as $index => &$user_photo)
        {
            $photo["foursquare"] = 0;
            $user_photo["Photo"]["url"]["large"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/large/' . $user_photo["Photo"]["filename"];
            $user_photo["Photo"]["url"]["medium"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/medium/' . $user_photo["Photo"]["filename"];
            $user_photo["Photo"]["url"]["small"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/small/' . $user_photo["Photo"]["filename"];
            $user_photo["Photo"]["url"]["thumb"] = 'http://cdn.app-movement.com/apps/geolocation/uploads/thumb/' . $user_photo["Photo"]["filename"];
        }

        // Get foursquare photos

        $foursquare_photos = array();

        if (count($user_photos) < $photo_limit)
        {
            $foursquare_id = $venue["Venue"]["foursquare_id"];

            $url = 'https://api.foursquare.com/v2/venues/' . $foursquare_id . '/photos/?client_id=' . $this->foursquare_client_id . '&client_secret=' . $this->foursquare_client_secret . '&v=' . $this->foursquare_version . '&limit=' . ($photo_limit - count($user_photos));
        
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt ($ch, CURLOPT_URL, $url);

            $foursquare_response = curl_exec($ch);
            curl_close($ch);

            $foursquare_photos_response = json_decode($foursquare_response);

            if ($foursquare_photos_response != null)
            {
                if (property_exists($foursquare_photos_response, "response"))
                {
                    if (property_exists($foursquare_photos_response->response, 'photos'))
                    {
                        if (property_exists($foursquare_photos_response->response->photos, 'items'))
                        {
                            foreach ($foursquare_photos_response->response->photos->items as $index => $photo) {
                                
                                $foursquare_photos[$index]["Photo"]["id"] = $photo->id;
                                $foursquare_photos[$index]["Photo"]["user_id"] = 0;
                                $foursquare_photos[$index]["Photo"]["venue_id"] = $venue_id;
                                $foursquare_photos[$index]["Photo"]["filename"] = $photo->prefix . '300x300' . $photo->suffix;

                                $foursquare_photos[$index]["Photo"]["url"]["large"] = $photo->prefix . '500x500' . $photo->suffix;
                                $foursquare_photos[$index]["Photo"]["url"]["medium"] = $photo->prefix . '300x300' . $photo->suffix;
                                $foursquare_photos[$index]["Photo"]["url"]["small"] = $photo->prefix . '100x100' . $photo->suffix;
                                $foursquare_photos[$index]["Photo"]["url"]["thumb"] = $photo->prefix . '36x36' . $photo->suffix;
            
                                $foursquare_photos[$index]["Photo"]["foursquare"] = 1;
                                $foursquare_photos[$index]["Photo"]["flag"] = 0;
                                $foursquare_photos[$index]["Photo"]["created"] = gmdate("Y-m-d H:i:s", $photo->createdAt);
                                $foursquare_photos[$index]["Photo"]["modified"] = gmdate("Y-m-d H:i:s", $photo->createdAt);

                            }
                        }
                    }
                }
            }
        }

        // Combine photo arrays
        $venue_photos = array_merge($user_photos, $foursquare_photos);

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $venue_photos;
        $this->response->output();
    }

    /**
     * @api {post} /api/geolocation/:identifier/review /review - Post Review 
     * @apiName Review
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows the user to submit a review for a given venue. You can also send a file in the header of the request which will be used as a review image.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/review
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the venue you wish to review
     * @apiParam    {String}    q1               q1 rating 0 - 5 for rating option A
     * @apiParam    {String}    q2               q2 rating 0 - 5 for rating option B
     * @apiParam    {String}    q3               q3 rating 0 - 5 for rating option C
     * @apiParam    {String}    q4               q4 rating 0 - 5 for rating option D
     * @apiParam    {String}    review_text      Text of the review (VARCHAR 800)
     * @apiParam    {String}    session_key      Session key of authenticated user
     * @apiParam    {String}    api_key          API Key
     *
     * @apiSuccess {Review} Review                           Review Object
     * @apiSuccess {String} Review.id                        Review id in db
     * @apiSuccess {String} Review.venue_id                  Venue id of the reviewed venue
     * @apiSuccess {String} Review.user_id                   User id who left the review
     * @apiSuccess {String} Review.q1                        Q1 rating by user
     * @apiSuccess {String} Review.q2                        Q2 rating by user
     * @apiSuccess {String} Review.q3                        Q3 rating by user
     * @apiSuccess {String} Review.q4                        Q4 rating by user
     * @apiSuccess {String} Review.review_text               Review text of the review left by the user
     * @apiSuccess {String} Review.created                   Created datetime
     * @apiSuccess {String} Review.modified                  Modified datetime
     * @apiSuccess {String} Review.share_link                Unique share link for this review, can be accessed in a web browser to see the venue and discussion online
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "Review": {
     *            "user_id": "2",
     *            "venue_id": "21",
     *            "q1": "4",
     *            "q2": "4",
     *            "q3": "4",
     *            "q4": "4",
     *            "review_text": "Great place",
     *            "modified": "2015-07-28 15:48:21",
     *            "created": "2015-07-28 15:48:21",
     *            "id": "1146",
     *            "share_link": "https://app-movement.com/share/xPB"
     *        }
     *    }
     * }
     * 
     * @apiError   user_id          Please provide user_id
     * @apiError   venue_id         Please provide venue_id
     * @apiError   q1               Please provide q1
     * @apiError   q2               Please provide q2
     * @apiError   q3               Please provide q3
     * @apiError   q4               Please provide q4
     * @apiError   review_text      Please provide review_text
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide user_id",
     *        "Please provide venue_id",
     *        "Please provide q1",
     *        "Please provide q2",
     *        "Please provide q3",
     *        "Please provide q4",
     *        "Please provide review_text"
     *    ],
     *    "data": []
     * }
     */
    // Submit Review
    public function review()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Check session key
        if ($this->request->data('session_key')) {
            $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
        }

        // Get parameters
        $user_id = $this->request->data('user_id');
        $venue_id = $this->request->data('venue_id');
        $q1 = $this->request->data('q1');
        $q2 = $this->request->data('q2');
        $q3 = $this->request->data('q3');
        $q4 = $this->request->data('q4');
        $review_text = $this->request->data('review_text');        

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }
        if (!$q1) { array_push($this->response->errors, __('Please provide q1')); }
        if (!$q2) { array_push($this->response->errors, __('Please provide q2')); }
        if (!$q3) { array_push($this->response->errors, __('Please provide q3')); }
        if (!$q4) { array_push($this->response->errors, __('Please provide q4')); }
        if (!$review_text) { array_push($this->response->errors, __('Please provide review_text')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        unset($this->request->data['api_key']);

        // Save review
        $review = $this->Review->save($this->request->data);

        // app_identifier
        $published_app = $this->PublishedApp->find('first', array(
                                                    'conditions' => array(
                                                        'app_identifier' => $this->params['identifier']
                                                    )
                                                ));

        $review['Review']['share_link'] = $this->ShareLink->generate_review_share_link($this->Review->id, $published_app['PublishedApp']['id'], $user_id);

        if (!$review) { array_push($this->response->errors, __('Failed to submit review')); }

        if (isset($_FILES['photo']))
        {
            // Upload photo

            $filename = $user_id . '_' . bin2hex(mcrypt_create_iv(22, MCRYPT_DEV_URANDOM)) . '.jpg';

            $allowedExts = array("gif", "jpeg", "jpg", "png");
            $temp = explode(".", $_FILES['photo']["name"]);
            $extension = end($temp);

            $uploadedFile = $_FILES['photo']['tmp_name'];
            $destination = "img/apps/geolocation/uploads/original/" . $filename;
            
            if(move_uploaded_file($uploadedFile, $destination))
            {
                // Instantiate the class
                $s3 = new S3(awsAccessKey, awsSecretKey);
                
                // Transfer original to S3
                $s3->putObjectFile($destination, "app-movement", "apps/geolocation/uploads/original/" . $filename, S3::ACL_PUBLIC_READ);

                $image = new ResizeImage();

                $image->load($destination);
                
                // Large
                $image->resizeToHeight(640);
                $image->save("img/apps/geolocation/uploads/large/" . $filename);
                $s3->putObjectFile("img/apps/geolocation/uploads/large/" . $filename, "app-movement", "apps/geolocation/uploads/large/" . $filename, S3::ACL_PUBLIC_READ);
                
                // Medium
                $image->resizeToHeight(320);
                $image->save("img/apps/geolocation/uploads/medium/" . $filename);
                $s3->putObjectFile("img/apps/geolocation/uploads/medium/" . $filename, "app-movement", "apps/geolocation/uploads/medium/" . $filename, S3::ACL_PUBLIC_READ);
                
                // Small
                $image->resizeToHeight(160);
                $image->save("img/apps/geolocation/uploads/small/" . $filename);
                $s3->putObjectFile("img/apps/geolocation/uploads/small/" . $filename, "app-movement", "apps/geolocation/uploads/small/" . $filename, S3::ACL_PUBLIC_READ);
                
                // Thumb
                $image->resizeToHeight(80);
                $image->save("img/apps/geolocation/uploads/thumb/" . $filename);
                $s3->putObjectFile("img/apps/geolocation/uploads/thumb/" . $filename, "app-movement", "apps/geolocation/uploads/thumb/" . $filename, S3::ACL_PUBLIC_READ);

                // Delete photos from server
                unlink("img/apps/geolocation/uploads/thumb/" . $filename);
                unlink("img/apps/geolocation/uploads/small/" . $filename);
                unlink("img/apps/geolocation/uploads/medium/" . $filename);
                unlink("img/apps/geolocation/uploads/large/" . $filename);
                unlink("img/apps/geolocation/uploads/original/" . $filename);

                // Save review photo
                $this->Photo->create();
                $data = array('review_id' => $review["Review"]["id"], 'venue_id' => $venue_id, 'user_id' => $user_id, 'filename' => $filename);
                $this->Photo->save($data);

            }
        }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }
        
        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $review;
        $this->response->output();
    }


    /**
     * @api {post} /api/geolocation/:identifier/add_venue /add_venue - Add Venue
     * @apiName Add_Venue
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows users contribute new venues. The lat/lng combination will be geocoded on the server and the address, city and country will be filled in automatically.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/add_venue
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the review being reported
     * @apiParam    {String}    name             Name of new venue
     * @apiParam    {String}    latitude         Latitude of new location
     * @apiParam    {String}    longitude        Longitude of new location
     * @apiParam    {String}    [foursquare_id]  Foursquare ID if added using the "Add Venue" through foursquare search
     * @apiParam    {String}    session_key      Session key of authenticated user NEEDS TO BE CHECKED
     * @apiParam    {String}    api_key          API Key NEED TO ADD THIS check NEEDS TO BE CHECKED
     *
     * @apiSuccess  {Venue}    Venue               Venue Object
     * @apiSuccess  {String}    Venue.id            Venue id
     * @apiSuccess  {String}    Venue.latitude      Latitude of new location
     * @apiSuccess  {String}    Venue.longitude     Longitude of new location
     * @apiSuccess  {String}    Venue.city          City description (geocoded)
     * @apiSuccess  {String}    Venue.country       Country (geocoded)
     * @apiSuccess  {String}    Venue.created       Created datetime
     * @apiSuccess  {String}    Venue.modified      Modified datetime
     * 
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "Venue": {
     *            "user_id": "1",
     *            "name": "boopbing",
     *            "latitude": "23",
     *            "longitude": "-1.3",
     *            "city": "Bordj Badji Mokhtar",
     *            "country": "Algeria",
     *            "modified": "2015-07-28 16:36:34",
     *            "created": "2015-07-28 16:36:34",
     *            "id": "1113"
     *        }
     *    }
     * }
     * 
     * @apiError   user_id          Please provide user_id
     * @apiError   venue_id         Please provide venue_id NEEDS TO BE CHECKED
     * @apiError   latitude         Please provide a latitude
     * @apiError   longitude        Please provide a longitude
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide user_id",
     *        "Please provide venue name",
     *        "Please provide latitude",
     *        "Please provide longitude"
     *    ],
     *    "data": []
     * }
     */
    // Add Venue
    public function add_venue()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Check session key
        if ($this->request->data('session_key')) {
            $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
        }

        // Get parameters
        $user_id = $this->request->data('user_id');
        $foursquare_id = $this->request->data('foursquare_id');
        $name = $this->request->data('name');
        $latitude = $this->request->data('latitude');
        $longitude = $this->request->data('longitude');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$name) { array_push($this->response->errors, __('Please provide venue name')); }
        if (!$latitude) { array_push($this->response->errors, __('Please provide latitude')); }
        if (!$longitude) { array_push($this->response->errors, __('Please provide longitude')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        if (!$foursquare_id) {
            $venue = $this->Venue->insert_venue($user_id, $name, $latitude, $longitude);
        } else {
            $venue = $this->Venue->insert_foursquare_venue($user_id, $foursquare_id, $name, $latitude, $longitude);
        }
        
        if (!$venue) { array_push($this->response->errors, __('Failed to add venue')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $venue;
        $this->response->output();
    }

    /**
     * @api {post} /api/geolocation/:identifier/like /like - Like Review
     * @apiName Like
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows users to "Like" a review.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/like
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the review being reported
     * @apiParam    {String}    review_id        Review id of the review being reported
     * @apiParam    {String}    session_key      Session key of authenticated user NEEDS TO BE CHECKED
     * @apiParam    {String}    api_key          API Key NEED TO ADD THIS check NEEDS TO BE CHECKED
     *
     * @apiSuccess  {Like}      Like                Like Object
     * @apiSuccess  {String}    Like.id             Report id
     * @apiSuccess  {String}    Like.user_id        User Id who submitted the like
     * @apiSuccess  {String}    Like.venue_id       Venue id of the review being liked
     * @apiSuccess  {String}    Like.review_id      Review id of the review being liked
     * @apiSuccess  {String}    Like.created        User Id who submitted the like
     * @apiSuccess  {String}    Like.modified       User Id who submitted the like
     * 
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "Like": {
     *            "user_id": "2",
     *            "venue_id": "2",
     *            "review_id": "2",
     *            "receiver_id": "1",
     *            "modified": "2015-07-28 16:28:30",
     *            "created": "2015-07-28 16:28:30",
     *            "id": "219"
     *        }
     *    }
     * }
     * 
     * @apiError   user_id          Please provide user_id
     * @apiError   venue_id         Please provide venue_id NEEDS TO BE CHECKED
     * @apiError   review_id        Please provide review_id NEEDS TO BE CHECKED
     * @apiError   receiver_id      NEEDS TO BE CHECKED
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide user_id",
     *        "Please provide venue_id",
     *        "Please provide review_id",
     *        "Please provide receiver_id"
     *    ],
     *    "data": []
     * }
     */
    // Like Review
    public function like()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Check session key
        if ($this->request->data('session_key')) {
            $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
        }

        // Get parameters
        $user_id = $this->request->data('user_id');
        $venue_id = $this->request->data('venue_id');
        $review_id = $this->request->data('review_id');
        $receiver_id = $this->request->data('receiver_id');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }
        if (!$review_id) { array_push($this->response->errors, __('Please provide review_id')); }
        if (!$receiver_id) { array_push($this->response->errors, __('Please provide receiver_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        unset($this->request->data['api_key']);
        
        if (!$this->Like->hasAny( array('Like.user_id' => $user_id, 'Like.venue_id' => $venue_id, 'Like.review_id' => $review_id) )) {

            $data = array(
                'user_id' => $user_id,
                'venue_id' => $venue_id,
                'review_id' => $review_id,
                'receiver_id' => $receiver_id
                );
            
            $like = $this->Like->save($data);
        
        }

        if (!$like) { array_push($this->response->errors, __('Failed to like review')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }
        
        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $like;
        $this->response->output();
    }


    /**
     * @api {post} /api/geolocation/:identifier/unlike /unlike - Unlike Review
     * @apiName Unlike
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows users to "Unlike" a review.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/unlike
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the review being liked
     * @apiParam    {String}    review_id        Review id of the review being liked
     * @apiParam    {String}    session_key      Session key of authenticated user NEEDS TO BE CHECKED
     * @apiParam    {String}    api_key          API Key NEED TO ADD THIS check NEEDS TO BE CHECKED
     * 
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": []
     * }
     * 
     * @apiError   user_id          Please provide user_id
     * @apiError   venue_id         Please provide venue_id NEEDS TO BE CHECKED
     * @apiError   review_id        Please provide review_id NEEDS TO BE CHECKED
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide user_id",
     *        "Please provide venue_id",
     *        "Please provide review_id"
     *    ],
     *    "data": []
     * }
     */
    // Unlike Review
    public function unlike()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Check session key
        if ($this->request->data('session_key')) {
            $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
        }

        // Get parameters
        $user_id = $this->request->data('user_id');
        $venue_id = $this->request->data('venue_id');
        $review_id = $this->request->data('review_id');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }
        if (!$review_id) { array_push($this->response->errors, __('Please provide review_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        unset($this->request->data['api_key']);
        
        if ($this->Like->hasAny( array('Like.user_id' => $user_id, 'Like.venue_id' => $venue_id, 'Like.review_id' => $review_id) )) {

            $like = $this->Like->find('first', array(
                'conditions' => array('Like.user_id' => $user_id, 'Like.venue_id' => $venue_id, 'Like.review_id' => $review_id)
                ));

            $this->Like->delete($like["Like"]["id"]);
        
        }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }
        
        // Send response
        $this->response->meta['success'] = true;
        $this->response->output();
    }


    /**
     * @api {post} /api/geolocation/:identifier/report /report - Report Review
     * @apiName Report
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows users to leave a freeform text report for a given review.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/report
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    venue_id         Venue id of the review being reported
     * @apiParam    {String}    review_id        Review id of the review being reported
     * @apiParam    {String}    report_text      Free form text submitted by user to report review
     * @apiParam    {String}    session_key      Session key of authenticated user NEEDS TO BE CHECKED
     * @apiParam    {String}    api_key          API Key NEED TO ADD THIS check NEEDS TO BE CHECKED
     *
     * @apiSuccess  {Report}    Report              Report Object
     * @apiSuccess  {String}    Report.id           Report id
     * @apiSuccess  {String}    Report.user_id      User Id who submitted the report
     * @apiSuccess  {String}    Report.venue_id     Venue id of the review being reported
     * @apiSuccess  {String}    Report.review_id    Review id of the review being reported
     * @apiSuccess  {String}    Report.report_text  Reported text submitted by the user
     * @apiSuccess  {String}    Report.created      User Id who submitted the report
     * @apiSuccess  {String}    Report.modified     User Id who submitted the report
     * 
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "Report": {
     *            "user_id": "2",
     *            "venue_id": "2",
     *            "review_id": "2",
     *            "report_text": null,
     *            "modified": "2015-07-28 16:21:10",
     *            "created": "2015-07-28 16:21:10",
     *            "id": "12"
     *        }
     *    }
     * }
     * 
     * @apiError   user_id          Please provide user_id
     * @apiError   venue_id         Please provide venue_id NEEDS TO BE CHECKED
     * @apiError   review_id        Please provide review_id NEEDS TO BE CHECKED
     * @apiError   review_text      NEEDS TO BE CHECKED
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide user_id",
     *        "Please provide venue_id",
     *        "Please provide review_id"
     *    ],
     *    "data": []
     * }
     */
    // Report Review
    public function report()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Check session key
        if ($this->request->data('session_key')) {
            $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
        }

        // Get parameters
        $user_id = $this->request->data('user_id');
        $venue_id = $this->request->data('venue_id');
        $review_id = $this->request->data('review_id');
        $report_text = $this->request->data('report_text');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }
        if (!$review_id) { array_push($this->response->errors, __('Please provide review_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        unset($this->request->data['api_key']);
        
        $report = null;

        $data = array(
            'user_id' => $user_id,
            'venue_id' => $venue_id,
            'review_id' => $review_id,
            'report_text' => $report_text
            );
        
        $report = $this->Report->save($data);

        if (!$report) { array_push($this->response->errors, __('Failed to report review')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }
        
        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $report;
        $this->response->output();
    }

    /**
     * @api {post} /api/geolocation/:identifier/delete /delete - Delete Review
     * @apiName Delete
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows you to mark reviews as deleted. These will be flagged and not shown when retrieved by subsequent requests.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/delete
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    profile_id       Profile user_id of the user you wish to retrieve
     * @apiParam    {String}    session_key      Session key of authenticated user
     * @apiParam    {String}    api_key          API Key NEED TO ADD THIS check NEEDS TO BE CHECKED
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": []
     * }
     * 
     * @apiError   user_id          NEEDS TO BE CHECKED
     * @apiError   venue_id         Please provide a venue_id
     * @apiError   review_id        Please provide a review_id
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide venue_id",
     *        "Please provide review_id"
     *    ],
     *    "data": []
     * }
     */
    // Delete Review
    public function delete()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        $this->checkKey($this->request->data('api_key'));

        // Check session key
        if ($this->request->data('session_key')) {
            $this->checkSessionKey($this->request->data('session_key'), $this->request->data('user_id'));
        }

        // Get parameters
        $user_id = $this->request->data('user_id');
        $venue_id = $this->request->data('venue_id');
        $review_id = $this->request->data('review_id');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$venue_id) { array_push($this->response->errors, __('Please provide venue_id')); }
        if (!$review_id) { array_push($this->response->errors, __('Please provide review_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions
        unset($this->request->data['api_key']);
        
        if ($this->Review->hasAny( array('Review.id' => $review_id, 'Review.user_id' => $user_id) )) {

            // Update review to mark as deleted
            $this->Review->read(null, $review_id);
            
            $this->Review->set(array(
                'deleted' => true
            ));

            if ($this->Review->save()) {
                $this->response->meta['success'] = true;
            }
        }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }
        
        // Send response
        $this->response->output();
    }

    /**
     * @api {get} /api/geolocation/:identifier/user_profile /user_profile - User Profile
     * @apiName Profile
     * @apiGroup Geolocation
     * @apiDescription This endpoint allows you to retrieve a user profile for a given user.
     * @apiSampleRequest https://app-movement.com/api/geolocation/movement_422_64/user_profile
     *
     * @apiParam    {String}    user_id          User id of user making the request
     * @apiParam    {String}    profile_id       Profile user_id of the user you wish to retrieve
     * @apiParam    {String}    session_key      Session key of authenticated user
     * @apiParam    {String}    api_key          API Key NEED TO ADD THIS check
     *
     * @apiSuccess {User}   User                            User object
     * @apiSuccess {String} User.id                         Users id
     * @apiSuccess {String} User.fullname                   Full name of user
     * @apiSuccess {String} User.password                   Hashed Password
     * @apiSuccess {String} User.email                      User Email
     * @apiSuccess {String} User.photo                      User photo of file (url needs to be constructed to request this image)
     * @apiSuccess {String} User.locale                     Language of user (when registering)
     * @apiSuccess {String} User.role                       User role on site (will always be "user" unless specified otherwise)
     * @apiSuccess {String} User.code                       Forgotten password code that is currently active when user performed forgotten password request
     * @apiSuccess {String} User.receives_email_updates     Boolean to show if user receives email updates
     * @apiSuccess {String} User.created                    User account creation datetime
     * @apiSuccess {String} User.modified                   User account modified datetime
     * @apiSuccess {Integer}User.reviews_posted_count       Count of reviews that the user has posted
     * @apiSuccess {Integer}User.likes_received_count       Count of likes the user has received through their reviews
     *
     * @apiSuccessExample Success-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": true,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [],
     *    "data": {
     *        "User": {
     *            "id": "2",
     *            "username": "atgarbett",
     *            "fullname": "Andrew Garbett",
     *            "password": "HASHEDPASSWORD",
     *            "email": "atgarbett@gmail.com",
     *            "photo": "2_4079.jpg",
     *            "locale": "en",
     *            "role": "admin",
     *            "code": null,
     *            "receives_email_updates": "1",
     *            "created": "2014-01-01 00:00:00",
     *            "modified": "2015-07-28 15:50:50",
     *            "reviews_posted_count": 3,
     *            "likes_received_count": 4
     *        }
     *        ]
     *    }
     * }
     * 
     * @apiError   user_id          Please provide user_id
     * @apiError   profile_id       Please provide profile_id
     * @apiError   session_key      NEEDS TO BE CHECKED
     * @apiError   api_key          API key required
     * 
     * @apiErrorExample Error-Response:
     * HTTP/1.1 200 OK
     * {
     *    "meta": {
     *        "success": false,
     *        "min_version": 1,
     *        "session_valid": true
     *    },
     *    "errors": [
     *        "Please provide user_id",
     *        "Please provide profile_id"
     *    ],
     *    "data": []
     * }
     */
    // Get user profile
    public function user_profile()
    {
        // Setup response object
        $this->response = new ResponseObject();

        // Check API key
        // $this->checkKey($this->request->query('api_key'));

        // Check session key
        if ($this->request->query('session_key')) {
            $this->checkSessionKey($this->request->query('session_key'), $this->request->query('user_id'));
        }
        
        // Get parameters
        $user_id = $this->request->query('user_id');
        $profile_id = $this->request->query('profile_id');

        // Check parameters
        if (!$user_id) { array_push($this->response->errors, __('Please provide user_id')); }
        if (!$profile_id) { array_push($this->response->errors, __('Please provide profile_id')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Perform functions

        $this->User->bindModel(
            array('hasMany' => array(
                    'Review' => array(
                        'className' => 'Review',
			'conditions' => array('Review.deleted' => 0)
                    )
                )
            )
        );

        $user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $profile_id,
                ),
            'contain' => array(
                'Review' => array(
                    'limit' => 50,
                    'User' => array(
                        'fields' => array(
                            'id', 'username', 'fullname', 'photo')
                        ),
                        'order' => 'Review.created DESC'
                    )
                ),
            ));

        $user['User']['reviews_posted_count'] = $this->Review->find('count', array('conditions' => array('Review.user_id' => $profile_id)));
        $user['User']['likes_received_count'] = $this->Like->find('count', array('conditions' => array('Like.receiver_id' => $profile_id)));

        $stripped_user = $this->User->find('first', array(
            'conditions' => array(
                'User.id' => $profile_id
                )));

        foreach ($user["Review"] as &$review) {

            $review_id = $review["id"];

            $review["liked"] = $this->Like->hasAny(array('Like.user_id' => $user_id, 'Like.review_id' => $review_id));

            $review["User"] = $stripped_user["User"];

        }

        if (!$user) { array_push($this->response->errors, __('No user found')); }

        // Check for errors
        if (count($this->response->errors) > 0) { $this->response->output(); }

        // Send response
        $this->response->meta['success'] = true;
        $this->response->data = $user;
        $this->response->output();
    }

    function beforeFilter() 
    {
        parent::beforeFilter();
        $this->Auth->allow();
    }
}
?>
