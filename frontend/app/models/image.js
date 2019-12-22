import DS from 'ember-data';

export default DS.Model.extend({
  title: DS.attr(),
  coords: DS.attr(),
  src: DS.attr(),
  thumbnailSrc: DS.attr(),
  orderEphemeral: DS.attr()
});
