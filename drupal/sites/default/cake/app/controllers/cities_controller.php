<?php
class CitiesController extends AppController {

	var $name = 'Cities';
	// var $helpers = array('Html', 'Form', 'Number');
	var $uses = array('City', 'County', 'Listing');
	var $paginate = array('order' => 'City.name ASC');
	
	/*
	function index() {
		$this->City->recursive = 0;
		$this->set('cities', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid City.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('city', $this->City->read(null, $id));
	}

	function admin_index() {
		$this->City->recursive = 0;
		$cities = $this->paginate();
		$count = 0;
		foreach($cities as $city){
			$cities[$count]['Listings']['city'] = $this->Listing->find('count', array('conditions' => array('Listing.city' => $city['City']['name'])));
			$cities[$count]['Listings']['county'] = $this->Listing->find('count', array('conditions' => array('Listing.county' => $city['County']['name'])));
			$count++;
		}
		$this->set(compact('cities'));
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid City.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('city', $this->City->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->City->create();
			if ($this->City->save($this->data)) {
				$this->Session->setFlash(__('The City has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		$counties = $this->City->County->find('list');
		$this->set(compact('counties'));
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid City', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->City->save($this->data)) {
				$this->Session->setFlash(__('The City has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->City->read(null, $id);
		}
		$counties = $this->City->County->find('list');
		$this->set(compact('counties'));
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for City', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->City->del($id)) {
			$this->Session->setFlash(__('City deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}
*/

/*


	function add() {
		if (!empty($this->data)) {
			$this->City->create();
			if ($this->City->save($this->data)) {
				$this->Session->setFlash(__('The City has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		$counties = $this->City->County->find('list');
		$this->set(compact('counties'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid City', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->City->save($this->data)) {
				$this->Session->setFlash(__('The City has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The City could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->City->read(null, $id);
		}
		$counties = $this->City->County->find('list');
		$this->set(compact('counties'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for City', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->City->del($id)) {
			$this->Session->setFlash(__('City deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}




*/


}
?>