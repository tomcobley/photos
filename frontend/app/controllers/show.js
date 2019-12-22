import Ember from 'ember';
import Controller from '@ember/controller';

export default Controller.extend({
  isFirstImage: Ember.computed('model', function() {
    return this.model.orderEphemeral === 0;
  }),
  isLastImage: Ember.computed('model', function() {
    return this.model.orderEphemeral === this.get('allImages').get('length') - 1;
  }),
  currentImageIndex: Ember.computed('model', function() {
    return this.get('allImages').indexOf(this.get('model'));
  }),

  actions: {
    /**
     * Navigate to the next image.
     */
    nextImage() {
      let index = this.get('currentImageIndex');
      if (index < this.get('allImages').get('length') - 1) {
        this.transitionToRoute('show', this.get('allImages').objectAt(index + 1));
      }
    },

    /**
     * Navigate to the previous image.
     */
    previousImage() {
      let index = this.get('currentImageIndex');
      debugger;
      if (index > 0) {
        this.transitionToRoute('show', this.get('allImages').objectAt(index - 1));
      }
    },

    escape() {
      this.transitionToRoute('index');
    }
  }

});
