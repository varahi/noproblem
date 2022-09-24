<template>
  <div class="row">
    <!-- app -->
    <div id="app" class="modal-vue">
      <!-- button show -->
      <button @click="showModal = true">show</button>

      <!-- overlay -->
      <div class="overlay" v-if="showModal" @click="showModal = false"></div>

      <!-- modal -->
      <div class="modal" v-if="showModal">
        <button class="close" @click="showModal = false">x</button>
        <h3>Title</h3>
        <p>Description</p>
      </div>

    </div>
  </div>
</template>

<script>
export default {

  data: () => ({
    showModal: false,
  }),

  //data() {
  //  return {
  //    items: [],
  //    errors: []
  //  }
  //}
}
</script>


<style scoped>
.modal-vue .overlay {
  position: fixed;
  z-index: 9998;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, .5);
}

.modal-vue .modal {
  position: relative;
  width: 300px;
  z-index: 9999;
  margin: 0 auto;
  padding: 20px 30px;
  background-color: #fff;
}

.modal-vue .close{
  position: absolute;
  top: 10px;
  right: 10px;
}
</style>