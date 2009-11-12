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
foreach ($BusinessOpps as $BusinessOpp):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
		<?php echo $html->link($html->image(array('admin' => 0, 'controller' => 'listings', 'action' => 'image', $BusinessOpp['BusinessOpp']['ML_Number_Display'].'/thumb'), array('title' => $text->trim($BusinessOpp['BusinessOpp']['Marketing_Remarks'], 500))), array('controller' => 'business_opps', 'action' => 'view', $BusinessOpp['BusinessOpp']['ML_Number_Display']), null, null, false); ?>&nbsp;
		</td>

		<td>
			<?php echo $html->link(__($BusinessOpp['BusinessOpp']['ML_Number_Display'], true), array('admin' => 0, 'action'=>'view', $BusinessOpp['BusinessOpp']['ML_Number_Display'])); ?>&nbsp;
		</td>
		<td>
			<?php echo $number->currency($BusinessOpp['BusinessOpp']['Search_Price']);  ?>&nbsp;
		</td>
		<td>
			<?php // echo  $BusinessOpp['BusinessOpp']['Street_Full_Address'];  ?>
			<?php echo  $BusinessOpp['BusinessOpp']['Street_Number'].' '.$BusinessOpp['BusinessOpp']['Street_Name'].' '.$BusinessOpp['BusinessOpp']['Street_Suffix'];  ?>&nbsp;
		</td>
			<?php if  ($this->params['action'] != 'city') {  ?>
		<td>
			<?php echo  $html->link($BusinessOpp['BusinessOpp']['City'], array('controller' => 'business_opps', 'action' => 'city', strtolower((Inflector::slug($BusinessOpp['BusinessOpp']['City']))))) ;  ?>&nbsp;
		</td>
			<?   } ?>
			<?php if  ($this->params['action'] != 'region') {  ?>
				<td>
				<?php echo  $html->link($BusinessOpp['BusinessOpp']['County'], array('controller' => 'business_opps', 'action' => 'region', strtolower((Inflector::slug($BusinessOpp['BusinessOpp']['County']))))) ;  ?>&nbsp;
				</td>
			<?   } ?>

		<?php if  ($this->params['action'] != 'zip') {  ?>
		<td>
			<?php echo  $html->link(substr($BusinessOpp['BusinessOpp']['Zip_Code'], 0, 5), array('controller' => 'business_opps', 'action' => 'zip', strtolower((Inflector::slug(substr($BusinessOpp['BusinessOpp']['Zip_Code'], 0, 5)))))) ;  ?>&nbsp;
		</td>
		<? } ?>
		
		<td>
			<?php echo $time->relativeTime($BusinessOpp['BusinessOpp']['modified']) ;  ?>&nbsp;
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


