<template>
  <div class="relative p-4 w-full bg-white rounded-lg overflow-hidden">
    <div class="animate-pulse-fast">
      <div class="flex-1 py-1 flex gap-2" v-for="i in 10" :key="i">
        <div class="h-10 bg-gray-200 rounded w-1/6"></div>
        <div class="h-10 bg-gray-200 rounded w-4/6"></div>
        <div class="h-10 bg-gray-200 rounded w-1/6"></div>
        <div class="h-10 bg-gray-200 rounded w-1/6"></div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name:"Loading"
}
</script>