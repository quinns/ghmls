<?php
	$latitude = $this->params['coordinates']['Coordinate']['latitude'];
	$longitude = $this->params['coordinates']['Coordinate']['longitude'];

	//$html_info_window = addslashes($html->image(array('controller' => 'listings', 'action' => 'image', '20903419/thumb')));
	//$html_info_window .= '<br />';
	//$html_info_window .= $this->params['coordinates']['Coordinate']['address'];
	$map_init = '
	    <script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAlPjZCqxWs0OAXuFm9YSgmhRg-aVY9BlnwNEvhA8_bKfCb7GClxQaq4A-6B8Wlpvs8JZOWjHwuWZUkw&sensor=false" type="text/javascript"></script>

    <script type="text/javascript">

    function initialize() {
      if (GBrowserIsCompatible()) {
        var map = new GMap2(document.getElementById("map_canvas"));
        map.setCenter(new GLatLng('.$longitude.', '.$latitude.'), 13);
        //map.setMapType(G_HYBRID_MAP);
        map.setUIToDefault();
        
         var point = new GLatLng('.$longitude.', '.$latitude.');
map.addOverlay(new GMarker(point));

        
        
      }
    }

    </script>';
    echo $map_init;
    ?>




<?php
/*
		// start Google Map display
	if($listing['Geo']['latitude'] != '' && $listing['Geo']['longitude'] != ''){
		$default = array('type' => '0', 'zoom' => 4, 'lat' => $listing['Geo']['latitude'], 'long'=> $listing['Geo']['longitude']);
		//$points[0]['Business']['title']  = addslashes('<b>'.$listing['Listing']['street_number'].'</b><br>');
		//$points[0]['Business']['html']  = addslashes($listing['Listing']['address_1'].'<br />'.$listing['Listing']['city'].', CA '.$listing['Listing']['zip'].'<br />');
		$points[0]['Geo']['latitude']  = $listing['Geo']['latitude'];
		$points[0]['Geo']['longitude']  = $listing['Geo']['longitude'];
		
		$this->map_output = $googleMap->map($default, $style = 'width:550px; height: 500px' );
		$this->map_output .= $googleMap->addMarkers($points, NULL, true);
		$out = "<div id=\"map\"";
		$out .= isset($style) ? "style=\"".$style."\"" : null;
		$out .= " ></div>";
		echo $out;
		
		echo '<div id="map_canvas">';
		echo $googleMap->map($default, $style = 'width:550px; height: 500px' );
		echo $googleMap->addMarkers($points, NULL, true);
		echo '</div>';
	}
		// end Google Map display
		*/
?>