// $Id: transformations_ui.js,v 1.3 2009/06/17 23:43:30 jpetso Exp $

/**
 * Auto-attach for connection button highlighting behavior.
 */
Drupal.behaviors.transformationsUiConnectionHighlighting = function(context) {
  var connections = Drupal.settings.transformationsUiConnections;

  $('.transformations-operation-slot', context).mouseover(function() {
    var elementId = $(this).attr('id');

    // If the form defines any connections to highlight, process those.
    if (connections[elementId] != undefined) {
      var thisElement = $(this);
      var otherElementIds = connections[elementId];

      $.each(otherElementIds, function(index, otherElementId) {
        // The corresponding connection can easily retrieved by its name attribute.
        var otherName = otherElementId['keyType'] + '-'
          + otherElementId['entity'] + '-' + otherElementId['key'];
        var selector = '.transformations-operation-' + otherElementId['keyType']
          + '[id="' + otherName + '"]';

        // Assign the orange background color to the corresponding submit button.
        var otherElement = $(selector);
        var otherConnector = $('.transformations-operation-slot-connector', otherElement);
        var otherOriginalCss = otherConnector.attr('style');
        otherConnector.css('background-color', '#ffee99');

        // On mouseout, return to the previous state.
        thisElement.mouseout(function() {
          otherConnector.attr('style', otherOriginalCss);
        });
      });

      var thisConnector = $('.transformations-operation-slot-connector', this);
      var thisOriginalCss = thisConnector.attr('style');
      thisConnector.css('background-color', '#ffee99');

      thisElement.mouseout(function() {
        thisConnector.attr('style', thisOriginalCss);
      });
    }
  });
}
