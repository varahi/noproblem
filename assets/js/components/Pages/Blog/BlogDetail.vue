<template>
  <div class="article">
    <div class="content gridblock">
      <Category />
      <div>
        <div class="new_blockarticle">
          <h1 >{{ item.name }}</h1>
<!--          <img src="{{ asset('uploads/' ~ article.image) }}" class="lazyLoad isLoaded img_new_article" alt="" />-->
          <article v-html="item.bodytext">{{item.bodytext}}</article>
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
      item: [],
      errors: [],
    }
  },

  // Fetches items when the component is created.
  created() {
    axios.get(`api/article-${item.id}`)
        .then(response => {
          // JSON responses are automatically parsed.
          this.item = response.data
        })
        .catch(e => {
          this.errors.push(e)
        })
  }
}
</script>