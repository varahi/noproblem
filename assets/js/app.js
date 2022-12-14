
// Import Vue components
import Courses from './components/Pages/Courses/CourseList';
//import Announcements from './components/User/Announcements';
//import TryAndRegister from './components/TryAndRegister';

//import TryAndRegisterModal from './components/TryAndRegisterModal';
import Register from './components/Register';
import MenuAuth from './components/MenuAuth';
import SetCity from './components/SetCity';
import Login from './components/Login';
import SearchWork from './components/SearchWork';
import Vue from 'vue';

import VeeValidate, { Validator } from 'vee-validate';
import ru from 'vee-validate/dist/locale/ru';

Validator.localize('ru', ru);
Vue.use(VeeValidate, {
    locale: 'ru',
});

// any CSS you import will output into a single css file (app.css in this case)
//import '../css/style.css';
//import '../css/custom.css';

new Vue({
    el: '#app',
    components: {
        Courses,
        //TryAndRegister,
        //Announcements
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
    el: '#menu-set-city',
    data: {
        trans: null
    },
    mounted: function() {
        this.trans = this.$el.getAttribute('data-trans');
    },
    render: h => h(SetCity)
});

new Vue({
    el: '#menu-registration',
    render: h => h(Register)
});

// Turned off
//new Vue({
//    el: '#try-and-register-modal',
//    render: h => h(TryAndRegisterModal)
//});

new Vue({
    el: '#search-work',
    render: h => h(SearchWork)
});
