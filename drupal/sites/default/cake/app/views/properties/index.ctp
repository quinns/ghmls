<?php


$favorites = $this->params['favorites'];
$added = $this->params['added'];
$removed =  $this->params['removed'];


$transaction_type['L'] = 'Lease';
$transaction_type['S'] = 'Sale';

if(isset($this->params['named']['limit'])){
	if(!in_array($this->params['named']['limit'], $pagination_limits)){
		$this->cakeError('error404');
	}
}

?>
<h2><?php echo $this->pageTitle; ?></h2>
<?
	echo $this->element('favorites');

/*
if($this->params['action'] != 'favorites'){
	echo $this->element('favorites');
} else {
	echo $html->link('Remove all favorites', array('controller' => 'favorites', 'action' => 'clear'), null, 'Are you sure you wish to remove all '.count($this->params['favorites']).' favorite properties?');
}
*/
if(isset($open_house)){
	echo $this->element('open_house');
}

if(isset($office) && !empty($office)){
	echo $this->element('office', array('office' => $office));
}

if(isset($agent) && !empty($agent)){
	echo $this->element('agent', array('agent' => $agent));
}

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
	echo $this->element('property_search_list', array('property_search_list', $search_list, 'search_options' => $search_options));
} 
	if (isset($categories)) {
		echo $this->element('category_bar', array('params' => array('categories' => $categories, 'property_type' => $property_type, 'region' => $region, 'area' => $area)));
	
	}

	//echo $this->element('property_search_bar_simple');

?>

