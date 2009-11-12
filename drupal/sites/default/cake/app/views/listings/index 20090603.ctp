<? $this->params['prototype'] = true; ?>
<div class="listings index">
<h2><?php echo $this->pageTitle; ?></h2>
<?php if(isset($search_list)) {  echo $this->element('search_list', array('search_list' => $search_list)); } ?>
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
	<th><?php echo 'Thumbnail' ;?></th>
	<th><?php echo $paginator->sort('MLS Id', 'mls_number');?></th>
	<th><?php echo $paginator->sort('county');?></th>
	<th><?php echo $paginator->sort('city');?></th>
	<th><?php echo $paginator->sort('zip_code');?></th>
	<? /* <th><?php echo $paginator->sort('listing_type');?></th> */ ?>
	<th><?php echo $paginator->sort('listing_price');?></th>
	<th><?php echo $paginator->sort('bedrooms');?></th>
	<th><?php echo $paginator->sort('bathrooms');?></th>
	<th><?php echo $paginator->sort('senior');?></th>
	<? /* <th><?php echo $paginator->sort('status');?></th> */ ?>
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
			<?php 
			if(!empty($listing['ListingImage'][0]['id']['local_path'])){
				echo $html->link($html->image(array('controller' => 'listing_images', 'action' => 'listing_thumbnail', $listing['ListingImage'][0]['id'])), array('controller' => 'listings', 'action' => 'view', $listing['Listing']['mls_number']), null,  null, false);
			} else {
				echo 'No Image';
			}	
			 ?>
		</td>
		<td>
			<?php echo $html->link(__($listing['Listing']['mls_number'], true), array('action'=>'view', $listing['Listing']['mls_number'])); ?>
		</td>
		<td>
			<?php echo $html->link($listing['Listing']['county'], array('controller' => 'listings', 'action' => 'region', $listing['County']['id'])); ?>
		</td>
		<td>
			<?php echo $html->link($listing['Listing']['city'], array('controller' => 'listings', 'action' => 'city', $listing['City']['id'])); ?>
		</td>
		<td>
			<?php echo $listing['Listing']['zip_code']; ?>
		</td>
		<? /*
		<td>
			<?php echo $listing['Listing']['listing_type']; ?>
		</td>
		*/ ?>
		<td>
			<?php echo $number->currency($listing['Listing']['listing_price']); ?>
		</td>
		<td>
			<?php echo $listing['Listing']['bedrooms']; ?>
		</td>
		<td>
			<?php echo $listing['Listing']['bathrooms']; ?>
		</td>
		<td>
			<?php echo $listing['Listing']['senior']; ?>&nbsp;
		</td>
		<? /*
		<td>
			<?php echo $listing['Listing']['status']; ?>
		</td>
		*/ ?>
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
