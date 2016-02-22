<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
App::uses('AppController', 'Controller');

App::import('Vendor','HashIds');
/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PagesController extends AppController {
    public $helpers = array('Html', 'Form', 'Session');
    public $components = array('Session', 'RequestHandler');

/**
 * @var array
 */
	public $uses = array('Movement', 'NewsItem', 'Supporter', 'ReferralUrl', 'ShareLink', 'ShareLinkType', 'ShareLinkClick', 'ReferralUrlClick');

/**
 * Displays a view
 *
 * @param mixed What page to display
 * @return void
 * @throws NotFoundException When the view file could not be found
 *	or MissingViewException in debug mode.
 */
	public function display() {
		$path = func_get_args();

		$count = count($path);
		if (!$count) {
			return $this->redirect('/');
		}
		$page = $subpage = $title_for_layout = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}

		// Check if home
		if ($page == 'home') {

			$movements = $this->Movement->get_featured_movements();

			$this->set('movements', $movements);
		}

		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title_for_layout = Inflector::humanize($path[$count - 1]);
		}
		$this->set(compact('page', 'subpage', 'title_for_layout'));

		try {
			$this->render(implode('/', $path));
		} catch (MissingViewException $e) {
			if (Configure::read('debug')) {
				throw $e;
			}
			throw new NotFoundException();
		}
	}

	public function share_link_redirect()
	{
		$this->autoRender = false;
        $this->layout = 'ajax';
		
		$shortUrl = strtolower($this->params->url);
		$hashids = new HashIds(Configure::read('urlSalt'), Configure::read('urlHashMinLength'), Configure::read('hashAlphabet'));
        $hash = $hashids->decrypt($shortUrl);

		if(!$hash){ throw new NotFoundException(); }

		$shareLinkRecord = $this->ShareLink->findById($hash[0]);

		if(!$shareLinkRecord){ throw new NotFoundException(); }

		$this->ShareLinkClick->create();
		$this->ShareLinkClick->set('share_link_id', $shareLinkRecord['ShareLink']['id']);
		$this->ShareLinkClick->set('site_user_id', $this->Session->read('site_user.id'));
		$this->ShareLinkClick->set('user_agent', $this->request->header('User-Agent'));
		$this->ShareLinkClick->set('user_ip', $this->request->clientIp());
		
		$user = $this->Auth->user();
		// if we have a user who has an account and is logged in, save it
		if($user)
    	{
    		$this->ShareLinkClick->set('user_id', $this->Auth->user('id'));
    	}

		$this->ShareLinkClick->set('referer_data', $this->request->referer());
		$this->ShareLinkClick->save();

		switch($shareLinkRecord['ShareLink']['share_link_type_id']){
			case 1:
				$this->redirect(array('controller' => 'movements', 'action' => 'view', $shareLinkRecord['ShareLink']['parent_id']));
				break;
			case 2:
				$this->redirect(array('controller' => 'published_apps', 'action' => 'venue', $shareLinkRecord['ShareLink']['published_app_id'], $shareLinkRecord['ShareLink']['parent_id']));
				break;
			case 3:
				$this->redirect(array('controller' => 'published_apps', 'action' => 'review', $shareLinkRecord['ShareLink']['published_app_id'], $shareLinkRecord['ShareLink']['parent_id']));
				break;
			case 4:
				$this->redirect(array('controller' => 'published_apps', 'action' => 'download', $shareLinkRecord['ShareLink']['published_app_id']));
			default:
				throw new NotFoundException();
				break;
		}
	}

	public function placeholder()
	{
		$this->set('placeholder_active', true);
	}

	// public function beforeFilter() {
 //    	$this->Auth->allow(array('share_link_redirect'));
 //    }
}
