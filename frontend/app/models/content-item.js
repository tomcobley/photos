import DS from 'ember-data';

export default DS.Model.extend({
  contentType: DS.attr(),
  position: DS.attr(),
  dividerId: DS.attr(),
  imageId: DS.attr(),
});
