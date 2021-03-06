/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */
require('./bootstrap');

window.Vue = require('vue');

export const EventBus = new Vue();

window.SvgVue = require('svg-vue');
Vue.use(SvgVue);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('sudoku-game', require('./components/SudokuGame.vue').default);
Vue.component('sudoku-input', require('./components/SudokuInput.vue').default);
Vue.component('sudoku-grid', require('./components/SudokuGrid.vue').default);
Vue.component('sudoku-cell', require('./components/SudokuCell.vue').default);
Vue.component('control-pad', require('./components/ControlPad.vue').default);
Vue.component('navigation-bar', require('./components/NavBar.vue').default);

const app = new Vue({
    el: '#app'
});
