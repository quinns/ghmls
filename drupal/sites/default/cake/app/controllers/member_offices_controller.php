<?php
class MemberOfficesController extends AppController {

	var $name = 'MemberOffices';
	var $helpers = array('Html', 'Form');
	var $paginate = array('conditions' => array('MemberOffice.Office_MLS_ID <>' =>  ''));


	var $cacheAction = array(
		'view/' => 		'5 minutes',
/*
		'index' => '5 minutes',
		'find-office' => '5 minutes',
*/
	);
		
		
			
/*
	function index() {
		$this->MemberOffice->recursive = 1;
		$this->set('memberOffices', $this->paginate());
	}
*/
		function beforeFilter() {
			parent::beforeFilter();
        	$this->Auth->allow('index','view');
	}

	function index() {
		$this->pageTitle = 'Great Homes Member Offices';
		$this->MemberOffice->recursive = 1;	
		$conditions = null;		
		$office = null;
		$city = null;
		if(isset($this->params['url']['city'])){
			$city = $this->params['url']['city'];
		}
		if(isset($this->params['url']['office'])){
			$office = $this->params['url']['office'];
		}
		
		if(isset($this->params['named']['office'])){
			$office = $this->params['named']['office'];
		}	
		if(isset($this->params['named']['city'])){
			$city = $this->params['named']['city'];
		}	
		if(isset($city)){
			$conditions[] = array('MemberOffice.Office_City LIKE' => '%'.$city.'%');			
		}
		if(isset($office)){
			$conditions[] = array('MemberOffice.Office_Long_Name LIKE' => '%'.$office.'%');			
		}
		$memberOffices = $this->paginate('MemberOffice', $conditions);
		if(count($memberOffices) == 1){
/* 			$this->redirect(array('controller' => 'member_office', 'action' => 'view', $memberOffice[0]['MemberOffice']['Office_MLS_ID'])); */
		}
		if(count($memberOffices) == 0){
			$this->Session->setFlash('No agents were found matching your search criteria. Please try again.');
/* 			$this->redirect('index'); */
		}
		$count = 0;
		$this->set('memberOffices',$memberOffices);
		$this->set('office', $office);
		$this->set('city', $city);
		
/*
		debug($office);
		debug($city);
		debug($conditions);
		debug($this->params);
*/
	}



	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid MemberOffice.', true));
			$this->redirect(array('action'=>'index'));
		} 
		$memberOffice = $this->MemberOffice->read(null, $id);

		if(empty($memberOffice)){
			$this->cakeError('error404');
		}
		$this->set('memberOffice', $memberOffice);
	}

/*
	function add() {
		if (!empty($this->data)) {
			$this->MemberOffice->create();
			if ($this->MemberOffice->save($this->data)) {
				$this->Session->setFlash(__('The MemberOffice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The MemberOffice could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid MemberOffice', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->MemberOffice->save($this->data)) {
				$this->Session->setFlash(__('The MemberOffice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The MemberOffice could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->MemberOffice->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for MemberOffice', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->MemberOffice->del($id)) {
			$this->Session->setFlash(__('MemberOffice deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}


	function admin_index() {
		$this->MemberOffice->recursive = 0;
		$this->set('memberOffices', $this->paginate());
	}

	function admin_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid MemberOffice.', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->set('memberOffice', $this->MemberOffice->read(null, $id));
	}

	function admin_add() {
		if (!empty($this->data)) {
			$this->MemberOffice->create();
			if ($this->MemberOffice->save($this->data)) {
				$this->Session->setFlash(__('The MemberOffice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The MemberOffice could not be saved. Please, try again.', true));
			}
		}
	}

	function admin_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid MemberOffice', true));
			$this->redirect(array('action'=>'index'));
		}
		if (!empty($this->data)) {
			if ($this->MemberOffice->save($this->data)) {
				$this->Session->setFlash(__('The MemberOffice has been saved', true));
				$this->redirect(array('action'=>'index'));
			} else {
				$this->Session->setFlash(__('The MemberOffice could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->MemberOffice->read(null, $id);
		}
	}

	function admin_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for MemberOffice', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->MemberOffice->del($id)) {
			$this->Session->setFlash(__('MemberOffice deleted', true));
			$this->redirect(array('action'=>'index'));
		}
	}

*/
}
?>