<?
if(isset($this->params['domain_info']['this_client']['client_name'])){
	$app_name = $this->params['domain_info']['this_client']['client_name'];
} else {
	$app_name = APP_NAME;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
  <head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>
    	<?php __($app_name); ?> : 
		<?php echo $title_for_layout; ?>
	</title>
  
   	<?php
/* 		echo $html->css('cake.generic'); */
		echo $scripts_for_layout;
	?>
	  <link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/cck/theme/content-module.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/date/date.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/aggregator/aggregator.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/node/node.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/system/defaults.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/system/system.css?p" />

<link type="text/css" rel="stylesheet" media="all" href="/modules/system/system-menus.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/user/user.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/ctools/css/ctools.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/extlink/extlink.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/filefield/filefield.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/logintoboggan/logintoboggan.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/thickbox/thickbox.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/all/modules/thickbox/thickbox_ie.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/modules/acquia/cck/modules/fieldgroup/fieldgroup.css?p" />
<link type="text/css" rel="stylesheet" media="all" href="/sites/default/files/color/garland-2f5018f4/style.css?p" />
<link type="text/css" rel="stylesheet" media="print" href="/themes/garland/print.css?p" />
    <script type="text/javascript" src="/misc/jquery.js?p"></script>
<script type="text/javascript" src="/misc/drupal.js?p"></script>
<script type="text/javascript" src="/sites/all/modules/extlink/extlink.js?p"></script>

<script type="text/javascript" src="/sites/all/modules/thickbox/thickbox.js?p"></script>
<script type="text/javascript">
<!--//--><![CDATA[//><!--
jQuery.extend(Drupal.settings, { "basePath": "/", "extlink": { "extTarget": 0, "extClass": "ext", "extSubdomains": 1, "extExclude": "", "extInclude": "", "extAlert": 0, "extAlertText": "This link will take you to an external web site. We are not responsible for their content.", "mailtoClass": "mailto" }, "thickbox": { "close": "Close", "next": "Next \x3e", "prev": "\x3c Prev", "esc_key": "or Esc Key", "next_close": "Next / Close on last", "image_count": "Image !current of !total" } });
//--><!]]>
</script>
    <!--[if lt IE 7]>
      <link type="text/css" rel="stylesheet" media="all" href="/themes/garland/fix-ie.css" />    <![endif]-->
 


 </head>
  <body class="sidebar-left">

<!-- Layout -->
  <div id="header-region" class="clear-block"></div>

    <div id="wrapper">

    <div id="container" class="clear-block">

      <div id="header">
        <div id="logo-floater">
        <h1><a href="/" title="Nu-Designs MLS"><span><?php echo $app_name; ?></span></a></h1>        </div>

                                                    
      </div> <!-- /header -->

              <div id="sidebar-left" class="sidebar">
			
			
			  <h2>Browse Properties</h2>
			  <ul>
            <li><?php echo $html->link('My Property Listings', array('controller' => 'properties', 'action' => 'index')); ?></li>
            <li><?php echo $html->link('All Property Listings', array('controller' => 'properties', 'action' => 'index', 'all:1')); ?></li>
			</ul>
<?

/*
                    <div id="block-user-0" class="clear-block block block-user">


  <h2>User login</h2>

  <div class="content"><form action="/node?destination=node"  accept-charset="UTF-8" method="post" id="user-login-form">
<div><div class="form-item" id="edit-name-wrapper">
 <label for="edit-name">Username: <span class="form-required" title="This field is required.">*</span></label>
 <input type="text" maxlength="60" name="name" id="edit-name" size="15" value="" tabindex="1" class="form-text required" />
</div>

<div class="form-item" id="edit-pass-wrapper">
 <label for="edit-pass">Password: <span class="form-required" title="This field is required.">*</span></label>
 <input type="password" name="pass" id="edit-pass"  maxlength="60"  size="15"  tabindex="2" class="form-text required" />
</div>
<input type="submit" name="op" id="edit-submit" value="Log in"  tabindex="3" class="form-submit" />
<div class="item-list"><ul><li class="first"><a href="/user/register" title="Create a new user account.">Create new account</a></li>
<li class="last"><a href="/user/password" title="Request new password via e-mail.">Request new password</a></li>
</ul></div><input type="hidden" name="form_build_id" id="form-5f81dd1d2d6691be589ec5fcf57ddcd8" value="form-5f81dd1d2d6691be589ec5fcf57ddcd8"  />
<input type="hidden" name="form_id" id="edit-user-login-block" value="user_login_block"  />

</div></form>


</div>
</div>
*/

?>
<div id="block-user-1" class="clear-block block block-user">

  <h2>Navigation</h2>

  <div class="content"><ul class="menu"><li class="leaf last"><a href="/tracker">Recent posts</a></li>
</ul></div>
</div>
        </div>
      
      <div id="center"><div id="squeeze"><div class="right-corner"><div class="left-corner">

                                                                                          <div class="clear-block">
           
			<?php $session->flash(); ?>

			<?php echo $content_for_layout; ?>
         
            
              </div>

                    <div id="footer"><div id="block-system-0" class="clear-block block block-system">


  <div class="content"><a href="http://drupal.org"><img src="/misc/powered-blue-80x15.png" alt="Powered by Drupal, an open source content management system" title="Powered by Drupal, an open source content management system" width="80" height="15" /></a></div>
</div>
</div>
      </div></div></div></div> <!-- /.left-corner, /.right-corner, /#squeeze, /#center -->

      
    </div> <!-- /container -->
  </div>

<!-- /layout -->

    </body>
</html>
