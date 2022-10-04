<template>
  <div class="content" v-if="items && items.length">
    <h1 class="main_course">Курсы от NOPROBLEM</h1>
    <div  class="blockarticle">
      <div class="grid_course"  v-for="item of items">
        <div class="images">
          <img class="lazyLoad isLoaded img_course" :src="`uploads/${item.image}`" />
        </div>
        <div class="title_course">
          <h3>{{item.title}}</h3>
          <p v-html="item.teaser">{{item.teaser}}</p>
          <div  class="blockprice">
            <a v-bind:href="'/course/detail/'+ item.slug" class="btn_article">Подробнее</a>
            <div class="price_course" v-if="item.price">
              <p>Стоимость:<span > {{item.price}} руб.</span></p>
            </div>
            <div class="price_course" v-else>
              <p>Стоимость:<span > Бесплатно</span></p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
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
    axios.get(`api/courses`)
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