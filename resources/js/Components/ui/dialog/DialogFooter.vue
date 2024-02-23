<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = defineProps<{ class?: HTMLAttributes['class'] }>()
</script>

<template>
  <div
    :class="
      cn(
        'flex flex-col-reverse md:flex-row md:justify-end md:gap-x-2',
        props.class,
      )
    "
  >
    <slot />
  </div>
</template>
