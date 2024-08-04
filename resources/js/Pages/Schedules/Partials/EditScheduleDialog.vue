<script lang="ts" setup>
import { ScheduleSelectStatusColor, ScheduleStatus } from "@/Pages/Schedules/Data/schema";
import type { Customer } from "@/Pages/Customers/Data/schema";
import type { Service } from "@/Pages/Services/Data/schema";
import { ref, watch } from "vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { useForm } from "vee-validate";
import { useForm as inertiaUseForm } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import { formatDateTime } from "@/Utilities/date";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import {
	Dialog,
	DialogDescription,
	DialogFooter,
	DialogHeader,
	DialogScrollContent,
	DialogTitle
} from "@/Components/ui/dialog";
import { Checkbox } from "@/Components/ui/checkbox";
import { Calendar } from "@/Components/ui/v-calendar";
import Combobox from "@/Components/Combobox.vue";
import { Button } from "@/Components/ui/button";
import { useEditScheduleDialog } from "@/Pages/Schedules/Composables/useEditScheduleDialog";
import { cn } from "@/lib/utils";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/Components/ui/select";
import { Circle } from "lucide-vue-next";

const { editScheduleDialogState, toggle } = useEditScheduleDialog()

const props = defineProps<{
	customers: Customer[],
	services: Service[],
}>()

const emit = defineEmits<{
	(e: 'filter'): void
}>()

const calendarValue = ref('')
const statusValue = ref<ScheduleStatus | undefined>()

const editScheduleFormSchema = toTypedSchema(z.object({
	scheduleDate: z.string().datetime(),
	customerId: z.coerce.number({ invalid_type_error: "Escolha um cliente" }).positive('Escolha um cliente'),
	serviceId: z.coerce.number({ invalid_type_error: "Escolha um serviço" }).positive('Escolha um serviço'),
	status: z.nativeEnum(ScheduleStatus),
	hasRecurrence: z.boolean().default(false)
}))

const { values, setValues, handleSubmit, setErrors, resetForm } = useForm({
	validationSchema: editScheduleFormSchema
})

const customersDialogSetValue = (value) => setValues({ customerId: value })
const customersDialogComboboxArrayKeys = { id: 'id', label: 'name' }
const customersDialogComboboxOptions = { searchMessage: 'Pesquise um cliente', selectMessage: 'Selecione o cliente' }

const servicesDialogSetValue = (value) => setValues({ serviceId: value })
const servicesDialogComboboxArrayKeys = { id: 'id', label: 'name' }
const servicesDialogComboboxOptions = { searchMessage: 'Pesquise um serviço', selectMessage: 'Selecione o serviço' }

const submitType = ref(0)

const onSubmit = handleSubmit((formValues) => {
	if (!editScheduleDialogState.schedule) return

	const form = inertiaUseForm({...formValues, 'submitType': submitType.value})
	form.put(route('schedules.update', editScheduleDialogState.schedule.id), {
		preserveScroll: true,
		onError: (errors) => {
			setErrors(errors)
		},
		onSuccess: () => {
			resetForm()
			editScheduleDialogState.open = false
		},
		onFinish: () => {
			emit('filter')
		},
	})
})

watch(calendarValue, async (newCalendarValue) => {
	setValues({ scheduleDate: formatDateTime(newCalendarValue.toString()) })
})

watch(() => editScheduleDialogState.open, (newOpenState) => {
	if (!newOpenState) return
	if (!editScheduleDialogState.schedule) return editScheduleDialogState.open = false
	let schedule = editScheduleDialogState.schedule
	setValues({
		customerId: schedule.customer?.id,
		serviceId: schedule.service.id,
		status: schedule.status,
		hasRecurrence: !!schedule.recurrence_id
	})

	statusValue.value = schedule.status

	calendarValue.value = schedule.start_date
	setValues({ scheduleDate: formatDateTime(schedule.start_date.toString()) })
})
</script>
<template>
	<Dialog v-model:open="editScheduleDialogState.open">
		<DialogScrollContent class="sm:max-w-3xl">
			<DialogHeader>
				<DialogTitle>Editar agendamento</DialogTitle>
				<DialogDescription>Altere os campos para editar o agendamento.</DialogDescription>
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
								<Calendar v-model.string="calendarValue" mode="datetime" :masks="{ modelValue: 'YYYY-MM-DD HH:mm:ss' }" is24hr locale="pt-BR" timezone="America/Sao_Paulo" class="rounded-lg border shadow-sm" key="scheduleDate" />
								<FormMessage/>
							</FormItem>
						</FormField>

						<FormField v-slot="{ componentField }" name="status">
							<FormItem v-auto-animate>
								<FormLabel>Status</FormLabel>
								<Select v-bind="componentField" v-model="statusValue">
									<SelectTrigger>
										<SelectValue :class="cn(statusValue ? 'capitalize' : 'text-muted-foreground')" placeholder="Selecione o status" />
									</SelectTrigger>
									<SelectContent>
										<SelectItem v-for="status in ScheduleStatus" :value="status" class="capitalize">
											<div class="flex gap-1.5 items-center">
												<Circle :fill="ScheduleSelectStatusColor[status.toUpperCase()]" :color="ScheduleSelectStatusColor[status.toUpperCase()]" class="w-4 h-4" />
												<span>{{ status }}</span>
											</div>
										</SelectItem>
									</SelectContent>
								</Select>
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
					<template v-if="!editScheduleDialogState.schedule?.recurrence_id">
						<Button @click="submitType = 0" type="submit">
							Salvar
						</Button>
					</template>

					<template v-else>
						<Button @click="submitType = 0" type="submit">
							Salvar apenas esse
						</Button>

						<Button @click="submitType = 1" type="submit">
							Salvar todos
						</Button>

						<Button @click="submitType = 2" type="submit">
							Salvar a partir desse em diante
						</Button>
					</template>
				</DialogFooter>
			</form>
		</DialogScrollContent>
	</Dialog>
</template>