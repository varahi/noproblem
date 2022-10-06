// Import Vue components
import Courses from './components/Pages/Courses/CourseList';
import TryAndRegister from './components/TryAndRegister';
import Login from './components/Login';
import Vue from 'vue';

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

new Vue({
    el: '#login',
    render: h => h(Login)
});
