<template>
  <div class="indexa modal-vue">
    <div class="main content">
      <h2>Найдите надежного работника<br> или работу самостоятельно<br> <b>без посредников</b></h2>
      <div class="btn_try" style="cursor:pointer">
        <a @click="showModal = true">Попробовать</a>
      </div>
      <div class="main_background">
      </div>
    </div>

    <!-- overlay -->
    <div class="overlay" v-if="showModal" @click="showModal = false"></div>
    <div class="overlay" v-if="showSecondModal" @click="showSecondModal = false"></div>

    <!-- first modal -->
    <div class="modal" v-if="showModal">
      <div class="modal-title">
        <h2>Выберите свою роль</h2>
        <button class="close" @click="showModal = false"><img src="assets/img/krest.svg"></button>
      </div>
      <div class="modal-content">
        <div class="button" @click="showSecondModal = true, showModal = false">
          <a>Работодатель</a>
        </div>
        <div class="button" @click="showSecondModal = true, showModal = false">
          <a>Работник</a>
        </div>
        <div class="button" @click="showSecondModal = true, showModal = false">
          <a>Покупатель</a>
        </div>
      </div>
    </div>

    <div class="indexa modal-vue registration">
      <!-- Registration modal box -->
      <div class="modal" v-if="showSecondModal">
        <div class="modal-title">
          <h2>Регистрация</h2>
          <button class="close" @click="showSecondModal = false">
            <img src="assets/img/krest.svg">
          </button>
        </div>

        <div class="modal-content">
          <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
            Поздравляем вас! Вы успешно зарегистрировались. Проверьте вашу почту и активируйте аккаунт.
          </div>

          <form method="post" class="form-std" v-on:submit.prevent="submitForm" v-else>
            <div class="form-group">
              <label for="fullname">Fullname</label>
              <input type="text" class="form-control" id="fullname" v-model="fullname" placeholder="Ваше Имя">
              <small class="form-text text-danger" v-if="validationErrors.fullname">
                {{ validationErrors.fullname }}
              </small>
            </div>
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" class="form-control" id="username" v-model="username" aria-describedby="emailHelp" placeholder="Ваш логин">
              <small class="form-text text-danger" v-if="validationErrors.username">
                {{ validationErrors.username }}
              </small>
            </div>

            <div class="form-group">
              <label for="email">Email address</label>
              <input type="email" class="form-control" id="email" v-model="email" aria-describedby="emailHelp" placeholder="Ваш E-mail">
              <small class="form-text text-danger" v-if="validationErrors.email">
                {{ validationErrors.email }}
              </small>
            </div>
            <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" v-model="password" placeholder="Пароль">
              <small class="form-text text-danger" v-if="validationErrors.password">
                {{ validationErrors.password }}
              </small>
            </div>
            <div class="btn_try btn_try_custom">
              <button type="submit" class="btn btn-success">Регистрация</button>
            </div>

          </form>

        </div>
      </div>
    </div>

  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'SignUpForm',
  data() {
    return {
      showModal: false,
      showSecondModal: false,

      msg: 'SignUpForm',

      fullname: '',
      username: '',
      email: '',
      password: '',

      validationErrors: {},
      formSubmittedSuccess: false
    }
  },
  methods: {
    submitForm: function (event) {
      event.preventDefault();

      let component = this;
      let body = {
        fullname: this.fullname,
        username: this.username,
        email: this.email,
        password: this.password,
      };

      console.log(fullname);

      axios.create().post('/sign-up-handler', body).then(function (response) {
        if(response.data.status === 400){
          component.validationErrors = response.data.errors;
        }
        else{
          component.formSubmittedSuccess = true;
          component.validationErrors = {};
        }
      }).catch(function (error) {
        let message = 'Internal server error';
        alert(message);
        console.log(message);
        console.log(error.response);
      });
    }
  }
}
</script>

<style scoped>
/* Modal */
.modal-vue .overlay {
  backdrop-filter: blur(4px);
  background: rgba(110, 99, 99, 0.34);
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.modal-vue .modal {
  z-index: 9999;
  margin: 0 auto;
  padding: 30px 30px;
  background-color: #fff;
  border-radius: 10px;
  position: fixed;
  width: auto;
  min-width: 600px;
  height: auto;
  top: 40%;
  left: 50%;
  margin-top: -75px; /* Negative half of height. */
  margin-left: -350px; /* Negative half of width. */
}

.modal-vue .close {
  position: absolute;
  top: 20px;
  right: 40px;

  width: 33px;
  height: 33px;
  cursor: pointer;
  border: none;
  background: #fff;
  font-size: 36px;
}
.title h2 {
  font-size: 36px;
  font-weight: lighter;
}
/* Buttons */
.close img {
  width: 33px;
  height: 33px;
  cursor: pointer;
}
.modal-content {
  margin: 30px auto 0;
  display: grid;
  grid-template-columns: auto auto auto;
  align-items: center;
  grid-gap: 25px;
  width: max-content;
}
.button a {
  font-size: 20px;
  color: #fff;
}

.button {
  background: #5B5367;
  border-radius: 40px;
  width: 200px;
  display: grid;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 10px 5px 10px 5px;
}
.button:hover{
  transition: 200ms;
  background: #6d3d67;
}

.modal-title h2 {
  font-size: 42px;
}

input.form-control {
  color: #606060;
  font-size: 18px;
  border-radius: 40px;
  border: 1px solid #dbdbdb;
  height: auto;
  padding: 12px 5px 12px 22px;
}

.btn_try_custom {
  text-align: center;
}
.btn_try button {
  font-size: 18px;
  padding: 20px 94px;
  color: #fff;
  background: #6d3d67;
  background: var(--violet);
  border-radius: 40px;
}

form.form-std {
  width: 400px;
}
input.form-control {
  width: 400px;
  margin-bottom: 20px;
}
.form-group label {
  display: none;
}

</style>