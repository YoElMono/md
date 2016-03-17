<?php
use Phalcon\Events\Event,
	Phalcon\Mvc\User\Plugin,
	Phalcon\Acl;

use Phalcon\Db\Result\Pdo,
	Phalcon\Tag as Tag,
	Phalcon\Db\Column;


class BannersPlugin extends Plugin
{



	public function get(){
		$Data = array();
		$Table = new Banners();
		$Result = $Table->find(array(
				"columns" => "id ,img",
			    "conditions" => "status=1 AND tipo=:tipo:",
			    "bind" => array("tipo" => "Principal"),
			    "bindTypes" => array("tipo" => Column::BIND_PARAM_STR),
			    "limit" => 10
		));
		foreach($Result as $value){
			$Data[] = array("id" => $value->id , "img" => $value->img);
		}
		return $Data;
	}








}