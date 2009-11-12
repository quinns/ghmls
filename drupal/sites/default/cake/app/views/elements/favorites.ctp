<?
if(Configure::read('debug') > 0){
	if(!empty($this->params['favorites'])){
		$fav_link = 'Favorites ('.count($this->params['favorites']).')';
		echo $html->link($fav_link, array('controller' => 'properties', 'action' => 'favorites'));	
			echo ' | ';
			echo $html->link('Remove all favorites', array('controller' => 'favorites', 'action' => 'clear'), null, 'Are you sure you wish to remove all '.count($this->params['favorites']).' favorite properties?');
			echo ' | ';
			echo $html->link('View/print all favorites', array('controller' => 'properties', 'action' => 'view_favorites'), null);

		echo '<br />';
	}

}

?>