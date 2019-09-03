import Route from '@ember/routing/route';

export default Route.extend({
  model() {

    // Declare empty array for the page content
    let content = [];

    // Allow access to this.store inside blocks below
    let _this = this;

    this.store.findAll('content-item').then( function(contentItems) {

      contentItems.sortBy('position').forEach( function(contentItem, index) {

        content[index] = {};
        content[index].position = contentItem.get('position');
        content[index].contentType = contentItem.get('contentType');

        if (contentItem.get('contentType') === 'image') {
          _this.store.findRecord('image', contentItem.get('imageId')).then( function(image) {
            content[index].imageId = contentItem.get('imageId');
            content[index].thumbnailSrc = image.get('thumbnailSrc');
            content[index].altText = image.get('title');
          });

        } else if (contentItem.get('contentType') === 'divider') {
          _this.store.findRecord('divider', contentItem.get('dividerId')).then( function(divider) {
            content[index].dividerId = contentItem.get('dividerId');
            content[index].dividerCity = divider.get('city');
            content[index].dividerCountry = divider.get('country');
          });

        } else {
          // eslint-disable-next-line no-console
          console.warning("Unknown content type recieved: " + contentItem.get('contentType'));
        }

      });
    });

    return content;
  }
});
