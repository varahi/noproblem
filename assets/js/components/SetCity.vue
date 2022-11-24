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

        <ul v-if="errors && errors.length">
          <li v-for="error of errors">
            {{error.message}}
          </li>
        </ul>

        <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
          <p class="alert-success">Город выбран</p>
        </div>
        <form class="form-std select-form" autocomplete="off" method="post" v-on:submit.prevent="setCity" v-else>
          <select v-model="selectedCity">
            <option v-for="model in items" :key="model.id" :value="model.id">
              {{ model.title }}
            </option>
          </select>
          <div class="btn_try btn_try_custom">
            <button type="submit" class="btn btn-success">Выбрать город</button>
          </div>
        </form>
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
      items: [],
      errors: [],
      showModal: false,
      msg: '',
      id: '',
      validationErrors: {},
      formSubmittedSuccess: false,
      alert: false,
      alertMessage: ''
    }
  },

  created() {
    axios.get(`api/get-cities`)
        .then(response => {
          this.items = response.data
        })
        .catch(e => {
          this.errors.push(e)
        })
  },

  methods: {
    setCity: function (event) {
      event.preventDefault();
      let component = this;
      let body = {
        id: this.selectedCity,
      };

      axios.create().post('/api/set-city', body).then(response => {
        if(response.data.status === 400){
          component.validationErrors = response.data.errors;
        }
        else{
          console.log(response.data);
          //this.items = response.data;
          component.formSubmittedSuccess = true;
          component.validationErrors = {};
        }
      })
          .catch(error => {
            //console.log(error.response.data);
            this.alert = true;
            this.alertMessage = 'Что - то пошло не так, попробуйте позже';
          });
    }
  }
}
</script>


<style scoped>
.select-form select {
  width: 100%;
}
.select-form input, .select-form textarea, .select-form select {
  color: #606060;
  font-size: 18px;
  border-radius: 40px;
  border: 1px solid #dbdbdb;
  height: auto;
  padding: 12px 5px 12px 22px;
  width: 95%;
  margin-bottom: 30px;
}
</style>