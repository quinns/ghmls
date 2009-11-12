<?php
/*
echo $this->element('category_bar');
*/




/*
debug($this->params);
*/
?>
<?
$type_str = null;
if(isset($type)){
	if(isset($type['PropertyType'])){
		$type_str = $type['PropertyType']['slug'];
	} else {
		$type_str = $type['slug'];
	}
}
/*
	debug($property_type);
*/
if(isset($search_list)){
	//echo 'Search Results for: ';
	//echo $text->toList($search_list);
	echo $this->element('property_search_list', array('property_search_list', $search_list));
} 
	if (isset($categories)) {
		echo $this->element('category_bar', array('params' => array('categories' => $categories, 'property_type' => $property_type, 'region' => $region, 'area' => $area)));
	
	}

	//echo $this->element('property_search_bar_simple');

?>
<div class="listings index">
<p>
<?php
$paginator->options(array('url' => $this->passedArgs));
//$paginator->options(array('update' => 'content', 'indicator' => 'spinner'));
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<?php 
//debug($paginator->hasPrev());
if ($paginator->hasPrev() || $paginator->hasNext()) { ?>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php } ?>
<table cellpadding="0" cellspacing="0" width="100%" class="ghresultstable" >
<tr  class="ghresultscolheader">
	<th nowrap="nowrap">Image</th>
	<?php if  (!isset($this->params['search_type'])) { 
		if(!isset($property_type) || empty($property_type)){
			?> 
					<th nowrap="nowrap"><?php echo $paginator->sort('Property Type', 'Property_Type', array('rel' => 'nofollow'));?>&nbsp;</th>
			<?
		} 
	} ?>
	<th nowrap="nowrap"><?php echo $paginator->sort('MLS Number', 'ML_Number_Display', array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('List Price', 'Search_Price', array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Beds', 'Bedrooms' , array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Baths', 'Full_Bathrooms', array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Street Address', 'Street_Full_Address', array('rel' => 'nofollow'));?>&nbsp;</th>
		<?php if  ($this->params['action'] != 'city') {  ?>
			<th nowrap="nowrap"><?php echo $paginator->sort('City', 'City', array('rel' => 'nofollow'));?>&nbsp;</th>
		<? } ?>
	<?php if  ($this->params['action'] != 'region') {  ?>
		<th nowrap="nowrap"><?php echo $paginator->sort('County', 'County', array('rel' => 'nofollow'));?>&nbsp;</th>
	<?php } ?>
	<?php if  ($this->params['action'] != 'zip') {  ?>
	<th nowrap="nowrap"><?php echo $paginator->sort('Zip_Code', 'Zip', array('rel' => 'nofollow'));?>&nbsp;</th>
	<?php } ?>
	<th nowrap="nowrap"><?php echo $paginator->sort('Updated', 'modified', array('rel' => 'nofollow'));?>&nbsp;</th>
</tr>
<?php
$i = 0;
foreach ($properties as $property):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
		<?php echo $html->link($html->image(array('admin' => 0, 'controller' => 'properties', 'action' => 'image', $property['Property']['ML_Number_Display'].'/thumb'), array('title' => $text->trim($property['Property']['Marketing_Remarks'], 500))), array('controller' => 'properties', 'action' => 'view', 'admin' => 0, $property['Property']['ML_Number_Display']), null, null, false); ?>
		
		</td>
			<?php if  (!isset($this->params['search_type'])) {
			
					if(!isset($property_type) || empty($property_type)){
	  ?>

		<td>
			<?php
/*
			 echo $html->link($property['PropertyType']['name'], array('controller' => 'properties', 'action' => 'index', $property['PropertyType']['slug'])); 
*/
			 echo $property['PropertyType']['name']; 
		
			
			?>&nbsp;
		</td>
		<?
			}
		 } ?>
		<td>
			<?php echo $html->link(__($property['Property']['ML_Number_Display'], true), array('admin' => 0, 'action'=>'view', $property['Property']['ML_Number_Display'])); ?>&nbsp;
		</td>

		<td>
			<?php echo $number->currency($property['Property']['Search_Price']);  ?>&nbsp;
		</td>
		<td>
			<?php if(empty( $property['Property']['Bedrooms'])){
				echo '0';
			} else {
				echo $property['Property']['Bedrooms']; 
			} ?>&nbsp;
		</td>
		<td>
			<?php echo $property['Property']['Full_Bathrooms'];  ?>&nbsp;
		</td>
		<td>
			<?php // echo  $property['Property']['Street_Full_Address'];  ?>
			<?php
			

			$address_display_field = $property['Property']['Property_Type'].'_Address_on_Internet_Desc';
/*
			if(strtolower($property['Property']['RESI_Address_on_Internet_Desc']) == 'full'){
*/
			if(!isset($property['Property'][$address_display_field]) || strtolower($property['Property'][$address_display_field]) == 'full'){
			
				 echo  $property['Property']['Street_Number'].' '.$property['Property']['Street_Name'].' '.$property['Property']['Street_Suffix'];  
			 } else {
			 	echo '<span style="color:gray; font-style:italic">Suppressed by seller</span>';
			 }?>&nbsp;
		</td>
			<?php if  ($this->params['action'] != 'city') {  ?>
		<td>
			<?php echo  $html->link($property['Property']['City'], array('admin' => 0, 'controller' => 'properties', 'action' => 'city', strtolower((Inflector::slug($property['Property']['City']))))) ;  ?>&nbsp;
		</td>
			<?   } ?>
			<?php if  ($this->params['action'] != 'region') {  ?>
				<td>
				<?php echo  $html->link($property['Property']['County'], array('admin' => 0, 'controller' => 'properties', 'action' => 'region', strtolower(Inflector::slug($property['Property']['County'])))) ;  ?>&nbsp;
				</td>
			<?   } ?>

		<?php if  ($this->params['action'] != 'zip') {  ?>
		<td>
			<?php echo  $html->link(substr($property['Property']['Zip_Code'], 0, 5), array('admin' => 0, 'controller' => 'properties', 'action' => 'zip', strtolower((Inflector::slug(substr($property['Property']['Zip_Code'], 0, 5)))))) ;  ?>&nbsp;
		</td>
		<? } ?>
		
		<td>
			<?php echo $time->relativeTime($property['Property']['modified']) ;  ?>&nbsp;
		</td>
			</tr>
<?php endforeach; ?>
</table>
</div>
<p>
<? echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
)); ?>
</p>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>

<? 	
/*
	debug($properties[0]);
*/
?>
