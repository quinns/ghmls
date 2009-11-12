<?
	$open_house_param = null;
	if(isset($this->params['named']['open_house'])){
		$open_house_param = '/open_house:1';
		$this->pageTitle = 'Open House Search: First Select a County';
	} else {
		$this->pageTitle = 'First Select a County';

	}

	$primary_counties = $this->requestAction('/counties/get_primary_counties', array('return'));
		if(!empty($open_house_counties)){
			$all_counties = $open_house_counties;
		}
		
/*
		debug($all_counties);
		debug($primary_counties);
*/
		
		foreach($all_counties as $value){
			if(!in_array($value, $primary_counties)){
				$county_list[strtolower(Inflector::slug($value)).$open_house_param] = $value;
			}
		}
		foreach($primary_counties as $key => $value){
			$primary_county_select['_'.Inflector::slug(strtolower($value)).$open_house_param] = $value;
		}
		natsort($primary_county_select);
		$first = array('__' => '----- Choose a county -----');
		$primary_county_select['_'] = ' ----- ';
		$county_list = array_merge($first, $primary_county_select, $county_list);
/* 		echo $this->element('random_images', array('images' => $images, 'cache' => array('time' => '5 minutes', 'key' => null))); */

?>		
		


<h2><?php echo $this->pageTitle; ?></h2>
<?php 
/*
echo '<div id="city_list">';
*/


	echo '<p>We provide listings in many counties throughout Northern California and beyond. Please start your search by choosing the county you are interested in.</p>'; 


		echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'get')); // form start
		echo $form->select('county', $county_list, $this->params['named']['county'], array('onChange' => 'this.form.submit()'), false);
		echo '<br />';
		echo '<noscript>';
		echo '<br />';
		echo $form->submit('Get Started');
		echo '</noscript>';
		echo $form->end();
		echo '<br />';
		
/*
echo '</div>'; 
*/
/*
	echo '<div id="other_search_criteria">';
*/
//echo '<p>If you want to search multiple counties as once, try our '.$html->link('Advanced Search', array('controller' => 'properties', 'action' => 'search', 'option:advanced')).' tool.</p>';		

if(isset($this->params['named']['open_house'])){
	echo '<p>'.$html->link('Browse all open  houses', array('controller' => 'properties', 'action' => 'open_houses'));
}
/* echo '</div>'; */
		
		
/* 		echo '</div>'; */
		
		
		
/*
	echo '<div id="other_search_criteria" style="line-height:1.7">';
	echo $form->create('Property', array('action' => 'search', 'admin' => 0, 'type' => 'post')); // form start
	echo '<div class="input text">';
	echo $form->input('price_low', array('label' => 'Min. Price', 'div' => null, 'maxlength' => 7, 'size' => 7));
	echo $form->input('price_high', array('label' => 'Max. Price', 'div' => null, 'maxlength' => 7, 'size' => 7));
	echo '<br />(Full dollar amount only, <b>no dollar sign, commas or decimals</b>)<br />';
	echo '</div>';
	echo $form->input('bedrooms', array('label' => '<b>Min.</b> #  of Bedrooms', 'options' => $num_range));
	echo $form->input('bathrooms', array('label' => '<b>Min.</b> #  of Bathrooms','options' => $num_range));
	echo $form->input('zip_code');
	echo $form->submit('Search'); 
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
*/
/*
	echo '</div>';
*/
	
	
	
	
		
		
		
/*
		debug($this->viewVars);
*/
?>