<!--
<div class="BusinessOpps index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('ML_Number_Display');?></th>
	<th><?php echo $paginator->sort('Acres');?></th>
	<th><?php echo $paginator->sort('City');?></th>
	<th><?php echo $paginator->sort('State');?></th>
	<th><?php echo $paginator->sort('Street_Name');?></th>
	<th><?php echo $paginator->sort('Street_Number');?></th>
	<th><?php echo $paginator->sort('Street_Suffix');?></th>
	<th><?php echo $paginator->sort('Zip_Code');?></th>
	<th><?php echo $paginator->sort('Agent_Email_Address');?></th>
	<th><?php echo $paginator->sort('Agent_First_Name');?></th>
	<th><?php echo $paginator->sort('Agent_Last_Name');?></th>
	<th><?php echo $paginator->sort('Agent_Name');?></th>
	<th><?php echo $paginator->sort('Agent_NRDS_ID');?></th>
	<th><?php echo $paginator->sort('Agent_Number');?></th>
	<th><?php echo $paginator->sort('Area_Display');?></th>
	<th><?php echo $paginator->sort('Bathrooms_Display');?></th>
	<th><?php echo $paginator->sort('Bedrooms');?></th>
	<th><?php echo $paginator->sort('County');?></th>
	<th><?php echo $paginator->sort('Cross_Street');?></th>
	<th><?php echo $paginator->sort('Full_Bathrooms');?></th>
	<th><?php echo $paginator->sort('Half_Bathrooms');?></th>
	<th><?php echo $paginator->sort('IDX');?></th>
	<th><?php echo $paginator->sort('Listing_Agent_Email');?></th>
	<th><?php echo $paginator->sort('Lot_Size_Sq_Ft');?></th>
	<th><?php echo $paginator->sort('Lot_Size_Acres');?></th>
	<th><?php echo $paginator->sort('Lot_Size_Source');?></th>
	<th><?php echo $paginator->sort('Map_Coordinates');?></th>
	<th><?php echo $paginator->sort('Map_Page');?></th>
	<th><?php echo $paginator->sort('Marketing_Remarks');?></th>
	<th><?php echo $paginator->sort('Office_Broker_ID');?></th>
	<th><?php echo $paginator->sort('Office_Full_Name_and_Phones');?></th>
	<th><?php echo $paginator->sort('Office_Long_Name');?></th>
	<th><?php echo $paginator->sort('Office_NRDS_ID');?></th>
	<th><?php echo $paginator->sort('Office_Short_Name');?></th>
	<th><?php echo $paginator->sort('Office_ID');?></th>
	<th><?php echo $paginator->sort('Office_Phone_1');?></th>
	<th><?php echo $paginator->sort('Property_Subtype_1_Display');?></th>
	<th><?php echo $paginator->sort('Property_Type');?></th>
	<th><?php echo $paginator->sort('Publish_to_Internet');?></th>
	<th><?php echo $paginator->sort('Search_Price');?></th>
	<th><?php echo $paginator->sort('Search_Price_Display');?></th>
	<th><?php echo $paginator->sort('Square_Footage');?></th>
	<th><?php echo $paginator->sort('Square_Footage_and_Source');?></th>
	<th><?php echo $paginator->sort('Square_Footage_Source');?></th>
	<th><?php echo $paginator->sort('Status');?></th>
	<th><?php echo $paginator->sort('Street_Direction');?></th>
	<th><?php echo $paginator->sort('Street_Full_Address');?></th>
	<th><?php echo $paginator->sort('Subdivision_Display');?></th>
	<th><?php echo $paginator->sort('Total_Bathrooms');?></th>
	<th><?php echo $paginator->sort('Virtual_Tour_URL');?></th>
	<th><?php echo $paginator->sort('Year_Built');?></th>
	<th><?php echo $paginator->sort('Agent_Nickname');?></th>
	<th><?php echo $paginator->sort('Agent_Office_Number');?></th>
	<th><?php echo $paginator->sort('Area');?></th>
	<th><?php echo $paginator->sort('Lot_Size');?></th>
	<th><?php echo $paginator->sort('Map_Book');?></th>
	<th><?php echo $paginator->sort('Map_Book_Display');?></th>
	<th><?php echo $paginator->sort('Map_Page_and_Coordinates');?></th>
	<th><?php echo $paginator->sort('MLS');?></th>
	<th><?php echo $paginator->sort('Pictures_Count');?></th>
	<th><?php echo $paginator->sort('Primary_Picture_Filename');?></th>
	<th><?php echo $paginator->sort('Primary_Picture_Url');?></th>
	<th><?php echo $paginator->sort('Listing_Office_Fax_Phone');?></th>
	<th><?php echo $paginator->sort('Transaction_Type');?></th>
	<th><?php echo $paginator->sort('Area_Short_Display');?></th>
	<th><?php echo $paginator->sort('Listing_Date');?></th>
	<th><?php echo $paginator->sort('Agent_Phones');?></th>
	<th><?php echo $paginator->sort('Agent_Phone_1');?></th>
	<th><?php echo $paginator->sort('Reciprocal_Member_Name');?></th>
	<th><?php echo $paginator->sort('Reciprocal_Member_Phone');?></th>
	<th><?php echo $paginator->sort('Reciprocal_Office_Name');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Email_Address');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Fax_Phone');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Full_Name');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Phone_Type_1');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Web_Page_Address');?></th>
	<th><?php echo $paginator->sort('Co_Office_Fax_Phone');?></th>
	<th><?php echo $paginator->sort('Co_Office_Full_Name_and_Phones');?></th>
	<th><?php echo $paginator->sort('Agent_Web_Page_Address');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($BusinessOpps as $BusinessOpp):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['ML_Number_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Acres']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['City']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['State']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Number']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Suffix']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Zip_Code']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Email_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_First_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Last_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_NRDS_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Number']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Area_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Bathrooms_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Bedrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['County']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Cross_Street']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Full_Bathrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Half_Bathrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['IDX']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Agent_Email']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Sq_Ft']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Acres']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Source']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Coordinates']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Page']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Marketing_Remarks']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Broker_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Full_Name_and_Phones']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Long_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_NRDS_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Short_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Phone_1']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Property_Subtype_1_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Property_Type']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Publish_to_Internet']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Search_Price']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Search_Price_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage_and_Source']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage_Source']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Status']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Direction']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Full_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Subdivision_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Total_Bathrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Virtual_Tour_URL']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Year_Built']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Nickname']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Office_Number']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Area']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Book']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Book_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Page_and_Coordinates']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['MLS']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Pictures_Count']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Primary_Picture_Filename']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Primary_Picture_Url']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Office_Fax_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Transaction_Type']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Area_Short_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Date']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Phones']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Phone_1']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Member_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Member_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Office_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Email_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Fax_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Full_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Phone_Type_1']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Web_Page_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Office_Fax_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Office_Full_Name_and_Phones']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Web_Page_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['created']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $BusinessOpp['BusinessOpp']['ML_Number_Display'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $BusinessOpp['BusinessOpp']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $BusinessOpp['BusinessOpp']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $BusinessOpp['BusinessOpp']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>


