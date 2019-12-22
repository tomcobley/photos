import Route from '@ember/routing/route';

export default Route.extend({
  model(params) {
    return this.store.findRecord('image', params.image_id);
  },

  setupController(controller, model) {
    this._super(controller, model);
    //controller.set('memberName', model.get('fullName'));

    this.store.findAll('image').then(function(images) {
      controller.set('allImages', images);
    });

  }
});
