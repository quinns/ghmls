// $Id: transformations_ui.dragndrop.js,v 1.4 2009/06/30 09:29:30 jpetso Exp $

/**
 * Initialize drag & drop functionality for the pipeline edit form.
 */
Drupal.behaviors.transformationsUiDragAndDrop = function(context) {
  var blocksDiv = $('.transformations-operation-blocks', context);

  // This allows the user to resize the operation block area. It works like
  // Drupal's original grippie, but grippie is for textareas only.
  $('.transformations-pipeline-canvas', context).resizable({
    maxWidth : $('.transformations-pipeline-canvas', context).width(),
    minWidth : $('.transformations-pipeline-canvas', context).width(),
    minHeight : $('.transformations-pipeline-canvas', context).height(),
    handles : { s : $('#transformations-grippie', context) },
  });
  $('#transformations-grippie', context).show();

  var delayedPositions = [];

  // Set the position for each operation.
  $('.transformations-operation-block', context).each(function() {
    var operationDiv = $(this);

    var position = Drupal.settings.transformationsUiOperations[this.id];
    if (position['top'] != undefined && position['left'] != undefined) {
      operationDiv.css('top', position['top'] + 'px');
      operationDiv.css('left', position['left'] + 'px');
      operationDiv.css('position', 'absolute');
    }
    else {
      // For operations that don't yet have a position assigned, set them to
      // "position: absolute" with their current position, so that the
      // position-save callback stores the correct values.
      // However, we can't set them to "position: absolute" just yet because
      // that would modify the other blocks' positions as well. So let's store
      // the current positions first, and set them in one go afterwards.
      delayedPositions = delayedPositions.concat({
        div: operationDiv,
        top: operationDiv.position().top,
        left: operationDiv.position().left,
      });
    }
  });
  $.each(delayedPositions, function(index, item) {
    item.div.css('position', 'absolute');
    item.div.css('top', item.top + 'px');
    item.div.css('left', item.left + 'px');
  });

  // Initialize all draggable operation blocks.
  $('.transformations-operation-block', context).draggable({
    stack: {
      group: '.transformations-operation-block',
      min: 1,
    },
    containment: 'parent',
    handle: '.transformations-operation-block-header-cell',
    stop: function(event, ui) {
      var arguments = Drupal.settings.transformationsUiPipelinePersistenceId
        + '/' + $(this).attr('id') + '/' + $(this).css('z-index')
        + '/' + ui.position.top  + '/' + ui.position.left;
      $.get('?q=transformations/ajax/position-save/' + arguments);

      // If an operation block was moved, it might be required to have the
      // minHeight set in the operation block area, otherwise the user will
      // be able to drag an operation block out of it.
      // The minHeight will also be set if the lowest object was moved.
      Drupal.transformationsUiSetCanvasMinimumHeight('no-autoresize');
    },
  });

  // Initialize the draggables.
  $('.transformations-operation-output', context).draggable({
    cursor: 'move',
    helper: 'clone',
    opacity: 0.5,
  });
  $('.transformations-operation-input', context).draggable({
    cursor: 'move',
    helper: 'clone',
    opacity: 0.5,
  });

  // Initialize the droppables.
  $('.transformations-operation-output .transformations-drop-target', context).droppable({
    accept: function(draggable) {
      if (draggable == undefined) {
        return false;
      }
      if (!draggable.hasClass('transformations-operation-input')) {
        return false;
      }
      return Drupal.transformationsUiIsDroppable(
        $(this).parent().attr('id'), draggable.attr('id')
      );
    },
    hoverClass: 'transformations-operation-slot-hover',
    tolerance: 'pointer',
    drop: function(event, ui) {
      Drupal.transformationsUiConnectSlots(
        $(this).parent().attr('id'), ui.draggable.attr('id')
      );
    },
  });
  $('.transformations-operation-input .transformations-drop-target', context).droppable({
    accept: function(draggable) {
      if (draggable == undefined) {
        return false;
      }
      if (!draggable.hasClass('transformations-operation-output')) {
        return false;
      }
      return Drupal.transformationsUiIsDroppable(
        draggable.attr('id'), $(this).parent().attr('id')
      );
    },
    hoverClass: 'transformations-operation-slot-hover',
    tolerance: 'pointer',
    drop: function(event, ui) {
      Drupal.transformationsUiConnectSlots(
        ui.draggable.attr('id'), $(this).parent().attr('id')
      );
    },
  });

  Drupal.transformationsUiSetCanvasMinimumHeight('autoresize');
};

/**
 * Connect an output slot with an input one in the current pipeline,
 * and reload the page afterwards.
 */
Drupal.transformationsUiConnectSlots = function(outputSlotId, inputSlotId) {
  var source = Drupal.settings.transformationsUiSlots[outputSlotId];
  var target = Drupal.settings.transformationsUiSlots[inputSlotId];

  if (source == undefined || target == undefined) {
    return;
  }
  arguments = Drupal.settings.transformationsUiPipelinePersistenceId
    + '/' + source.entity + '/' + escape(source.key)
    + '/' + target.entity + '/' + escape(target.key);
  $.get('?q=transformations/slot-connect/' + arguments + '&ajax=1', null, function(data) {
    window.location.reload();
  });
}

/**
 * Check if a given slots may be connected.
 */
Drupal.transformationsUiIsDroppable = function(outputSlotId, inputSlotId) {
  var source = Drupal.settings.transformationsUiSlots[outputSlotId];
  var target = Drupal.settings.transformationsUiSlots[inputSlotId];

  if (source == undefined || target == undefined) {
    return false;
  }
  if (source.entity == target.entity) {
    return false; // Don't allow connecting two slots from the same entity.
  }
  if (source.entity == '2'/* pipeline parameters */ && target.entity == '4'/* pipeline outputs */) {
    return false; // Don't allow connecting pipeline parameters with pipeline outputs.
  }
  return true;
}

/**
 * Look for the bottom-most operation block and set the canvas's
 * minimum height appropriately.
 */
Drupal.transformationsUiSetCanvasMinimumHeight = function(resizeMode) {
  totalSize = 0;

  $('.transformations-operation-block').each(function() {
    var operationDiv = $(this);
    var size = operationDiv.position().top + operationDiv.height();

    if (size > totalSize) {
      totalSize = size;
    }
  });

  if (resizeMode == 'autoresize') {
    $('.transformations-pipeline-canvas').css('height', totalSize + 'px');
  }
  $('.transformations-pipeline-canvas').resizable('option', 'minHeight', totalSize);
}
