<script setup>
import { Primitive } from "reka-ui";
import { cn } from "@/lib/utils";
import { buttonVariants } from ".";

const props = defineProps({
  variant: { type: null, required: false },
  size: { type: null, required: false },
  class: { type: null, required: false },
  asChild: { type: Boolean, required: false, default: false },
  as: { type: null, required: false, default: "button" },
  disabled: { type: Boolean, required: false, default: false },
});
</script>

<template>
  <Primitive
    :as="asChild ? 'span' : 'button'" 
    :as-child="asChild"
    :class="cn(buttonVariants({ variant, size }), props.class)"
    :disabled="!asChild ? disabled : undefined" 
    type="button"
  >
    <slot />
  </Primitive>
</template>
