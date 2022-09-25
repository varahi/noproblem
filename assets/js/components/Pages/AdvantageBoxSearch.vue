<template>
  <div class="modal-vue">
    <!-- overlay -->
    <div class="overlay" v-if="showModal" @click="showModal = false"></div>
    <!-- modal -->
    <div class="okno fon oneblock modal" v-if="showModal">
      <div class="in">
        <div class="title">
          <h2>Чем мы можем вам помочь?</h2>
          <button class="close" @click="showModal = false"><img src="assets/img/krest.svg"></button>
        </div>
        <div class="double">
          <div class="button">
            <a>Ищу работу </a>
          </div>
          <div class="button">
            <a>Ищу работника</a>
          </div>
        </div>
      </div>
    </div>

    <div class="advantage_box-search">
      <div class="inner">
        <h3 class="advantage-search_header">Поиск работника или работы</h3>
        <div class="advantage-search_btn-box">
          <div v-for="item of items">
            <button type="button" class="advantage-search_btn" @click="showModal = true">
              <img class="advantage-search_btn-img" :src="`uploads/${item.image}`" width="180" height="180" alt="check" />
              <span class="advantage-search_btn-text">{{item.title}}</span>
            </button>
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

<style scoped>
.advantage-search_btn:hover {
  box-shadow: 1px 1px 20px 9px hsl(0deg 0% 86% / 97%);
  cursor: pointer;
}

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
  width: 600px;
  height: 120px;
  top: 50%;
  left: 50%;
  margin-top: -60px; /* Negative half of height. */
  margin-left: -300px; /* Negative half of width. */

}

.modal-vue .close {
  position: absolute;
  top: 20px;
  right: 40px;

  width: 33px;
  height: 33px;
  cursor: pointer;
  border: none;
  background: #fff;
  font-size: 36px;
}
.title h2 {
  font-size: 36px;
  font-weight: lighter;
  text-align: center;
}
/* Buttons */
.krest img {
  width: 33px;
  height: 33px;
  cursor: pointer;
}
.double {
  margin: 30px auto 0;
  display: grid;
  grid-template-columns: auto auto auto;
  align-items: center;
  grid-gap: 25px;
  width: max-content;
}
.button a {
  font-size: 20px;
  color: #fff;
}

.button {
  background: #5B5367;
  border-radius: 40px;
  width: 250px;
  display: grid;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 10px 5px 10px 5px;
}
.button:hover{
  transition: 200ms;
  background: #6d3d67;
}

@media only screen and (max-width: 800px) {
  .okno {
    width: 100%;
  }
  .double {
    grid-template-columns: 1fr;
    margin-top: 45px;
    gap: 10px;
    margin-bottom: 40px;
  }
  .okno .in {
    padding: 10px;
  }
  .okno .oneblock .button {
    height: 39px;
    width: 220px;
  }
  .okno .oneblock .button a {
    font-size: 20px;

  }
}
</style>