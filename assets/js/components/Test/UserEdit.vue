<template>
  <div class="user-edit-form">
    <input type="text" v-model="username">
    <span style="display: block;">How cool is your name: {{coolness}}</span>
    <input type="submit" v-on:click="send">
  </div>
</template>


<script>
export default {
  data: () => ({
    username: '',
  }),
  computed: {
    coolness: function() {
      switch(this.username.toLowerCase()) {
        case 'nehalist':
        case 'kevin':
          return 'Pretty cool';
        case 'bob':
          return 'Awesome!';
        case 'ajit pai':
          return 'Terrible';
        default:
          return 'Not cool';
      }
    }
  },
  methods: {
    send: function() {
      // send your form ...
    }
  },
}
</script>