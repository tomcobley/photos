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

  if (contentItem['attributes']['hidden'] === "1") {
    var hiddenString = "item-hidden";
  } else {
    var hiddenString = "";
  }

  var html = '<div data-content-item-id="'+contentItem['id']+'" class="item-edit-panel '+hiddenString+'">';

  var itemType = contentItem['attributes']['content-type'];
  var actualContentId = contentItem['attributes'][itemType+'-id'];

  if (itemType === 'image') {

    // get thumbnail src
    //html += '<h6>Image ' + actualContentId + '</h6>';
    var itemInfo = searchById(imagesArray, actualContentId);

    html +=   '<img class="edit-mode-thumbnail" src="' +itemInfo['attributes']['thumbnail-src']+ '">';
    html +=   '<input name="image-title" value="' +itemInfo['attributes']['title']+ '" class="text edit-mode-text-input form-control" >';

  } else if (itemType === 'divider') {

    // get divider text
    var itemInfo = searchById(dividersArray, actualContentId);
    //html += '<h6>Divider ' +actualContentId+ '</h6>';
    html += '<input name="divider-city" value="' +itemInfo['attributes']['city']+ '" class="input edit-mode-text-input form-control" >';
    html += '<input name="divider-country" value="' +itemInfo['attributes']['country']+ '" class="text edit-mode-text-input form-control" >';

  }

  html += toolbarContent(itemType, contentItem['attributes']['timestamp']);

  html += '</div>';

  $('#edit-panels-wrapper').append(html);

}


function toolbarContent(itemType, timestampValue) {

  var html = '<div class="toolbar d-flex justify-content-around">';

  if (itemType === 'divider') {
    html +=   '<button type="button" class="button-increase-position btn btn-light">Up</button>'
         +    '<button type="button" class="button-reduce-position btn btn-light">Down</button>'
         +    '<input type="text" class="form-control edit-mode-text-input" name="timestamp" value='+timestampValue+'>';
  } else if (itemType === 'image') {
    html +=   '<input readonly type="text" class="form-control edit-mode-text-input" name="timestamp" value='+timestampValue+'>';
  }

  html +=   '<button type="button" class="button-toggle-hidden btn btn-light"></button>';
  html += '</div>';

  return html;

}




function addInsertDividerButton() {

  var html = '<div class="insert-divider"><span class="icon">+</span></div>';

  $('#edit-panels-wrapper').append(html);
}


function updateToApi(request_type, target_id, target_attribute="", target_attribute_value="", reRenderPage=false) {
  $.post(
    'update.php',
    {
      request_type: request_type,
      target_id: target_id,
      target_attribute: target_attribute,
      target_attribute_value: target_attribute_value,
      auth_token: $('#auth_token').val()
    },
    function( response ) {
      processResponse(response);
      if (reRenderPage) {
        renderPage();
      }
    }

  ).fail(function(){
    alert("Error processing request. Check console and PHP logs.")
  });
}


function insertToApi(data, reRenderPage=false) {
  $.post(
    'update.php',
    {
      request_type: 'insert',
      data: JSON.stringify(data),
      auth_token: $('#auth_token').val()
    },
    function( response ) {
      processResponse(response);
      if (reRenderPage) {
        renderPage();
      }
    }

  ).fail(function(){
    alert("Error processing request. Check console and PHP logs.")
  });
}


function processResponse(response) {
  if (response) {
    var responseJson = JSON.parse(response);
    if (responseJson.errors) {
      if (responseJson.errors.redirect) {
        window.location.replace(responseJson.errors.redirect);
        throw new Error("Page redirecting!");
      }
      alert(responseJson.errors.status + " error occured. Check console and php logs.");
      console.error(responseJson.errors.status + " error occured: "+ responseJson.errors.title);
    }
  }

}


function addEventListeners() {

  $('input[name=timestamp]').change(function(){

    updateToApi(
      'update', $(this).parent().parent().data('content-item-id'),
      'timestamp', $(this).val(), true
    );

  });

  $('.button-increase-position').click( function() {
    movePanel( $(this).parent().parent(), 'up');
  });
  $('.button-reduce-position').click( function() {
    movePanel( $(this).parent().parent(), 'down');
  });

  $('.button-toggle-hidden').click(function() {
    var containingDiv = $(this).parent().parent();
    var hidden = containingDiv.hasClass('item-hidden');
    if (hidden) {
      var action = 'show';
    } else {
      var action = 'hide';
    }
    updateToApi(action, containingDiv.data('content-item-id'));
    containingDiv.toggleClass('item-hidden');
  });

  $('input[name=divider-city]').change(function(){
    updateToApi(
      'update', $(this).parent().data('content-item-id'),
      'city', $(this).val(), false
    );
  });

  $('input[name=divider-country]').change(function(){
    updateToApi(
      'update', $(this).parent().data('content-item-id'),
      'country', $(this).val(), false
    );
  });

  $('input[name=image-title]').change(function(){
    updateToApi(
      'update', $(this).parent().data('content-item-id'),
      'title', $(this).val(), false
    );
  });

  $('.insert-divider').click(function(){
    insertNewDivider( $(this) );
  });

}


function renderPage() {
  // clear page to begin with
  $('#edit-panels-wrapper').empty();

  $.getJSON(
    "http://localhost:80/api/content-items?includeHidden=1",
    function(contentItems) {
      $.getJSON(
        "http://localhost:80/api/dividers",
        function(dividers) {
          $.getJSON(
            "http://localhost:80/api/images",
            function(images) {
              // all resources are now loaded

              // note that contentItems is ordered by `timestamp` (from api)
              addInsertDividerButton();
              $(contentItems.data).each(function(){
                addEditPanel(this, images.data, dividers.data);
                addInsertDividerButton();
              });

              addEventListeners();

              // prevent page moving back to top when page is rerendered
              var divHeight = $('#edit-panels-wrapper').height();
              $('#edit-panels-wrapper').css('min-height', divHeight);


            }
          );
        }
      );
    }
  );
}


function movePanel(panel, direction) {

  if (direction === 'up') {
    var newTimestamp = Number( panel.prev().prev().find('input[name=timestamp]').val() ) - 1;
  } else {
    var newTimestamp = Number( panel.next().next().find('input[name=timestamp]').val() ) + 1;
  }
  // fire event listener
  panel.find('input[name=timestamp]').val(newTimestamp).change();

}


function insertNewDivider( locationElement ) {
  // make request to backend then refresh page
  var timestamp = Number( locationElement.next().find('input[name=timestamp]').val() ) - 1;
  insertToApi( {elementType: 'divider', timestamp: timestamp}, true );

}



$(document).ready(function() {
  renderPage();


});
