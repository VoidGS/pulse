<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() => {
    return props.active
        ? 'font-semibold !text-foreground transition-colors hover:text-foreground/80 text-foreground/60'
        : 'transition-colors hover:text-foreground/80 text-foreground/60';
});
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
