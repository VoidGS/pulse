<script lang="ts" setup>
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import Combobox from "@/Components/Combobox.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { useForm } from "vee-validate";
import type { Service } from "@/Pages/Services/data/schema";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import { Input } from "@/Components/ui/input";
import { Percent, Plus } from 'lucide-vue-next';
import { Button } from "@/Components/ui/button";
import type { Discount } from "@/Pages/Customers/data/schema";
import { ref, watch } from "vue";

const props = defineProps<{
	services: Service[],
	getServicesWithoutDiscount: (services: Service[]) => Service[],
	updateServices: (services: Service[]) => void,
	pushDiscount: (discount: Discount) => void,
	removeDiscount: (index: number) => void,
}>()

const discountFormSchema = toTypedSchema(z.object({
	service: z.string().min(1, 'Selecione um serviço'),
	discount: z.coerce.number({ invalid_type_error: "Obrigatório" }).min(1).max(100)
}))

const { values, setValues, handleSubmit, resetForm } = useForm({
	validationSchema: discountFormSchema
})

const discountOnSubmit = handleSubmit((formValues) => {
	// console.log(formValues)

	props.pushDiscount(formValues)
	props.updateServices(props.getServicesWithoutDiscount(props.services))

	resetForm()
})

const servicesSetValue = (value) => setValues({ service: value })
const servicesComboboxArrayKeys = { id: 'name', label: 'name' }
const servicesComboboxOptions = { searchMessage: 'Pesquise um serviço...', selectMessage: 'Selecione um serviço...' }

const discountTotalValue = ref(0)

watch(values, (newValues) => {
	if (!newValues.service) return
	if (!newValues.discount) return

	const service = props.services.find((service) => service.name === newValues.service?.toString())

	discountTotalValue.value = service ? (service.price - (service.price * (newValues.discount / 100))) : 0
})

// 'R$ ' + (props.services.find(service => service.name === values.service?.toString())?.price - props.services.find(service => service.name === values.service?.toString())?.price * (10 / 100)) ?? 'R$ 0,00'
</script>
<template>
	<form @submit="discountOnSubmit" class="space-y-4">
		<div class="grid grid-cols-4 gap-6">
			<FormField v-slot="{ componentField }" name="service">
				<FormItem v-auto-animate>
					<FormLabel>Serviços</FormLabel>
					<Combobox :items="props.services"
							  :items-keys="servicesComboboxArrayKeys"
							  :options="servicesComboboxOptions"
							  :item-selected="values.service?.toString()"
							  :item-set-value="servicesSetValue"/>
					<FormMessage/>
				</FormItem>
			</FormField>

			<FormField v-slot="{ componentField }" name="discount">
				<FormItem v-auto-animate>
					<FormLabel>Desconto</FormLabel>
					<FormControl>
						<div class="relative w-full max-w-sm items-center">
							<Input type="number" placeholder="Desconto" v-bind="componentField" class="pl-8"/>
							<span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
								<Percent class="size-4 text-muted-foreground"/>
							</span>
						</div>
					</FormControl>
					<FormMessage/>
				</FormItem>
			</FormField>

			<div class="space-y-2" v-auto-animate>
				<div class="opacity-0">Adicionar</div>
				<div class="flex items-center">
					<Button type="submit" size="icon">
						<Plus class="size-6"/>
					</Button>
					<span class="ml-3 text-muted-foreground text-sm font-semibold">
						{{
							discountTotalValue.toLocaleString('pt-br', {
								style: 'currency',
								currency: 'BRL',
								minimumFractionDigits: 2
							})
						}}
					</span>
				</div>
			</div>
		</div>
	</form>
</template>