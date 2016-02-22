<?php
class CaseStudiesController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler', 'Paginator');
    public $uses = array('CaseStudy');

    // View all case studies
    public function index() {
        $this->set('subpage', 'Case Studies');
        $this->set('title_for_layout', 'Case Studies');

        // Load case studies from database

        $case_studies = $this->CaseStudy->find('all');

        $this->set(array('case_studies' => $case_studies));

    }

}
?>;