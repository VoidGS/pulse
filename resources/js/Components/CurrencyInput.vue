<script lang="ts" setup>
import { watch, onMounted } from 'vue';
import { CurrencyDisplay, useCurrencyInput } from 'vue-currency-input';
import { Input } from '@/Components/ui/input';
import { useField } from "vee-validate";

const props = defineProps<{
	name: String,
}>()

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

const { handleChange, value } = useField<Number>(props.name);

onMounted(() => {
	setTimeout(() => {
		if (value.value) {
			setValue(value.value)
		}
	}, 300)
})

watch(numberValue, (newValue) => {
	handleChange(newValue);
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