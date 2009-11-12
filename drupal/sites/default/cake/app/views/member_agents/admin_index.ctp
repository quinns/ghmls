<div class="mlsAgents index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('Agent_MLS_ID');?></th>
	<th><?php echo $paginator->sort('Agent_First_Name');?></th>
	<th><?php echo $paginator->sort('Agent_Last_Name');?></th>
	<th><?php echo $paginator->sort('Agent_Middle_Initial');?></th>
	<th><?php echo $paginator->sort('Agent_Phone_1');?></th>
	<th><?php echo $paginator->sort('Office_MLS_ID');?></th>
	<th><?php echo $paginator->sort('Agent_Class_Bill_Type');?></th>
	<th><?php echo $paginator->sort('Agent_Email_Address');?></th>
	<th><?php echo $paginator->sort('Agent_Web_Page_Address');?></th>
	<th><?php echo $paginator->sort('Agent_License');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
</tr>
<?php
$i = 0;
foreach ($memberAgents as $memberAgent):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>

		<td>
		<?php echo $html->link(__( $memberAgent['MemberAgent']['Agent_MLS_ID'], true), array('action'=>'view', $memberAgent['MemberAgent']['Agent_MLS_ID'])); ?>
			
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_First_Name']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_Last_Name']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_Middle_Initial']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_Phone_1']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Office_MLS_ID']; ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_Class_Bill_Type']; ?>
		</td>
		<td>
			<?php echo $text->autoLink($memberAgent['MemberAgent']['Agent_Email_Address']); ?>
		</td>
		<td>
			<?php echo $text->autoLink($memberAgent['MemberAgent']['Agent_Web_Page_Address']); ?>
		</td>
		<td>
			<?php echo $memberAgent['MemberAgent']['Agent_License']; ?>
		</td>

		<td>
			<?php echo $memberAgent['MemberAgent']['modified']; ?>
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

