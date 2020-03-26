<template>
  <div>
    <div class="container" v-if="loading">loading...</div>

    <div class="container" v-if="!loading">

      <account-card :account="{ id: account.id, name: account.name, balance: account.balance }" @toggle-payment="showPaymentForm = !showPaymentForm"></account-card>

      <payment-form-card v-show="showPaymentForm" @payment-success="onPaymentSuccess"></payment-form-card>

      <payment-history-card :transactions="{ items: transactions }"></payment-history-card>

    </div>
  </div>
</template>

<script lang="ts">

  import axios from "axios";
  import Vue from "vue";

  import AccountCard from './components/account_card.vue';
  import PaymentFormCard from './components/payment_form_card.vue';
  import PaymentHistoryCard from './components/payment_history_card.vue';

  export default {

  data() {
    return {
      showPaymentForm: false,
      payment: {},

      account: null,
      transactions: null,

      loading: true,

      error: ''
    };
  },

    components: {
      'account-card': AccountCard,
      'payment-form-card': PaymentFormCard,
      'payment-history-card': PaymentHistoryCard
    },

  mounted() {
    const that = this;

    that.retrieveAccountData();
    that.retrieveTransactionData();
  },

  methods: {

    /**
     * Hide the payment form when the transaction has been completed
     * and update the data on the Account and Payment History Cards
     * 
     */
    onPaymentSuccess() {

      const that = this;

      that.showPaymentForm = false;

      that.retrieveAccountData();
      that.retrieveTransactionData();

    },

    /**
     * Retrieve and update the account information
     *
     */
    retrieveAccountData() {

      const that = this;

      axios
      .get(`http://localhost:8000/api/accounts/${this.$route.params.id}`)
      .then(function(response) {
        if (!response.data.length) {
          window.location = "/";
        } else {
          that.account = response.data[0];

          if (that.account && that.transactions) {
            that.loading = false;
          }
        }
      });

    },

    /**
     * Retrieve and update the Transactions list
     *
     */
    retrieveTransactionData() {

      const that = this;

      axios
      .get(
        `http://localhost:8000/api/accounts/${
          that.$route.params.id
        }/transactions`
      )
      .then(function(response) {

        that.displayTransactions(response);

        if (that.account && that.transactions) {
          that.loading = false;
        }

      });

    },

    /**
     * Display recent transactions
     * 
     * @param response
     */
    displayTransactions(response) {

      const that = this,
            transactions = [];

      that["transactions"] = response.data;

      for (let i = 0; i < that.transactions.length; i++) {

        that.transactions[i].amount =
        (that.account.currency === "usd" ? "$" : "€") +
        that.transactions[i].amount;

        if (that.account.id != that.transactions[i].to) {
              that.transactions[i].amount = "-" + that.transactions[i].amount;
        }

        transactions.push(that.transactions[i]);

      }

      that.transactions = transactions;
    }
  }
};
</script>

<!-- Additional Styles -->
<style scoped>

  .warning{

    display: inline-block;
    margin-left: 10px;
    color: red;

  }

</style>
