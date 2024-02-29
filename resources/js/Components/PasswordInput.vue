<script lang="ts" setup>
import { Input } from "@/Components/ui/input";
import { Eye, EyeOff } from "lucide-vue-next";
import { type HTMLAttributes, ref } from "vue";
import { cn } from "@/lib/utils";

const props = defineProps<{
	componentBind?: any
	placeholder?: string
	class?: HTMLAttributes['class']
}>()

const showPassword = ref(false)
const togglePasswordVisibility = () => showPassword.value = !showPassword.value
</script>
<template>
	<div class="relative inline-flex w-full max-w-md items-center">
		<Input :class="cn('pr-10', props.class)" :type="showPassword ? 'text' : 'password'" :placeholder="props.placeholder ?? ''" v-bind="componentBind ?? ''" />
		<div class="absolute inset-y-0 right-0 flex cursor-pointer items-center pr-2.5 text-gray-400" @click="togglePasswordVisibility()">
			<Eye v-if="!showPassword" class="w-5 h-5" />
			<EyeOff v-if="showPassword" class="w-5 h-5" />
		</div>
	</div>
</template>