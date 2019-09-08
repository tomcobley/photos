"use strict"; // Use strict mode


function searchById(array, id) {
  // id must be top-level attribute with key "id"

  var item;
  $(array).each(function() {
    if (this.id === id) {
      // break out of each lop
      item = this;
      return false;
    }
  });
  if (item) return item;
  // else
  console.error("searchById failed. No item with given ID found in array. Given ID: " + id);
  return null;
}


function addEditPanel(contentItem, imagesArray, dividersArray) {

  var html = '<div data-order="" data-content-item-id="'+contentItem['id']+'" class="item-edit-panel">';

  var itemType = contentItem['attributes']['content-type'];
  var actualContentId = contentItem['attributes'][itemType+'-id'];

  if (itemType === 'image') {

    // get thumbnail src
    html += '<h6>Image ' + actualContentId + '</h6>';
    var itemInfo = searchById(imagesArray, actualContentId);

    html += '<img class="edit-mode-thumbnail" src="' +itemInfo['attributes']['thumbnail-src']+ '">';
    html += '<input name="image-title" value="' +itemInfo['attributes']['title']+ '" class="text edit-mode-text-input" >';


  } else if (itemType === 'divider') {

    // get divider text
    var itemInfo = searchById(dividersArray, actualContentId);
    html += '<h6>Divider ' +actualContentId+ '</h6>';
    html += '<input name="divider-city" value="' +itemInfo['attributes']['city']+ '" class="input edit-mode-text-input" >';
    html += '<input name="divider-country" value="' +itemInfo['attributes']['country']+ '" class="text edit-mode-text-input" >';

  }

  html += '<div class="order-adjuster">'
       +    '<button type="button" class="button-up">Up</button>'
       +    '<button type="button" class="button-down">Down</button>'
       +    '<input type="text" name="order">'
       +  '</div>';

  html += '</div>';

  $('#edit-panels-wrapper').append(html);

}


function updateOrderValues() {
  var order = 0;
  $(".item-edit-panel").each(function(){
    //debugger;
    $(this).find('input[name=order]').attr('value', order);
    $(this).attr('data-order', order);
    order++;
  });
}


function movePanelTo(panel, newOrderNo) {
  // copy the panel to a different location based on order no.

  var panel = $('#edit-panels-wrapper [data-order=2]');
  $('#edit-panels-wrapper').append(panel);

  //
  // $(".item-edit-panel").each(function(){
  //   //debugger;
  //   $(this).find('input[name=order]').attr('value', order);
  //   order++;
  // });
}



$(document).ready(function() {
  $.getJSON(
    "http://localhost:80/api/content-items",
    function(contentItems) {
      $.getJSON(
        "http://localhost:80/api/dividers",
        function(dividers) {
          $.getJSON(
            "http://localhost:80/api/images",
            function(images) {
              // all resources are now loaded

              $(contentItems.data).each(function(){
                addEditPanel(this, images.data, dividers.data);
              });

              updateOrderValues();
              movePanelTo("","");

            }
          );
        }
      );
    }
  );
});
