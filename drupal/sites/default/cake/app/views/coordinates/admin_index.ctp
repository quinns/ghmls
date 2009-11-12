<div class="coordinates index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('listing_id');?></th>
	<th><?php echo $paginator->sort('latitude');?></th>
	<th><?php echo $paginator->sort('longitude');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($coordinates as $coordinate):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $coordinate['Coordinate']['id']; ?>
		</td>
		<td>
			<?php echo $coordinate['Coordinate']['listing_id']; ?>
		</td>
		<td>
			<?php echo $coordinate['Coordinate']['latitude']; ?>
		</td>
		<td>
			<?php echo $coordinate['Coordinate']['longitude']; ?>
		</td>
		<td>
			<?php echo $coordinate['Coordinate']['created']; ?>
		</td>
		<td>
			<?php echo $coordinate['Coordinate']['modified']; ?>
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $coordinate['Coordinate']['id'])); ?>
			<?php echo $html->link(__('Edit', true), array('action'=>'edit', $coordinate['Coordinate']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $coordinate['Coordinate']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $coordinate['Coordinate']['id'])); ?>
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
		<li><?php echo $html->link(__('New Coordinate', true), array('action'=>'add')); ?></li>
	</ul>
</div>
