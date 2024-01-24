<template>
  <div>
    <Topbar/>
    <div class="mt-16 xl:mt-20 mb-6">
      <router-view/>
    </div>
  </div>
</template>

<script>
export default {
  mounted() {
    console.log('App Mounted.')
  }
}
</script>
