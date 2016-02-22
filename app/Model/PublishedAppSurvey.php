<?php
App::uses('AppModel', 'Model');
/**
 * PublishedAppSurvey Model
 *
 */
class PublishedAppSurvey extends AppModel {


	public $belongsTo = array('Survey', 'SurveyType');

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'published_app_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter numeric values only',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter a published_app_id',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'survey_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter numeric values only',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter a survey_id',
				'allowEmpty' => false,
				'required' => true
			),
		),
	);
}
