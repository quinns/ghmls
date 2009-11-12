<?php

class UsersController extends AppController {

    var $name = 'Users';    
    
    function beforeFilter() {
			parent::beforeFilter();
        	$this->Auth->allow('register');
	}
	
     /**
     *  The AuthComponent provides the needed functionality
     *  for login, so you can leave this function blank.
     */
    function login() {
    }

    function logout() {
    	$this->Session->setFlash('You have logged out');
        $this->redirect($this->Auth->logout());
    }
    
    function admin_login(){
    	$this->redirect(array('controller' => 'users', 'action' => 'login', 'admin' => 0));
    	exit();
    }
    
    function register() {
    die('This function is currently not available');
    if ($this->data) {
    	$user = $this->User->findByUsername($this->data['User']['username']);
    	if(!empty($user)){
    		$this->Session->setflash('That username is already in use.');
    		$this->render();
    		
    	}
        if ($this->data['User']['password'] == $this->Auth->password($this->data['User']['password2'])) {
            $this->User->create();
            $this->User->save($this->data);
            $this->flash('New account has been created', '/');
        } else {
        	$this->Session->setflash('Passwords do not match');
        }
    }
}

    
}


?>