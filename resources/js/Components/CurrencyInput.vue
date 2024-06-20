<script lang="ts" setup>
import { watch, onMounted, ref } from 'vue';
import { CurrencyDisplay, useCurrencyInput } from 'vue-currency-input';
import { Input } from '@/Components/ui/input';

const props = defineProps<{
	handleChange: (e: (Event | unknown), shouldValidate?: boolean) => void,
	fieldValue: number|string|null
}>()

const value = ref(props.fieldValue)

const { inputRef, formattedValue, numberValue, setValue } = useCurrencyInput({
	locale: 'pt-BR',
	currency: 'BRL',
	currencyDisplay: CurrencyDisplay.symbol,
	precision: 2,
	hideCurrencySymbolOnFocus: false,
	hideGroupingSeparatorOnFocus: false,
	hideNegligibleDecimalDigitsOnFocus: false,
	autoDecimalDigits: true,
	useGrouping: true,
	accountingSign: false,
});

onMounted(() => {
	setTimeout(() => {
		if (value) {
			setValue(value.value)
		}
	}, 300)
})

watch(numberValue, (newValue) => {
	props.handleChange(newValue);
});

watch(value, (newValue) => {
	if (numberValue !== null && newValue !== numberValue) {
		setValue(newValue)
	}
});
</script>

<template>
	<Input v-model="formattedValue" type="text" ref="inputRef"/>
</template>