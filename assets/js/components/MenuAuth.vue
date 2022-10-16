<template>
  <div class="login modal-vue">
    <!-- overlay -->
    <div class="overlay" v-if="showModal" @click="showModal = false"></div>

    <div class="modal" v-if="showModal">
      <div class="modal-title">
        <h2>Вход</h2>
        <button class="close" @click="showModal = false"><img src="/assets/img/krest.svg"></button>
      </div>

      <div class="modal-content">
        <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
          Вы успешно вошли в систему
        </div>

        <form class="form-std" autocomplete="off" method="post" v-on:submit.prevent="performLogin" v-else>
          <div class="form-input">
            <input type="text" class="form-control" id="inputEmail" v-model="email" placeholder="E-mail">
            <small class="form-text text-danger" v-if="validationErrors.email">
              {{ validationErrors.email }}
            </small>
          </div>
          <div class="form-input">
            <input type="password" class="form-control" id="password" v-model="password" placeholder="Password">
            <small class="form-text text-danger" v-if="validationErrors.password">
              {{ validationErrors.password }}
            </small>
          </div>
          <div class="forgot-password"><a href="/reset-password">Забыли пароль?</a></div>
          <div class="btn_try btn_try_custom">
            <button type="submit" class="btn btn-success">Войти</button>
          </div>
        </form>
      </div>
    </div>

    <a href="#" @click="showModal = true">Авторизация</a>
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: 'LoginForm',
    data() {
      return {
        showModal: false,
        msg: 'LoginForm',

        email: '',
        password: '',

        validationErrors: {},
        formSubmittedSuccess: false
      }
    },

    methods: {
      performLogin: function (event) {
        event.preventDefault();
        let component = this;
        let body = {
          email: this.email,
          password: this.password,
        };

        axios.create().post('/api/login', body).then(function (response) {
          if(response.data.status === 400){
            component.validationErrors = response.data.errors;
          }
          else{
            window.location.href='/lk'
            //component.formSubmittedSuccess = true;
            //component.validationErrors = {};
          }
        }).catch(function (error) {
          let message = 'Неверная пара логин/пароль';
          alert(message);
          console.log(message);
          console.log(error.response);
        });
      }
    }
  }
</script>


<style scoped>
.login{
  display: flex;
  max-width: 450px;
  column-gap: 25px;
}

.forgot-password {
  text-align: center;
  margin: 10px 0;
  font-size: 20px;
}
.forgot-password a {
  text-decoration: underline;
}
.forgot-password a:hover {
  text-decoration: none;
}

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
  min-width: 350px;
  height: auto;
  top: 40%;
  left: 50%;
  margin-top: -75px; /* Negative half of height. */
  margin-left: -200px; /* Negative half of width. */
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
/*  grid-gap: 25px;*/
  width: max-content;
}
.button a {
  font-size: 20px;
  color: #fff;
}

.button {
  background: #6d3d67;
  border-radius: 40px;
  width: 200px;
  display: grid;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 10px 5px 10px 5px;
}
.button:hover{
  opacity: .8;
  transition: .2s;
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
  width: auto;
}
input.form-control {
  width: 400px;
  margin-bottom: 20px;
}
.form-group label {
  display: none;
}
</style>