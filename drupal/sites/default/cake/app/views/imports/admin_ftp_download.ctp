<h1>Import Summary</h1>
<dl><?php $i = 0; $class = ' class="altrow"';?>		
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Updates'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo @ $message['report']['updates']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Import Date'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo @ $message['report']['date']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Processing Time'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo @ $message['report']['total']; ?>
			&nbsp;
		</dd>
	</dl>
<br />
<?php
	echo '<h1>Residential data file(s) downloaded</h1>';
	echo '<ol>';
	foreach($residential_files as $file){
		echo '<li>'.$html->link($file, array('controller' => 'imports', 'action' => 'getfile', 'admin' => 1, $file)).'</li>';
	}
	echo '</ol>';
	echo '<br />';
	echo $html->link('Review Update Details', array('controller' => 'update_logs', 'action' => 'view', 'admin' => 1, $log_entry));
	echo '<br />';
	echo 'Memory usage: '.$number->toReadableSize(memory_get_peak_usage());
?>