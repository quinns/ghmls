<?php
class PropertiesController extends AppController {
	var $name = 'Properties';
	var $paginate = array('order' => 'PropertyType.sort_order ASC, Property.Search_Price ASC');
	var $uses = array('Property', 'City', 'County', 'UpdateLog', 'Coordinate', 'BusinessOpp', 'Listing', 'PropertyType', 'MemberOffice', 'MemberAgent');
	var $components = array('Geocoder', 'Session', 'Cookie');
//	var $uses = array('Property', 'UpdateLog');
//	var $helpers = array('Text', 'GoogleMap');
	var $helpers = array('Text', 'GoogleMap', 'Ajax', 'Javascript');	
//	var $cache_expiration = '5 minutes';
	var $cacheAction = array(
		'view/' => 		'5 minutes',
		'property/'	 => '5 minutes',
		'city/'  => 	'5 minutes',
		'region/' => 	'5 minutes',
		'county/' => 	'5 minutes',
		'counties/' => 	'5 minutes',
		'zip/' =>		'5 minutes',
/*
		'search/' =>	'5 minutes',
*/
		'agent/' =>		'5 minutes',
		'office/' =>	'5 minutes',
		'types/' =>	'5 minutes',
		'open_houses/' => '5 minutes',
		//'index/' => '5 minutes',
		'search/county:sonoma' => '5 minutes',
	);
	
	var $pagination_limits = array(5 => 5, 10 => 10, 20 => 20, 50 => 50, 100 => 100);
	
	function start(){
		$this->render();
	}
	
	
	function open_house_start(){
		$this->autoRender = false;
		$this->redirect(array('controller' => 'properties', 'action' => 'search', 'open_house:1'));
	}

	function beforeFilter() {
			parent::beforeFilter();
        	$this->Auth->allow('index','view', 'region', 'search_city', 'counties', 'city', 'zip', 'search', 'search_result', 'thumbnail', 'fullsize_image', 'image', 'listing_agent', 'listing_office', 'search_agents', 'tag_cloud', 'types', 'all_agents', 'open_houses', 'favorites', 'view_favorites', 'start');
        	$this->set('pagination_limits', $this->pagination_limits);
        	$client_data = $this->_client_data();
        	$this->set('client_data', $client_data);
        	$client_id = $client_data['id'];
/*
        	if(!isset($this->params['named']['filter']) || !isset($this->params['named']['client'])){
        		$this->redirect('http://'.HTTP_HOST.$_SERVER['REQUEST_URI']. '/filter:all/client:'.$client_id);
        		exit();
        	}
*/
	}

	
	function types(){
		$this->pageTitle = 'Property Types';
		$types = $this->PropertyType->findAll();
		$count = 0;
		foreach($types as $value){
			$types[$count]['PropertyType']['count'] = $this->Property->find('count', array('conditions' => array('Property.Property_Type' => $types[$count]['PropertyType']['code'])));
			$count++;
		}
		$this->set(compact('types'));
	}
	
	function _client_data(){		
		if(isset($this->params['domain_info']['this_client']) && !isset($this->params['named']['all'])){
			$output = $this->params['domain_info']['this_client'];
			return($output);
		} else {
			return(null);
		}
	}
	
	function _parse_client_param(){
		$client_data = $this->_client_data();
/* 		debug($client_data); */
		$param = @$this->params['named']['filter'];
		if(!empty($param)){
			$conditions = $client_data['search_conditions'][$param]['conditions'];
			return ($conditions);
		} else{
			return(null);
		}
	}
	
	

	function index($property_type = null) {

		$this->pageTitle = 'Properties Index';
		$conditions = null;	
		$conditions[] = $this->_parse_client_param(); // GET CLIENT PARAMETERS
		$title = $this->_client_data();
		$this->pageTitle = $title['search_conditions'][$this->params['named']['filter']]['name'];
/* 		debug($title); */
/* 		debug($conditions); */
		if($property_type != null){
			$type = $this->PropertyType->findBySlug($property_type);			
			$conditions['PropertyType.code'] = strtoupper($type['PropertyType']['code']);
			if(!empty($type)){
				$this->pageTitle .= ': '.$type['PropertyType']['name'];
				$this->params['search_type'] = $type['PropertyType']['code'];
			}
		}
		

		$properties = $this->paginate('Property', $conditions);
		$property_type_filter = null;
		$count = 0;
		if(isset($this->params['type'])){
			$property_type_filter = $this->params['type'];
		}
		foreach($properties as $property){
			$city = $this->City->find('first', array('conditions' => array('City.name' => $property['Property']['City'])));
			//RESI_Address_on_Internet_Desc
			$properties[$count]['City'] = $city['City'];
			$properties[$count]['County'] = $city['County'];

			$code = $properties[$count]['Property']['Property_Type'];
			$address_display_field = $code.'_Address_on_Internet_Desc';
			$properties[$count]['code'] = $address_display_field;
			$count++;
		}
		//debug($type);
		$this->set(compact('properties', 'type'));
		if($this->layout == 'mobile'){
			$this->render('index_mobile');
		}
	}

	function all(){
		/*
		$properties = $this->County->find('list', array('order' => 'County.name ASC'));
		debug($properties);
		*/
			$counties = $this->County->find('list', array('conditions' => array('County.status_id' => 1), 'order' => 'County.name ASC', 'fields' => 'County.name'));
			$output = array();
			foreach($counties as $key => $value){	
				$cities = $this->City->find('list', array('conditions' => array('City.county_id' => $key), 'order' => 'City.name ASC'));
				//debug($cities);
				if(!empty($cities)){
					$output[$value] = $cities;
				}	
			}
			$cities = $output;	
			$this->set('output', $output);
	}
	
	function region($region = null, $category = null) {
		if(isset($this->params['url']['region'])){
			$this->redirect(array('controller' => 'properties', 'action' => 'region', urlencode(strtolower($this->params['url']['region']))));
			exit();
		}
		if(is_numeric($region)){
			$county = $this->County->read(null, $region);
			$region = $county['County']['name'];
			$this->redirect(array('controller' => 'properties', 'action' => 'region', strtolower(Inflector::slug($county['County']['name']))));
			exit();
		}
		$conditions['Property.county'] = Inflector::humanize($region);
		$property_type = null;
		if($category != null){
			$property_type = $this->PropertyType->findBySlug($category);
			if(!empty($property_type)){
				$conditions['Property.property_type'] = $property_type['PropertyType']['code'];
			}
		}
		$this->County->recursive = -1;
		$area =  $this->County->findByName(Inflector::humanize($region));
		$this->set('area', $area['County']['name']);
/* 		debug($area); */
		$this->set('property_type', $property_type);

		$properties = $this->paginate('Property', $conditions ); // try to find via string



		$my_county = $this->County->find('first', array('conditions' => array('County.name' => Inflector::humanize($region))));
		if(!empty($properties)){
			$count = 0;
			foreach($properties as $property){
				$city = $this->City->find('first', array('conditions' => array('City.name' => $property['Property']['City']), 'fields' => 'City.id, City.name, County.id, County.name'));
				$properties[$count]['City'] = $city['City'];
				$properties[$count]['County'] = $city['County'];
				$count++;
			}
			if(count($properties) == 1){ // only 1 result? go directly to details
				$this->redirect(array('controller' => 'properties', 'action' => 'view', $properties[0]['Property']['ML_Number_Display']));
			} else { // otherwise show pagination
				$this->pageTitle = 'Properties in County: '.$properties[0]['Property']['County'];
				if(!empty($property_type)){
					$this->pageTitle .= ' ('.$property_type['PropertyType']['name'].')';
				}
				$this->set(compact('properties'));
				$this->params['county_id'] = $my_county['County']['id'];
				$this->set('categories', $this->_categories('County', $region));
				$this->set('region', $region);
				
				if($this->layout == 'ajax'){
					$this->render('index_mobile');
				}else {
					$this->render('index');
				}
			}
		} else { // no results? error
			$this->cakeError('error404');
		}
		

	}
	
	function _categories($filter_type = null, $filter_value = null){
		$categories =  $this->PropertyType->find('all');
		$count = 0;
		foreach($categories as $category){
			$categories[$count]['PropertyType']['count'] = $this->Property->find('count', array('conditions' => array("Property.$filter_type" => $filter_value, 'Property.Property_Type' => $category['PropertyType']['code'])));
				$count++;
		}
	
		return($categories);
	}
	
