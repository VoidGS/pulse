<script setup lang="ts">
import { type HTMLAttributes, computed } from 'vue'
import { ToastViewport, type ToastViewportProps } from 'radix-vue'
import { cn } from '@/lib/utils'

const props = defineProps<ToastViewportProps & { class?: HTMLAttributes['class'] }>()

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props

  return delegated
})
</script>

<template>
  <ToastViewport v-bind="delegatedProps" :class="cn('fixed top-14 right-0 z-[100] flex max-h-screen flex-col-reverse p-4 md:flex-col md:max-w-[420px]', props.class)" />
</template>
