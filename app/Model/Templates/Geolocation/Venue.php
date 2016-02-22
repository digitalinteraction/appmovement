<?php
class Venue extends AppModel {

    public $specific = true;

    public $actsAs = array('Containable');

    var $hasMany = array('Review');

    var $belongsToMany = array('VenueCategory');

    var $virtualFields = array(
        'review_count' => 'SELECT COUNT(*) FROM reviews as Review WHERE Review.venue_id = Venue.id AND Review.flag = 0 AND Review.deleted = 0',
        'average_review' => 'SELECT ROUND(((AVG(q1) + AVG(q2) + AVG(q3) + AVG(q4)) / 4), 1) FROM reviews as Review WHERE Review.venue_id = Venue.id AND Review.flag = 0 AND Review.deleted = 0',
        'q1_average' => 'SELECT ROUND((AVG(q1)), 1) FROM reviews as Review WHERE Review.venue_id = Venue.id AND Review.flag = 0 AND Review.deleted = 0',
        'q2_average' => 'SELECT ROUND((AVG(q2)), 1) FROM reviews as Review WHERE Review.venue_id = Venue.id AND Review.flag = 0 AND Review.deleted = 0',
        'q3_average' => 'SELECT ROUND((AVG(q3)), 1) FROM reviews as Review WHERE Review.venue_id = Venue.id AND Review.flag = 0 AND Review.deleted = 0',
        'q4_average' => 'SELECT ROUND((AVG(q4)), 1) FROM reviews as Review WHERE Review.venue_id = Venue.id AND Review.flag = 0 AND Review.deleted = 0',
    );

    private $foursquare_client_id = Configure::read('geolocation.foursquare_client_id');
    private $foursquare_client_secret = Configure::read('geolocation.foursquare_client_secret');
    private $foursquare_version = Configure::read('geolocation.foursquare_version');

    public function insert_venue($user_id, $name, $latitude, $longitude)
    {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" . $latitude . "," . $longitude . "&sensor=false";
        $data = @file_get_contents($url);
        $jsondata = json_decode($data,true);
        if(is_array($jsondata) && $jsondata['status'] == "OK")
        {
            $result = $jsondata['results']['0'];

            $data = array();

            $data['user_id'] = $user_id;
            $data['name'] = $name;
            $data['latitude'] = $latitude;
            $data['longitude'] = $longitude;

            $location = array();

            foreach ($result['address_components'] as $component) {

                switch ($component['types']) {
                    case in_array('route', $component['types']):
                        $data['address'] = $component['long_name'];
                        break;
                    case in_array('locality', $component['types']):
                        $data['city'] = $component['long_name'];
                        break;
                    case in_array('administrative_area_level_2', $component['types']):
                        $data['state'] = $component['long_name'];
                        break;
                    case in_array('postal_code', $component['types']):
                        $data['postcode'] = $component['long_name'];
                        break;
                    case in_array('country', $component['types']):
                        $data['country'] = $component['long_name'];
                        break;
                }
            }

            $venue = $this->save($data);

            return $venue;
        }
    }

    public function insert_foursquare_venue($user_id, $foursquare_id, $name, $latitude, $longitude)
    {
        $url = 'https://api.foursquare.com/v2/venues/' . $foursquare_id . '?client_id=' . $this->foursquare_client_id . '&client_secret=' . $this->foursquare_client_secret . '&v=' . $this->foursquare_version;

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_URL, $url);

        $foursquare_response = curl_exec($ch);
        curl_close($ch);

        $foursquare_venues = json_decode($foursquare_response);

