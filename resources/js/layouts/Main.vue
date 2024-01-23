<template>
  <div class="w-full flex items-center justify-center mx-2 my-4 px-2 py-4">
    <router-view/>
  </div>
</template>

<script>
export default {
  mounted() {
    console.log('App Mounted.')
  }
}
</script>
