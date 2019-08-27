import Route from '@ember/routing/route';
import { hash } from 'rsvp';

export default Route.extend({

  model() {
    // load all required data into store from the backend (for faster loading)
    return hash({
      contentItems: this.store.findAll('content-item'),
      images: this.store.findAll('image'),
      dividers: this.store.findAll('divider'),
    });
  }

});
