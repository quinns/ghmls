<?

	echo '<h2>'.$this->pageTitle.'</h2>';
	echo '<fieldset>';
		echo '<legend>Search Options</legend>';
		if(isset($county['County']['id'])){
			$county_id = $county['County']['id'];
		} else {
			$county_id = 0;
		}
		if(isset($this->params['named']['county']) && $this->params['named']['county'] != 'find'){
			echo $this->element('random_images', array('images' => $images, 'cache' => array('time' => '5 minutes', 'key' => $county_id)));
		} else {
			echo $this->element('random_images', array('images' => $images, 'cache' => array('time' => '5 minutes', 'key' => null)));
		}
		
		$primary_counties = $this->requestAction('/counties/get_primary_counties', array('return'));
	
		
		echo '<div id="city_list">';
		echo '<b>Select a county:</b>';
		
		foreach($primary_counties as $key => $value){
			$primary_county_select['_'.Inflector::slug(strtolower($value))] = $value;
		}

		$primary_county_select['_'] = ' ----- ';
		$county_list = $counties;
		$county_list = array_merge($primary_county_select, $county_list);
		echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'get')); // form start
		echo $form->select('county', $county_list, $this->params['named']['county'], array('onChange' => 'this.form.submit()'), false);


		
		echo '<br />';
		echo '<br />';


		
		echo $form->submit('Get Started');
				
		echo '<br />';
		echo '</fieldset>';

/* 	} */
	
/*
	debug($counties);
*/
?>