	function tag_cloud(){
		$this->layout = null;
		$counties = $this->County->find('all', array('fields' => 'County.name','conditions' => array('County.id <> ""')));
		$county_list = array();
		foreach($counties as $county){
			if(!empty($county['City'])){
				$county_list[$county['County']['id']] = $county['County']['name'];
			}
		}
		$counties = $county_list;
		foreach($counties as $key => $value){
			$counter = $this->Property->find('count', array('conditions' => array('Property.county' => $value, 'Property.city <> ' => 'Other')));
			if($counter > 0){ // exclude "other" 
				$county_count[$value] = $counter;		
			}
		}				
		$tag_cloud = $this->_tagCloud($county_count, true);
		$this->set('tag_cloud', $tag_cloud);	
	}
	
	function search($filter = null){
		$this->cacheAction = true;
		App::import('Helper', 'Html'); 
        $html = new HtmlHelper();
		if(!isset($this->params['named']['county']) && !isset($this->params['named']['option'])){
			$this->params['named']['county'] = 'find';
		}
		if(isset($this->params['pass'][0]) && $this->params['pass'][0] == 'county:find'){
			$this->params['named']['county'] = 'find';
		}
		if(isset($this->params['url']['county'])){
			if($this->params['url']['county'] == 'find' || empty($this->params['named']['county'])){
			
			}
			//$county_string = $this->params['url']['county'][0];
			//if($county_string == '_'){
				$this->redirect(array('controller' => 'properties', 'action' => 'search', 'county:'.ltrim($this->params['url']['county'], '_')));
				exit();
			//}
			$this->redirect(array('controller' => 'properties', 'action' => 'search', 'county:'.$this->params['url']['county']));
			exit();
		}
		$this->pageTitle = 'Search for Properties';
		$search_list = null;
		$this->set('transaction_types', $this->transaction_types);
		$this->set('resi_subtypes', $this->resi_subtypes);
		$property_types = $this->PropertyType->find('list', array('order' => 'PropertyType.sort_order ASC, PropertyType.name ASC'));
		$this->set('property_types', $property_types);
		$this->set('images', $this->_random_images());
			$open_house_counties = null;
			if(isset($this->params['named']['open_house'])){
				$open_house_counties = $this->Property->find('list', array('conditions' => array('Property.Open_House_Start_Timestamp IS NOT NULL'), 'fields' => 'Property.County'));
				$open_house_counties = array_unique($open_house_counties);
				sort($open_house_counties);
			}
		
		
		if(empty($this->data)){
			if(!empty($filter)){ // if we are re-doing a search, load the previous values
				$this->data = $this->Session->read('search_options');
				$search_list = $this->Session->read('search_list');
				$search_options = $this->Session->read('search_options');
				unset($this->data['Property']['submit']); // remove this so we don't get in an infinite loop
			}

				$counties = $this->County->find('list', array('conditions' => array('County.status_id' => 1), 'order' => 'County.name ASC', 'fields' => 'County.name'));

			

			$output = array();
			$city_names = array();
			foreach($counties as $key => $value){	
				$cities = $this->City->find('list', array('conditions' => array( 'City.county_id' => $key), 'order' => 'City.name ASC'));
				if(!empty($cities)){
					$output[$value] = $cities;
				}	
			}
			$cities = $output;
			$this->set('num_range', range(0, 10));
			$this->set('search_list', $search_list);
			if(isset($search_options)) {
				$this->set('search_options', $search_options);
		
			}
			
			$this->set('open_house_counties', $open_house_counties);
			$this->set(compact('cities', 'counties', 'num_range'));

		}


/*
		$city_extra_conditions = null;
		if(isset($this->params['named']['county'])){
			$city_extra_conditions = array('Property.County' => Inflector::humanize($this->params['named']['county']));
		}
*/
		
		
		$cities = $this->City->find('all', array('fields' => 'City.name, City.id,  County.id','conditions' => array('County.id <> ""', 'City.status_id' => 1), 'order' => 'City.name ASC'));		
		$city_count = array();


		foreach($cities as $value){
						

			$city_count[$value['City']['name']] = $this->Property->find('count', array('conditions' => array('Property.city' => $value['City']['name'])));
		}
		$this->set('city_count', $city_count);
		if(!isset($this->params['named']['option'])){
			$this->params['jquery'] = true;		
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
		foreach($counties as $key => $value){
			$counter = $this->Property->find('count', array('conditions' => array('Property.county' => $value, 'Property.city <> ' => 'Other')));
			if($counter > 0){ // exclude "other" 
				$county_count[$value] = $counter;		
			}
		}
		$this->set('county_count', $county_count);
				
		$tag_cloud = $this->_tagCloud($county_count);
		$this->set('tag_cloud', $tag_cloud);
		



		if(isset($this->params['named']['county'])){ // new "search by county" featue (named param)
		



			
			
			
		
			$this->County->recursive = -1;
			$this->City->recursive = -1;
			$county_raw = $this->County->find('first', array('conditions' => array('County.name' => array(Inflector::humanize($this->params['named']['county']))), 'fields' => 'County.id, County.name'));
			
			if(isset($this->params['named']['open_house'])){
				$open_house_cities = $this->Property->find('all', array('conditions' => array('Property.City <> ' => 'Y', 'Property.Open_House_Start_Timestamp IS NOT NULL', 'Property.County' => Inflector::humanize($this->params['named']['county'])), 'fields' => 'DISTINCT Property.City'));
				foreach($open_house_cities as $value){
					$cities_in_county[]['City']['name'] = $value['Property']['City'];
				}
			} else {
			
/* 				$cities_in_county = $this->City->find('all', array('conditions' => array('City.county_id' => $county_raw['County']['id'], 'City.name <> ' => 'Y'), 'fields' => 'DISTINCT City.name', 'order' => 'City.name ASC')); */
		
			
			
			
			$new_cities = $this->Property->find('list', array(
				'fields' => 'Property.City, Property.City', 
				'conditions' => array(
					'Property.County' => Inflector::humanize($this->params['named']['county']),
					'Property.City <>' => 'Y',
				),
				'order' => 'Property.City ASC'
				)
			);
			$new_city_count = 0;
			$new_cities_in_county = array();
			foreach($new_cities as $value){
				if(!empty($value)){
					$new_cities_in_county[$new_city_count]['City']['name'] = $value;
					$new_city_count++;
				}
			}
			$cities_in_county = $new_cities_in_county;
			
			
		}
			
/*
			debug($new_cities_in_county);	
			debug($cities_in_county);	
*/		

			$city_list = array();
			if(isset($cities_in_county) && !empty($cities_in_county)){
				foreach($cities_in_county as $key => $value){
					$city_count_conditions = 	array('Property.City' => $value['City']['name']);
					if(isset($this->params['named']['county'])){
						$city_count_conditions[] = array('Property.County' => Inflector::humanize($this->params['named']['county']));
					}
					if(isset($this->params['named']['open_house'])){
						$now = time();
						$city_count_conditions['Property.Open_House_Start_Timestamp >='] = $now;
					}	
				
					$city_count = $this->Property->find('count', array('conditions' => $city_count_conditions));
	/*
					$city_list[$value['City']['name']] = $value['City']['name'].' ('.$city_count.')';
	*/
					$city_list[$value['City']['name']] = $value['City']['name'].' ('.$city_count.')';
				}
			}
			
			$all_counties = $this->County->find('list', array('conditions' => array('County.name <> ' => ''), 'order' => 'County.name ASC'));
			$all_properties_in_county_conditions = array('Property.County' => Inflector::humanize($this->params['named']['county']));
			if(isset($this->params['named']['open_house'])){
				$this->pageTitle = 'Search for Open Houses';
				$now = time();
				$all_properties_in_county_conditions['Property.Open_House_Start_Timestamp >='] = $now;
			}
			
			$all_properties_in_county = $this->Property->find('count', array('conditions' => $all_properties_in_county_conditions ));
			
			$this->set('total_property_count', $all_properties_in_county);
			$this->pageTitle = $this->pageTitle.': '.$county_raw['County']['name'];
			$this->set('city_list', $city_list);
			$this->set('search_county', $county_raw);
			$this->set('search_by_county', true);
			$this->set('county', $county_raw);
			$this->set('all_counties', $all_counties);
		}	 // end search by named param (county)
		

		
		
		if(!empty($this->data['Property']['submit'])){ // perform search
		


			if(!empty($this->data['Property']['mls_number'])){ // search for single property via MLS number
				$property = $this->Property->find('first', array('conditions' => array('Property.ML_Number_Display' => $this->data['Property']['mls_number'])));
				if(!empty($property)){
					$this->redirect(array('controller' => 'properties', 'action' => 'view', $property['Property']['ML_Number_Display']));
					exit();
				} else {
					$this->Session->setFlash('Sorry, no property was found with the specified MLS Number.');
					$this->redirect(array('controller' => 'properties', 'action' => 'search'));
					exit();
				}
			}
			if(!empty($this->data['Property']['agent_first_name']) || !empty($this->data['Property']['agent_last_name'])){ // search by agent name
					$first_name = $this->data['Property']['agent_first_name'];
					$last_name = $this->data['Property']['agent_last_name'];
					$agents = $this->Property->find('all', array('conditions' => array('Property.Agent_First_Name LIKE' => "%$first_name%", 'Property.Agent_Last_Name LIKE' => "%$last_name%"), 'fields' => 'DISTINCT Property.Agent_Name, Property.Agent_Number, Property.Agent_First_Name, Property.Agent_Last_Name, Property.Agent_Email_Address, Property.Office_Long_Name, Property.Office_Broker_ID, Property.Agent_Phone_1, Property.Agent_Web_Page_Address', 'order' => 'Property.Agent_Last_Name, Property.Agent_First_Name ASC'));
					$this->pageTitle = 'Search By Agent Name';
					$this->set('agents', $agents);
					$this->render('agent_list');
			} else {
				if(isset($this->data['Property']['city'])){
					$city_names = $this->City->find('list', array('conditions' => array('City.id' => $this->data['Property']['city'])));
				} else if (isset($this->data['City']['id'])) {
					$city_names = $this->data['City']['id'];
				}
				$search_options = $this->data;
	
	
			if(isset($this->params['Property']['open_house_search'])){
				$search_options['Open_House_Start_Date'] >= $this->params['Property']['Open_House_Start_Date'];
				//$search_list[] = 'OPEN HOUSE SEARCH';
			}
		
	
	
				if(isset($city_names)){
					$search_options['City'] = $city_names;
				}
				$this->Session->write('search_options', $search_options);
				$this->redirect(array('controller' => 'properties', 'action' => 'search_result'));
			}
		}
				
		if(isset($this->params['named']['limit'])){
			$search_options['limit'] = $this->params['named']['limit'];
			
		}
		
		

		if(isset($this->params['named']['county'])){
			if($this->params['named']['county'] == 'find' || empty($this->params['named']['county'])){
				$this->render('choose_county');
				return;
			}
		} 

		 if(isset($this->params['named']['option'])){
			$this->render();
		} else { 
			$this->render('semi_advanced');
		}		
	}
	
	function search_result(){
	

        App::import('Helper', 'Number'); 
        $number = new NumberHelper(); 
		$search_options = $this->Session->read('search_options');
			
		
		if(empty($search_options['Property']['property_types'])){
			$search_options['Property']['property_types'][] = 'RESI';
		}
		
/*
			debug($search_options);
*/
			
		$this->pageTitle = 'Search Results';
		$properties = null;
		$search_list = array();
		$conditions = array();
		if(!empty($search_options['County']['id'])){
			$this->County->recursive = -1;
			$county_name = $this->County->read('County.name', $search_options['County']['id']);
			$county = $county_name['County']['name'];
			$conditions[] = array('Property.County' => $county);
			$search_list[] = 'County: '.$county;
		}
		
		if(!empty($search_options['City']) && !isset($search_options['Property']['search_by_county'])){
			//$conditions[] = array('Property.City' => Inflector::humanize($search_options['City'])); // find by city
			if(count($search_options['City']) == 1){
					if(is_string($search_options['City'])){
						$search_options['City'] = Inflector::humanize($search_options['City']);
						$search_list[] = 'City: '.Inflector::humanize($search_options['City']);
					}
			}
			if($search_options['City'] != '---' ){
			/*
				$conditions[] = array('Property.City' => $search_options['City']); // find by city
			*/
			// if we get no results try using the city slug as a lookup
				$my_slug = null;
/*
				debug($search_options['City']);
*/
				if(is_string($search_options['City'])){
					$my_slug = strtolower(Inflector::slug($search_options['City']));
				} else if(is_array($search_options['City'])){
				
				}
				$this->City->recursive = -1;
				if(!empty($my_slug)){
					$search_city = $this->City->find('first', array('conditions' => array('City.slug' => $my_slug)));	
				}
				$city_variants =array();
				if(!empty($search_city['City']['name'])){
					$city_variants[] = $search_city['City']['name'];
				}
				if(!empty($search_city['City']['slug'])){
					$city_variants[] = $search_city['City']['slug'];
				}
				if(!empty($search_options['City'])){
					$city_variants[] = $search_options['City'];
				}	
				$city_variants = $city_variants[0];
				$conditions[] = array('Property.City' => $city_variants);
			}
			if(is_array($search_options['City'])){
				foreach($search_options['City'] as $key => $value){
					$search_list[] = 'City: '.Inflector::humanize($value);
				}
				sort($search_list);
			}
		}
		if(isset($search_options['City']) && is_string($search_options['City'])){
			$search_options['City'] = Inflector::humanize($search_options['City']);
		}
		
				
				
		if(isset($search_options['Property']['search_by_county'])){
			$search_list[] = 'County: '.$search_options['County']['name'];
			
			if($search_options['County']['name']){
				$conditions[] = array('Property.County' => $search_options['County']['name']);
			}
			if(true){
				if(!empty($search_options['City']['name'])){
					$conditions[] = array('Property.City' => $search_options['City']['name']);
				}
			}
		}




		
		if(!empty($search_options['Property']['zip_code'])){ // find by zip code
			$conditions[] = array('Property.Zip_Code' => $search_options['Property']['zip_code']);
			$search_list[] = 'Zip Code '.$search_options['Property']['zip_code'];
		}
		if(!empty($search_options['Property']['price_low'])){ // find by min price
		
/*
			preg_replace ('/[^\d]/', '', $search_options['Property']['price_low'])
*/
		
/*
			$conditions[] = array('Property.Search_Price >=' => $search_options['Property']['price_low']);
*/
			$conditions[] = array('Property.Search_Price >=' => $search_options['Property']['price_low']);

			$search_list[] = 'Min. Price: '.$number->currency($search_options['Property']['price_low']);
		}
		if(!empty($search_options['Property']['price_high'])){ // find by max price
/*
			$conditions[] = array('Property.Search_Price <=' => $search_options['Property']['price_high']);
*/
			$conditions[] = array('Property.Search_Price <=' => $search_options['Property']['price_high']);

			$search_list[] = 'Max. Price: '.$number->currency($search_options['Property']['price_high']);
		}
		if(!empty($search_options['Property']['bedrooms'])){ // find by min. bedrooms
			$conditions[] = array('Property.Bedrooms >= ' => $search_options['Property']['bedrooms']);
			$search_list[] = 'Min. Bedrooms: '.$search_options['Property']['bedrooms'];
		}
		if(!empty($search_options['Property']['bathrooms'])){ // find by min. bedrooms
			$conditions[] = array('Property.Full_Bathrooms >= ' => $search_options['Property']['bathrooms']);
			$search_list[] = 'Min. Bathrooms: '.$search_options['Property']['bathrooms'];
		}
		if(!empty($search_options['Property']['senior'])){ // find by senior community		
			foreach($search_options['Property']['property_types'] as $my_property_type){
				$conditions['OR'][] = array('Property.'.strtoupper($my_property_type).'_Senior_Desc' => array('Yes', 1));
			}
			$search_list[] = 'Senior Communities Only';
		}
		if(isset($search_options['City'])){
			if((is_string($search_options['City']) || count($search_options['City']) == 1) && $search_options['City'] != 0){
				$search_list[] = 'City: '.$search_options['City'];
			}
		}
		if(isset($search_options['Property']['transaction_type']) && !empty($search_options['Property']['transaction_type'])){
			$conditions[] = array('Property.transaction_type' => $search_options['Property']['transaction_type']);
			foreach($search_options['Property']['transaction_type'] as $value){
				$search_list[] = 'Transaction Type: '.$this->transaction_types[$value];
			}
		}
		if(isset($search_options['Property']['property_types']) && !empty($search_options['Property']['property_types'])){
			$conditions[] = array('Property.Property_Type' => $search_options['Property']['property_types']);
			foreach($search_options['Property']['property_types'] as $value){
				$prop_type = $this->PropertyType->find('first', array('conditions' => array('PropertyType.code' => $value)));
				$search_list[] = 'Property Type: '.$prop_type['PropertyType']['name'];
			}
		}
		if(is_array(@$search_options['Property']['property_subtype_1_display'])){
			$search_options['Property']['property_subtype_1_display'] = array_merge($search_options['Property']['property_subtype_1_display'], $this->hidden_subtypes);
		}
		if(isset($search_options['Property']['property_subtype_1_display']) && !empty($search_options['Property']['property_subtype_1_display'])){
			$conditions[] = array('Property.Property_Subtype_1_Display' => $search_options['Property']['property_subtype_1_display']);
			foreach($search_options['Property']['property_subtype_1_display'] as $value){
				
				if(isset($this->resi_subtypes[$value])){
					$search_list[] = 'Property Type: '.$this->resi_subtypes[$value];
				}
				
			}
		}

/*
		if(isset($search_options['Property']['limit'])){
			//$conditions[] = array('limit' => $search_options['Property']['limit']);
			$this->params['named']['limit'] = $search_options['Property']['limit'];
		}
		
*/


		

/*
		$conditions[] = array('Property.County' => $search_options['County']['name']);
*/
	

/*

		debug($conditions);
*/
/* 		debug($search_options); */
		if(isset($search_options['Property']['open_house_search'])){
/* 			$start_date = $search_options['Property']['Open_House_Start_Date']['month'].'-'.$search_options['Property']['Open_House_Start_Date']['day'].'-'.$search_options['Property']['Open_House_Start_Date']['year']; */
			$start_date = $search_options['Property']['Open_House_Start_Date']['year'].'-'.$search_options['Property']['Open_House_Start_Date']['month'].'-'.$search_options['Property']['Open_House_Start_Date']['day'];
			$start_time = $search_options['Property']['Open_House_Start_Time']['hour'].':'.str_pad($search_options['Property']['Open_House_Start_Time']['min'], 2, '0', STR_PAD_RIGHT).' '.$search_options['Property']['Open_House_Start_Time']['meridian'];
			//debug($start_time);
			$start_time = strtotime($start_time);
			//debug($start_time);
			$start_time = date("H:i:s", $start_time);
			//debug($start_time);
	
			$end_date = $search_options['Property']['Open_House_End_Date']['year'].'-'.$search_options['Property']['Open_House_End_Date']['month'].'-'.$search_options['Property']['Open_House_End_Date']['day'];
			$end_time = $search_options['Property']['Open_House_End_Time']['hour'].':'.str_pad($search_options['Property']['Open_House_End_Time']['min'], 2, '0', STR_PAD_RIGHT).' '.$search_options['Property']['Open_House_End_Time']['meridian'];
			$end_time = strtotime($end_time);
			$end_time = date("H:i:s", $end_time);
	
			

/*
			debug($start_date);
			debug($start_time);
			debug($end_date);
			debug($end_time);
*/

			$start_date = $start_date.' '.$start_time;
			$end_date = $end_date.' '.$end_time;
			
			$conditions[] = array(
				'Property.Open_House_Start_Timestamp >= ' => $start_date,
				'Property.Open_House_Start_Timestamp <= ' => $end_date,
				
				'Property.City <> ' => 'Y',
				);
			$search_list[] = 'Open Houses: '.$start_date.' - '.$end_date;
			$this->set('open_house', 1);
			$this->paginate['order'] = 'Property.Open_House_Start_Timestamp ASC';
		}
		

		$properties = $this->paginate('Property', $conditions);
		
		
		$this->Session->write('search_list', $search_list);
		$this->set('search_list', $search_list);

		if(count($properties) == 1){ // only 1 listing? Go straight to detail view
			$this->redirect(array('controller' => 'properties', 'action' => 'view', $properties[0]['Property']['ML_Number_Display']));
			exit();
		}
		if(empty($properties)){ // no listings? print error and redirect to search form, add "filter" param to let search know we are modifying a previous set of inputs

			$this->Session->setFlash('Sorry, no properties were found matching your specified search criteria.');
			if(Configure::read('debug') > 0){
				exit();
			}
/* 			if(Configure::read('debug')  > 0){ */
				$return_county = null;
				$return_open_house = null;				
				if(isset($search_options['County']['name'])){
					$return_county = '/county:'.strtolower(Inflector::slug($search_options['County']['name']));
				}
				if(isset($search_options['Property']['open_house_search'])){
					$return_open_house = '/open_house:1';
				}
				$return_url = array('controller' => 'properties', 'action' => 'search', $return_county.$return_open_house);
				$this->redirect($return_url);
							
/* 			}
			if(Configure::read('debug') ==0){
				$this->redirect(array('controller' => 'properties', 'action' => 'search', 'filter'));
			}
 */
 
		} else { // we have listings ... prep for render
			$count = 0;
			/*foreach($properties as $property){ // loop thru our current page and load the extra info (city/county data)
				$city = $this->City->find('first', array('conditions' => array('City.name' => $property['Property']['city']), 'fields' => 'City.id, City.name, County.id, County.name'));
				$properties[$count]['City'] = $city['City'];
				$properties[$count]['County'] = $city['County'];
				$count++;
			}
			*/
			$this->set(compact('properties'));
		if($this->Session->valid('search_list')){
			$this->set('search_list', $this->Session->read('search_list'));
		
		}
		
		
		
/* 			debug($search_options); */
			$this->set('search_options' , $search_options);
			$this->render('index');
			
		}
	}

	function search_city(){
		$this->autoRender = false;
		if(isset($this->data['City']['id'])){
			if(is_numeric($this->data['City']['id'])){
				$this->redirect(array('controller' => 'properties', 'action' => 'region', Inflector::slug(strtolower($this->data['County']['id']))));
				exit();
			}
			$city = $this->City->read(null, $this->data['City']['id']);
			if(empty($city)){
				$city = $this->City->find('first', array('conditions' => array('City.name' => Inflector::humanize($this->data['City']['id']))));
			}
			if(empty($city)){
				$this->Session->setFlash("You did not choose anything to search. If you need more options, fill out this form.");
				$this->redirect(array('controller' => 'properties', 'action' => 'search'));
				exit();
			} else {
				$this->redirect(array('controller' => 'properties', 'action' => 'city', Inflector::slug(strtolower($city['City']['id']))));
			}
		}
	}
	
	
	
	function open_houses(){	
		$this->pageTitle = 'All Upcoming Open Houses';
		$now = time();
		$now_humanized = (date('Y-m-d H:i:s', $now));
/* 		debug($now_humanized); */
		$conditions = array(
/* 			'Property.Open_House_Start_Timestamp >=' =>  $now, */			
			'Property.Open_House_End_Timestamp >=' => $now_humanized,
			'Property.City <> ' => 'Y',
			
		);
		if(isset($this->params['named']['county'])){
			$conditions[] = array('Property.County' => Inflector::humanize($this->params['named']['county']));
		}

		$this->paginate['order'] = 'Property.Open_House_Start_Timestamp ASC';
		$properties = $this->paginate('Property', $conditions);
	
	
		if(isset($this->params['named']['county'])){
			$this->pageTitle.= ' in '.$properties[0]['Property']['County'];
		}
		
			
		$property_count = $this->Property->find('count', array('conditions' => $conditions));
		$this->set('properties', $properties);
		$this->set('property_count', $property_count);
		$this->set('open_house', true);
		$this->render('index');
	}
	

	
	function city($id = null, $property_type = null){
		$city = $this->City->read(null, $id);
		if(is_numeric($id)){
			$this->redirect(array('controller' => 'properties', 'action' => 'city', Inflector::slug(strtolower($city['City']['name']))));
			exit();
		}		
		if(empty($city)){
			$slug = Inflector::humanize($id);			
			$city = $this->City->find('first', array('conditions' => array('City.name' => $slug)));
		}
		if(empty($city)){
			$city = $this->City->find('first', array('conditions' => array('City.slug' => $id)));
		}
		if(empty($city)){	
			$this->cakeError('error404');
		} else {
			$conditions = array('Property.City ' => $city['City']['name']);
			if($property_type != null){
				$property_type = $this->PropertyType->findBySlug($property_type);
				if(!empty($property_type)){
					$conditions[] = array('Property.Property_Type' => $property_type['PropertyType']['code']);
				}
			}
		$properties = $this->paginate('Property', $conditions);
		if(!empty($properties)){
			if(count($properties) == 1){ // only 1 result? go directly to details
				$this->redirect(array('controller' => 'properties', 'action' => 'view', $properties[0]['Property']['ML_Number_Display']));
			} else { // otherwise show pagination
				$this->pageTitle = 'Search City: '.	$city['City']['name'];
				if(!empty($property_type)){
					$this->pageTitle .= ' ('.$property_type['PropertyType']['name'].')';
				}
				$count = 0;
				foreach($properties as $property){
					$properties[$count]['City'] = $city['City'];
					$properties[$count]['County'] = $city['County'];
					$count++;
				}
				$this->set(compact('properties'));
				$this->set('property_type', $property_type);
				$this->set('categories', $this->_categories('City', $city['City']['name']));
				$this->set('type', true);
				$this->set('region', null);
				$this->set('area', $city['City']['name']);
				$this->render('index');
			}
		} else { // no results? error
			$this->cakeError('error404');
		}
		$this->set('city', $city);
		}
	}
	
		
	function listing_agent($agent_number = null){
		$properties = $this->paginate('Property', array('Property.Agent_Number ' => $agent_number));
		if(empty($properties)){
 		//	$properties = $this->paginate('Property', array('Property.Agent_MLS_ID ' => $agent_number)); 
		}
		if(empty($properties)){
			$properties = $this->paginate('Property', array('Property.Agent_NRDS_ID ' => $agent_number));
		}
		if(empty($properties)){
			$properties = $this->paginate('Property', array('Property.Agent_Number ' => $agent_number));
		}
		
		if(empty($properties)){
			$this->MemberAgent->recursive = -1;
			$agent = $this->MemberAgent->findByAgentMlsId($agent_number);
			$properties = $this->paginate('Property', array('Property.Agent_Email_Address' => $agent['MemberAgent']['Agent_Email_Address']));
		}
		if(empty($properties)){
				$properties = $this->paginate('Property', array('Property.Co_Agent_Email_Address' => $agent['MemberAgent']['Agent_Email_Address']));
	
		}
		if(isset($properties[0]['Property']['Agent_NRDS_ID'])){
				$agent_id = $properties[0]['Property']['Agent_NRDS_ID'];

		}
		$this->MemberAgent->recursive = -1;
		if(empty($agent)){
			$agent = $this->MemberAgent->findByAgentMlsId($agent_id);
		}

		$office = $this->MemberOffice->findByOfficeMlsId($agent['MemberAgent']['Office_MLS_ID']);
		$agent['MemberOffice'] = $office['MemberOffice'];
		$this->set('agent', $agent);

		if(empty($agent)){
			$this->cakeError('error404');
		} else {
			$this->pageTitle = 'Search Agent: '.@$agent['MemberAgent']['Agent_First_Name'].' '.@$agent['MemberAgent']['Agent_Last_Name'];
			$this->set('properties', $properties);
			$this->render('index');

	 	}
	}
	
	function all_agents(){
		$this->pageTitle = 'All Agents';
		$fields = array(
			'DISTINCT Property.Agent_Name',
			'Property.Agent_First_Name',
			'Property.Agent_Last_Name',
			'Property.Agent_Number',
			'Office_Long_Name',
/*
			'Agent_Nickname',
			'Agent_NRDS_ID', 
			'Agent_Number', 
			'Agent_Office_Number', 
			'Agent_Phone_1', 
			'Agent_Phones' 
*/
		);
		$agents = $this->Property->find('all',array('fields' => $fields, 'order' => 'Property.Agent_Last_Name, Property.Agent_First_Name ASC',  'limit' => 100));
		
		$this->set('agents', $agents);
	}
	
	function listing_office($office_number = null){
		$properties = $this->paginate('Property', array('Property.Office_NRDS_ID ' => $office_number));
		$this->MemberOffice->recursive = -1;
		if(isset($properties[0]['Property']['Office_ID'])){
			$office = $this->MemberOffice->findByOfficeMlsId($properties[0]['Property']['Office_ID']);
		} else {
			$office = $this->MemberOffice->findByOfficeMlsId($office_number);
		}
		$this->MemberAgent->recursive = -1;
		$agents = $this->MemberAgent->find('all', array('conditions' => array('MemberAgent.Office_MLS_ID' => $office['MemberOffice']['Office_MLS_ID']), 'fields' => 'MemberAgent.Agent_First_Name, MemberAgent.Agent_Last_Name, MemberAgent.Agent_MLS_ID', 'order' => 'MemberAgent.Agent_Last_Name, MemberAgent.Agent_First_Name ASC'));
		$office['MemberAgent'] = $agents;
		$this->set('office', $office);
		if(empty($properties)){
			$this->redirect(array('controller' => 'member_offices', 'action' => 'view', $office_number));
			exit();
		} else {
			$this->pageTitle = 'Search Property Office: '.$properties[0]['Property']['Office_Long_Name'];
			$this->set('properties', $properties);
			$this->render('index');
		}
	}

	function zip($id = null, $property_type = null){
/*
		$min = $id;
		$range = array();
		while($min <= $zip_max){
			$range[] = $min++;
			if(count($range) > 10){
				$this->Session->setFlash('Too many zip codes provided. Please choose a smaller range.');
				$this->redirect(array('controller' => 'properties', 'action' => 'index'));
			}
		}
		
	*/
				$conditions[] = array('Property.Zip_Code' => $id);
				if($property_type != null){
					$property_type = $this->PropertyType->findBySlug($property_type);
					if(!empty($property_type)){
						$conditions[] = array('Property.Property_Type' => $property_type['PropertyType']['code']);
					}
				}
		$properties = $this->paginate('Property', $conditions);
		if(!empty($properties)){
			if(count($properties) == 1){ // only 1 result? go directly to details
				$this->redirect(array('controller' => 'properties', 'action' => 'view', $properties[0]['Property']['ML_Number_Display']));
			} else { // otherwise show pagination

				$this->pageTitle = 'Search Zip Code: '.	$id;
				if(!empty($property_type)){
					$this->pageTitle .= ' ('.$property_type['PropertyType']['name'].')';
				}
				$this->set(compact('properties'));
				$this->set('property_type', null);
				$this->set('categories', $this->_categories('Zip_Code', $id));
				$this->set('type', true);
				$this->set('region', null);
				$this->set('area', $id);
				$this->render('index');
			}
		} else { // no results? error
			$this->error404();
		}		
	}


	function view_favorites(){
		$favorites = $this->Cookie->read('favorites');
		$this->set('favorites', $favorites);
		if(empty($favorites)){
			$this->Session->setFlash('Your favorites list is empty');
			$this->redirect($this->referer());
		}
		
	}

	function view($id = null){
	
		//$this->layout = 'map_only';
		//$this->Property->recursive = 1;
		$property = $this->Property->read(null, $id);
		if(empty($property)){
/*
			$this->Session->setFlash('Invalid Property Id');
			$this->redirect('index');
*/
		//	$this->cakeError('error404',array(array('url'=> $this->params['url']['url'])));
		} else{ 
			App::import('Helper', 'Text');
			App::import('Helper', 'Number');
			$text = new TextHelper();
			$number = new NumberHelper();
			$city = $this->City->find('first', array('conditions' => array('City.name' => array($property['Property']['City']))));
			//debug($city);
			// image setup
			// primary image
			
			// load agent record for extra data
			$this->MemberAgent->recursive = -1;
			$agent = $this->MemberAgent->find('first', array('conditions' => array('MemberAgent.Agent_Email_Address' => $property['Property']['Agent_Email_Address']), 'fields' => 'MemberAgent.Agent_License'));
			$property['Property']['Agent_License'] = $agent['MemberAgent']['Agent_License'];
			$co_agent = $this->MemberAgent->find('first', array('conditions' => array('MemberAgent.Agent_Email_Address' => $property['Property']['Co_Agent_Email_Address']), 'fields' => 'MemberAgent.Agent_License'));
			$property['Property']['Co_Agent_License'] = $co_agent['MemberAgent']['Agent_License'];
			
/* 						debug($agent); */
			
			if(!empty($property['Property']['Primary_Picture_Url'])){ // main image exists
				$image_attributes = null;
				// first try to get image attributes from local cache
				$cached_image = WWW_ROOT.'img_cache_fullsize/'.md5(base64_encode($property['Property']['Primary_Picture_Url']));
				if(is_file($cached_image)){
					$image_size = @getimagesize($cached_image);
				} else { // otherwise try to get image size from remote site
					$image_size = @getimagesize($property['Property']['Primary_Picture_Url']);
				}
				if(!empty($image_size)){
					$image_attributes = array('height' => $image_size[1], 'width' => $image_size[0], 'alt' => 'Primary Image', 'title' => 'Primary Image');
				
				} else {
					$image_attributes = null;
				}
				$primary_image['source'] = $property['Property']['Primary_Picture_Url'];
				//$primary_image['source'] = '/listings/fullsize_image/'.base64_encode($property['Property']['Primary_Picture_Url']);
				$primary_image['attributes'] = $image_attributes;
			} else { // main images does not exist
				$primary_image['source'] =  $html->image('/img/mls-no-image.gif'); // the "no image" thumb
				$primary_image['attributes'] =  array('height' => 90, 'width' => 125);
			}
			// find any secondary images
		//	$secondary_images = $this->_extra_images($property['Property']['Primary_Picture_Url'], $property['Property']['ML_Number_Display']);
		//	$secondary_images = $this->_extra_images($property['Property']['ML_Number_Display'], $property['Property']['Pictures_Count']);
			$secondary_images = $property['Property']['Pictures_Count'];
			// finished image setup
			$neighbors = $this->Property->find('neighbors', array('fields' => array('Property.ML_Number_Display')));
			
			// get most recent DB update time
			$db_updated = $this->UpdateLog->find('all', array('order' => 'modified DESC', 'limit' => 1, 'fields' => 'modified') );
		
			/*
			$allowed_fields = array(
				// add all the fields we want to show here
				'Search_Price_Display',
				'Street_Full_Address',
				'County',
				'Bedrooms',
				'Bathrooms_Display',
				'ML_Number_Display',
				'Acres',
				'City',
				'State',
				'Street_Name',
				'Street_Number',
				'Street_Suffix',
				'Zip_Code',
				'Agent_Email_Address',
				'Agent_Name',
				'Bathrooms_Display',
				'Bedrooms',
				'County',
				'Cross_Street',
				'Full_Bathrooms',
				'Half_Bathrooms',
				'RESI_HOA_Code',
				'RESI_HOA_Amount',
				'RESI_HOA_Desc',
				'Property_Agent_Email',
				'Lot_Size_-_Sq_Ft',
				'Lot_Size_-_Acres',
				'Lot_Size_Source',
				'Marketing_Remarks',
				'Office_Broker_ID',
				'Office_Full_Name_and_Phones',
				'Office_Long_Name',
				'Office_Short_Name',
				'Office_Phone_1',
				'Property_Subtype_1_Display',
				'Property_Type',
				'Search_Price_Display',
				'Square_Footage',
				'Status',
				'Street_Direction',
				'Street_Full_Address',
				'Total_Bathrooms',
				'RESI_Total_Rooms',
				'Virtual_Tour_URL',
				'Year_Built',
				'Agent_Nickname',
				'Lot_Size',
				'MLS',
				'RESI_Unit_Blk_Lot',
				'Transaction_Type',
				'RESI_Senior_Code',
				'RESI_Senior_Desc',
				'Property_Date',
				'RESI_On_Market_Date',
				'Agent_Phones',
				'Agent_Phone_1',
				'Reciprocal_Member_Name',
				'Reciprocal_Member_Phone',
				'Reciprocal_Office_Name',
				'Co_Agent_Email_Address',
				'Co_Agent_Fax_Phone',
				'Co_Agent_Full_Name',
				'Co_Agent_Phone_Type_1',
				'Co_Agent_Web_Page_Address',
				'Co_Office_Full_Name_and_Phones',
				'RESI_2nd_Unit_on_Lot_Code',
				'RESI_2nd_Unit_on_Lot_Desc',
				'Property_Type',
				'Agent_Web_Page_Address'
			);
			*/
			$item = $property['Property'];
		
			// co-listing agent info cleanup 

			if(stristr($item['Co_Agent_Full_Name'], '(ID:')){
				$item['Co_Agent_Full_Name'] = explode('(ID:', $item['Co_Agent_Full_Name']);
				$item['Co_Agent_Full_Name'] = $item['Co_Agent_Full_Name'][0];
			}
			if(stristr($item['Co_Office_Full_Name_and_Phones'], 'Phone:')){
				$item['Co_Office_Full_Name_and_Phones'] = explode('Phone:', $item['Co_Office_Full_Name_and_Phones']);
				$item['Co_Office_Full_Name_and_Phones'] = $item['Co_Office_Full_Name_and_Phones'][0];
			}
			
			$code = $item['Property_Type'];
/* 			die($code); */

			$address_display_field = $code.'_Address_on_Internet_Desc';
			$this->set('address_display_field', $address_display_field);
			if(!isset($item[$address_display_field]) || strtolower($item[$address_display_field]) == 'full'){
/*
			if(strtolower($item['RESI_Address_on_Internet_Desc']) == 'full'){
*/
				$address_display = $item['Street_Number'].' '.$item['Street_Name'].' '.$item['Street_Suffix'];
				$this->pageTitle = $item['Street_Full_Address'];
			} else {
/* 				$address_display = '<i>(Full Address Not Shown)</i>'; */
				$address_display = null;
				$this->pageTitle = $item['City'].', '.$item['County'].' '.$item['Zip_Code'];
			}
			
			$property_type = $item['Property_Type'];
			
			// find co-listing agent details
			$this->MemberAgent->recursive = -1;
			$co_agent_id = null;
			$co_agent = $this->MemberAgent->find('first', array('conditions' => array('MemberAgent.Agent_Email_Address' => $item['Co_Agent_Email_Address']), 'fields' => 'MemberAgent.Agent_MLS_ID') );
/* 			debug($co_agent); */
			if(!empty($co_agent)){
				$co_agent_id = $co_agent['MemberAgent']['Agent_MLS_ID'];			
			}
			$this->MemberOffice->recursive = -1;
			$member_office = $this->MemberOffice->find('first', array('conditions' => array('MemberOffice.Office_MLS_ID' => $property['Property']['Office_ID'])));
			$this->set('member_office', $member_office);
/* 			debug($member_office); */
			
			$this->set('co_agent_id', $co_agent_id);
			$grouping = array(
				'Summary' => array(
				//	'Public Comments' =>	$item['Marketing_Remarks'],
					'Status' =>				$item['Status'],
					'MLS Number' =>			$item['ML_Number_Display'],
					'Price' =>			'$'.$item['Search_Price_Display'],
					'Property Type' =>		$item['Property_Subtype_1_Display'],
					'Transaction Type' =>	$this->transaction_types[$item['Transaction_Type']],
					'Number of Units' =>	@$item[$property_type.'___of_Units'],
				),
				
				'Property_Details' => array(
					'Address' => 					$address_display,
					'City' => 						$item['City'],
					'Zip' => 						$item['Zip_Code'],
					'County' =>						$item['County'],
					'Bedrooms' =>					$item['Bedrooms'],
					'Bathrooms' =>					$item['Bathrooms_Display'],
					'Square Footage (approx.)' =>	$number->format($item['Square_Footage']),
//					'Building Sq. Ft. (approx.)' =>	$number->format($item['Lot_Size_-_Sq_Ft']),
					'Building Sq. Ft. (approx.)' =>	$number->format($item['Lot_Size_Sq_Ft']),
					'Lot Size - Acres (approx.)' =>	$item['Acres'],
					'2nd Unit on Lot' =>			@$item[$property_type.'_2nd_Unit_on_Lot_Desc'],
					'HOA' =>						@$item[$property_type.'_HOA_Desc'],
					'Senior Community' =>			@$item[$property_type.'_Senior_Desc'],
					'Pool (Y/N)' =>					@$item[$property_type.'_Pool_Desc'],
				//	'Virtual Tour URL' =>			$item['Virtual_Tour_URL'],
					'Year Built' =>					$item['Year_Built'],
				),
			
				'Contact_Information' => array(
					'Listing Agent' =>					$item['Agent_Name'],
					'Listing Agent Phone' =>			$item['Agent_Phone_1'],
					'Listing Agent Email'=> 			$item['Agent_Email_Address'],
					'Agent Web Page Address' =>			$item['Agent_Web_Page_Address'],
					'Listing Office' =>					$item['Office_Long_Name'],
					'Listing Office Phone' =>			$item['Agent_Phone_1'],
					'Co-Listing Agent' =>				$item['Co_Agent_Full_Name'],
					'Co-Listing Agent Email' =>			$item['Co_Agent_Email_Address'],
					'Co-Lisitng Agent Phone' =>			$item['Co_Office_Fax_Phone'],
					
				)

			);
			
			/*
			// GEOCODING STUFF
			
			$this->params['map'] = true;
			$address = $property['Property']['Street_Full_Address'];		
			$this->Coordinate->recursive = -1;
			$coordinate_finder =  array('conditions' => array('Coordinate.ML_Number_Display' => $property['Property']['ML_Number_Display']));
			$coordinates = $this->Coordinate->find('first', $coordinate_finder);
			if(empty($coordinates)){
				$geo_data = $this->Geocoder->getLatLng($address, $this->google_map_API_key); 
				$coordinate['Coordinate']['id'] = $coordinates['Coordinate']['id'];
				$coordinate['Coordinate']['ML_Number_Display']  = $property['Property']['ML_Number_Display'];
				$coordinate['Coordinate']['latitude'] = $geo_data['lat'];
				$coordinate['Coordinate']['longitude'] = $geo_data['lng'];
				$this->Coordinate->create();
				$this->Coordinate->save($coordinate);
				//$this->Session->setFlash('Coordinate data saved for property '.$property['Property']['ML_Number_Display']);
				$coordinates = $this->Coordinate->find('first', $coordinate_finder);
			}

				$coordinates['Coordinate']['address'] = $property['Property']['Street_Full_Address'];
			
			$this->params['coordinates'] = $coordinates; 
			// END GEOCODING STUFF
			*/
		//	die(debug($grouping));
			
			$this->set('property', $property);
			$this->set('primary_image', $primary_image);
			$this->set('secondary_images', $secondary_images);
			$this->set('neighbors', $neighbors);
			$this->set('db_updated', $db_updated);
			// $this->set('allowed_fields', $allowed_fields);
			$this->set('grouping', $grouping);
			$this->set('city', $city);
			// $this->set('coordinates', $coordinates);
		}
	}
	/*	
	function _extra_images($id = null, $image_count = null){
		$output = null;
		$count = 1;
		if($image_count > 1){
			while($count <= $image_count){
				$output[] = '/listings/image/'.$id.'/full/'.str_pad($count, 2, '0', STR_PAD_LEFT).'.jpg';
				$count++;
			}
		}
		return $output;
	}
	

	function _extra_images($primary_image_url = null, $property_mls_num = null){ // set up secondary images
		//strstr(string haystack, string needle)
		if($primary_image_url != null && !(strstr($primary_image_url, 'nophoto'))){
			$split_url = explode($property_mls_num, $primary_image_url );			
			$glue = $property_mls_num."_{COUNT}";
			$images['secondary'] = $this->_image_incrementer(implode($glue, $split_url));
		 return $images;
		} else {
			return null;
		}
	
	}
	
	function _image_incrementer($path = null){ // build each individual secondary image if it exists
		if($path != null){
			$output = null;
			$count = 1;		 
			$path_replace = str_ireplace('{COUNT}', '01', $path); // the first potential secondary image
			while($this->_check_remote_image($path_replace) && $count <= 25){ // max out at 25 so we don't somehow get in an infinite loop
				// keep checking the potential secondary images, incrementing each time. if remote file exists, add to our output array
				$output[] = $path_replace;
				$count++;
				$path_replace = str_ireplace('{COUNT}', str_pad($count, 2, 0, STR_PAD_LEFT), $path);
			}
			return $output;
		} else {
			return null;
		}
	}
	*/
	
	function _check_remote_image($image = null){ // determine if a remote file exists. adpated from 
		if (@fclose(@fopen($image, "r"))) {
			return true;
		} else {
			return false;
		} 
	}
	
	
	function counties(){
		$this->Property->recursive = -1;
		$counties = $this->Property->find('all', array('fields' => 'DISTINCT Property.county', 'order' => 'Property.county ASC'));
		$county_list = Set::combine($counties, '{n}.Property.county', '{n}.Property.county');
		$this->set('counties', $county_list);
	}
	
	
	function admin_index() {
		$this->pageTitle = 'Property Properties Index';
		$properties = $this->paginate();
		
		$count = 0;
		foreach($properties as $property){
			$city = $this->City->find('first', array('conditions' => array('City.name' => $property['Property']['City'])));
			
			$properties[$count]['City'] = $city['City'];
			$properties[$count]['County'] = $city['County'];
			$count++;
		}
		$this->set(compact('properties'));
	}

	
	function fullsize_image($url = null){
		// encoded example:
		// aHR0cDovL21lZGlhbGF4Yi5yYXBtbHMuY29tL25vcmNhbG1scy9saXN0aW5ncGljcy9iaWdwaG90by8wMjMvMjA5MTI0MjNfMDIuanBnP2FyZz0yMDA5MDUwNzE2MDM1Nw==
		// example url: http://medialaxb.rapmls.com/norcalmls/listingpics/bigphoto/023/20912423_02.jpg?arg=20090507160357
		$this->autoRender = false;
		$raw_url = $url;
		if($url != null){
			$hash = md5($url);
			$url = base64_decode($url);
			$local_file = WWW_ROOT.'img_cache_fullsize/'.$hash;		
			if(is_file($local_file)){ // file exists in cache, read it and exit
				header('Content-type: image/jpeg');
				readfile($local_file);
				exit();
			}	
			// otherwise, continue
			$remote_image_attributes = @ getimagesize($url);				
			if(empty($remote_image_attributes)){
				die('Error: Could not read remote image '.$url);
			}
			if(!empty($remote_image_attributes)){
				$height = $remote_image_attributes[1];
				$width = $remote_image_attributes[0];
				$attributes = "height:$height/width:$width/";				
			} else {
				$attributes = null;
			}
		//	$this->thumbnail($raw_url, $height, $width); // calling this direct doesn't work so let's use requestAction()
			$action = '/listings/thumbnail/size:full/'.$attributes.$raw_url;
			$this->requestAction($action);
		} else {
			die('Image URL not provided');
		}
	}
	
	
	function image($id = null, $mode = null,  $version = null){
		$this->autoRender = false;
		$model = 'Property';
		$property = $this->Property->read('Property.ML_Number_Display, Property.Primary_Picture_Url', $id); // RESI image
/*
		if(empty($property)){
			die('Invalid Property ID');
		} 
*/
		if(empty($property)){
			$model = 'BusinessOpp';
			$property = $this->BusinessOpp->read('BusinessOpp.ML_Number_Display, BusinessOpp.Primary_Picture_Url', $id); // RESI image

		}		
		
		
			$url = @ $property[$model]['Primary_Picture_Url'];
			if(empty($url)){
				die('This property has no primary image');
			}
			$raw_url = $url;
			if($version != null){
				$url = str_ireplace('.jpg', '_'.$version.'.jpg', $raw_url);				
			}
			$url = base64_encode($url);
			if($mode == 'thumb'){
				$this->thumbnail($url);
			} else {
				$this->fullsize_image($url);
			}
		
	}
	
	function thumbnail($url = NULL, $height = 100, $width = 135){
		$this->autoRender = false;
		if(empty($url)){
			die('Invalid image');
		}else{
			if(isset($this->params['named']['size']) && $this->params['named']['size'] == 'full'){
				$cache_dir = 'img_cache_fullsize';
				if(isset($this->params['named']['height'])){
					$height = $this->params['named']['height'];
				}
				if(isset($this->params['named']['width'])){
					$width = $this->params['named']['width'];
				}
			} else {
				$cache_dir = 'img_cache';
			}
			$hash = md5($url);
//			if(false){
			if($bytes = @filesize(WWW_ROOT.'/'.$cache_dir.'/'.$hash)){ // cached
				header('Content-type: image/jpeg');
				readfile(WWW_ROOT.'/'.$cache_dir.'/'.$hash);	
			} else { // not cached: create the image	
		

			$url = base64_decode($url);
			$file_type = 'image/jpeg';
			$filename = $url;
			// Get new dimensions
			list($width_orig, $height_orig) = getimagesize($filename);
			@$ratio_orig = $width_orig/$height_orig;
			if ($width/$height > $ratio_orig) {
			   $width = $height*$ratio_orig;
			} else {
			   $height = $width/$ratio_orig;
			}
			// Resample
			$image_p = @imagecreatetruecolor($width, $height);
			if($file_type == 'image/jpeg' || $file_type == 'image/pjpeg'){
				$image = imagecreatefromjpeg($filename);
			} else if ($file_type == 'image/gif'){
				$image = imagecreatefromgif($filename);
			} else if ($file_type == 'image/png') {
				$image = imagecreatefrompng($filename);
			} else {
				// non image format, show default thumbnail
				header('Content-type: image/gif');
				$image = imagecreatefromgif(WWW_ROOT.'/img/mls-no-image.gif');
				imagegif($image);
				imagedestroy($image);
				exit();
			}
			header('Content-type: image/jpeg');
			imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
			imagejpeg($image_p, null, 70);
			imagejpeg($image_p, WWW_ROOT.$cache_dir.'/'.$hash); // save the image to cache
			// die(debug($result));
			imagedestroy($image_p);
			imagedestroy($image);
	
			
			}
		}
	}


	function all_thumbs(){
		$this->Property->recursive = -1;
		$this->layout = null;
		$thumbs = $this->Property->find('all', array('fields' => 'Primary_Picture_Url') );
		$this->set('thumbs', $thumbs);
	}
	
	function _random_images($count = 20){
/* 		debug($this->params['named']['county']); */
		if(isset($this->params['named']['county']) && $this->params['named']['county'] != 'find' && !empty($this->params['named']['county'])){
			$conditions['County'] = Inflector::humanize($this->params['named']['county']);
		}
		
		$conditions['Primary_Picture_Filename <>'] = 'p_nophoto.jpg';
		$image_ids = $this->Property->find('list', array('limit' => $count, 'order' => 'rand()', 'conditions' => $conditions));
		
		
		$images = $this->Property->find('all', array('conditions' => array('ML_Number_Display' => $image_ids), 'fields' => 'id, ML_Number_Display, Primary_Picture_Url, City, County, Marketing_Remarks'));
/*
		$this->set('images', $images);
*/
/*
		debug($images);
*/
		return($images);
	}
	
	function _geoCode($property = null){
		// see http://bakery.cakephp.org/articles/view/adding-a-google-map-to-your-app
		$address['address'] = $property['Property']['street_number'].' '.$property['Property']['street_name'];
		$address['city'] = $property['Property']['city'];
		//$address['state'] = 'CA';
		$address['zipcode'] = $property['Property']['zip_code'];
		App::import('Vendor', 'google_geo');
		$googleGeo = new GoogleGeo($address);
		$geo = $googleGeo->geo();
		//debug($address);
		//debug($geo);
		if ( empty($geo) ) { 
			$geo = array(); 
			//$this->data['geostatus'] = false;
			//$this->_makeGeoError();
		} else { 
			//$this->data['geostatus'] = true;
		 }
		//$property = array_merge($property,$geo);
		return($geo);
	}
	
	function reset_session(){
		$this->autoRender = false;
		$session = $this->Session->read();
		debug($session);
		$this->Session->delete('search_options');
		$this->Session->delete('search_list');
		$session = $this->Session->read();
		debug($session);

	}

	function _tagCloud($tags, $mode = null) {
			// adapted from http://www.bytemycode.com/snippets/snippet/415/
	        // $tags is the array
	      // 	die(debug($tags));
	        arsort($tags);
	    	//debug($tags);
	    	
	    	
	   
	        $max_size = 36; // max font size in pixels
	        $min_size = 14; // min font size in pixels
	       
	        // largest and smallest array values
	        $max_qty = max(array_values($tags));
	        $min_qty = min(array_values($tags));
	       
	        // find the range of values
	        $spread = $max_qty - $min_qty;
	        if ($spread == 0) { // we don't want to divide by zero
	                $spread = 1;
	        }
	       
	        // set the font-size increment
	        $step = ($max_size - $min_size) / ($spread);
	       
	        // loop through the tag array
	        $output = null;
	        foreach ($tags as $key => $value) {
	                // calculate font-size
	                // find the $value in excess of $min_qty
	                // multiply by the font-size increment ($size)
	                // and add the $min_size set above
	                $size = round($min_size + (($value - $min_qty) * $step));
	                if(!isset($mode)){
		       			$output .= '<a href="#'.strtolower(Inflector::slug($key)).'_county" style="font-size: ' . $size . 'px" title="' . $value . ' listings in ' . $key . '">' . $key . '</a> ';
	                
	                } else {
		       			$output .= '<a href="/mls/county/'.strtolower(Inflector::slug($key)).'" style="font-size: ' . $size . 'px" title="' . $value . ' listings in ' . $key . '">' . $key . '</a> ';
	                
	                }
	           
	        }
	             return $output;
	}
	
	
	function agents(){
		$agents = $this->Property->findAll('Property.Agent_Email_Address <> ""', array('Property.id', 'Property.ML_Number_Display', 'Property.Agent_Email_Address', 'Property.Agent_Name'), null, 20);
		$this->set(compact('agents'));
	}
	
	function contact(){
		$this->autoRender = false;
		$params = $this->params['named']['params'];
		$params = @ unserialize(@ base64_decode(@ urldecode($params)));
		if(is_array($params)){
			$this->Session->write('recipient_name', $params['name']);
			
		
/* 			$this->redirect('/contact-agent'); */

			header('Location: /contact-agent');
			exit();

		} else {
			die("Invalid input");
		}
/*
		debug($params);
*/
	}
	
	function setup_contact(){
		debug($_POST);
	}
	function check_session(){
		$this->autoRender = false;
		$params = $this->Session->read('recipient_name');
		debug($_SESSION);
	}
	
	function admin_cache_clear(){
		$this->autoRender = false;
		$clear = clearCache();
		echo 'The cache has been cleared';	
		echo $clear;
	}

	function slugify_cities(){
		die('This function is disabled');
		$this->autoRender =false;
		$this->City->recursive = -1;
		$cities = $this->City->find('all', array('conditions' => 'City.slug IS NULL'));
		debug($cities);
		
		foreach($cities as $city){
			$this->City->create();
			$this->data['City']['slug'] = Inflector::slug(strtolower($city['City']['name']));
			$this->data['City']['id'] = $city['City']['id'];
			$this->City->save($this->data);
			
		
		}
		
	}


	function favorites(){
		$referer = $this->referer();
		$this->pageTitle = 'My Favorite Properties';
		$favorites = $this->Cookie->read('favorites');
		$conditions = array('Property.ML_Number_Display' =>$favorites);
		$properties = $this->paginate('Property', $conditions);
		$this->set('properties', $properties);
		if(empty($properties)){
			$this->Session->setFlash('You have no properties saved in your favorites list!');
			if($referer == '/properties/favorites'){
				$this->redirect('/');
			} else {
				$this->redirect($this->referer());
			}
		}
		$this->render('index');
	}


	

}
