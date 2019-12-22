import Ember from 'ember';
import { EKMixin, keyDown, getKeyCode } from 'ember-keyboard';

export default Ember.Component.extend(EKMixin, {

  activateKeyboard: Ember.on('init', function() {
    this.set('keyboardActivated', true);
  }),

  nextItem: Ember.on(keyDown('ArrowRight'), function() {
    this.sendAction('next');
  }),
  previousItem: Ember.on(keyDown('ArrowLeft'), function() {
    this.sendAction('previous');
  }),
  escapeItem: Ember.on(keyDown('Escape'), function() {
    this.sendAction('escape');
  }),

});
