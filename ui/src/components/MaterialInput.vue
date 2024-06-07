<template>
  <div class="input-group" :class="`input-group-${variant} ${getStatus(error, success)}`">
    <label :class="[
      variant === 'static' ? '' : 'form-label',
      color === 'dark' ? 'text-black' : color
    ]">{{ label }}</label>
    <input :id="id" :type="type" class="form-control" :class="getClasses(size)" :name="name" :value="value"
      :placeholder="placeholder" :isRequired="isRequired" :disabled="disabled"
      :min="min" :max="max" :step="step"
      @input="(e) => this.$emit('update:value', e.target.value)" />
  </div>
</template>

<script>
import setMaterialInput from "@/assets/js/material-input.js";

export default {
  name: "MaterialInput",
  props: {
    variant: {
      type: String,
      default: "outline",
    },
    label: {
      type: String,
      default: "",
    },
    size: {
      type: String,
      default: "default",
    },
    success: {
      type: Boolean,
      default: false,
    },
    error: {
      type: Boolean,
      default: false,
    },
    disabled: {
      type: Boolean,
      default: false,
    },
    name: {
      type: String,
      default: "",
    },
    id: {
      type: String,
      required: true,
    },
    value: {
      type: String,
      default: "",
    },
    placeholder: {
      type: String,
      default: "",
    },
    type: {
      type: String,
      default: "text",
    },
    isRequired: {
      type: Boolean,
      default: false,
    },
    color: {
      type: String,
      default: "dark"
    },
    min: {
      type: String,
      default: ''
    },
    max: {
      type: String,
      default: ''
    },
    step: {
      type: String,
      default: ''
    },
  },

  emits: ['update:value'],
  mounted() {
    setMaterialInput();
  },
  methods: {
    getClasses: (size) => {
      let sizeValue;

      sizeValue = size ? `form-control-${size}` : null;

      return sizeValue;
    },
    getStatus: (error, success) => {
      let isValidValue;

      if (success) {
        isValidValue = "is-valid";
      } else if (error) {
        isValidValue = "is-invalid";
      } else {
        isValidValue = null;
      }

      return isValidValue;
    },
  },
};
</script>
