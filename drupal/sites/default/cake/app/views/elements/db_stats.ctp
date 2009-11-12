<?  
/* <cake:nocache> */
?>
	<div class="property_footer"><?php
	$stats = $this->params['db_stats'];
/* 	echo '<p><i>Database last updated '.$time->relativeTime($stats['modified']).'. Now listing ' .$number->format($stats['total']).' active records in  '.$html->link($stats['categories'].' categories', array('controller' => 'properties', 'action' => 'types')).' and '.$html->link($stats['counties'].' counties', array('controller' => 'counties', 'action' => 'index')).'.</i>'; */
	echo '<p><i>Database updated every 30 minutes. Now listing ' .$number->format($stats['total']).' active records in  '.$html->link($stats['categories'].' categories', array('controller' => 'properties', 'action' => 'types')).' and '.$html->link($stats['counties'].' counties', array('controller' => 'counties', 'action' => 'index')).'.</i>';
?>
</div>
<?
/* </cake:nocache> */
?>