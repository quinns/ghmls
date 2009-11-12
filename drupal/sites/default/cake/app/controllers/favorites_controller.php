<?php

class FavoritesController extends AppController {
	var $components = array('Session', 'Cookie');
    var $name = 'Favorites';    
	var $uses = array('Favorite', 'Property');
	    
    function beforeFilter() {
        	$this->Auth->allow('*');
	}
	
   function index(){
   		$this->redirect(array('controller' => 'properties', 'action' => 'favorites'));
/*
   		$this->pageTitle = 'Favorites';
   		$favorites = $this->Cookie->read('favorites');
   		if(is_array($favorites)){
			$favorites = array_unique($favorites);	
   		}	
		$properties = $this->Property->find('all', array('fields' => 'id, ML_Number_Display, Marketing_Remarks, City, County, Search_Price', 'conditions' => array('ML_Number_Display' => $favorites)));
		$this->set(compact('properties', 'favorites'));
*/
   }

	function list_favorites(){
	   		$favorites = $this->Cookie->read('favorites');
			return($favorites);
	}
	
	function add($id){
		$this->autoRender = false;
		$referer = $this->referer();
		if(!empty($id)){
			$this->Property->recursive = -1;
			$property = $this->Property->read('ML_Number_Display', $id);
			$favorites =  $this->Cookie->read('favorites');
			$id = $property['Property']['ML_Number_Display'];
			$favorites[$id] = $id;
 			if(!empty($property)){
				$this->Cookie->write('favorites', $favorites, false, time("+1 year"));
				$this->Session->setFlash('Property ID '.$property['Property']['ML_Number_Display'].' has been added to your favorites list.');
				$this->Session->write('added', $property['Property']['ML_Number_Display']);
			} else {
				$this->Session->setFlash('Invalid property ID. The listing may have expired.');
			}
			if(!empty($referer) && $referer != '/'){
				$this->redirect($referer);
			} else {
				$this->redirect(array('controller' => 'properties', 'action' => 'favorites'));
			}

		}
	}
	
	function remove($id){
		$referer = $this->referer();
		$this->autoRender = false;
		$favorites = $this->Cookie->read('favorites');
		unset($favorites[$id]);
		$this->Property->recursive = -1;
		$property = $this->Property->read('id, ML_Number_Display', $id);
		if(!empty($property)){
			$this->Cookie->write('favorites', $favorites, false, time("+1 year"));
			$this->Session->setFlash('Property MLS ID '.$id.' was removed from your favorites list.');
			$this->Session->write('removed', $property['Property']['ML_Number_Display']);

		} else {
			$this->Session->setFlash('The property you are attempting to remove is no longer valid.');
		}
		if(!empty($referer) && $referer != '/'){
			$this->redirect($referer);
		} else {
			$this->redirect(array('controller' => 'properties', 'action' => 'favorites'));
		}
	}

	function clear(){
		$referer = $this->referer();
		$this->autoRender = false;	
		$this->Session->setFlash('All properties have been removed from your favorites list.');
		$this->Cookie->destroy('favorites');
		if(!empty($referer) && $referer != '/'){
			$this->redirect($referer);
		} else {
			$this->redirect(array('controller' => 'properties', 'action' => 'favorites'));
		}
	}
    
}

