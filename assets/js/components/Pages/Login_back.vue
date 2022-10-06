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

<!--    <button type="button" class="nav_btn-worker" @click="showModal = true">Стать работником</button>
    <button type="button" class="nav_btn-employer active" @click="showModal = true">Стать работодателем</button>-->

    <div class="btn_worker" style="cursor: pointer;"  @click="showModal = true"><a>Стать работником</a></div>
    <div class="btn_employer"  style="cursor: pointer;"  @click="showModal = true"><a>Стать работодателем</a></div>
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

</style>