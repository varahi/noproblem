/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

import Vue from 'vue';
import Home from './components/Home';
import Articles from './components/Articles';

// any CSS you import will output into a single css file (app.css in this case)
import '../css/app.css';

// start the Stimulus application
//import '../bootstrap';

new Vue({
    el: '#app',
    render: h => h(Home)
});

new Vue({
    el: '#articles',
    render: h => h(Articles)
});