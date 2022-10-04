<template>
  <div  class="sidebar">
    <div class="grid_one_block" v-if="items && items.length">
      <div class="onegrid" v-for="item of items">
        <img class="lazyLoad isLoaded img_onegrid" :src="`uploads/${item.image}`" alt={item.title} width="800" />
        <a href="" class="">{{item.title}}</a>
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
    axios.get(`api/blog-categories`)
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