<template>
  <header class="header fixed w-screen z-20 top-0 bg-white shadow-md flex items-center justify-between p-2">
    <!-- logo -->
    <h1 class="w-4/12 flex gap-1 md:mx-4">
      <router-link :to="{name:'home'}">
        <img :src="'/images/logo.png'"
             class="w-44 h-10 md:h-12 xl:w-48 xl:h-14" alt="PenIT logo" width="150" height="35">
      </router-link>
    </h1>
  </header>
</template>

<script>
export default {
  name: "TopBar",
  setup() {

  }
}
</script>