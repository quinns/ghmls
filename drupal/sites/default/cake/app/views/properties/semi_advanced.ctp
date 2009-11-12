<script type="text/javascript">

function cSet(clist,n){
	for(var i=0;i<clist.length;i++){
		clist[i].checked = n;
	}
}

</script>

<?php
if(Configure::read('debug') > 0){ ?>

<!--
<input type="button" name="CheckAll" value="Check All" onClick="checkAll(document.PropertySearchForm.data.City)">
-->
<? } 
/*
	debug($open_house_counties);
*/
	$open_house_param = null;
	if(isset($this->params['named']['open_house'])){
		$open_house_param = '/open_house:1';
	}

	echo '<h2>'.$this->pageTitle.'</h2>';
	echo '<fieldset>';
	echo '<legend>Search Options</legend>';
	if(isset($county['County']['id'])){
		$county_id = $county['County']['id'];
	} else {
		$county_id = 0;
	}
/*
	if(isset($this->params['named']['county']) && $this->params['named']['county'] != 'find'){
		echo $this->element('random_images', array('images' => $images, 'cache' => array('time' => '5 minutes', 'key' => $county_id)));
	} else {
		echo $this->element('random_images', array('images' => $images, 'cache' => array('time' => '5 minutes', 'key' => null)));
	}
*/
	$primary_counties = $this->requestAction('/counties/get_primary_counties', array('return'));
	if(isset($city_list)){
		if(!empty($open_house_counties)){
			$all_counties = $open_house_counties;
		}
		foreach($all_counties as $value){
			$county_list[strtolower(Inflector::slug($value)).$open_house_param] = $value;
		}

		echo '<div id="city_list">';
		foreach($primary_counties as $key => $value){
			$primary_county_select['_'.Inflector::slug(strtolower($value)).$open_house_param] = $value;
		}
		$primary_county_select['_'] = ' ----- ';
		$county_list = array_merge($primary_county_select, $county_list);
		echo '<b>Select a county:</b>';
		echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'get')); // form start
		$selected_county = $this->params['named']['county'].$open_house_param;
		if(!in_array('_'.$this->params['named']['county'].$open_house_param, $county_list)){
			//$selected_county = '_'.$this->params['named']['county'].$open_house_param;
		} 
		echo $form->select('county', $county_list, $selected_county, array('onChange' => 'this.form.submit()'), false);
		if(!isset($this->params['named']['open_house'])){
			echo ' '.$html->link('Advanced Search', array('controller' => 'properties', 'action' => 'search', 'option:advanced'));	
		}
		echo '<br />';
		if(isset($this->params['named']['open_house'])){
			echo $html->link('Browse all open house listings in '.$county['County']['name'], array('controller' => 'properties', 'action' => 'open_houses', 'county:'.$this->params['named']['county'])).'<br />';
		}
		echo $form->end();
	}
	echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'post')); // form start
	if(isset($city_list)){	 // multi-select or checkbox format
			if($this->params['named']['county'] != 'find' && !empty($this->params['named']['county'])){
				echo '<b>Choose one or more cities to search.</b><br />';
				$city_keys = array_flip($city_list);
				if(empty($city_list)){
					$city_list['Other'] = 'Other';	
				}
				// select all 
				?>
				<input type="button" name="control" onclick="cSet(this.form['data[City][name][]'],1);" value="Select All" /> 
				<input type="button" name="control" onclick="cSet(this.form['data[City][name][]'],0);" value="Select None" />
				 <? 
				echo $form->select('City.name', $city_list, null, array('multiple' => 'checkbox'));
				echo $form->submit('Search'); 
				if(!empty($city_list)){
					echo '</div>'; // END CITY LIST DIV
				}
		}
		if(!empty($city_list)){
			echo '<div id="other_search_criteria">';
			echo $form->submit('Search'); 
		}
		echo $form->input('County.name', array('type' => 'hidden', 'value' => $county['County']['name'])); 
		echo $form->input('search_by_county', array('type' => 'hidden', 'value' => 1));
	} else {
	 	// start split jquery select
		$selected_county = null;
		echo '<select id="parent" name="data[County][id]">';
		echo '<option disabled="disabled" selected> -- Choose County -- </option>';
		asort($primary_counties);
		foreach($primary_counties as $key => $value){ // PRIMARY counties
				echo '<option value="'.$key.$open_house_param.'" >'.$value.'</option>'."\r\n";
		}
		echo '<option disabled>-----</option>';
		asort($counties);
		foreach($counties as $key => $value){ // SECONDARY counties
			if(!in_array($value, $primary_counties)){
				echo '<option value="'.$key.$open_house_param.'" >'.$value.'</option>'."\r\n";
			}
		}
		echo '</select>';
		echo '<select id="child" name="data[City][id]">';
		$properties = null;
		foreach($counties as $key => $value){
			if($county_count[@$value] == 1){
				$properties = 'property';
			} else {
				$properties = 'properties';
			}
			echo '<option class="sub_'.$key.'" value="0" >All Cities'.' ('.$county_count[$value].' '.$properties.')</option>'."\r\n";
		}
		foreach($cities as $city){ // CITIES
		
			if($city_count[$city['City']['name']] == 1){
				$properties = 'property';
			} else {
				$properties = 'properties';
			}
			echo '<option class="sub_'.$city['County']['id'].'" value="'.strtolower(Inflector::slug($city['City']['name'])).'">'.$city['City']['name'].' ('.$city_count[$city['City']['name']].' '.$properties.')</option>';
		}
		echo '</select>';
		// end split jquery select
	}
	echo '<div class="input text">';
	
	
	if(isset($this->params['named']['open_house'])){
		$now = time();
		$then = mktime(0, 0, 0, date("m")+1, date("d"),   date("Y"));
		$minYear = date('Y');
		$maxYear = date('Y')+3;
		
		echo '<fieldset>';
		echo '<legend><b>Open House Search</b></legend>';
		echo '<p>To find open houses, choose the desired date range. You may combine other search criteria on this page.</p>';

/*
		echo '<b>Open House Search</b><br />';
*/
		echo 'From:<br />';
		echo $form->datetime('Property.Open_House_Start_Date', 'MDY', 'NONE', $now, array('minYear' => $minYear, 'maxYear' => $maxYear), false);
		echo '<br />';
		echo $form->datetime('Property.Open_House_Start_Time', 'NONE', '12', $now, array('interval' => 15), false);
		echo '<br />';

		echo 'To:<br/>';
		echo $form->datetime('Property.Open_House_End_Date', 'MDY', 'NONE', $then, array('minYear' => $minYear, 'maxYear' => $maxYear), false);
		echo '<br />';
		echo $form->datetime('Property.Open_House_End_Time', 'NONE', '12', $now, array('interval' => 15), false);
		echo $form->hidden('Property.open_house_search', array('value' => 1));
		echo '<br />';


		echo $form->submit('Search');

		echo '</fieldset>';

	
	}
	

	
	echo $form->input('price_low', array('label' => 'Min. <b>Price</b>', 'div' => null, 'maxlength' => 7, 'size' => 7));
	echo $form->input('price_high', array('label' => 'Max. <b>Price</b>', 'div' => null, 'maxlength' => 7, 'size' => 7));
	echo '<br />(Full dollar amount only, <b>no dollar sign, commas or decimals</b>)<br />';
	echo '</div>';
	echo $form->input('bedrooms', array('label' => 'Min. #  of <b>Bedrooms</b>', 'options' => $num_range));
	echo $form->input('bathrooms', array('label' => 'Min. #  of <b>Bathrooms</b>','options' => $num_range));
	echo $form->input('zip_code');
