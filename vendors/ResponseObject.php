<?php 
class ResponseObject {

    public $meta = array();
    public $errors = array();
    public $data = array();

    public function __construct()
    {
    	$this->meta['success'] = false;
        $this->meta['min_version'] = 1.0;
        $this->meta['session_valid'] = true;
    }

	function output()
	{
		echo json_encode($this);
		exit();
	}
}
?>