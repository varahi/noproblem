<template>
  <div class="block1 content">
    <div class="cart fon search_work">
      <h2>Поиск работника или работы</h2>
      <div class="work" v-if="items && items.length">
        <!-- for -->
        <div  class="bob" v-for="item of items">
          <div class="inside" @click="showModal = true">
            <img class="lazyLoad isLoaded" :src="`uploads/${item.image}`" alt="" />
            <h3>{{ item.title }}</h3>
          </div>
        </div>
      </div>
    </div>

    <div class="login modal-vue">
      <!-- overlay -->
      <div class="overlay" v-if="showModal" @click="showModal = false"></div>
      <div class="modal" v-if="showModal">
        <div class="modal-title">
          <h2>Найти работника</h2>
          <button class="close" @click="showModal = false"><img src="/assets/img/krest.svg" alt=""></button>
        </div>
        <div class="modal-content">
          <div class="buttons">
            <div class="button" @click="goToLinkWorkers()">
              <a>Найти работника</a>
            </div>
            <div class="button" @click="goToLinkVacancies()">
              <a>Найти работу</a>
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
      showModal: false,
      items: [],
      errors: []
    }
  },

  // Fetches items when the component is created.
  created() {
    axios.get(`api/categories`)
        .then(response => {
          // JSON responses are automatically parsed.
          this.items = response.data
        })
        .catch(e => {
          this.errors.push(e)
        })
  },
  methods: {
    async goToLinkWorkers(slug) {
      window.location.href='/workers'
    },
    async goToLinkVacancies(slug) {
      window.location.href='/vacancies'
    }
  }
}
</script>


<style scoped>

/* Modal */
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
  margin-left: -350px; /* Negative half of width. */
}

.modal-content {
  text-align: center;
}

.title h2 {
  font-size: 36px;
  font-weight: lighter;
}
.modal-title h2 {
  font-size: 42px;
  text-align: center;
}

.button a {
  font-size: 20px;
  color: #fff;
}
.buttons {
  grid-template-columns: auto auto auto;
  width: max-content;
  align-items: center;
  display: grid;
  grid-gap: 25px;
}
.button {
  background: #6d3d67;
  border-radius: 40px;
  width: 200px;
  display: grid;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 10px 5px 10px 5px;
}
.button:hover{
  opacity: .8;
  transition: .2s;
}

</style>