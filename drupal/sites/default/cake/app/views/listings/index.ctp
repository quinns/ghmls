<h2><?php echo $this->pageTitle; ?></h2>
<?
if(isset($search_list)){
	//echo 'Search Results for: ';
	//echo $text->toList($search_list);
	echo $this->element('search_list', array('search_list', $search_list));
}
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
foreach ($listings as $listing):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
		<?php echo $html->link($html->image(array('admin' => 0, 'controller' => 'listings', 'action' => 'image', $listing['Listing']['ML_Number_Display'].'/thumb'), array('title' => $text->trim($listing['Listing']['Marketing_Remarks'], 500))), array('controller' => 'listings', 'action' => 'view', $listing['Listing']['ML_Number_Display']), null, null, false); ?>&nbsp;
		</td>

		<td>
			<?php echo $html->link(__($listing['Listing']['ML_Number_Display'], true), array('admin' => 0, 'action'=>'view', $listing['Listing']['ML_Number_Display'])); ?>&nbsp;
		</td>
		<td>
			<?php echo $number->currency($listing['Listing']['Search_Price']);  ?>&nbsp;
		</td>
		<td>
			<?php echo $listing['Listing']['Bedrooms'];  ?>&nbsp;
		</td>
		<td>
			<?php echo $listing['Listing']['Full_Bathrooms'];  ?>&nbsp;
		</td>
		<td>
			<?php // echo  $listing['Listing']['Street_Full_Address'];  ?>
			<?php
			
			if(strtolower($listing['Listing']['RESI_Address_on_Internet_Desc']) == 'full'){
			
				 echo  $listing['Listing']['Street_Number'].' '.$listing['Listing']['Street_Name'].' '.$listing['Listing']['Street_Suffix'];  
			 } else {
			 	echo '<span style="color:gray; font-style:italic">Suppressed by seller</span>';
			 }?>&nbsp;
		</td>
			<?php if  ($this->params['action'] != 'city') {  ?>
		<td>
			<?php echo  $html->link($listing['Listing']['City'], array('controller' => 'listings', 'action' => 'city', strtolower((Inflector::slug($listing['Listing']['City']))))) ;  ?>&nbsp;
		</td>
			<?   } ?>
			<?php if  ($this->params['action'] != 'region') {  ?>
				<td>
				<?php echo  $html->link($listing['Listing']['County'], array('controller' => 'listings', 'action' => 'region', strtolower((Inflector::slug($listing['Listing']['County']))))) ;  ?>&nbsp;
				</td>
			<?   } ?>

		<?php if  ($this->params['action'] != 'zip') {  ?>
		<td>
			<?php echo  $html->link(substr($listing['Listing']['Zip_Code'], 0, 5), array('controller' => 'listings', 'action' => 'zip', strtolower((Inflector::slug(substr($listing['Listing']['Zip_Code'], 0, 5)))))) ;  ?>&nbsp;
		</td>
		<? } ?>
		
		<td>
			<?php echo $time->relativeTime($listing['Listing']['modified']) ;  ?>&nbsp;
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

<? // debug($listings[0]); ?>
