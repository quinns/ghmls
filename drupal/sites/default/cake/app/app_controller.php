<?php
	class AppController extends Controller {
		var $helpers = array('Html', 'Form', 'Number', 'Time', 'Javascript', 'Ajax', 'Cache', 'Text', 'Rss', 'Xml');
		var $components = array('RequestHandler', 'Auth', 'Session', 'Cookie'); 
		var $cacheAction = array(
			'mls' => '5 minutes'
		);
   
		var $uses = array('Listing', 'City', 'County', 'Property', 'PropertyType', 'Client');
		var $google_map_API_key = 'ABQIAAAAlPjZCqxWs0OAXuFm9YSgmhRg-aVY9BlnwNEvhA8_bKfCb7GClxQaq4A-6B8Wlpvs8JZOWjHwuWZUkw';
		var $accessKey = '44c7e4c38a6b28fdde9bd780c20b79f1';
		var $transaction_types = array('S' => 'For Sale', 'L' => 'For Lease');
/* 		var $layout = 'garland'; */
		var $resi_subtypes = array(
			'Single Family' => 'Single Family', 
			'Condo/Coop' => 'Condo/Coop', 
			'Farms/Ranches' =>'Farms/Ranches',
		);
		var $hidden_subtypes = array(
			'Acreage', 
			'Agricultural', 
			'Floating Home', 
			'Industrial', 
			'Mixed Use', 
			'Office', 
			'Other', 
			'Research & Develop', 
			'Retail', 
			'Warehouse', 		
		);
		
				
		function get_client($domain = null){ // determine which client is loading the app
			$domains = $this->Client->_client_domains();
			foreach($domains as $domain){
				if(in_array(HTTP_HOST, $domain['alias'])){
					$client_data = $domain;
				}
			}
			if(!empty($client_data)){
				return $client_data;
			
			} else {
				return null;
			}
		}
		
		function domain_information(){ // combine various domain- and client- related information
			$info['domain'] = HTTP_HOST;
/* 			$info['clients'] = $this->Client->_client_domains(); */
			$info['this_client'] = $this->get_client($info['domain']);
			$this->params['domain_info'] = $info;
			return $info;
		
		}
				
		// following function from http://dev.sypad.com/projects/drake/documentation/known-issues/
		function redirect($url, $status=null) {
                if (defined('DRAKE'))
                {
                        $drake =& Drake::getInstance();   
                        $url = $drake->getUrl($url);
                }   
                return parent::redirect($url, $status);
        }
        
        function beforeFilter(){
        			$domain_info = $this->domain_information();
			//	die(debug($domain_info));
        }
        
        function beforeRender() {

/* 			debug($this->params['domain_info']['this_client']);  */
			if(empty($this->params['domain_info']['this_client']) || !isset($this->params['domain_info']['this_client'])){
			//	die('FAIL: Could not read configuration data.');
			}
/*
			// invoke drupal (not working yet)
		    $currdir=getcwd();
		   	 chdir($_SERVER['DOCUMENT_ROOT']);
		    require_once("./includes/bootstrap.inc");
		    @ drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
		    chdir($currdir);

			global $theme_key;
			$themes = list_themes();
			$theme_object = $themes[$theme_key];
			// print all the object fields
			var_dump($theme_object);
			$current_theme = variable_get('theme_default','none');
			$themes = list_themes();
			$theme_object = $themes[$current_theme];
			// print all the object fields
			var_dump($theme_object);
*/
			// render our client's "cakeified" drupal theme
	

			if(isset($this->params['domain_info']['this_client']['theme']) && is_file(LAYOUTS.DS.$this->params['domain_info']['this_client']['theme'].'.ctp')){
				$this->layout = $this->params['domain_info']['this_client']['theme'];
			} else if($this->layout == 'default'){
/*         		$this->layout = 'garland'; */
        	}


        //	debug(list_themes());
        	//	drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
/*         	debug($this->params); */
			// we want to deny any attempts to access this from outside GreatHomes.org domain
			$host = strtolower($_SERVER['HTTP_HOST']);
			if($host != 'greathomes.org' && $host != 'www.greathomes.org'){
/* 				$this->redirect('http://'.$host, 301); */
			}        
        
     //   	debug($this->RequestHandler);
        	
			if(isset($this->params['named']['media']) && $this->params['named']['media'] == 'print'){
				$this->layout = 'print';
			}
/*

			echo '<!-- ';
        	echo('CONTROLLER: '.$this->params['controller'])."\r\n";
        	echo('ACTION: '.$this->params['action'])."\r\n";
			echo '-->';
*/

/*
			debug($_SERVER);
			debug($this->params);
*/

        	$this->params['user'] = $this->Auth->user();
        	$this->Property->recursive = -1;
        	$categories = $this->PropertyType->find('count');
        	$this->params['db_stats']['total'] = $this->Property->find('count');
        	$this->params['db_stats']['categories'] = $categories;
        	$this->params['db_stats']['counties'] =  $this->County->find('count');
        	
        	$this->params['favorites'] = $this->Cookie->read('favorites');
        	$this->params['added'] = $this->Session->read('added');
        	$this->params['removed'] = $this->Session->read('removed');
        	if(isset($this->params['admin']) && $this->params['admin'] == 1){
        		$this->layout = 'admin';
        	}

        }



	
        function afterFilter(){
			$this->Session->delete('added');
			$this->Session->delete('removed');
        }
	}
?>