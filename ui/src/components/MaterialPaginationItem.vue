<template>
  <li class="page-item" :class="getClasses(active, disabled)">
    <a class="page-link" href="javascript:;">
      <i v-if="label.indexOf('Previous') >= 0" class="fa fa-angle-left"></i>
      <i v-else-if="label.indexOf('Next') >= 0" class="fa fa-angle-right"></i>
      <span v-else>{{ label }}</span>
    </a>
  </li>
</template>

<script>
export default {
  name: "MaterialPaginationItem",
  props: {
    label: {
      type: String,
      default: "",
    },
    active: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    prev: {
      type: Boolean,
      default: false,
    },
    next: {
      type: Boolean,
      default: false,
    },
  },
  methods: {
    getClasses: (active, disabled) => {
      let activeValue, disabledValue;

      activeValue = active ? "active" : '';
      disabledValue = disabled ? "disabled" : '';

      return `${activeValue} ${disabledValue}`;
    },
  },
};
</script>
