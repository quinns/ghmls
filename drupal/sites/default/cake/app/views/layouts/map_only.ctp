<!DOCTYPE html "-//W3C//DTD XHTML 1.0 Strict//EN" 
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <title><?php echo $title_for_layout; ?></title>
	<? echo $this->element('map'); ?>
  	</head>
  <body onload="initialize()" onunload="GUnload()" border="0" margin="0" topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
  <?php // echo $html->image('http://greathomes.nu-designs.us/sites/greathomes.nu-designs.us/files/zen_logo.png'); ?>
<?php echo $content_for_layout; ?>
  </body>
</html>