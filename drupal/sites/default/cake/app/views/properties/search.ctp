<?php
/*
	debug($search_list);
	debug($search_options);
*/
/*
	echo $this->element('property_search_list', array('property_search_list' => $search_list));
*/
	echo '<h2>Search for Properties</h2>';
	echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'post')); // form start
	
		echo '<fieldset>';
	echo '<legend>Search Options</legend>';
	echo $form->input('zip_code', array('maxlength' => 5, 'size' => 5));

	echo '<div class="input text">';
	echo $form->input('price_low', array('label' => 'Min. Price', 'div' => null));
	echo $form->input('price_high', array('label' => 'Max. Price', 'div' => null));
	echo '<br />(Full dollar amount only, no dollar sign or decimals)<br />';
	echo '</div>';
	
/*
	echo $form->input('price_low', array('label' => 'Minimum Price', 'after' => 'Full dollar amount only, no dollar sign or decimals'));
	echo $form->input('price_high', array('label' => 'Maximum Price', 'after' => 'Full dollar amount only, no dollar sign or decimals'));
*/

	echo $form->input('bedrooms', array('label' => 'Minimum number of Bedrooms', 'options' => $num_range));
	echo $form->input('bathrooms', array('label' => 'Minimum number of Bathrooms','options' => $num_range));
	echo $form->input('senior', array('type' => 'checkbox', 'label' => 'Search only Senior Communities'));
	
	echo $form->submit('Search'); 

/*
	echo $form->input('transaction_type', array('multiple' => 'checkbox', 'options' => $transaction_types));
	echo $form->input('property_subtype_1_display', array('label' => 'Property Types', 'multiple' => 'checkbox', 'options' => $resi_subtypes));
*/

	echo $form->input('transaction_type', array('label' => '<b>Transaction Types</b>', 'multiple' => 'checkbox', 'options' => $transaction_types));
	echo $form->input('property_types', array('label' => '<b>Property Types</b>', 'multiple' => 'checkbox', 'options' => $property_types));
	echo $form->input('property_subtype_1_display', array('label' => '<b>Property Sub-Types (Residential only)</b>', 'multiple' => 'checkbox', 'options' => $resi_subtypes));

	echo $form->submit('Search');
	echo '</fieldset>';
	
	
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


	
	echo '<fieldset>'; // counties fieldset
	echo '<legend>Counties</legend>';
	echo 'Jump to county: <br />';

	echo $tag_cloud;
//	debug($county_count);
	
	$county_count_new = $county_count;
	arsort($county_count_new);
//	debug($county_count_new);
	
	
	foreach($county_count_new as $key => $value){
		if(isset($cities[$key])){
				$cities_new[$key] = $cities[$key];
		}
	}
	
//	debug($cities_new);
	//debug($cities);
	
	//debug($city_count);
//	foreach($cities as $key => $value){
	foreach($cities_new as $key => $value){
		echo '<a name="'.Inflector::slug(strtolower($key)).'_county"></a>';
		$county_name = $key;
		echo '<fieldset>';
		echo '<legend>';
		echo $html->link($key, array('controller' => 'properties', 'action' => 'region' , Inflector::slug(strtolower($key))));
		echo ' ('.$county_count[$key];
		if($county_count[$key] > 1){
			echo ' properties';
		} else {
			echo ' property';
		}
		echo ')';
		echo '</legend>';
		foreach($cities[$key] as $city){

			$flip = array_flip($cities[$key]);
			$val = $flip[$city];
			$checked_status = null;			
			if(isset($search_list) && in_array($city, $search_list)){
				$checked_status = 'checked="checked"';
			} 
		
			if(isset($search_list) && in_array('County: '.$county_name, $search_list)){
				$checked_status = 'checked="checked"';
			}
			echo '<div class="checkbox"><input name="data[Property][city][]" '.$checked_status.' value="'.$val.'" id="ListingCity'.$val.'" type="checkbox">'.$html->link($city, array('controller' => 'properties', 'action' => 'city', Inflector::slug(strtolower($city)))).' ('.$city_count[$city].') </div>';
			//	$cities_in_county = (count($cities[$key]));

		}

		echo '</fieldset>';
/* 		echo $form->submit('Search'); */
		echo $html->link('Top of Page', '#top');
	}
	echo $form->input('submit', array('type' => 'hidden', 'value' => 1));
	echo '</fieldset>'; // end counties fieldset
	echo $form->end('Search'); // form end
	//echo '<p>'.$html->link('List of All Regions', array('controller' => 'properties', 'action' => 'all')).'</p>';
?>