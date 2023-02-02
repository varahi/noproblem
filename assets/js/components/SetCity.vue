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

<!--        <ul v-if="errors && errors.length">
          <li v-for="error of errors">
            {{error.message}}
          </li>
        </ul>-->

        <!-- <div class="alert alert-success" role="alert" v-if="formSubmittedSuccess">
          <p class="alert-success">Город выбран</p>
        </div> -->
        <form class="form-std select-form" autocomplete="off" method="post" v-on:submit.prevent="setCity">
          <select v-model="selectedCity" class="city">
            <option disabled v-if="cityName && cityName" value="">{{ cityName }}</option>
            <option v-for="model in items"
                    :key="model.id"
                    :value="model.title">{{ model.title }}
            </option>
<!--            <option v-for="model in items"
                    :key="model.id"
                    :value="model.title"
                    :selected="cityName === model.title">{{ model.title }}
            </option>-->
          </select>
          <div class="btn_try btn_try_custom">
            <button type="submit" class="btn btn-success">Выбрать город</button>
          </div>
        </form>
      </div>
    </div>

    <a href="#" @click="showModal = true" v-if="selectedCity && selectedCity.length">Ваш город / <span>{{ selectedCity }}</span></a>
    <a href="#" @click="showModal = true" v-else>Ваш город / <span>{{ cityName }}</span></a>
  </div>
</template>

<script>
import axios from 'axios';
export default {
  name: 'SetCity',
  data() {
    return {
      items: [],
      //errors: [],
      showModal: false,
      cname: '',
      validationErrors: {},
      formSubmittedSuccess: false,
      alert: false,
      alertMessage: '',
      selectedCity: '',
      cityName,
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
        cname: this.selectedCity,
      };

      axios.create().post('/api/set-city', body).then(response => {
        if(response.data.status === 400){
          component.validationErrors = response.data.errors;
        }
        else{
          component.formSubmittedSuccess = true;
          component.showModal = false;
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
  min-width: 400px;
  height: auto;
  top: 40%;
  left: 50%;
  margin-top: -75px; /* Negative half of height. */
  margin-left: -200px; /* Negative half of width. */
}

.select-form select {
  width: 100%;
}
.select-form input, .select-form textarea, .select-form select {
  color: #606060;
  font-size: 18px;
  border-radius: 10px;
  border: 1px solid #dbdbdb;
  height: auto;
  padding: 12px 5px 12px 22px;
  width: 95%;
  margin-bottom: 30px;
}
select.city {
  width: 100%;
}
</style>