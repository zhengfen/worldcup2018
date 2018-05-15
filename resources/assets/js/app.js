
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');
import moment from 'moment';
import vueApp from './vueApp';
import router from './router';
import store from './store/index';

Vue.config.productionTip = false;

moment.relativeTimeThreshold('m', 60);
moment.relativeTimeThreshold('d', 3000);
moment.relativeTimeThreshold('h', 24);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
// import Vue components
// Vue.component('score_home_p', require('./components/ScoreHomeP.vue'));
// Vue.component('score_away_p', require('./components/ScoreAwayP.vue'));
// Vue.component('match_score_home', require('./components/MatchScoreHome.vue'));
// Vue.component('match_score_away', require('./components/MatchScoreAway.vue'));

const helloWorld = require('./helloworld').helloWorld();
console.log(helloWorld);

const app = new Vue({
    router,
    // By providing the store option to the root instance, the store will be injected into all child components of the root and will be available on them as this.$store. 
    store,
    render: (h) => h(vueApp),
}).$mount('#vue_app');