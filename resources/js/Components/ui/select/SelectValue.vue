<script setup lang="ts">
import { SelectValue, type SelectValueProps } from 'radix-vue'
import { cn } from "@/lib/utils";

const props = defineProps<SelectValueProps>()
</script>

<template>
  <SelectValue :class="cn('font-medium')" v-bind="props">
    <slot />
  </SelectValue>
</template>
