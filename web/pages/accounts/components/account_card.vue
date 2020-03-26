<template>

  <b-card :header="'Welcome, ' + account.name" class="mt-3">
    <b-card-text>
      <div>
        Account: <code>{{ account.id }}</code>
      </div>
      <div>
        Balance:
        <code>
          {{
            account.currency === "usd" ? "$" : "€"
          }}{{ account.balance }}
        </code>
      </div>
    </b-card-text>
    <b-button size="sm" variant="success" v-on:click="$emit('toggle-payment')">New payment</b-button>

    <b-button class="float-right"
              variant="danger"
              size="sm"
              nuxt-link
              to="/">Logout</b-button>
  </b-card>

</template>

<script>

  export default {

    name: 'AccountCard',

    props: {
      account: Object
    }

  }

</script>
