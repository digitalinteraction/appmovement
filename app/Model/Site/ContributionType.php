<?php
class ContributionType extends AppModel {

	public function get_contribution_type_by_id_or_name($value){
		if(is_numeric($value))
		{
			$contribution_type = $this->find('first', array(
				'conditions' => array(
					'id' => $value
				),
				'limit' => 1
			));
		}
		else
		{
			$contribution_type = $this->find('first', array(
				'conditions' => array(
					'type' => $value
				),
				'limit' => 1
			));
		}

		if(count($contribution_type))
		{
			return $contribution_type;
		}
		else
		{
			return false;
		}
	}
}
?>