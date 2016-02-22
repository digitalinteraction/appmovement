<?php
class CommentType extends AppModel {
	public $name = 'CommentType';

	public function get_comment_type_by_id_or_name($value){
		if(is_numeric($value))
		{
			$comment_type = $this->find('first', array(
				'conditions' => array(
					'id' => $value
				),
				'limit' => 1
			));
		}
		else
		{
			$comment_type = $this->find('first', array(
				'conditions' => array(
					'type' => $value
				),
				'limit' => 1
			));
		}

		if(count($comment_type))
		{
			return $comment_type;
		}
		else
		{
			return false;
		}
	}
}
?>