

<?php
	// NOTE THIS CODE HAS BEEN MOVED TO /views/listings/semi_advanced.ctp
	
	/*
	$primary_counties = array(
		10 => 'Lake',
		34 => 'Sonoma',
		13 => 'Marin',
		17 => 'Napa',
		33 => 'Solano',	
	);
	*/
	$primary_counties = $this->requestAction('/counties/get_primary_counties', array('return'));

	echo '<fieldset>';
	echo '<legend>Search</legend>';
	echo $form->create(array('url' => '/listings/search_city')); 
	
	// multi-stage select form adapted from:
	// http://www.ajaxray.com/blog/2007/11/08/jquery-controlled-dependent-or-cascading-select-list-2/
	$selected_county = null;
	
//	$selected_county = $this->params['county_id'];
	echo '<select id="parent" name="data[County][id]">';
	echo '<option disabled="disabled" selected> -- Choose County -- </option>';
	
/*
	echo '<option value="10">Lake</option>';
	echo '<option value="34">Sonoma</option>';
	echo '<option value="14">Mendocino</option>';
	echo '<option value="13">Marin</option>';
	echo '<option value="17">Napa</option>';
	echo '<option value="33">Solano</option>';
	echo '<option disabled>-----</option>';
*/
	asort($primary_counties);
	foreach($primary_counties as $key => $value){
			echo '<option value="'.$key.'">'.$value.'</option>'."\r\n";
	
	}
	echo '<option disabled>-----</option>';
	asort($counties);
	foreach($counties as $key => $value){
	
		if(!in_array($value, $primary_counties)){

			echo '<option value="'.$key.'">'.$value.'</option>'."\r\n";
		}
	}
	echo '</select>';
	echo '<select id="child" name="data[City][id]">';
	foreach($counties as $key => $value){
		echo '<option class="sub_'.$key.'" value="0">All Cities</option>'."\r\n";
	}
	foreach($cities as $city){
		echo '<option class="sub_'.$city['County']['id'].'" value="'.strtolower(Inflector::slug($city['City']['name'])).'">'.$city['City']['name'].'</option>';
	
	}
	
	echo '</select>';
	
	echo $form->end('Search');
	echo $html->link('Advanced Search', array('controller' => 'listings', 'action' => 'search'));
	
	echo '</fieldset>';

?>