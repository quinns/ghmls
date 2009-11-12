<?
	if($status_id == 0){
		echo '<font color="red">Inactive</font>';
	} else if ($status_id == 1){
		echo 'Active';
	} else {
		echo 'Unknown';
	}

?>