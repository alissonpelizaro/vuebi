<template>
  <ul class="pagination pagination-footer" :class="getClasses(color, size)">
    <slot />
  </ul>
</template>

<script>
export default {
  name: "MaterialPagination",
  props: {
    color: {
      type: String,
      default: "success",
    },
    size: {
      type: String,
      default: "md",
    },
  },
  methods: {
    getClasses: (color, size) => {
      let colorValue, sizeValue;

      colorValue = color ? `pagination-${color}` : null;
      sizeValue = size ? `pagination-${size}` : null;

      return `${colorValue} ${sizeValue}`;
    },
  },
};
</script>

<style scoped>
.pagination-footer {
    margin: 1rem !important;
}
</style>