<?php if (!empty($properties)) { ?>
<div class="listings index">
<p>
<?
/*
if(Configure::read('debug') > 0){
*/



	$my_limit = null;
	$passed_args = null;
	foreach($this->params['pass'] as $pass){
		$passed_args.= '/'.$pass;
	}
	if(isset($this->params['named']['county'])){
		$passed_args .= '/county:'.$this->params['named']['county'];
	}
	if($this->params['paging']['Property']['count'] >= 5){
		echo 'Items per page: ';
		foreach($pagination_limits as $value){
			if($value <= $this->params['paging']['Property']['count'] * 2 ){
				echo $html->link($value, array('controller' => $this->params['controller'], 'action' => $this->params['action'], $passed_args.'/limit:'.$value));
				echo '&nbsp;';
			}
		}	
	}

		echo '<br />';
/*
}
*/

?>
<?php
$paginator->options(array('url' => $this->passedArgs));
//$paginator->options(array('update' => 'content', 'indicator' => 'spinner'));
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
echo ' (Click column headers to sort results.)';
?></p>
<?php 
//debug($paginator->hasPrev());
if ($paginator->hasPrev() || $paginator->hasNext()) { ?>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<?php } ?> <br />



<table cellpadding="0" cellspacing="0" width="100%" class="ghresultstable" >
<tr  class="ghresultscolheader">
	<th nowrap="nowrap">Image</th>
	<?php if (isset($open_house)) { ?>
	<th nowrap="nowrap"><?php echo $paginator->sort('Open House Date', 'Open_House_Start_Timestamp', array('rel' => 'nofollow'));?>&nbsp;</th>
    <?	} ?>
	
	
	<?php if  (!isset($this->params['search_type'])) { 
				if(!isset($property_type) || empty($property_type)){
					if(!isset($open_house)){
			?> 
					<th nowrap="nowrap"><?php echo $paginator->sort('Type', 'Property_Type', array('rel' => 'nofollow'));?>&nbsp;</th>
			<?
				} 
			}
		}
	 ?>
	<?php if (!isset($open_house)) { ?>
		<th nowrap="nowrap"><?php echo $paginator->sort('S/L', 'Transaction_Type', array('rel' => 'nofollow'));?>&nbsp;</th>
	<? } ?>
	<th nowrap="nowrap"><?php echo $paginator->sort('MLS #', 'ML_Number_Display', array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Price', 'Search_Price', array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Beds', 'Bedrooms' , array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Baths', 'Full_Bathrooms', array('rel' => 'nofollow'));?>&nbsp;</th>
	<th nowrap="nowrap"><?php echo $paginator->sort('Sq. Ft.', 'Square_Footage', array('rel' => 'nofollow'));?>&nbsp;</th>

	<th nowrap="nowrap"><?php echo $paginator->sort('Lot Size', 'Lot_Size', array('rel' => 'nofollow'));?>&nbsp;</th>

	<th nowrap="nowrap"><?php echo $paginator->sort('Address', 'Street_Full_Address', array('rel' => 'nofollow'));?>&nbsp;</th>
		<?php if  ($this->params['action'] != 'city') {  ?>
			<th nowrap="nowrap"><?php echo $paginator->sort('City', 'City', array('rel' => 'nofollow'));?>&nbsp;</th>
		<? } ?>
	<?php if  ($this->params['action'] != 'region' && !isset($open_house)) {  ?>
		<th nowrap="nowrap"><?php echo $paginator->sort('County', 'County', array('rel' => 'nofollow'));?>&nbsp;</th>
	<?php } ?>
	<?php if  ($this->params['action'] != 'zip' && !isset($open_house)) {  ?>
	<th nowrap="nowrap"><?php echo $paginator->sort('Zip', 'Zip', array('rel' => 'nofollow'));?>&nbsp;</th>
	<?php } ?>
<!--
	<th nowrap="nowrap"><?php echo $paginator->sort('Updated', 'modified', array('rel' => 'nofollow'));?>&nbsp;</th>
-->
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
		<?php echo $html->link($html->image(array('admin' => 0, 'controller' => 'properties', 'action' => 'image', $property['Property']['ML_Number_Display'].'/thumb/image:thumb_'.$property['Property']['ML_Number_Display'].'.jpg'), array('title' => $text->trim($property['Property']['Marketing_Remarks'], 500))), array('controller' => 'properties', 'action' => 'view', $property['Property']['ML_Number_Display']), null, null, false); 

		
		?>
		
		</td>
	<?php if (isset($open_house)) {
		$date = $property['Property']['Open_House_Start_Date'];
		$date = strtotime($date);
		$date = date('D M j, Y', $date);
		$start_time = $property['Property']['Open_House_Start_Time'];
		$start_time = strtotime($start_time);
		$start_time = date('g:i a', $start_time);
		$end_time = $property['Property']['Open_House_End_Time'];
		$end_time = strtotime($end_time);
		$end_time = date('g:i a', $end_time);
		echo '<td>';
		if($time->isToday($date)){
			echo '<b>Today!</b><br />';
		} else 
		if($time->isTomorrow($date)){
			echo '<b>Tomorrow!</b><br />';
		} 
		echo $date.'<br />'.$start_time.' &mdash; '.$end_time;

		echo '</td>';
	} ?>
		
			<?php if  (!isset($this->params['search_type'])) {
			
					if(!isset($property_type) || empty($property_type)){
						if(!isset($open_house)){
	  ?>

		<td>
		<?php
			 echo ' '.$property['PropertyType']['name']; 
		?>
		<cake:nocache>
		<?php	if(Configure::read('debug') > 0){
				if($added == $property['Property']['ML_Number_Display']){
					echo '<br /><b>ADDED!</b>';
				}
				else if($removed == $property['Property']['ML_Number_Display']){
					echo '<br /><b>REMOVED!</b>';
				}
				if(is_array($favorites) && in_array($property['Property']['ML_Number_Display'], $favorites)){
					if($this->params['action'] != 'favorites'){	
						echo '<br />'.$html->link('Remove from favorites', array('controller' => 'favorites', 'action' => 'remove', $property['Property']['ML_Number_Display']));
					} else {
						echo '<br />'.$html->link('Remove', array('controller' => 'favorites', 'action' => 'remove', $property['Property']['ML_Number_Display']));
				
					}
				} else {
				echo '<br />'.$html->link('Add to favorites', array('controller' => 'favorites', 'action' => 'add', $property['Property']['ML_Number_Display']));
				}
			}
		?>
		</cake:nocache>
		</td>
		<?
				}
			}
		 }
		if (!isset($open_house)) { ?>
 		<td>
			<?php echo $transaction_type[$property['Property']['Transaction_Type']]; ?>&nbsp;
		</td>
		<?  } ?>
		
		<td>
			<?php echo $html->link(__($property['Property']['ML_Number_Display'], true), array('admin' => 0, 'action'=>'view', $property['Property']['ML_Number_Display'])); ?>&nbsp;
		</td>

		<td>
			<?php // echo $number->currency(round($property['Property']['Search_Price']));  ?>
			<?php echo '$'.$number->format($property['Property']['Search_Price'], ',');  ?>&nbsp;
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
			<?php 
			if($property['Property']['Square_Footage'] > 0){
				echo $number->format($property['Property']['Square_Footage'], ',');
			} else {
				echo '<span style="color:gray; font-style:italic">n/a</span>';
			}
			  ?>&nbsp;
		</td>

		<td>
			<?php 
			if($property['Property']['Lot_Size'] > 0){
				echo $number->format($property['Property']['Lot_Size'], ',');
			} else {
				echo '<span style="color:gray; font-style:italic">n/a</span>';
			}
			  ?>&nbsp;
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
			<?php echo  $html->link($property['Property']['City'], array('controller' => 'properties', 'action' => 'city', strtolower((Inflector::slug($property['Property']['City']))))) ;  ?>&nbsp;
		</td>
			<?   } ?>
			<?php if  ($this->params['action'] != 'region' && !isset($open_house)) {  ?>
				<td>
				<?php echo  $html->link($property['Property']['County'], array('controller' => 'properties', 'action' => 'region', strtolower(Inflector::slug($property['Property']['County'])))) ;  ?>&nbsp;
				</td>
			<?   } ?>

		<?php if  ($this->params['action'] != 'zip' && !isset($open_house)) {  ?>
		<td>
			<?php echo  $html->link(substr($property['Property']['Zip_Code'], 0, 5), array('controller' => 'properties', 'action' => 'zip', strtolower((Inflector::slug(substr($property['Property']['Zip_Code'], 0, 5)))))) ;  ?>&nbsp;
		</td>
		<? } ?>
		
<!--
		<td>
			<?php echo $time->relativeTime($property['Property']['modified']) ;  ?>&nbsp;
		</td>
-->
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
<?  }  // end-if-not-empty ?>