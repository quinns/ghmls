<?php
class PropertiesController extends AppController {

	var $name = 'Properties';
	var $paginate = array('order' => 'Property.modified DESC');
	var $helpers = array('html', 'text');
	
	function admin_index(){
		$properties = $this->paginate('Property');
		$this->set('properties', $properties);
	}


	function admin_csv(){
		Configure::write('debug', 0);
		$this->paginate = array('limit' => 100);
		$this->layout = null;
 		$properties = $this->paginate('Property', array('Property.Property_Type' => 'RESI')); 
		$this->set('properties', $properties);
		header('Content-type: text/plain');
	}
}
