<div class="listings index">
<p>
<?php
$paginator->options(array('url' => $this->passedArgs));
//$paginator->options(array('update' => 'content', 'indicator' => 'spinner'));
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
	<th>Image</th>
	<th><?php echo $paginator->sort('MLS Id', 'mls_number');?></th>
	<th><?php echo $paginator->sort('Street_Full_Address');?></th>
	<th><?php echo $paginator->sort('Search_Price');?></th>
	<th><?php echo $paginator->sort('County');?></th>
	<th><?php echo $paginator->sort('created');?></th>
	<th><?php echo $paginator->sort('modified');?></th>

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
		<?php  echo $html->image(array('controller' => 'listings', 'action' => 'thumbnail', base64_encode($image)), array('class' => 'thickbox')); 
		//	$thumbnail = array('controller' => 'listings', 'action' => 'thumbnail', base64_encode($image));
	//	echo $html->link($thumbnail, array('controller' => 'listings' ,'action' => 'fullsize_image',  base64_encode($image).'.jpg'));
		?>
		</td>

		<td>
			<?php echo $html->link(__($listing['Listing']['ML_Number_Display'], true), array('admin' => 0, 'action'=>'view', $listing['Listing']['ML_Number_Display'])); ?>
		</td>
		<td>
			<?php echo $listing['Listing']['Street_Full_Address'];  ?>
		</td>
		<td>
			<?php echo $number->currency($listing['Listing']['Search_Price']);  ?>
		</td>
		<td>
			<?php echo  $listing['Listing']['County'] ;  ?>
		</td>
		<td>
			<?php echo  $listing['Listing']['created'] ;  ?>
		</td>
		<td>
			<?php echo  $listing['Listing']['modified'] ;  ?>
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
