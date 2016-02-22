<?php
App::uses('AppModel', 'Model');
/**
 * IssuedSurvey Model
 *
 */
class IssuedSurvey extends AppModel {

	// public $belongsTo = 'PublishedAppSurvey'
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'published_app_survey_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter a numeric value',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter a published_app_survey_id',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter a numeric value',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter a user_id who will receive this survey',
				'allowEmpty' => false,
				'required' => true
			),
		),
	);
}
