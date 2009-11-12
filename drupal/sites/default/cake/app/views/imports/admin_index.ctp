<ul>
<?php
	echo '<li>'.$html->link('Download Data from FTP Server and Import to DB', array('controller' => 'imports', 'action' => 'ftp_download', 'admin' => 1));
	echo '<li>'.$html->link('Review Database Update Logs', array('controller' => 'update_logs', 'action' => 'index', 'admin' => 1));
?>
</ul>