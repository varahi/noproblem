<template>
  <div class="advantage_box-search">
    <div class="inner">
      <h3 class="advantage-search_header">Поиск работника или работы</h3>
      <div class="advantage-search_btn-box">
        <button type="button" class="advantage-search_btn" v-for="item of items">
            <img class="advantage-search_btn-img" :src="`uploads/${item.image}`" width="180" height="180" alt="check"/>
            <span class="advantage-search_btn-text">{{item.title}}</span>
        </button>
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
      errors: []
    }
  },
  created() {
    axios.get(`api/categories`)
        .then(response => {
          this.items = response.data
        })
        .catch(e => {
          this.errors.push(e)
        })
    }
}
</script>