<h2>Select a County and/or City</h2>

<?php

echo $this->element('search_bar', array('cities' => $cities, 'counties', $counties)); 

/*
echo $form->create(array('url' => '/listings/search_city')); 

//echo $form->input('county_id', array('id' => 'parent'));
//echo $form->input('city_id', array('id' => 'child', 'type' => 'select'));

// multi-stage select form adapted from:
// http://www.ajaxray.com/blog/2007/11/08/jquery-controlled-dependent-or-cascading-select-list-2/

echo '<select id="parent" name="data[County][id]">';
echo '<option disabled="disabled" selected> -- Choose County -- </option>';
foreach($counties as $key => $value){
	echo '<option value="'.$key.'">'.$value.'</option>';

}
echo '</select>';
echo '<select id="child" name="data[City][id]">';
foreach($counties as $key => $value){
	echo '<option class="sub_'.$key.'" value="0">All Cities</option>';
}
foreach($cities as $city){
//	echo '<option class="sub_'.$city['County']['id'].'" value="0">All</option>';
	echo '<option class="sub_'.$city['County']['id'].'" value="'.strtolower(Inflector::slug($city['City']['name'])).'">'.$city['City']['name'].'</option>';

}

echo '</select>';

echo $form->end('Search');
echo $html->link('Advanced Search', array('controller' => 'listings', 'action' => 'search'));

*/

/*
<form>
<select id="parent">
	<option> -- Select -- </option>
	<option value="1">Flower</option>
	<option value="2">Animal</option>
</select>

<select id="child">
	<option class="sub_1" value="1">Rose</option>
	<option class="sub_1" value="2">Sunflower</option>
	<option class="sub_1" value="3">Orchid</option>
	<option class="sub_2" value="4">Cow</option>
	<option class="sub_2" value="5">Dog</option>
	<option class="sub_2" value="6">Cat</option>
	<option class="sub_2" value="7">Tiger</option>	
</select>

</form>
*/
?>