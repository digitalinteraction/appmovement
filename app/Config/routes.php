<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	/* Home Page */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));

	/* Other pages */
	Router::connect('/about', array('controller' => 'pages', 'action' => 'display', 'about'));
	Router::connect('/contact', array('controller' => 'pages', 'action' => 'display', 'contact'));	
	Router::connect('/terms', array('controller' => 'pages', 'action' => 'display', 'terms'));
	Router::connect('/privacy', array('controller' => 'pages', 'action' => 'display', 'privacy'));
	Router::connect('/faq', array('controller' => 'pages', 'action' => 'display', 'faq'));
	Router::connect('/press', array('controller' => 'pages', 'action' => 'display', 'press'));

	/* Users */ 
	Router::connect('/login', array('controller' => 'users', 'action' => 'login'));	
	Router::connect('/register', array('controller' => 'users', 'action' => 'register'));	
	Router::connect('/logout', array('controller' => 'users', 'action' => 'logout'));
	Router::connect('/dashboard', array('controller' => 'users', 'action' => 'dashboard'));
	Router::connect('/profile/*', array('controller' => 'users', 'action' => 'view'));
	Router::connect('/forgot', array('controller' => 'users', 'action' => 'forgot'));
	Router::connect('/reset', array('controller' => 'users', 'action' => 'reset'));
	Router::connect('/admin', array('controller' => 'users', 'action' => 'admin'));
	Router::connect('/users/:action/*', array('controller' => 'users'));

	/* Movements */
	Router::connect('/start', array('controller' => 'movements', 'action' => 'start'));
	Router::connect('/suggest/*', array('controller' => 'movements', 'action' => 'suggest_type'));
	Router::connect('/onboarding/*', array('controller' => 'movements', 'action' => 'onboarding'));
	Router::connect('/view/*', array('controller' => 'movements', 'action' => 'view'));
	Router::connect('/edit/*', array('controller' => 'movements', 'action' => 'edit'));
	Router::connect('/discover', array('controller' => 'movements', 'action' => 'index'));
	Router::connect('/overview', array('controller' => 'movements', 'action' => 'overview'));
	Router::connect('/code', array('controller' => 'movements', 'action' => 'code'), array('pass' => array('id'), 'id' => '[0-9]+'));
	Router::connect('/confirm_support', array('controller' => 'supporters', 'action' => 'confirm_support'));
	Router::connect('/promote/:movement_id/add', array('controller' => 'promoters', 'action' => 'add'), array('pass' => array('movement_id'), 'movement_id' => '[0-9]+'));
	Router::connect('/movements/:action/*', array('controller' => 'movements'));

	/* Movement Updates */
	Router::connect('/updates/add', array('controller' => 'movements', 'action' => 'add_movement_update'));
	Router::connect('/updates/delete/*', array('controller' => 'movements', 'action' => 'delete_movement_update'));
	
	/* Design Tasks */
	Router::connect('/design/:id/task/:task', array('controller' => 'tasks', 'action' => 'view'), array('pass' => array('id', 'task'), 'id' => '[0-9]+', 'task' => '[0-9]+'));
	Router::connect('/tasks/create/*', array('controller' => 'tasks', 'action' => 'create_design_tasks'));

	/* Contributions */
	Router::connect('/design/:id/task/:task/contribution/add', array('controller' => 'contributions', 'action' => 'add'), array('pass' => array('id', 'task'), 'id' => '[0-9]+', 'task' => '[0-9]+'));
	Router::connect('/contributions/add', array('controller' => 'contributions', 'action' => 'add'));
	Router::connect('/contributions/add/file', array('controller' => 'contributions', 'action' => 'add_file'));
	Router::connect('/contributions/delete/*', array('controller' => 'contributions', 'action' => 'delete'));
	Router::connect('/contributions/report/*', array('controller' => 'contributions', 'action' => 'report'));

	/* Design Phase */
	Router::connect('/design/*', array('controller' => 'tasks', 'action' => 'landing'));

	/* Launch Phase */
	Router::connect('/launch/*', array('controller' => 'movements', 'action' => 'launch'));		

	/* AJAX Requests */
	Router::connect('/stats/:movement_id', array('controller' => 'supporters', 'action' => 'supporter_stats'), array('pass' => array('movement_id'), 'movement_id' => '[0-9]+'));
	Router::connect('/imageuploader', array('controller' => 'movements', 'action' => 'image_uploader'));

	/* Comments */
	Router::connect('/comments/get/:parent_id/type/:type_id', array('controller' => 'comments', 'action' => 'get'), array('pass' => array('parent_id', 'type_id'), 'parent_id' => '[0-9]+'));
	Router::connect('/comments/add', array('controller' => 'comments', 'action' => 'add'));
	Router::connect('/comments/delete/*', array('controller' => 'comments', 'action' => 'delete'));
	Router::connect('/comments/vote', array('controller' => 'comments', 'action' => 'vote'));
	Router::connect('/comments/*', array('controller' => 'comments', 'action' => 'test'));

	/* Votes */
	Router::connect('/votes/vote', array('controller' => 'votes', 'action' => 'vote'));

	/* Preview */
	Router::connect('/preview/:action', array('controller' => 'previews'));

	/* Generate API */
	Router::connect('/generate/app/*', array('controller' => 'generate', 'action' => 'generate_app'));
	Router::connect('/generate/config/*', array('controller' => 'generate', 'action' => 'fetch_config'));
	Router::connect('/generate/marker/*', array('controller' => 'generate', 'action' => 'marker'));
	Router::connect('/generate/:action', array('controller' => 'generate'));
	
	/* Mobile API */
	Router::connect('/support/*', array('controller' => 'supporters', 'action' => 'add'));	
	Router::connect('/check/*', array('controller' => 'supporters', 'action' => 'check'));	
	Router::connect('/api/confirm', array('controller' => 'supporters', 'action' => 'confirm'));
	Router::connect('/api/recent', array('controller' => 'movements', 'action' => 'recent'));
	Router::connect('/api/user/info', array('controller' => 'users', 'action' => 'info'));

	Router::connect('/mobile/about/*', array('controller' => 'mobile', 'action' => 'about'));
	Router::connect('/mobile/terms/*', array('controller' => 'mobile', 'action' => 'terms'));
	Router::connect('/mobile/survey/complete', array('controller' => 'mobile', 'action' => 'survey_complete'));
	Router::connect('/mobile/survey/*', array('controller' => 'mobile', 'action' => 'survey'));

	/* Template Global API */
	Router::connect('/api/auth/:action', array('controller' => 'auth'));
	Router::connect('/api/auth/:action/*', array('controller' => 'auth'));
	Router::connect('/api/share', array('controller' => 'auth', 'action' => 'share'));
	Router::connect('/api/:identifier/version', array('controller' => 'auth', 'action' => 'version'), array('pass' => array('identifier')));
	Router::connect('/api/:identifier/setup', array('controller' => 'auth', 'action' => 'setup'), array('pass' => array('identifier')));
    Router::connect('/api/:controller/:identifier/:action');
    Router::connect('/api/:controller/:identifier/:action/*');

    /* Published Apps */
    Router::connect('/apps/*', array('controller' => 'published_apps', 'action' => 'index'));

    /* Geolocation Endpoints */
    Router::connect('/v/*', array('controller' => 'published_apps', 'action' => 'venue'));
    Router::connect('/r/*', array('controller' => 'published_apps', 'action' => 'review'));
    Router::connect('/m/*', array('controller' => 'published_apps', 'action' => 'map'));
    Router::connect('/p/*', array('controller' => 'published_apps', 'action' => 'photos'));
    Router::connect('/s/*', array('controller' => 'published_apps', 'action' => 'stats'));
    Router::connect('/d/*', array('controller' => 'published_apps', 'action' => 'download'));
    
    /* Notification Cron Jobs */
	Router::connect('/notifications/send', array('controller' => 'notifications', 'action' => 'send'));
	Router::connect('/notifications/view/*', array('controller' => 'notifications', 'action' => 'view'));
	Router::connect('/notifications/generate', array('controller' => 'notifications', 'action' => 'generate'));
	Router::connect('/notifications/generate/built', array('controller' => 'notifications', 'action' => 'built'));
	Router::connect('/notifications/clear', array('controller' => 'notifications', 'action' => 'clear')); 

	/* Feedback */
	Router::connect('/feedback/submit', array('controller' => 'feedbacks', 'action' => 'submit')); 

	/* Logging */
	Router::connect('/logs/share_button/:movement_id/:ref_link/:type', array('controller' => 'logs', 'action' => 'share_button'), array('pass' => array('ref_link', 'movement_id', 'type'))); 

	/* Tranlsations */
	Router::connect('/translations/generate', array('controller' => 'tabletranslations', 'action' => 'generate'));

	Router::connect('/merge', array('controller' => 'pages', 'action' => 'merge_ref_url'));

	/* All Other Routes */
	Router::connect('/*', array('controller' => 'pages', 'action' => 'share_link_redirect'));

	Router::parseExtensions();

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
