export default function() {

  this.namespace = '/api';

  let contentItems = [{
      type: 'content-items',
      id: '1',
      attributes: {
        'content-type': 'divider',
        'position': 1,
        'divider-id': '1',
      },
    }, {
      type: 'content-items',
      id: '2',
      attributes: {
        'content-type': 'image',
        'position': 2,
        'image-id': '4',
      },
    }, {
      type: 'content-items',
      id: '3',
      attributes: {
        'content-type': 'divider',
        'position': 3,
        'divider-id': '2',
      },
    }, {
      type: 'content-items',
      id: '4',
      attributes: {
        'content-type': 'image',
        'position': 4,
        'image-id': '1',
      },
    }, {
      type: 'content-items',
      id: '5',
      attributes: {
        'content-type': 'image',
        'position': 5,
        'image-id': '2',
      },
    }, {
      type: 'content-items',
      id: '6',
      attributes: {
        'content-type': 'image',
        'position': 6,
        'image-id': '3',
      },
    }, {
      type: 'content-items',
      id: '7',
      attributes: {
        'content-type': 'image',
        'position': 7,
        'image-id': '4',
      },
    }, {
      type: 'content-items',
      id: '7',
      attributes: {
        'content-type': 'divider',
        'position': 8,
        'divider-id': '3',
      },
    }, {
      type: 'content-items',
      id: '9',
      attributes: {
        'content-type': 'image',
        'position': 9,
        'image-id': '5',
      },
    },
  ];

  let images = [{
      type: 'images',
      id: '1',
      attributes: {
        title: 'Comuna 13 View',
        coords: '3.2423, 2.2452',
        src: '/img/IMG_20190331_125114.jpg',
        'thumbnail-src': '/img/thumbnails/IMG_20190331_125114.jpg',
      }
    }, {
      type: 'images',
      id: '2',
      attributes: {
        title: 'Comuna 13 Graffiti',
        coords: '3.2423, 2.2452',
        src: '/img/IMG_20190331_124306.jpg',
        'thumbnail-src': '/img/thumbnails/IMG_20190331_124306.jpg',
      }
    }, {
      type: 'images',
      id: '3',
      attributes: {
        title: 'Comuna 13 Stairs',
        coords: '3.2477, 2.7272',
        src: '/img/IMG_20190331_123257.jpg',
        'thumbnail-src': '/img/thumbnails/IMG_20190331_123257.jpg',
      }
    },{
      type: 'images',
      id: '4',
      attributes: {
        title: 'Cartagena View',
        coords: '1.3453, -1.9456',
        src: '/img/IMG_20190321_174509.jpg',
        'thumbnail-src': '/img/thumbnails/IMG_20190321_174509.jpg',
      }
    }, {
      type: 'images',
      id: '5',
      attributes: {
        title: 'Gold Museum',
        coords: '1.3123, -9.3423',
        src: '/img/IMG_20190416_113711.jpg',
        'thumbnail-src': '/img/thumbnails/IMG_20190416_113711.jpg',
      }
    }
  ];

  let dividers = [{
      type: 'dividers',
      id: '1',
      attributes: {
        city: 'Cartagena',
        country: 'Colombia',
      }
    }, {
      type: 'dividers',
      id: '2',
      attributes: {
        city: 'Medellin',
        country: 'Colombia',
      }
    }, {
      type: 'dividers',
      id: '3',
      attributes: {
        city: 'Bogota',
        country: 'Colombia',
      }
    }
  ];

  // Return all content items when requested
  this.get('/content-items', function () {
    return { data: contentItems };
  });

  // Find and return an individual content item when requested
  this.get('/content-items/:id', function (db, request) {
    return { data: contentItems.find((contentItem) => request.params.id === contentItem.id) };
  });

  // Return all images when requested
  this.get('/images', function () {
    return { data: images };
  });

  // Find and return an individual image from our image list above when requested
  this.get('/images/:id', function (db, request) {
    return { data: images.find((image) => request.params.id === image.id) };
  });

  // Return all dividers when requested
  this.get('/dividers', function () {
    return { data: dividers };
  });

  // Find and return an individual divider when requested
  this.get('/dividers/:id', function (db, request) {
    return { data: dividers.find((divider) => request.params.id === divider.id) };
  });





  // These comments are here to help you get started. Feel free to delete them.

  /*
    Config (with defaults).

    Note: these only affect routes defined *after* them!
  */

  // this.urlPrefix = '';    // make this `http://localhost:8080`, for example, if your API is on a different server
  // this.namespace = '';    // make this `/api`, for example, if your API is namespaced
  // this.timing = 400;      // delay for each request, automatically set to 0 during testing

  /*
    Shorthand cheatsheet:

    this.get('/posts');
    this.post('/posts');
    this.get('/posts/:id');
    this.put('/posts/:id'); // or this.patch
    this.del('/posts/:id');

    https://www.ember-cli-mirage.com/docs/route-handlers/shorthands
  */
}
