<template>
  <div class="article">
    <div class="content gridblock">
      <Category />
      <div>
        <div class="" v-if="items && items.length">
          <div class="grid_two_block" v-for="item of items">
            <div class="title_blog">
              <h3>{{item.title}}</h3>
              <p v-html="item.teaser">{{item.teaser}}</p>
              <a v-bind:href="'/blog/detail/'+ item.slug" class="btn_article">Подробнее</a>

              <!--
              <router-link
                  to="/home"
                  class="btn_article">
                  <a class="btn_article">Home</a>
              </router-link>-->
              <!-- <router-link :to="{ name: 'details', params: { projectId: project.id }}">{{project.name}}</router-link>-->

            </div>
            <div class="images">
              <img class="lazyLoad isLoaded img_article" :src="`uploads/${item.image}`" alt={item.title} width="800" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';
import Category from "./Category";

export default {
  components: {
    Category
  },
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