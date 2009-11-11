// $Id: jquerymenu.js,v 1.5 2009/02/04 21:54:54 aaronhawkins Exp $
Drupal.behaviors.jquerymenu = function(context) {
  $('ul.jquerymenu:not(.jquerymenu-processed)', context).addClass('jquerymenu-processed').each(function(){
    $(this).find("li.parent span.parent").click(function(){
      momma = $(this).parent();
      if ($(momma).hasClass('closed')){
        $($(this).siblings('ul').children()).hide().fadeIn('3000');
        $(momma).children('ul').slideDown('700');
        $(momma).removeClass('closed').addClass('open');
        $(this).removeClass('closed').addClass('open');
      }
      else{
        $(momma).children('ul').slideUp('700');
        $($(this).siblings('ul').children()).fadeOut('3000');
        $(momma).removeClass('open').addClass('closed');
        $(this).removeClass('open').addClass('closed');
      }
    });
  });
}