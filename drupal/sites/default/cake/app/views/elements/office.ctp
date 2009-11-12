<?php
	$memberOffice = $office;
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

	<dl><?php $i = 0; $class = ' class="altrow"';?>


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

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Agents at this Office'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php 
/*
			$agent_count = 0;
			foreach($memberOffice['MemberAgent'] as $value) {
				echo $html->link($value['MemberAgent']['Agent_First_Name'].' '.$value['MemberAgent']['Agent_Last_Name'], array('controller' => 'properties', 'action' => 'listing_agent', $value['MemberAgent']['Agent_MLS_ID']));
				if($agent_count < count($memberOffice['MemberAgent'])-1){
					echo ', ';
				}
				$agent_count++;
			} 
*/
			$agent_count = count($memberOffice['MemberAgent']);
			echo $agent_count. ' agent';
			if($agent_count > 1 || $agent_count == 0){
				echo 's';
			}
			echo '. ';
			echo $html->link('See agent list', array('controller' => 'member_offices', 'action' => 'view', $memberOffice['MemberOffice']['Office_MLS_ID']));
			?>
			&nbsp;
		</dd>

		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $time->relativeTime($memberOffice['MemberOffice']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>


<? 

/*
debug($memberOffice);
*/

 ?>

