<p>Are you sure you wish to perform the database import at this time?</p>
<?php
	echo $form->create(array('controller' => 'imports', 'action' => 'ftp_download'));
	echo '<br />Note: This process can take some time to complete.';
	echo $form->hidden('Input.run_import');
	echo $form->submit('Yes, run update now!', array('onclick' => 'this.disabled=true;this.value="Please wait ..."'));
	echo 'Memory usage: '.$number->toReadableSize(memory_get_peak_usage());

?>
