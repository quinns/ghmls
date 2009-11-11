// $Id: jquerymenu_no_animation.js,v 1.2 2009/02/04 21:54:54 aaronhawkins Exp $
Drupal.behaviors.jquerymenu = function(context) {
  $('ul.jquerymenu:not(.jquerymenu-processed)', context).addClass('jquerymenu-processed').each(function(){
    $(this).find("li.parent span.parent").click(function(){
      momma = $(this).parent();
      if ($(momma).hasClass('closed')){
        $(momma).removeClass('closed').addClass('open');
        $(this).removeClass('closed').addClass('open');
      }
      else{
        $(momma).removeClass('open').addClass('closed');
        $(this).removeClass('open').addClass('closed');
      }
    });
  });
}