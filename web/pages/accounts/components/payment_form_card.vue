<template>

  <b-card class="mt-3" header="New Payment">
    <b-form @submit="onSubmit">
      <b-form-group id="input-group-1" label="To:" label-for="input-1">
        <b-form-input id="input-1"
                      size="sm"
                      v-model="payment.to"
                      type="number"
                      required
                      placeholder="Destination ID"></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-2" label="Amount:" label-for="input-2">
        <b-input-group prepend="$" size="sm">
          <b-form-input id="input-2"
                        v-model="payment.amount"
                        type="number"
                        required
                        placeholder="Amount"></b-form-input>
        </b-input-group>
      </b-form-group>

      <b-form-group id="input-group-3" label="Details:" label-for="input-3">
        <b-form-input id="input-3"
                      size="sm"
                      v-model="payment.details"
                      required
                      placeholder="Payment details"></b-form-input>
      </b-form-group>

      <b-button type="submit" size="sm" variant="primary">Submit</b-button>

      <div class="warning">{{ error }}</div>
    </b-form>
  </b-card>

</template>

<script>

  import axios from "axios";

  export default {

    data() {

      return {

        payment: {},
        error: ''

      };

    },

    name: 'PaymentFormCard',

    props: {
      transactions: Object
    },

    methods: {

      /**
       * Process the transaction when the submit button is pressed
       *
       * @param evt
       */
      onSubmit(evt) {

        const that = this;

        evt.preventDefault();

        axios.post(
          `http://localhost:8000/api/accounts/${
          this.$route.params.id
          }/transactions`,

          this.payment

        ).then(function (response) {

          const data = response.data;

          // if the transaction has errors
          if (data.error) {

            // if the receiving ID is invalid, display...
            if (data.error === -2) {
              that.error = 'Invalid recipient ID';
            }
            // else if the account has insuficient funds, display...
            else if (data.error === -1)
              that.error = 'Insufficient funds in account';
          }
          // if the transaction is successful
          else {

            // reset the form data
            that.payment = {};
            that.showPaymentForm = false;
            that.error = '';

            // and broadcast the successful payment
            that.$emit('payment-success');

          }

        });
      }
    }

  }

</script>

<!-- Additional Styles -->
<style scoped>


  .warning {
    display: inline-block;
    margin-left: 10px;
    color: red;
  }
</style>
