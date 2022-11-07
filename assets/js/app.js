// Import Vue components
import Courses from './components/Pages/Courses/CourseList';
import Announcements from './components/User/Announcements';
import TryAndRegister from './components/TryAndRegister';
import TryAndRegisterModal from './components/TryAndRegisterModal';
import Register from './components/Register';
import MenuAuth from './components/MenuAuth';
import Login from './components/Login';
import Vue from 'vue';

// any CSS you import will output into a single css file (app.css in this case)
//import '../css/style.css';
//import '../css/custom.css';

new Vue({
    el: '#app',
    components: {
        Courses,
        TryAndRegister,
        Announcements
    }
});

new Vue({
    el: '#login',
    render: h => h(Login)
});

new Vue({
    el: '#menu-auth',
    render: h => h(MenuAuth)
});

new Vue({
    el: '#menu-registration',
    render: h => h(Register)
});

new Vue({
    el: '#try-and-register-modal',
    render: h => h(TryAndRegisterModal)
});
