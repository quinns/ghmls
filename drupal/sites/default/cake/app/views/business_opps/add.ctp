<div class="BusinessOpps form">
<?php echo $form->create('BusinessOpp');?>
	<fieldset>
 		<legend><?php __('Add BusinessOpp');?></legend>
	<?php
		echo $form->input('ML_Number_Display');
		echo $form->input('Acres');
		echo $form->input('City');
		echo $form->input('State');
		echo $form->input('Street_Name');
		echo $form->input('Street_Number');
		echo $form->input('Street_Suffix');
		echo $form->input('Zip_Code');
		echo $form->input('Agent_Email_Address');
		echo $form->input('Agent_First_Name');
		echo $form->input('Agent_Last_Name');
		echo $form->input('Agent_Name');
		echo $form->input('Agent_NRDS_ID');
		echo $form->input('Agent_Number');
		echo $form->input('Area_Display');
		echo $form->input('Bathrooms_Display');
		echo $form->input('Bedrooms');
		echo $form->input('County');
		echo $form->input('Cross_Street');
		echo $form->input('Full_Bathrooms');
		echo $form->input('Half_Bathrooms');
		echo $form->input('IDX');
		echo $form->input('Listing_Agent_Email');
		echo $form->input('Lot_Size_Sq_Ft');
		echo $form->input('Lot_Size_Acres');
		echo $form->input('Lot_Size_Source');
		echo $form->input('Map_Coordinates');
		echo $form->input('Map_Page');
		echo $form->input('Marketing_Remarks');
		echo $form->input('Office_Broker_ID');
		echo $form->input('Office_Full_Name_and_Phones');
		echo $form->input('Office_Long_Name');
		echo $form->input('Office_NRDS_ID');
		echo $form->input('Office_Short_Name');
		echo $form->input('Office_ID');
		echo $form->input('Office_Phone_1');
		echo $form->input('Property_Subtype_1_Display');
		echo $form->input('Property_Type');
		echo $form->input('Publish_to_Internet');
		echo $form->input('Search_Price');
		echo $form->input('Search_Price_Display');
		echo $form->input('Square_Footage');
		echo $form->input('Square_Footage_and_Source');
		echo $form->input('Square_Footage_Source');
		echo $form->input('Status');
		echo $form->input('Street_Direction');
		echo $form->input('Street_Full_Address');
		echo $form->input('Subdivision_Display');
		echo $form->input('Total_Bathrooms');
		echo $form->input('Virtual_Tour_URL');
		echo $form->input('Year_Built');
		echo $form->input('Agent_Nickname');
		echo $form->input('Agent_Office_Number');
		echo $form->input('Area');
		echo $form->input('Lot_Size');
		echo $form->input('Map_Book');
		echo $form->input('Map_Book_Display');
		echo $form->input('Map_Page_and_Coordinates');
		echo $form->input('MLS');
		echo $form->input('Pictures_Count');
		echo $form->input('Primary_Picture_Filename');
		echo $form->input('Primary_Picture_Url');
		echo $form->input('Listing_Office_Fax_Phone');
		echo $form->input('Transaction_Type');
		echo $form->input('Area_Short_Display');
		echo $form->input('Listing_Date');
		echo $form->input('Agent_Phones');
		echo $form->input('Agent_Phone_1');
		echo $form->input('Reciprocal_Member_Name');
		echo $form->input('Reciprocal_Member_Phone');
		echo $form->input('Reciprocal_Office_Name');
		echo $form->input('Co_Agent_Email_Address');
		echo $form->input('Co_Agent_Fax_Phone');
		echo $form->input('Co_Agent_Full_Name');
		echo $form->input('Co_Agent_Phone_Type_1');
		echo $form->input('Co_Agent_Web_Page_Address');
		echo $form->input('Co_Office_Fax_Phone');
		echo $form->input('Co_Office_Full_Name_and_Phones');
		echo $form->input('Agent_Web_Page_Address');
	?>
	</fieldset>
<?php echo $form->end('Submit');?>
</div>
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('List BusinessOpps', true), array('action'=>'index'));?></li>
	</ul>
</div>
