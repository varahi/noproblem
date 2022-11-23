<template>
  <div class="login modal-vue">
    <!-- overlay -->
    <div class="overlay" v-if="showModal" @click="showModal = false"></div>
    <div class="modal" v-if="showModal">
      <div class="modal-title">
        <h2>Выберите город</h2>
        <p v-if="alert" class="alert-danger">{{ alertMessage }}</p>
        <button class="close" @click="showModal = false"><img src="/assets/img/krest.svg" alt=""></button>
      </div>

      <div class="modal-content">
        <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
          Set City
        </div>

<!--        <form class="form-std" autocomplete="off" method="post" v-on:submit.prevent="setCity" v-else>

        </form>-->
      </div>
    </div>

    <a href="#" @click="showModal = true">Ваш город / <span>Город не выбран</span></a>
  </div>
</template>


<script>
  import axios from 'axios';
  export default {
    name: 'SetCity',
    data() {
      return {
        showModal: false,
        msg: '',
        email: '',
        password: '',
        validationErrors: {},
        formSubmittedSuccess: false,
        alert: false,
        alertMessage: ''
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

        axios.create().post('/api/set-city', body).then(function (response) {
          if(response.data.status === 400){
            component.validationErrors = response.data.errors;
          }
          else{
            //window.location.href='/lk'
          }
        })
            .catch(error => {
              this.alert = true;
              this.alertMessage = 'Что -то пошло не так, попробуйте позже';
            });
      }
    }
  }
</script>