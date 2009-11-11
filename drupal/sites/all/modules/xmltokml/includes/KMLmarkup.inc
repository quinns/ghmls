<?php
class xmltokml_KML_markup {
  function xmltokml_KML_open() {
    $kml_out  = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
    $kml_out .= '<kml xmlns="http://earth.google.com/kml/2.2">' . "\r\n";
    $kml_out .= '  <Document>' . "\r\n";

    $name = variable_get('site_name', 'Recent News');

    $kml_out .= '    <name>' . $name . '</name>' . "\r\n";
    return $kml_out;
  } // function xmltokml_KML_open

  /**
   * Print a Placemark element.
   *
   * @param $title
   *   The title of the Drupal node.
   * @param $description
   *   The description of the Drupal node. Author name, body, and possibly image.
   * @param $start_time
   *   The formatted time of the Drupal node creation.
   * @param $end_time
   *   End time. Note, not currently used.
   * @param $lon
   *   The longitude coordinate of the story location.
   * @param $lat
   *   The latitude coordinate of the story location.
   */   
  function xmltokml_KML_placemark($title, $description, $lon, $lat) {
    $kml_out = '    <Placemark>' . "\r\n";
    $kml_out .= '      <name>' . $title . '</name>' . "\r\n";
    $kml_out .= '      <description>' . $description . '</description>' . "\r\n";

    if (!empty($lon) && !empty($lat)) {
      $kml_out .= '      <Point>' . "\r\n";
      $kml_out .= '        <coordinates>' . "$lon,$lat" . '</coordinates>' . "\r\n";
      $kml_out .= '      </Point>' . "\r\n";
    }

    $kml_out .= '    </Placemark>' . "\r\n";

    return $kml_out;
  } // function xmltokml_KML_placemark

  function xmltokml_KML_close() {
    $kml_out = '  </Document>' . "\r\n";
    $kml_out .= '</kml>';

    return $kml_out;
  } // function xmltokml_KML_close
} // class xmltokml_KML_markup
