<!doctype html>
<html>
  <head>
    <meta charset="UTF-8" />

    <title>Great Homes</title>

    <style type="text/css" media="screen">@import "/mls/jqtouch/jqtouch/jqtouch.min.css";</style>
    <style type="text/css" media="screen">@import "/mls/jqtouch/themes/jqt/theme.min.css";</style>
    <script src="/mls/jqtouch/jqtouch/jquery.1.3.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/mls/jqtouch/jqtouch/jqtouch.min.js" type="application/x-javascript" charset="utf-8"></script>
    <script type="text/javascript" charset="utf-8">
        $.jQTouch({
            icon: 'jqtouch.png',
            addGlossToIcon: false,
            startupScreen: 'jqt_startup.png',
            statusBar: 'black',
            preloadImages: [
                '/mls/jqtouch/themes/jqt/img/chevron_white.png',
                '/mls/jqtouch/themes/jqt/img/bg_row_select.gif',
                '/mls/jqtouch/themes/jqt/img/back_button.png',
                '/mls/jqtouch/themes/jqt/img/back_button_clicked.png',
                '/mls/jqtouch/themes/jqt/img/button_clicked.png',
                '/mls/jqtouch/themes/jqt/img/grayButton.png',
                '/mls/jqtouch/themes/jqt/img/whiteButton.png',
                '/mls/jqtouch/themes/jqt/img/loading.gif'
                ]
        });
        
        // Some sample Javascript functions:
        $(function(){
            
            // Show a swipe event on swipe test
            $('#swipeme').addTouchHandlers().bind('swipe', function(evt, data){                
                $(this).html('You swiped <strong>' + data.direction + '</strong>!');
                // alert('Swiped '+ data.direction +' on #' + $(evt.currentTarget).attr('id') + '.');
            });

            $('a[target="_blank"]').click(function(){
                if(confirm('This link opens in a new window.'))
                {
                    return true;
                }
                else
                {
                    $(this).removeClass('active');
                    return false;
                }
            });
            
            // Page transition callback events
            $('#pageevents').
                bind('pageTransitionStart', function(e, info){
                    $(this).find('.info').append('Started transitioning ' + info.direction + '&hellip; ');
                }).
                bind('pageTransitionEnd', function(e, info){
                    $(this).find('.info').append(' finished transitioning ' + info.direction + '.<br /><br />');
                });
                
            
            // AJAX with callback event
            $('#callback').
                bind('pageTransitionEnd', function(e, info){
                    if (info.direction == 'in' && $(this).data('loaded') != 'true')
                    {
                        $(this).
                            append($('<div>Loading&hellip;</div>').
                            load('ajax.html .info', function(){
                                $(this).parent().data('loaded', 'true');
                            }));
                    }
                });

            // Orientation callback event
            $('body').bind('turn', function(e, data){
                $('#orient').html('Orientation: ' + data.orientation);
            })
        });
    </script>
    <style type="text/css" media="screen">
        body.fullscreen #home .info {
            display: none;
        }
        #about {
            padding: 100px 10px 40px;
            text-shadow: rgba(255, 255, 255, 0.3) 0px -1px 0;
            font-size: 13px;
            text-align: center;
            background: #161618;
        }
        #about p {
            margin-bottom: 8px;
        }
        #about a {
            color: #fff;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div id="home">
        <div class="toolbar">
            <h1>Great Homes</h1>
            <a class="button slideup" id="infoButton" href="#about">About</a>
        </div>
 <?php echo $content_for_layout; ?>
<?php echo $this->element('footer_mobile'); ?>
</div>


    <div id="about">        
            <p><?php echo $html->image('http://greathomes.org/sites/greathomes.org/files/zen_logo.png'); ?></p>
           	<p><b>Mobile Site Beta</b></p>
           	<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p><br /><br /><a href="#" class="grayButton goback">Close</a></p>
    </div>
    
</body>
</html>
