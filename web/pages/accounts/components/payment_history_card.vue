<template>

  <b-card class="mt-3" header="Payment History">
    <b-table striped hover :items="transactions.items"></b-table>
  </b-card>

</template>

<script>

  export default {

    name: 'PaymentHistoryCard',

    props: {
      transactions: Object
    }

  }

</script>
