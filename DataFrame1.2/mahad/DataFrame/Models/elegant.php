<?php
namespace DataFrame\Models;
use DataFrame\Database\Elegant\Model;
use DataFrame\Universal;
	class Elegant extends Model{
		use Universal;
		public $login_id;
		public $req;
		public $res;
		public function __construct(array $options = array()){
			$this->login_id = $this->getLoggedInUserId();
			$this->req = $this->getRequest();
			$this->res = $this->getResponse();
			parent::__construct($options);
		}
		
	}