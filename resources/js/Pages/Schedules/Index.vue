<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import PageContainer from "@/Components/PageContainer.vue";
import TearOffCalendar from "@/Components/Emojis/TearOffCalendar.vue";
import { Button } from "@/Components/ui/button";
import { CalendarCog, Circle, Eraser, Trash } from "lucide-vue-next";
import { Calendar } from "@/Components/ui/v-calendar";
import { type Ref, ref, watch } from "vue";
import { type DateValue, getLocalTimeZone, today } from "@internationalized/date";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/Components/ui/card";
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/Components/ui/table";
import { FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import Combobox from "@/Components/Combobox.vue";
import type { Customer } from "@/Pages/Customers/Data/schema";
import type { Service } from "@/Pages/Services/Data/schema";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/Components/ui/select";
import { cn } from "@/lib/utils";
import CreateScheduleDialog from "@/Pages/Schedules/Partials/CreateScheduleDialog.vue";
import { type Schedule, ScheduleBorderStatusColor, ScheduleSelectStatusColor, ScheduleStatus } from "@/Pages/Schedules/Data/schema";
import { Avatar, AvatarImage } from "@/Components/ui/avatar";
import { displayScheduleDates } from "@/Utilities/date";
import DropdownAction, { type DataTableActionItem } from "@/Components/DataTable/DataTableDropdown.vue";
import { useForm } from "vee-validate";
import { Link, usePage, useForm as useInertiaForm } from "@inertiajs/vue3";
import EditScheduleDialog from "@/Pages/Schedules/Partials/EditScheduleDialog.vue";
import { useEditScheduleDialog } from "@/Pages/Schedules/Composables/useEditScheduleDialog";
import { route } from "momentum-trail";
import { Badge } from "@/Components/ui/badge";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from '@/lib/pt-zod';
import { filterSchedules } from "@/Utilities/utils";

const props = defineProps<{
	schedules: Schedule[],
	customers: Customer[],
	services: Service[]
}>()

const schedules = ref<Schedule[]>(props.schedules)
const customerValue = ref('')
const serviceValue = ref('')
const statusValue = ref<ScheduleStatus | undefined>()
const clearingFilter = ref(false)

const calendarValue = ref(today(getLocalTimeZone())) as Ref<DateValue>

const form = useForm({});
const inertiaForm = useInertiaForm({})

const { editScheduleDialogState, openDialog } = useEditScheduleDialog()

const filterScheduleSchema = toTypedSchema(z.object({
	scheduleDate: z.coerce.date().optional(),
	customerId: z.coerce.number({ invalid_type_error: "Escolha um cliente" }).positive('Escolha um cliente').optional(),
	serviceId: z.coerce.number({ invalid_type_error: "Escolha um serviço" }).positive('Escolha um serviço').optional(),
	status: z.nativeEnum(ScheduleStatus).optional(),
}))

const { values, setValues, handleSubmit, resetForm } = useForm({
	initialValues: {
		scheduleDate: new Date(),
		customerId: undefined,
		serviceId: undefined,
		status: undefined
	},
	validationSchema: filterScheduleSchema
})

const onSubmit = handleSubmit(async (formValues) => {
	schedules.value = await filterSchedules((formValues))
})

const clearFilterOptions = () => {
	clearingFilter.value = true

	calendarValue.value = today(getLocalTimeZone())
	customerValue.value = ''
	serviceValue.value = ''
	statusValue.value = undefined

	clearingFilter.value = false
	resetForm();
}

const customersSetValue = (value) => {
	customerValue.value = value
	setValues({ customerId: value })
}
const customersComboboxArrayKeys = { id: 'id', label: 'name' }
const customersComboboxOptions = { searchMessage: 'Pesquise um cliente', selectMessage: 'Selecione o cliente' }

const servicesSetValue = (value) => {
	serviceValue.value = value
	setValues({ serviceId: value })
}
const servicesComboboxArrayKeys = { id: 'id', label: 'name' }
const servicesComboboxOptions = { searchMessage: 'Pesquise um serviço', selectMessage: 'Selecione o serviço' }

watch(calendarValue, async (newCalendarValue) => {
	let newDate = newCalendarValue ? new Date(newCalendarValue.toString()) : undefined
	setValues({ scheduleDate: newDate })
})

watch(values, async (newValues) => {
	if (clearingFilter.value == true) return
	await onSubmit()
})
</script>

<template>
	<AppLayout title="Agendamentos">
		<PageContainer>
			<div>
				<TearOffCalendar class="w-10 h-10 mb-6" />

				<div class="space-y-4 md:space-y-0 md:flex md:items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Agendamentos
						</h2>

						<p class="text-muted-foreground">
							Listagem de agendamentos.
						</p>
					</div>

					<div>
						<CreateScheduleDialog @filter="onSubmit" :customers="props.customers" :services="props.services" />
					</div>
				</div>
			</div>

			<EditScheduleDialog @filter="onSubmit" :customers="props.customers" :services="props.services" />

			<div class="pt-4 space-y-6">
				<div class="grid gap-6">
					<form @submit="onSubmit">
						<div class="grid grid-cols-1 lg:grid-cols-3 gap-y-6 lg:gap-x-6">
							<FormField v-slot="{ componentField }" name="scheduleDate">
								<FormItem v-auto-animate>
									<Calendar v-model="calendarValue" locale="pt-BR" timezone="America/Sao_Paulo" class="rounded-lg border shadow-sm" />
									<FormMessage/>
								</FormItem>
							</FormField>

							<Card class="col-span-2">
								<CardHeader class="flex md:flex-row md:items-center space-y-2 md:space-y-0">
									<div class="grid">
										<CardTitle class="text-xl">Filtrar agendamentos</CardTitle>
									</div>
									<Button as-child @click="clearFilterOptions" variant="secondary" size="sm" class="md:ml-auto gap-2">
										<Link href="#" preserve-scroll preserve-state>
											Limpar filtros
											<Eraser class="h-4 w-4"/>
										</Link>
									</Button>
								</CardHeader>
								<CardContent>
									<div class="grid gap-6">
										<div class="md:grid md:grid-cols-2 md:gap-6">
											<FormField v-slot="{ componentField }" name="customerId">
												<FormItem v-auto-animate>
													<FormLabel>Cliente</FormLabel>
													<Combobox :items="props.customers"
															  :items-keys="customersComboboxArrayKeys"
															  :options="customersComboboxOptions"
															  :item-selected="customerValue"
															  :item-set-value="customersSetValue"/>
													<FormMessage/>
												</FormItem>
											</FormField>

											<FormField v-slot="{ componentField }" name="serviceId">
												<FormItem v-auto-animate>
													<FormLabel>Serviço</FormLabel>
													<Combobox :items="props.services"
															  :items-keys="servicesComboboxArrayKeys"
															  :options="servicesComboboxOptions"
															  :item-selected="serviceValue"
															  :item-set-value="servicesSetValue"/>
													<FormMessage/>
												</FormItem>
											</FormField>
										</div>

										<div class="md:grid md:grid-cols-2 md:gap-6">
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
										</div>
									</div>
								</CardContent>
							</Card>
						</div>
					</form>

					<div class="grid grid-cols-1">
						<Card>
							<CardHeader class="flex flex-row items-center">
								<div class="grid gap-2">
									<CardTitle class="text-xl">Agendamentos</CardTitle>
									<CardDescription>Agendamentos da data/filtros selecionados.</CardDescription>
								</div>
							</CardHeader>
							<CardContent>
								<Table>
									<TableHeader>
										<TableRow>
											<TableHead>Cliente</TableHead>
											<TableHead>Serviço</TableHead>
											<TableHead>Data/Hora</TableHead>
											<TableHead>Status</TableHead>
											<TableHead>Profissional</TableHead>
											<TableHead></TableHead>
										</TableRow>
									</TableHeader>
									<TableBody>
										<TableRow v-for="schedule in schedules" :key="schedule.id">
											<TableCell>
												<div class="font-medium">
													{{ schedule.customer.name }}
												</div>
												<div v-if="schedule.customer.guardians?.length > 0" class="text-sm text-muted-foreground">
													Resp. {{ schedule.customer.guardians[0].name }}
												</div>
											</TableCell>
											<TableCell>
												{{ schedule.service.name }}
											</TableCell>
											<TableCell>
												{{ displayScheduleDates(schedule.start_date, schedule.end_date) }}
											</TableCell>
											<TableCell class="capitalize">
												<Badge variant="outline" :class="cn('border-2', ScheduleBorderStatusColor[schedule.status.toUpperCase()])">{{ schedule.status }}</Badge>
											</TableCell>
											<TableCell class="flex items-center space-x-2">
												<Avatar size="xs">
													<AvatarImage :src="schedule.service.user.profile_photo_url" />
												</Avatar>
												<span>{{ schedule.service.user.name }}</span>
											</TableCell>
											<TableCell>
												<DropdownAction :items="[
													{
														label: 'Editar agendamento',
														href: '#',
														onClick: () => openDialog(schedule),
														icon: CalendarCog,
														show: usePage<any>().props.user_permissions.edit_schedules
													},
													{
														label: 'Inativar agendamento',
														href: route('schedules.destroy', schedule),
														icon: Trash,
														class: 'text-red-500 focus:text-red-500',
														deleteDialog: {
															title: 'Inativar agendamento',
															description: 'Tem certeza que deseja inativar este agendamento?',
															deleteActionName: 'Inativar',
															deleteAction: () => inertiaForm.delete(route('schedules.destroy', schedule), {
																preserveState: true,
																preserveScroll: true,
																onFinish: () => {
																	onSubmit()
																},
															})
														},
														show: usePage<any>().props.user_permissions.delete_schedules
													}
												]" />
											</TableCell>
										</TableRow>
									</TableBody>
								</Table>
							</CardContent>
						</Card>
					</div>
				</div>
			</div>
		</PageContainer>
	</AppLayout>
</template>