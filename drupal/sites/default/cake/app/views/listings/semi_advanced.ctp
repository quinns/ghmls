

<?php

	/*
	$primary_counties = array(
		10 => 'Lake',
		34 => 'Sonoma',
		13 => 'Marin',
		17 => 'Napa',
		33 => 'Solano',	
	);
	*/
	
	echo '<h2>Search for Properties</h2>';
	echo '<fieldset>';
	echo '<legend>Search Options</legend>';

	$primary_counties = $this->requestAction('/counties/get_primary_counties', array('return'));
	
/*
	debug($county_count);
	debug($city_count);
*/
//	echo '<fieldset>';
//	echo '<legend>Search</legend>';
		echo $form->create('Listing', array('action' => 'search', 'admin' => 0, 'type' => 'post')); // form start

	//echo $form->create(array('url' => '/listings/search_city')); 
	
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
	$properties = null;
	foreach($counties as $key => $value){
		if($county_count[$value] == 1){
			$properties = 'property';
		} else {
			$properties = 'properties';
		}
		echo '<option class="sub_'.$key.'" value="0">All Cities'.' ('.$county_count[$value].' '.$properties.')</option>'."\r\n";
	}
	foreach($cities as $city){
	
		if($city_count[$city['City']['name']] == 1){
			$properties = 'property';
		} else {
			$properties = 'properties';
		}
		echo '<option class="sub_'.$city['County']['id'].'" value="'.strtolower(Inflector::slug($city['City']['name'])).'">'.$city['City']['name'].' ('.$city_count[$city['City']['name']].' '.$properties.')</option>';
	
	}
	
	echo '</select>';
	
//	echo '</fieldset>';






//	echo $this->element('search_list', array('search_list' => $search_list));
//	echo $form->create('Listing', array('action' => 'search', 'admin' => 0, 'type' => 'post')); // form start
/*
	echo '<fieldset>';
	echo '<legend>Search by MLS Number</legend>';
	echo $form->input('mls_number', array('label' => 'If you know the MLS number, enter it here', 'type' => 'text'));
	echo $form->submit('Search');
	echo '</fieldset>';
	echo '<fieldset>';
	echo '<legend>Search by Agent Name</legend>';
	echo $form->input('agent_first_name', array('label' => 'First Name', 'type' => 'text'));
	echo $form->input('agent_last_name', array('label' => 'Last Name', 'type' => 'text'));
	echo $form->submit('Search');
	echo '</fieldset>';
*/


/*
	echo $form->input('zip_code', array('maxlength' => 5, 'size' => 5));
*/
	echo $form->input('price_low', array('label' => 'Minimum Price', 'after' => 'Full dollar amount only, no dollar sign or decimals'));
	echo $form->input('price_high', array('label' => 'Maximum Price', 'after' => 'Full dollar amount only, no dollar sign or decimals'));
	echo $form->input('bedrooms', array('label' => 'Minimum number of Bedrooms', 'options' => $num_range));
	echo $form->input('bathrooms', array('label' => 'Minimum number of Bathrooms','options' => $num_range));
	echo $form->input('transaction_type', array('label' => 'Transaction Types', 'multiple' => 'checkbox', 'options' => $transaction_types));
	echo $form->input('property_subtype_1_display', array('label' => 'Property Types', 'multiple' => 'checkbox', 'options' => $resi_subtypes));
/*
	echo $form->input('senior', array('type' => 'checkbox', 'label' => 'Search only Senior Communities'));
*/
//	echo $form->submit('Search');
/*
	echo '<fieldset>'; // counties fieldset
	echo '<legend>Counties</legend>';
	echo $form->select('city', $cities, null, array( 'multiple' => 'checkbox', 'showParents' => true)); // county/city checkboxes
	echo $form->input('submit', array('type' => 'hidden', 'value' => 1));
	echo '</fieldset>'; // end counties fieldset
*/
	echo $form->input('submit', array('type' => 'hidden', 'value' => 1));

	echo $form->end('Search'); // form end
	echo $html->link('Advanced Search', array('controller' => 'listings', 'action' => 'search', 'option:advanced'));

	echo '</fieldset>';

?>