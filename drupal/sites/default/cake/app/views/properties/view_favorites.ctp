<cake:nocache>
<?
if(isset($this->params['named']['print'])){
	$this->layout = 'print';
} else {
	echo $html->link('Print these properties', array('controller' => 'properties', 'action' => 'view_favorites', 'print:1'), array('target' => '_blank'));
}
$count = 1;
$limit = count($favorites);
foreach($favorites as $property){
	$url = 'properties/view/'.$property;
	echo '<fieldset>';
	echo '<legend><b>Property '.$count.' of '.$limit.'</b></legend>';
	echo $this->requestAction($url, array('return'));
	$this_url = 'http://greathomes.org/mls/'.$url;
	echo 'Property Web Page: '.$html->link($this_url, $this_url, array('target' => '_blank'));
	echo ' | ';
	echo $html->link('Remove from favorites', array('controller' => 'favorites', 'action' => 'remove', $property));
	echo '</fieldset>';
	$count++;
}
?>
</cake:nocache>