/*
	echo $form->submit('Search'); 
*/
	echo '<b>Property Types</b>';
	foreach($property_types as $key => $value){
		if($key == 'RESI'){
			$checked = 'checked = "checked"';
		}else{
			$checked = null;
		}
		echo '<div class="checkbox"><input name="data[Property][property_types][]" value="'.$key.'" id="PropertyPropertyTypes'.$key.'" type="checkbox"  '.$checked.'><label for="PropertyPropertyTypes'.$key.'">'.$value.'</label></div>';
	}
	echo $form->input('property_subtype_1_display', array('label' => '<b>Property Sub-Types (Residential only)</b>', 'multiple' => 'checkbox', 'options' => $resi_subtypes));
		echo '<label for="PropertyPropertyTransactionType"><b>For Sale/For Lease</b></label>';
		echo '<div class="checkbox"><input name="data[Property][transaction_type][]" value="S" id="PropertyTransactionTypeS" type="checkbox" checked="checked"><label for="PropertyTransactionTypeS">For Sale</label></div>';
		echo '<div class="checkbox"><input name="data[Property][transaction_type][]" value="L" id="PropertyTransactionTypeL" type="checkbox"><label for="PropertyTransactionTypeL">For Lease</label></div>';
	echo '<b>Senior Communities</b>';
	echo $form->input('senior', array('type' => 'checkbox', 'label' => 'Senior: Yes'));
	echo '<b>MLS Number Search</b>';
	echo $form->input('mls_number', array('label' => 'MLS #', 'type' => 'text'));
	echo $form->input('submit', array('type' => 'hidden', 'value' => 1));
	echo $form->end('Search'); // form end
	if(!isset($this->params['named']['county'])){
		echo $html->link('Advanced Search', array('controller' => 'properties', 'action' => 'search', 'option:advanced'));
	}
	if(!empty($city_list)){
		echo '</div>'; // END OTHER CRITERIA DIV
	}
	echo '</fieldset>';

?>