<template>
  <section class="banner modal-vue">
    <div class="inner">
      <h1 class="banner_header">Найдите надежного работника или работу самостоятельно</h1>
      <h3 class="banner_header-2">без посредников</h3>
      <div class="banner_btn-box">
        <button class="banner_btn" type="button" @click="showModal = true">Попробовать</button>
      </div>
    </div>

    <!-- overlay -->
    <div class="overlay" v-if="showModal" @click="showModal = false"></div>

    <!-- modal -->
    <div class="okno fon oneblock modal" v-if="showModal">
      <div class="in">
        <div class="title">
          <h2>Выберите свою роль</h2>
          <button class="close" @click="showModal = false">
            <img src="assets/img/krest.svg">
          </button>
        </div>

        <div class="double">
          <div class="button">
            <a>Работодатель</a>
          </div>
          <div class="button">
            <a>Работник</a>
          </div>
          <div class="button">
            <a>Покупатель</a>
          </div>
        </div>
      </div>
    </div>

  </section>
</template>

<script>
export default {
  data: () => ({
    showModal: false,
  }),
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
  width: 700px;
  height: 150px;
  top: 50%;
  left: 50%;
  margin-top: -75px; /* Negative half of height. */
  margin-left: -350px; /* Negative half of width. */
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
}
/* Buttons */
.krest img {
  width: 33px;
  height: 33px;
  cursor: pointer;
}
.double {
  margin: 60px auto 0;
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
  width: 200px;
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