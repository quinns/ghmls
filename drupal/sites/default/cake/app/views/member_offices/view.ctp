<? 
/*
echo '<!-- ';
print_r($memberOffice['MemberOffice']); 
echo '-->';
*/

	$this->pageTitle = 'Office Detail: '.$memberOffice['MemberOffice']['Office_Long_Name'];
?>
<style>
dl {
	line-height: 2em;
	margin: 0em 0em;
	/* width: 60%; */
}
dl.altrow {
	background: #f4f4f4;
}
dt {
	font-weight: bold;
	padding-left: 4px;
	vertical-align: top;
}
dd {
	margin-left: 200px;
	margin-top: -2em;
	vertical-align: top;
}
</style>

<div class="memberOffices view">
<h2><?php echo $this->pageTitle; ?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_Long_Name']; ?>
			&nbsp;
		</dd>

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Broker'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_Broker_Name']; ?>
			&nbsp;
		</dd>


		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office License #'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_Corporate_License']; ?>
			&nbsp;
		</dd>
		
		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Address'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_Address']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office City'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_City']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Zip Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_Zip_Code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office State'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_State']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Office Phone'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $memberOffice['MemberOffice']['Office_Phone_1']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Updated'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $time->relativeTime($memberOffice['MemberOffice']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<?
/*  debug($memberOffice);  */
 
 if(count($memberOffice['Property']) > 0){
 	if(count($memberOffice['Property']) > 1){
 		$string = count($memberOffice['Property']).' properties';
 	} else {
  		$string = count($memberOffice['Property']).' property';
	
 	}
 	echo '<br />'.$html->link('See '.$string.' Listed by this Office', array('controller' => 'properties', 'action' => 'listing_office', $memberOffice['MemberOffice']['Office_MLS_ID'])).'<br />';
 } else {
 	echo '<h3>This office does not currently list any properties in our system</h3>';
 }
 
 ?>

<?php if (!empty($memberOffice['MemberAgent'])) { ?>
<br />
<table cellpadding="0" cellspacing="0" class="ghresultstable" width="100%">
<tr>
	<th>Agent First Name&nbsp;</th>
	<th>Agent Last Name&nbsp;</th>
	<th>Phone Number&nbsp;</th>
	<th>Action</th>
</tr>
<?php
$i = 0;
foreach ($memberOffice['MemberAgent'] as $memberAgent):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>


		<td>
			<?php echo $memberAgent['Agent_First_Name']; ?>
		</td>
		<td>
			<?php echo $memberAgent['Agent_Last_Name']; ?>
		</td>
		<td>
			<?php echo $memberAgent['Agent_Phone_1']; ?>
		</td>



		<td>
		<?php echo $html->link('See Details', array('controller' => 'member_agents', 'action'=>'view', $memberAgent['Agent_MLS_ID'])); ?>
			
		</td>
	</tr>
<?php endforeach; ?>
</table>


<? } ?>






