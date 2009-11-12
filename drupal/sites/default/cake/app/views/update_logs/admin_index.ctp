
<div class="UpdateLogs index">
<p>
<?php
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('Date', 'modified');?></th>
	<th><?php echo $paginator->sort('records_updated');?></th>
	<th><?php echo $paginator->sort('memory_usage');?></th>
	<th><?php echo $paginator->sort('processing_time');?></th>
	<th class="actions"><?php __('Actions');?></th>
</tr>
<?php
$i = 0;
foreach ($UpdateLogs as $UpdateLog):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $UpdateLog['UpdateLog']['id']; ?>
		</td>
		<td>
			<?php echo $time->nice($UpdateLog['UpdateLog']['modified']).' ('.$time->relativeTime($UpdateLog['UpdateLog']['modified']).')'; ?>
		</td>
		<td>
			<?php echo $UpdateLog['UpdateLog']['records_updated']; ?>
		</td>
		<td>
			<?php echo $number->toReadableSize($UpdateLog['UpdateLog']['memory_usage']); ?>
		</td>
		<td>
			<?php echo $number->precision($UpdateLog['UpdateLog']['processing_time'], 4); ?> second(s)
		</td>
		<td class="actions">
			<?php echo $html->link(__('View', true), array('action'=>'view', $UpdateLog['UpdateLog']['id'])); ?>
			<?php echo $html->link(__('Delete', true), array('action'=>'delete', $UpdateLog['UpdateLog']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $UpdateLog['UpdateLog']['id'])); ?>
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

</div>
<? echo $this->element('admin_user'); ?>
