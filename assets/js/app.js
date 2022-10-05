/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */


// Pages
//import Main from './components/Pages/Main';
//import Login from './components/Pages/Login';
//import Articles from './components/Pages/Articles';
//import Blog from './components/Pages/Blog/Blog';
//import BlogDetail from './components/Pages/Blog/BlogDetail';
//import BlogCategory from './components/Pages/Blog/Category';
//import UserEdit from './components/Test/UserEdit';
//import AdvantageBoxSearch from './components/Pages/AdvantageBoxSearch';

import Courses from './components/Pages/Courses/CourseList';
import TryAndRegister from './components/Pages/TryAndRegister';
//import SignUpForm from './components/SignUpForm.vue'
import Vue from 'vue';

//Vue.component('SignUpForm', SignUpForm);

// any CSS you import will output into a single css file (app.css in this case)
import '../css/style.css';
import '../css/custom.css';

new Vue({
    el: '#app',
    components: {
        Courses,
        TryAndRegister
    }
});

/*new Vue({
    el: '#vue-app',
    render: h => h(SignUpForm),
});*/

/*new Vue({
    el: '#app',
    render: h => h(Main)
});*/

/*new Vue({
    el: '#user-edit-app',
    render: h => h(UserEdit)
});*/

/*new Vue({
    el: '#articles',
    render: h => h(Articles)
});*/

/*
new Vue({
    el: '#blog',
    render: h => h(Blog)
});
*/

/*new Vue({
    el: '#blog-category',
    render: h => h(BlogCategory)
});*/

/*new Vue({
    el: '#blog-detail',
    render: h => h(BlogDetail)
});*/

/*new Vue({
    el: '#login',
    render: h => h(Login)
});

new Vue({
    el: '#registration',
    render: h => h(Registration)
});

new Vue({
    el: '#advantage_box_search',
    render: h => h(AdvantageBoxSearch)
});*/
