<template>
  <div id="app">
    <div class="container my-4">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <h2 class="text-center mb-4">
            Sign Up Form with Google reCAPTCHA
          </h2>
          <form
              method="post"
              @submit.prevent="validate">
            <div class="form-group">
              <input
                  type="email"
                  name="email"
                  class="form-control"
                  placeholder="Enter your e-mail address"
                  v-validate.disable="'required|email'"
                  required />

              <div
                  v-show="errors.has('email')"
                  class="invalid-feedback d-block">
                {{ errors.first('email') }}
              </div>

            </div>

            <div class="form-group">
              <input
                  type="password"
                  name="password"
                  class="form-control"
                  placeholder="Enter your password"
                  v-validate.disable="'required|min:6|max:32'"
                  required />

              <div
                  v-show="errors.has('password')"
                  class="invalid-feedback d-block"
              >
                {{ errors.first('password') }}
              </div>

            </div>
            <div class="form-group">
              <vue-recaptcha
                  ref="recaptcha"
                  :sitekey="sitekey"
                  @verify="register"
                  @expired="onCaptchaExpired"
              />
              <button
                  type="submit"
                  class="btn btn-primary btn-block">
                Sign Up
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

import axios from 'axios';
import { VueRecaptcha } from 'vue-recaptcha';

//import * as dotenv from 'dotenv'
//require('dotenv').config()
//import { ValidationProvider } from 'vee-validate';

export default {
  name: 'Register',

  components: {
    VueRecaptcha
  },

  data () {
    return {
      email: null,
      password: null,
      sitekey: '6LdoPl4jAAAAAN5n82bRAsWsVuRBaxamPw_wovqZ'
      //sitekey: process.env.VUE_APP_RECAPTCHA_TOKEN
    }
  },

  methods: {
    register (recaptchaToken) {

      console.log(this.email);
      console.log(this.password);
      console.log(recaptchaToken);

      //console.log(process.env.VUE_APP_RECAPTCHA_TOKEN)
      //console.log(process.env)

      // axios.post('https://yourserverurl.com/register', {
      //   email: this.email,
      //   password: this.password,
      //   recaptchaToken: recaptchaToken
      // })
    },

    validate () {
      const self = this
      self.$validator.validateAll().then((result) => {
        console.log(result);
        if (result) {
          self.$refs.recaptcha.execute()
        }
      })
    },

    onCaptchaExpired () {
      this.$refs.recaptcha.reset()
    }
  }
}
</script>