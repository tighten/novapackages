<template>
  <div class="relative">
    <div role="button" class="inline-block select-none p-2" @click="open = !open">
      <slot name="link"></slot>
    </div>
    <div class="absolute right-0 w-auto mr-2" v-show="open">
      <slot name="dropdown"></slot>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      open: false
    };
  }
};
</script>
