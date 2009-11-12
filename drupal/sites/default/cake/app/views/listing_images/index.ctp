<div class="listingImages index">
<h2><?php echo $this->pageTitle; ?></h2>
<p>
<?php
$paginator->options(array('update' => 'content', 'indicator' => 'spinner'));
echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
));
?></p>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
<table cellpadding="0" cellspacing="0">
<tr>
	<th><?php echo $paginator->sort('id');?></th>
	<th><?php echo $paginator->sort('MLS Number', 'mls_number');?></th>
	<th><?php echo $paginator->sort('Thumbnail', 'id');?></th>
</tr>
<?php
$i = 0;
foreach ($listingImages as $listingImage):
	$class = null;
	if ($i++ % 2 == 0) {
		$class = ' class="altrow"';
	}
?>
	<tr<?php echo $class;?>>
		<td>
			<?php echo $html->link($listingImage['ListingImage']['id'], array('controller' => 'listing_images', 'action' => 'view', $listingImage['ListingImage']['id'])); ?>
		</td>
		<td>
			<?php echo $html->link($listingImage['ListingImage']['mls_number'], array('controller' => 'listings', 'action' => 'view', $listingImage['ListingImage']['mls_number'])); ?>
		</td>
		<td>
			<?php echo $this->element('listing_thumbnail', array('listingImage' => $listingImage));?>
		</td>
	</tr>
<?php endforeach; ?>
</table>
</div>
<? echo $paginator->counter(array(
'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
)); ?>
<div class="paging">
	<?php echo $paginator->prev('<< '.__('previous', true), array(), null, array('class'=>'disabled'));?>
 | 	<?php echo $paginator->numbers();?>
	<?php echo $paginator->next(__('next', true).' >>', array(), null, array('class'=>'disabled'));?>
</div>
