<?php
App::uses('AppModel', 'Model');
/**
 * Survey Model
 *
 */
class Survey extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'url' => array(
			'alphaNumeric' => array(
				'rule' => array('alphaNumeric'),
				'message' => 'Please enter a survey url',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter a survey url',
				'allowEmpty' => false,
				'required' => true
			),
		),
		'start' => array(
			'datetime' => array(
				'rule' => array('datetime'),
				'message' => 'Please only enter a datetime',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter a start datetime',
				'allowEmpty' => false,
				'required' => true,
			),
		),
		'duration_in_days' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Please enter a whole number (in days)',
				'allowEmpty' => false,
				'required' => true
			),
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Please enter the duration in days that you wish for the survey to be presented',
				'allowEmpty' => false,
				'required' => true
			),
		)
	);
}
