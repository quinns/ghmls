<?php
class MemberAgentsController extends AppController {

	var $name = 'MemberAgents';
	var $helpers = array('Html', 'Form');
	var $paginate = array('limit' => 20, 'order' => 'MemberAgent.Agent_Last_Name, MemberAgent.Agent_First_Name ASC');
	
	var $cacheAction = array(
		'view/' => 		'5 minutes',
/* 		'index' => '5 minutes', */
/* 		'find-agent' => '5 minutes', */
	);
	
		function beforeFilter() {
        	$this->Auth->allow('index','view');

	}

	
	function index() {
		$this->pageTitle = 'Great Homes Member Agents';
		$this->MemberAgent->recursive = 1;	
		$conditions = null;		
		$first_name = null;
		$last_name = null;
		$office = null;
		$city = null;
		if(isset($this->params['named']['first_name'])){
			$first_name = $this->params['named']['first_name'];
		}
		if(isset($this->params['url']['first_name'])){
			$first_name = $this->params['url']['first_name'];
		}
		if(isset($this->params['named']['last_name'])){
			$last_name = $this->params['named']['last_name'];
		}
		if(isset($this->params['url']['last_name'])){
			$last_name = $this->params['url']['last_name'];
		}	
		if(isset($this->params['url']['city'])){
			$city = $this->params['url']['city'];
		}
		if(isset($this->params['url']['office'])){
			$office = $this->params['url']['office'];
		}	
		if(isset($first_name)){
			$conditions[] = array('MemberAgent.Agent_First_Name LIKE' => '%'.$first_name.'%');		
		}
		if(isset($last_name)){
			$conditions[] = array('MemberAgent.Agent_Last_Name LIKE' => '%'.$last_name.'%');	
		}
		if(isset($city)){
			$conditions[] = array('MemberOffice.Office_City LIKE' => '%'.$city.'%');			
		}
		if(isset($office)){
			$conditions[] = array('MemberOffice.Office_Long_Name LIKE' => '%'.$office.'%');			
		}
		$memberAgents = $this->paginate('MemberAgent', $conditions);
		if(count($memberAgents) == 1){
			$this->redirect(array('controller' => 'member_agents', 'action' => 'view', $memberAgents[0]['MemberAgent']['Agent_MLS_ID']));
		}
		if(count($memberAgents) == 0){
			$this->Session->setFlash('No agents were found matching your search criteria. Please try again.');
			$this->redirect('index');
		}
		$count = 0;
		$this->set('memberAgents',$memberAgents);
		$this->set('first_name', $first_name);
		$this->set('last_name', $last_name);
		$this->set('office', $office);
		$this->set('city', $city);
		$this->set('conditions', $conditions);
	}




	function view($id = null) {
		$memberAgent = $this->MemberAgent->read(null, $id);
		if(empty($memberAgent)){
			$this->CakeError('error404');
		}
		$this->set('memberAgent', $memberAgent);
	}

	
}
?>