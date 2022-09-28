<template>
  <div class="modal-vue">
    <!-- overlay -->
    <div class="overlay" v-if="showModal" @click="showModal = false"></div>
    <!-- modal -->
    <div class="okno fon oneblock modal" v-if="showModal">
      <div class="in">
        <div class="title">
          <h2>Вход</h2>
          <button class="close" @click="showModal = false"><img src="assets/img/krest.svg"></button>
        </div>
        <div class="double">

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
            <div class="form-input">
              <button class="nav_btn-employer active enter-button">Войти</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <button type="button" class="nav_btn-worker" @click="showModal = true">Стать работником</button>
    <button type="button" class="nav_btn-employer active" @click="showModal = true">Стать работодателем</button>
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

form.form-std input {
  color: #606060;
  font-size: 18px;
  border-radius: 40px;
  border: 1px solid #dbdbdb;
  height: 37px;
  text-align: center;
}
.form-input {
  margin-bottom: 15px;
}
.enter-button {
  width: 100%;
}
.nav_btn-worker:hover {
  color: #fff;
  transition: 200ms;
  background: #6D3D67;
  cursor: pointer;
}
.active:hover{
  opacity: 0.8;
  transition: 200ms;
  cursor: pointer;
}
.advantage-search_btn:hover {
  box-shadow: 1px 1px 20px 9px hsl(0deg 0% 86% / 97%);
  cursor: pointer;
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
  width: 350px;
  height: 250px;
  top: 30%;
  left: 50%;
  margin-top: -125px; /* Negative half of height. */
  margin-left: -175px; /* Negative half of width. */

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
  text-align: center;
}
/* Buttons */
.krest img {
  width: 33px;
  height: 33px;
  cursor: pointer;
}
.double {
  margin: 30px auto 0;
  display: grid;
  grid-template-columns: auto auto auto;
  align-items: center;
  width: max-content;
}
.button a {
  font-size: 20px;
  color: #fff;
}

.button {
  background: #5B5367;
  border-radius: 40px;
  width: 250px;
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
</style>