<template>
  <div class="content">
    <div class="tit_account">
      <img src="assets/img/blockicon.svg">
      <h4>Мои объявления</h4>
    </div>
    <div class="owl-carousel owl-theme" id="slider_one">
      <div  v-for="item of items">
        <div class="box_account">
          <div class="box_account_content">
            <h4>{{ item.name }}</h4>
            <div class="box_person_information">
              <div class="person_information_city">
                <div class="city_icon">
                  <img class="lazyLoad isLoaded" :src="`uploads/${item.image}`" />
                  <p class="city_box_account">{{ item.city }}, {{ item.district }}</p>
                </div>
                <p>Начать: <i class="data_box_account">{{ job.startDate }}</i></p>
                <p>Опыт работы: </p>
              </div>
              <div class="person_information_pay">
                <p v-if="item.payment">Оплата: <i class="data_box_account">{{ item.payment }}</i></p>
                <p v-if="item.age">{{ 'Age'|trans }}: <i class="data_box_account">{{ job.age }}</i></p>
                <p v-if="item.education">{{ 'Education'|trans }}: <i class="data_box_account">{% include 'partials/misc/_education.html.twig' %}</i></p>
              </div>
            </div>
            <div class="person_description">
              <p>{{ item.description }}</p>
            </div>
            <div class="box_vacancies">
              <div class="box_logo">
                <img src="assets/img/ava_person_account.png">
              </div>
              <div class="name_vacancies">
                <p class="name_vacancies_text">Вакансия от частного лица</p>
                <p>{{ item.owner }}</p>
              </div>
            </div>
            <div class="btn_box_account">
              <button class="btn_box_account_one">Подробнее</button>
              <button class="btn_box_account_two"><a href="">Редактировать</a></button>
            </div>
          </div>
        </div>
      </div>
    </div>

<!--    <p v-else>У вас пока нет объявлений</p>-->
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: "courses",
  components: {
  },
  data() {
    return {
      items: [],
      errors: [],
    }
  },

  // Fetches items when the component is created.
  created() {
    axios.get(`api/announcements`)
        .then(response => {
          // JSON responses are automatically parsed.
          this.items = response.data
        })
        .catch(e => {
          this.errors.push(e)
        })
  }
}
</script>