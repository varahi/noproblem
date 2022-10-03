<template>
  <div class="article">
    <div class="content gridblock">
      <div  class="sidebar">
        <div class="grid_one_block">
          <div class="onegrid">
            <img src="assets/img/for_worker.jpg" class="lazyLoad isLoaded img_onegrid"><a href="" class="">Для работника</a>
          </div>
          <div  class="onegrid"><img src="assets/img/interesting.jpeg" class="lazyLoad isLoaded img_onegrid"> <a href="/blog/interesnoe" class="">Интересное</a>
          </div>
        </div>
      </div>
      <div>
        <div class="" v-if="items && items.length">
          <div class="grid_two_block" v-for="item of items">
            <div class="title_blog">
              <h3>{{item.title}}</h3>
              <p v-html="item.teaser">{{item.teaser}}</p>
              <a href="/blog/interesnoe/kakaya-to-statya-novaya-live" class="btn_article">Подробнее</a>
            </div>
            <div class="images">
              <img class="lazyLoad isLoaded img_article" :src="`uploads/${item.image}`" alt="" width="800" />
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
  data() {
    return {
      items: [],
      errors: [],
    }
  },

  // Fetches items when the component is created.
  created() {
    axios.get(`api/articles`)
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