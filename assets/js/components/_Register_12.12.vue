<template>

  <div>
    <div>
      <h1 style="text-align: center">Vue Recaptcha Demo</h1>
      <div class="content">
        <div v-show="!sucessfulServerResponse" class="auth-form-wrapper">
          <form class="auth-form" @submit.prevent="submit">
            <div class="header">
              <h1>
                Sign up
              </h1>
            </div>
            <div class="input-group">
              <label for="email">email
              </label>
              <input v-model="email" required placeholder="name@domain.com" class="" name="email"
                     id="email"/>
            </div>
            <div class="input-group">
              <label for="password">password</label>
              <input required v-model="password" type="password" placeholder="password" class=""
                     name="password" id="password"/>
            </div>
            <div class="input-group">
              <label for="password-confirmation">confirm password
              </label>
              <input required type="password" placeholder="confirm password" class=""
                     name="password confirmation"
                     id="password-confirmation"/>
            </div>
            <div class="input-group">
              <vue-recaptcha
                  ref="recaptcha"
                  size="invisible"
                  @verify="onCaptchaVerified"
                  @expired="onCaptchaExpired"
                  sitekey="6LdoPl4jAAAAAN5n82bRAsWsVuRBaxamPw_wovqZ">
              </vue-recaptcha>
              <button :disabled="status==='submitting'" type="submit" class="button">sign up</button>
            </div>
            <div v-cloak class="">
              <div v-show="serverError">
                {{serverError}}
              </div>
            </div>
          </form>
        </div>
        <div class="successful-server-response-wrapper" v-cloak>
          <div v-show="sucessfulServerResponse" class="successful-server-response">
            {{sucessfulServerResponse}}
          </div>
        </div>
      </div>
    </div>
  </div>

</template>

<script>
import { VueRecaptcha } from 'vue-recaptcha';
export default {
  name: 'Register',
  components: {
    VueRecaptcha
  },
  methods: {

    submit: function () {
      // this.status = "submitting";
      this.$refs.recaptcha.execute();
    },

    onCaptchaVerified: function (recaptchaToken) {
      const self = this;
      self.status = "submitting";
      self.$refs.recaptcha.reset();

      console.log(self.email);
      console.log(self.password);
      console.log(recaptchaToken);

/*      axios.post("/sign-up-handler-customer", {

        //email: self.email,
        //password: self.password,
        //recaptchaToken: recaptchaToken

      }).then((response) => {
        self.sucessfulServerResponse = response.data.message;
      }).catch((err) => {
        self.serverError = getErrorMessage(err);

        //helper to get a displayable message to the user
        function getErrorMessage(err) {
          let responseBody;
          responseBody = err.response;
          if (!responseBody) {
            responseBody = err;
          }
          else {
            responseBody = err.response.data || responseBody;
          }
          return responseBody.message || JSON.stringify(responseBody);
        }

      }).then(() => {
        self.status = "";
      });*/
    },

    onCaptchaExpired: function () {
      this.$refs.recaptcha.reset();
    }
  },

  data () {
    return {
      email: "",
      password: "",
      passwordConfirmation: "",
      status: "",
      sucessfulServerResponse: "",
      serverError: "",
      sitekey: '6LdoPl4jAAAAAN5n82bRAsWsVuRBaxamPw_wovqZ'
      //sitekey: process.env.VUE_APP_RECAPTCHA_TOKEN
    }
  },

}
</script>