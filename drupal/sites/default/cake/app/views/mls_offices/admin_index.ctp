<div class="mlsOffices index">
<h2><?php __('MlsOffices');?></h2>
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('Office_MLS_ID');?></th>
	<th><?php echo $paginator->sort('Office_Long_Name');?></th>
	<th><?php echo $paginator->sort('Office_Care_of');?></th>
	<th><?php echo $paginator->sort('Office_Address');?></th>
	<th><?php echo $paginator->sort('Office_City');?></th>
	<th><?php echo $paginator->sort('Office_Zip_Code');?></th>
	<th><?php echo $paginator->sort('Office_Zip_Plus_4');?></th>
	<th><?php echo $paginator->sort('Office_State');?></th>
	<th><?php echo $paginator->sort('Office_Status');?></th>
	<th><?php echo $paginator->sort('Office_Class_Type');?></th>
	<th><?php echo $paginator->sort('Office_Phone_1');?></th>
	<th><?php echo $paginator->sort('Office_BR');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($mlsOffices as $mlsOffice):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $mlsOffice['MlsOffice']['id']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_MLS_ID']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Long_Name']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Care_of']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Address']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_City']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Zip_Code']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Zip_Plus_4']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_State']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Status']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Class_Type']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_Phone_1']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['Office_BR']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['created']; ?>
		</td>
		<td>
			<?php echo $mlsOffice['MlsOffice']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $mlsOffice['MlsOffice']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $mlsOffice['MlsOffice']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $mlsOffice['MlsOffice']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $mlsOffice['MlsOffice']['id'])); ?>
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
<div class="actions">
	<ul>
		<li><?php echo $html->link(__('New MlsOffice', true), array('action'=>'add')); ?></li>
	</ul>
</div>
