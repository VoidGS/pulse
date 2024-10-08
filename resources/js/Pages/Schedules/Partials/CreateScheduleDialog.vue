<script lang="ts" setup>
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from "@/Components/ui/dialog";
import { Button } from "@/Components/ui/button";
import { CalendarPlus } from "lucide-vue-next";
import { computed, ref, watch } from "vue";
import { Calendar } from "@/Components/ui/v-calendar";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from '@/lib/pt-zod';
import { useForm } from "vee-validate";
import { useForm as inertiaUseForm } from '@inertiajs/vue3'
import { route } from "momentum-trail";
import Combobox, { type ComboboxProps } from "@/Components/Combobox.vue";
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import type { Customer } from "@/Pages/Customers/Data/schema";
import type { Service } from "@/Pages/Services/Data/schema";
import { formatDateTime, getDateTime } from "@/Utilities/date";
import { Checkbox } from "@/Components/ui/checkbox";
import { filterSchedules } from "@/Utilities/utils";
import type { CalendarPopover } from "@/Pages/Schedules/Data/schema";
import type { Page } from "v-calendar/dist/types/src/utils/page";

const props = defineProps<{
	customers: Customer[],
	services: Service[],
}>()

const emit = defineEmits<{
	(e: 'filter'): void
}>()

const currentMonth = ref<number>()
const currentCalendarPage = ref<Page>({})
const calendarPops = ref<CalendarPopover[]>([])

const attributes = computed(() => [
	...calendarPops.value.map(popover => ({
		dates: popover.dates,
		dot: {
			style: {
				backgroundColor: popover.color,
		  	},
			...(popover.isComplete && { class: 'opacity-75' }),
		},
		popover: {
			label: popover.description,
			visibility: 'hover',
			hideIndicator: true
		}
	}))
])

const openCreateScheduleDialog = ref(false)

const calendarValue = ref('')

const createScheduleFormSchema = toTypedSchema(z.object({
	scheduleDate: z.string().datetime(),
	customerId: z.coerce.number({ invalid_type_error: "Escolha um cliente" }).positive('Escolha um cliente'),
	serviceId: z.coerce.number({ invalid_type_error: "Escolha um serviço" }).positive('Escolha um serviço'),
	hasRecurrence: z.boolean().default(false)
}))

const { values, setValues, handleSubmit, setErrors, resetForm } = useForm({
	validationSchema: createScheduleFormSchema
})

const customersDialogSetValue = (value) => setValues({ customerId: value })
const customersDialogComboboxArrayKeys = { id: 'id', label: 'name' }
const customersDialogComboboxOptions = { searchMessage: 'Pesquise um cliente', selectMessage: 'Selecione o cliente' }

const servicesDialogSetValue = (value) => {
	setValues({ serviceId: value })

	if (currentCalendarPage) {
		addCalendarPopover(currentCalendarPage.value)
	}
}
const servicesDialogComboboxArrayKeys = { id: 'id', label: 'name' }
const servicesDialogComboboxOptions = { searchMessage: 'Pesquise um serviço', selectMessage: 'Selecione o serviço' }

const onSubmit = handleSubmit((formValues) => {
	const form = inertiaUseForm(formValues)
	form.post(route('schedules.store'), {
		preserveScroll: true,
		preserveState: true,
		onError: (errors) => {
			setErrors(errors)
		},
		onSuccess: () => {
			resetForm()
			openCreateScheduleDialog.value = false
		},
		onFinish: () => {
			emit('filter')
		},
	})
})

const addCalendarPopover = async (page: Page) => {
	if (!page) return

	let schedulesFiltered = await filterSchedules({ month: page.month, year: page.year, serviceId: values.serviceId })

	calendarPops.value = []
	schedulesFiltered.forEach((schedule) => {
		calendarPops.value.push({
			description: getDateTime(schedule.start_date) + '/' + getDateTime(schedule.end_date) + ' ' + schedule.customer.name + ' (' + schedule.service.name + ')',
			isComplete: false,
			dates: [new Date(schedule.start_date)],
			color: '#dc2626'
		})
	})
}

const calendarMovePage = async (event: Page[]) => {
	let page = event[0]

	if (page.month === currentMonth.value) return

	currentMonth.value = page.month
	currentCalendarPage.value = page
	await addCalendarPopover(page)
}

watch(calendarValue, async (newCalendarValue) => {
	setValues({ scheduleDate: formatDateTime(newCalendarValue.toString()) })
})
</script>
<template>
	<Dialog v-model:open="openCreateScheduleDialog">
		<DialogTrigger class="cursor-pointer" as-child>
			<Button as-child>
				<div>
					<CalendarPlus class="w-5 h-5 mr-2"/>
					Cadastrar agendamento
				</div>
			</Button>
		</DialogTrigger>
		<DialogContent class="sm:max-w-3xl">
			<DialogHeader>
				<DialogTitle>Cadastrar agendamento</DialogTitle>
				<DialogDescription>Preencha os campos para cadastrar um agendamento.</DialogDescription>
			</DialogHeader>

			<form @submit="onSubmit">
				<div class="grid gap-4 py-4">
					<div class="grid grid-cols-1 gap-6">
						<div class="grid grid-cols-2 gap-6">
							<FormField v-slot="{ componentField }" name="customerId">
								<FormItem v-auto-animate>
									<FormLabel>Cliente</FormLabel>
									<Combobox :items="props.customers"
											  :items-keys="customersDialogComboboxArrayKeys"
											  :options="customersDialogComboboxOptions"
											  :item-selected="values.customerId?.toString()"
											  :item-set-value="customersDialogSetValue"
											  key="customerId"/>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="serviceId">
								<FormItem v-auto-animate>
									<FormLabel>Serviço</FormLabel>
									<Combobox :items="props.services"
											  :items-keys="servicesDialogComboboxArrayKeys"
											  :options="servicesDialogComboboxOptions"
											  :item-selected="values.serviceId?.toString()"
											  :item-set-value="servicesDialogSetValue"
											  key="serviceId"/>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>

						<FormField v-slot="{ componentField }" name="scheduleDate">
							<FormItem v-auto-animate>
								<FormLabel>Dia/Hora do agendamento</FormLabel>
								<Calendar v-model.string="calendarValue" mode="datetime" @update:pages="calendarMovePage" :attributes="attributes" :masks="{ modelValue: 'YYYY-MM-DD HH:mm:ss' }" is24hr locale="pt-BR" timezone="America/Sao_Paulo" class="rounded-lg border shadow-sm" key="scheduleDate" />
								<FormMessage/>
							</FormItem>
						</FormField>

						<FormField v-slot="{ value, handleChange }" type="checkbox" name="hasRecurrence">
							<FormItem v-auto-animate class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
								<FormControl>
									<Checkbox :checked="value" @update:checked="handleChange"/>
								</FormControl>
								<div class="space-y-1 leading-none">
									<FormLabel>Possui recorrência semanal?</FormLabel>
									<FormDescription>
										Caso o agendamento aconteça toda semana neste mesmo dia e horário.
									</FormDescription>
									<FormMessage/>
								</div>
							</FormItem>
						</FormField>
					</div>
				</div>

				<DialogFooter>
					<Button type="submit">
						Salvar
					</Button>
				</DialogFooter>
			</form>
		</DialogContent>
	</Dialog>
</template>