        if ($foursquare_venues != null)
        {
            if (property_exists($foursquare_venues, "response"))
            {
                if (property_exists($foursquare_venues->response, 'venue'))
                {
                    $foursquare_venue = $foursquare_venues->response->venue;

                    if (!$this->hasAny(array('foursquare_id' => $foursquare_id))) {

                        $this->create();

                        $data = array();

                        $data['Venue']['user_id'] = $user_id;

                        if (property_exists($foursquare_venue, 'id')) {
                            $data['Venue']['foursquare_id'] = $foursquare_venue->id;
                            $foursquare_id = $data['Venue']['foursquare_id'];
                        }

                        if (property_exists($foursquare_venue, 'name')) {
                            $data['Venue']['name'] = $foursquare_venue->name;
                        }

                        if (property_exists($foursquare_venue->location, 'lat')) {
                            $data['Venue']['latitude'] = $foursquare_venue->location->lat;
                        }

                        if (property_exists($foursquare_venue->location, 'lng')) {
                            $data['Venue']['longitude'] = $foursquare_venue->location->lng;
                        }

                        if (property_exists($foursquare_venue->location, 'address')) {
                            $data['Venue']['address'] = $foursquare_venue->location->address;
                        }

                        if (property_exists($foursquare_venue->location, 'city')) {
                            $data['Venue']['city'] = $foursquare_venue->location->city;
                        }

                        if (property_exists($foursquare_venue->location, 'state')) {
                            $data['Venue']['state'] = $foursquare_venue->location->state;
                        }

                        if (property_exists($foursquare_venue->location, 'postalCode')) {
                            $data['Venue']['postode'] = $foursquare_venue->location->postalCode;
                        }

                        if(property_exists($foursquare_venue->location, 'country')) {
                            $data['Venue']['country'] = $foursquare_venue->location->country;
                        }

                        $venue = $this->save($data);

                    } else {

                        $venue = $this->findByFoursquareId($foursquare_id);
                    }

                    return $venue;
                }
            }
        }
    }

    public function get_venue_with_id($venue_id)
    {
        $venue = $this->find('first', array(
            'conditions' => array(
                'Venue.id' => $venue_id
            ),
            'contain' => array(
                'Review' => array(
                    'conditions' => array('Review.deleted' => 0),
                    'User' => array(
                        'fields' => array('id', 'username', 'fullname', 'photo')
                    ),
                    'Photo' => array(
                        'conditions' => array('Photo.flag' => 0)
                    ),
                    'order' => 'Review.created DESC'
                ),
            )
        ));

        return $venue;

    }

    public function get_venues_from_coordinates($latitude, $longitude)
    {
        $max_distance = 20.0;
        $limit = 200;

        $this->virtualFields['distance'] = "ROUND((3959 * acos(cos(radians(" . $latitude . ")) * cos(radians(latitude)) * cos(radians(longitude) - radians(" . $longitude . ")) + sin(radians(" . $latitude . ")) * sin(radians(latitude)))), 3)"; // Potential bottleneck :)

        $venues = $this->find('all', array(
            'conditions' => array(
                'Venue.distance <=' => $max_distance,
                'Venue.review_count >' => 0,
                'Venue.flag' => 0
            ),
            'contain' => array(
                'Review' => array(
                    'conditions' => array(
                        'Review.deleted' => 0,
                        'Review.flag' => 0
                    ),
                    'User' => array(
                        'fields' => array('id', 'username', 'fullname', 'photo')
                    ),
                    'Photo' => array(
                        'conditions' => array('Photo.flag' => 0)
                    )
                ),
            ),
            'order' => 'Venue.distance',
            'limit' => $limit
        ));

        return $venues;
    }

    public function get_bounding_box($latitude, $longitude, $radius)
    {
        $earth_radius = 3963.1; // Radius of earth in miles

        // Bearings - FIX
        $due_north = deg2rad(0);
        $due_south = deg2rad(180);
        $due_east = deg2rad(90);
        $due_west = deg2rad(270);

        // Convert latitude and longitude into radians
        $lat_r = deg2rad($latitude);
        $lon_r = deg2rad($longitude);

        $northmost  = asin(sin($lat_r) * cos($radius/$earth_radius) + cos($lat_r) * sin ($radius/$earth_radius) * cos($due_north));
        $southmost  = asin(sin($lat_r) * cos($radius/$earth_radius) + cos($lat_r) * sin ($radius/$earth_radius) * cos($due_south));

        $eastmost = $lon_r + atan2(sin($due_east)*sin($radius/$earth_radius)*cos($lat_r),cos($radius/$earth_radius)-sin($lat_r)*sin($lat_r));
        $westmost = $lon_r + atan2(sin($due_west)*sin($radius/$earth_radius)*cos($lat_r),cos($radius/$earth_radius)-sin($lat_r)*sin($lat_r));

        $northmost = rad2deg($northmost);
        $southmost = rad2deg($southmost);
        $eastmost = rad2deg($eastmost);
        $westmost = rad2deg($westmost);

        // Sort the lat and long so that we can use them for a between query
        if ($northmost > $southmost)
        {
            $lat1 = $southmost;
            $lat2 = $northmost;
        }
        else
        {
            $lat1 = $northmost;
            $lat2 = $southmost;
        }

        if ($eastmost > $westmost)
        {
            $lon1 = $westmost;
            $lon2 = $eastmost;
        }
        else
        {
            $lon1 = $eastmost;
            $lon2 = $westmost;
        }

        $result = new stdClass();
        $result->latitude_from = $lat1;
        $result->latitude_to = $lat2;
        $result->longitude_from = $lon1;
        $result->longitude_to = $lon2;

        return $result;
    }
}
?>
