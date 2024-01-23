<template>
  <div class="block">
    Hello {{ name }}!! This is the home page
    <div class="flex justify-center my-2">
      <router-link :to="{name:'bulk-upload'}">
        <button class="bg-purple-500 px-4 py-2 text-white rounded" type="button">Excel Upload</button>
      </router-link>
    </div>
  </div>
</template>

<script>
export default {
  name: "Home",
  setup() {
    return {
      name: "Lakshya",
    }
  }
}
</script>

<style lang="scss" scoped>

</style>