</div>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>




<?php
/*
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('ML_Number_Display');?></th>
	<th><?php echo $paginator->sort('Acres');?></th>
	<th><?php echo $paginator->sort('City');?></th>
	<th><?php echo $paginator->sort('State');?></th>
	<th><?php echo $paginator->sort('Street_Name');?></th>
	<th><?php echo $paginator->sort('Street_Number');?></th>
	<th><?php echo $paginator->sort('Street_Suffix');?></th>
	<th><?php echo $paginator->sort('Zip_Code');?></th>
	<th><?php echo $paginator->sort('Agent_Email_Address');?></th>
	<th><?php echo $paginator->sort('Agent_First_Name');?></th>
	<th><?php echo $paginator->sort('Agent_Last_Name');?></th>
	<th><?php echo $paginator->sort('Agent_Name');?></th>
	<th><?php echo $paginator->sort('Agent_NRDS_ID');?></th>
	<th><?php echo $paginator->sort('Agent_Number');?></th>
	<th><?php echo $paginator->sort('Area_Display');?></th>
	<th><?php echo $paginator->sort('Bathrooms_Display');?></th>
	<th><?php echo $paginator->sort('Bedrooms');?></th>
	<th><?php echo $paginator->sort('County');?></th>
	<th><?php echo $paginator->sort('Cross_Street');?></th>
	<th><?php echo $paginator->sort('Full_Bathrooms');?></th>
	<th><?php echo $paginator->sort('Half_Bathrooms');?></th>
	<th><?php echo $paginator->sort('IDX');?></th>
	<th><?php echo $paginator->sort('Listing_Agent_Email');?></th>
	<th><?php echo $paginator->sort('Lot_Size_Sq_Ft');?></th>
	<th><?php echo $paginator->sort('Lot_Size_Acres');?></th>
	<th><?php echo $paginator->sort('Lot_Size_Source');?></th>
	<th><?php echo $paginator->sort('Map_Coordinates');?></th>
	<th><?php echo $paginator->sort('Map_Page');?></th>
	<th><?php echo $paginator->sort('Marketing_Remarks');?></th>
	<th><?php echo $paginator->sort('Office_Broker_ID');?></th>
	<th><?php echo $paginator->sort('Office_Full_Name_and_Phones');?></th>
	<th><?php echo $paginator->sort('Office_Long_Name');?></th>
	<th><?php echo $paginator->sort('Office_NRDS_ID');?></th>
	<th><?php echo $paginator->sort('Office_Short_Name');?></th>
	<th><?php echo $paginator->sort('Office_ID');?></th>
	<th><?php echo $paginator->sort('Office_Phone_1');?></th>
	<th><?php echo $paginator->sort('Property_Subtype_1_Display');?></th>
	<th><?php echo $paginator->sort('Property_Type');?></th>
	<th><?php echo $paginator->sort('Publish_to_Internet');?></th>
	<th><?php echo $paginator->sort('Search_Price');?></th>
	<th><?php echo $paginator->sort('Search_Price_Display');?></th>
	<th><?php echo $paginator->sort('Square_Footage');?></th>
	<th><?php echo $paginator->sort('Square_Footage_and_Source');?></th>
	<th><?php echo $paginator->sort('Square_Footage_Source');?></th>
	<th><?php echo $paginator->sort('Status');?></th>
	<th><?php echo $paginator->sort('Street_Direction');?></th>
	<th><?php echo $paginator->sort('Street_Full_Address');?></th>
	<th><?php echo $paginator->sort('Subdivision_Display');?></th>
	<th><?php echo $paginator->sort('Total_Bathrooms');?></th>
	<th><?php echo $paginator->sort('Virtual_Tour_URL');?></th>
	<th><?php echo $paginator->sort('Year_Built');?></th>
	<th><?php echo $paginator->sort('Agent_Nickname');?></th>
	<th><?php echo $paginator->sort('Agent_Office_Number');?></th>
	<th><?php echo $paginator->sort('Area');?></th>
	<th><?php echo $paginator->sort('Lot_Size');?></th>
	<th><?php echo $paginator->sort('Map_Book');?></th>
	<th><?php echo $paginator->sort('Map_Book_Display');?></th>
	<th><?php echo $paginator->sort('Map_Page_and_Coordinates');?></th>
	<th><?php echo $paginator->sort('MLS');?></th>
	<th><?php echo $paginator->sort('Pictures_Count');?></th>
	<th><?php echo $paginator->sort('Primary_Picture_Filename');?></th>
	<th><?php echo $paginator->sort('Primary_Picture_Url');?></th>
	<th><?php echo $paginator->sort('Listing_Office_Fax_Phone');?></th>
	<th><?php echo $paginator->sort('Transaction_Type');?></th>
	<th><?php echo $paginator->sort('Area_Short_Display');?></th>
	<th><?php echo $paginator->sort('Listing_Date');?></th>
	<th><?php echo $paginator->sort('Agent_Phones');?></th>
	<th><?php echo $paginator->sort('Agent_Phone_1');?></th>
	<th><?php echo $paginator->sort('Reciprocal_Member_Name');?></th>
	<th><?php echo $paginator->sort('Reciprocal_Member_Phone');?></th>
	<th><?php echo $paginator->sort('Reciprocal_Office_Name');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Email_Address');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Fax_Phone');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Full_Name');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Phone_Type_1');?></th>
	<th><?php echo $paginator->sort('Co_Agent_Web_Page_Address');?></th>
	<th><?php echo $paginator->sort('Co_Office_Fax_Phone');?></th>
	<th><?php echo $paginator->sort('Co_Office_Full_Name_and_Phones');?></th>
	<th><?php echo $paginator->sort('Agent_Web_Page_Address');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($BusinessOpps as $BusinessOpp):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['id']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['ML_Number_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Acres']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['City']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['State']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Number']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Suffix']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Zip_Code']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Email_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_First_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Last_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_NRDS_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Number']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Area_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Bathrooms_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Bedrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['County']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Cross_Street']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Full_Bathrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Half_Bathrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['IDX']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Agent_Email']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Sq_Ft']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Acres']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size_Source']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Coordinates']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Page']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Marketing_Remarks']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Broker_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Full_Name_and_Phones']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Long_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_NRDS_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Short_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_ID']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Office_Phone_1']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Property_Subtype_1_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Property_Type']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Publish_to_Internet']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Search_Price']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Search_Price_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage_and_Source']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Square_Footage_Source']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Status']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Direction']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Street_Full_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Subdivision_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Total_Bathrooms']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Virtual_Tour_URL']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Year_Built']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Nickname']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Office_Number']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Area']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Lot_Size']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Book']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Book_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Map_Page_and_Coordinates']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['MLS']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Pictures_Count']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Primary_Picture_Filename']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Primary_Picture_Url']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Office_Fax_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Transaction_Type']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Area_Short_Display']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Listing_Date']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Phones']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Phone_1']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Member_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Member_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Reciprocal_Office_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Email_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Fax_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Full_Name']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Phone_Type_1']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Agent_Web_Page_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Office_Fax_Phone']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Co_Office_Full_Name_and_Phones']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['Agent_Web_Page_Address']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['created']; ?>
		</td>
		<td>
			<?php echo $BusinessOpp['BusinessOpp']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $BusinessOpp['BusinessOpp']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $BusinessOpp['BusinessOpp']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $BusinessOpp['BusinessOpp']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $BusinessOpp['BusinessOpp']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

*/

?>
-->
