<script setup>
const props = defineProps({
    thumbnail: String,
    message: String,
});
</script>
<template>
  <div class="flex items-center gap-3">
    <img v-if="thumbnail" :src="thumbnail" alt="Product Image" class="h-7 w-7 rounded object-cover border">
    <p class="font-semibold text-sm"> {{ message }}</p>
  </div>
</template>


<style>

</style>