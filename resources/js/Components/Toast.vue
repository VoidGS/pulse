<script lang="ts" setup>
import { h, onMounted, ref, watch, watchEffect } from "vue";
import { usePage } from "@inertiajs/vue3";
import Toaster from "@/Components/ui/toast/Toaster.vue";
import { type ToastVariants, useToast } from "@/Components/ui/toast";

const page = usePage();
const show = ref(true);
const style = ref<ToastVariants['variant']>('success');
const message = ref('');

const showToast = (message: string, variant: typeof style.value) => {
	useToast().toast({
		description: h('div', { class: 'flex items-center gap-2 mr-6' }, [
			h('img', { src: "/emojis/clapping-hands.png", class: "w-8 h-8 mb-1" }),
			h('div', [
				h('div', { class: 'text-sm font-semibold' }, message),
			])
		]),
		class: 'p-4',
		variant: variant
	})
}

watchEffect(async () => {
	style.value = page.props.jetstream.flash?.toastStyle || page.props.flash?.toastStyle || 'default';
	message.value = page.props.jetstream.flash?.toast || page.props.flash?.toast || '';
	show.value = true;
});

watch(message, (toastMessage) => {
	if (message.value) {
		showToast(message.value, style.value)
	}
})

onMounted(() => {
	if (message.value) {
		showToast(message.value, style.value)
	}
})
</script>

<template>
	<Toaster/>
</template>