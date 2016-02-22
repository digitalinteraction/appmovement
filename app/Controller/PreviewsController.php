<?php

App::import('Vendor','HashIds');

class PreviewsController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');
	public $uses = array();

    // Show Geolocation Venue Preview
    public function geolocation_venue() {

        $this->layout = 'preview';

        $this->set('primary_colour', '#' . $this->request->query['primary_colour']);
        $this->set('pin_colour', '#' . $this->request->query['pin_colour']);
        $this->set('star_colour', '#' . $this->request->query['star_colour']);

    }

    // Show Geolocation Map Preview
    public function geolocation_map() {

        $this->layout = 'preview';

        $this->set('primary_colour', '#' . $this->request->query['primary_colour']);
        $this->set('pin_colour', '#' . $this->request->query['pin_colour']);
        $this->set('star_colour', '#' . $this->request->query['star_colour']);

    }
}
?>