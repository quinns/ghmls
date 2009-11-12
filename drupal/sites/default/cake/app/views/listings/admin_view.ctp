
<table>
<?php foreach($listing['Listing'] as $key => $value){ ?>
<tr>
	<td><?php echo Inflector::humanize($key); ?></td>
	<td><?php echo $value; ?></td>
</tr>
<? } ?>
</table>

<?php // debug($listing); ?>
