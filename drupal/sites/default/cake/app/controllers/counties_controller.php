<?php
class CountiesController extends AppController {

	var $name = 'Counties';
	// var $helpers = array('Html', 'Form', 'Text');
	var $uses = array('County', 'City', 'Listing');

	var $cacheAction = array(
		'find_city' => 	'5 minutes',
		'index/' => '5 minutes',
		'index' => '5 minutes',
	);

	// var $scaffold;
   function beforeFilter() {
        	$this->Auth->allow('find_city', 'index', 'view', 'get_primary_counties');		
    }

	function find_city(){
		$this->params['jquery'] = true;		
		$this->redirect('/listings/search/option:default');
		exit();
		$cities = $this->City->find('all', array('fields' => 'City.id, City.name, County.id','conditions' => array('County.id <> ""', 'City.status_id' => 1), 'order' => 'City.name ASC'));
		$counties = $this->County->find('all', array('fields' => 'County.name','conditions' => array('County.id <> ""')));
		$county_list = array();
		foreach($counties as $county){
			if(!empty($county['City'])){
				$county_list[$county['County']['id']] = $county['County']['name'];
			}
		}
		$counties = $county_list;
		$this->set(compact('counties', 'cities'));
	}
		

	function get_primary_counties(){
		$this->autoRender = false;
		$this->County->recursive = -1;
		$counties = $this->County->find('all', array('conditions' => array('County.name' => array('Lake', 'Sonoma', 'Mendocino', 'Marin', 'Napa', 'Solano')), 'fields' => 'id, name'));
		foreach($counties as $value){
			$output[$value['County']['id']] = $value['County']['name'];
		}
	//	pr($output);
	return $output;
	}

	function index() {
		$this->County->recursive = 0;
		$counties =  $this->County->find('list', array('conditions' => array('status_id' => 1)));
		//debug($counties);
		asort($counties);
		
		foreach($counties as $key => $value){
			$summary[$key] = $this->Property->find('count', array('conditions' => array('Property.county' => array($value))));
		}
/* 		debug($summary); */
		$this->set(compact('counties', 'summary'));
		if($this->layout == 'ajax' || $this->layout == 'mobile'){
			$this->render('index_mobile');
		}
		
		
	}

	/*


	function view($id = null) {
		$county = $this->County->read(null, $id);
		if(empty($county)){
			  $this->cakeError('error404');
		} else {
			$this->set(compact('county'));
		}
	}


	function admin_index() {
		$this->County->recursive = 0;
		$counties = $this->paginate();
		$count = 0;
		foreach($counties as $county){
			$counties[$count]['City']['count'] = $this->City->find('count', array('conditions' => array('City.county_id' => $county['County']['id'])));
			$counties[$count]['Listing']['count'] = $this->Listing->find('count', array('conditions' => array('Listing.county' => $county['County']['name'])));
			$count++;
		}
		$this->set(compact('counties'));
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid County.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('county', $this->County->read(null, $id));
		$this->set(compact('county'));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->County->create();
			if ($this->County->save($this->data)) {
				$this->Session->setFlash(__('The County has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The County could not be saved. Please, try again.', true));
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid County', true));
			$this->redirect(array('action'=>'index'));
		}
			$cities = $this->County->City->find('list', array('order' => 'City.name ASC', 'conditions' => array("City.county_id = '' OR City.county_id IS NULL OR City.county_id = $id")));
			$cities_in_county = $this->City->find('list', array('order' => 'City.name ASC', 'conditions' => array('City.county_id' => $id)));
			$this->set('cities_in_county', $cities_in_county);
			$this->set('cities_outside_county', $this->City->find('list', array('order' => 'City.name ASC', 'conditions' => array('City.county_id <>' => $id))));
			$this->set(compact('cities'));
		if (!empty($this->data)) {
			if ($this->County->save($this->data)) {
				foreach($cities_in_county as $key => $value){
					$city['City']['id'] = $key;
					$city['City']['county_id'] = null;
					$this->City->save($city);
				}
				foreach($this->data['County']['City'] as $value){
					$city['City']['id'] = $value;
					$city['City']['county_id'] = $id;
					$this->City->save($city);
				}
				$this->Session->setFlash(__('The County has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The County could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->County->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for County', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->County->del($id)) {
			$this->Session->setFlash(__('County deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}


	function admin_bulk_update(){
		$this->County->recursive = -1;
		$this->autoRender = false;
		$listings = $this->Listing->find('all', array('fields' => 'DISTINCT Listing.County'));
		$count = 0;
		foreach($listings as $value){
			$counties[$count]['name'] = $value['Listing']['County'];
			$counties[$count]['status_id'] = 1;
			$count ++;
		}
		sort($counties);
		$message[] = '--- Setting up county updates ---';
		foreach($counties as $data){
			$current = $this->County->find('first', array('fields' => 'County.name', 'conditions' => array('County.name' => $data['name'])));
			if(!empty($current)){
				$message[] = 'County "'.$current['County']['name'].'" already exists.';
			} else {
				$this->County->create();
				$this->County->save($data);
				$message[] = 'Updated county: '.$data['name'];
			}

		}
			$message[] = '--- Finished County Updates ---';

			return($message);
	}
	*/

/*

	function add() {
		if (!empty($this->data)) {
			$this->County->create();
			if ($this->County->save($this->data)) {
				$this->Session->setFlash(__('The County has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The County could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid County', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->County->save($this->data)) {
				$this->Session->setFlash(__('The County has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The County could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->County->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for County', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->County->del($id)) {
			$this->Session->setFlash(__('County deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}



*/

}
?>