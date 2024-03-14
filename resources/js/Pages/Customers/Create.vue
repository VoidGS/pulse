<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import Stethoscope from "@/Components/Emojis/Stethoscope.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { validateCPF } from "@/Utilities/utils";
import { FieldArray, useFieldArray, useForm } from "vee-validate";
import { useForm as inertiaUseForm } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { Input } from "@/Components/ui/input";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import { vMaska } from "maska";
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { Button } from "@/Components/ui/button";
import { inputDate } from "@/Utilities/date";
import { CalendarIcon, Minus, Percent } from "lucide-vue-next";
import { Calendar } from "@/Components/ui/calendar";
import { cn } from "@/lib/utils";
import { Checkbox } from "@/Components/ui/checkbox";
import { Separator } from "@/Components/ui/separator";
import type { Service } from "@/Pages/Services/data/schema";
import AddDiscountForm from "@/Pages/Customers/Partials/AddDiscountForm.vue";
import type { Discount } from "@/Pages/Customers/data/schema";
import { ref, toRaw } from "vue";

const props = defineProps<{
	services: Service[]
}>()

const formSchema = toTypedSchema(z.object({
	name: z.string().min(2).max(255),
	cpf: z.string().refine((cpf: string) => validateCPF(cpf), "Digite um CPF válido.").optional(),
	birthdate: z.date({ required_error: "Data de nascimento obrigatória." }).max(new Date()),
	hasGuardian: z.boolean().default(false),
	guardianName: z.string().min(2).max(255).optional(),
	guardianCPF: z.string().refine((cpf: string) => validateCPF(cpf), "Digite um CPF válido.").optional(),
	guardianBirthdate: z.date().max(new Date()).optional(),
	hasDiscounts: z.boolean().default(false),
	discounts: z.array(z.object({
		service: z.string().min(1),
		discount: z.coerce.number().min(1).max(100)
	})),
}).partial()
	.superRefine(({ name, cpf, birthdate, hasGuardian, guardianName, guardianCPF, guardianBirthdate }, ctx) => {
		if (!name) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "O nome é obritagório.",
				path: ['name']
			})
		}

		if (!birthdate) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "A data de nascimento é obritagório.",
				path: ['birthdate']
			})
		}

		if (!hasGuardian && !cpf) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "CPF obrigatório caso não tenha responsável.",
				path: ['cpf']
			})
		}

		if (hasGuardian && !guardianName) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "Nome do responsável obrigatório.",
				path: ['guardianName']
			})
		}

		if (hasGuardian && !guardianCPF) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "CPF do responsável obrigatório.",
				path: ['guardianCPF']
			})
		}

		if (hasGuardian && !guardianBirthdate) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "Data de nascimento do responsável obrigatória.",
				path: ['guardianBirthdate']
			})
		}
	}))

const { values, setValues, handleSubmit, setErrors } = useForm({
	validationSchema: formSchema
})

const onSubmit = handleSubmit((formValues) => {
	console.log(formValues)

	const inertiaForm = inertiaUseForm(formValues)

	inertiaForm.post(route('customers.store'), {
		onError: (errors) => {
			setErrors(errors)
		}
	})
})

const discountsArray = useFieldArray('discounts')
const discountsPushValue = (discount: Discount) => discountsArray.push(discount)
const discountsRemoveValue = (index: number) => discountsArray.remove(index)

const servicesRef = ref<Service[]>(props.services)
const updateServicesRef = (services: Service[]) => {
	servicesRef.value = services
	console.log(services)
}
const getServicesWithoutDiscount = (services: Service[]) => {
	return services.filter((service) => {
		return !discountsArray.fields.value.some((field) => field.value.service === service.name)
	})
}

const removeDiscount = (index: number) => {
	discountsArray.remove(index)
	updateServicesRef(getServicesWithoutDiscount(props.services))
}
</script>
<template>
	<AppLayout title="Criar cliente">
		<PageContainer>
			<div>
				<Stethoscope class="w-10 h-10 mb-6"/>

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Clientes
						</h2>

						<p class="text-muted-foreground">
							Cadastrar um cliente.
						</p>
					</div>
				</div>
			</div>

			<div class="pt-4">
				<form @submit="onSubmit" class="space-y-4">
					<div class="gird gap-6">
						<div class="grid grid-cols-3 gap-8">
							<FormField v-slot="{ componentField }" name="name">
								<FormItem v-auto-animate>
									<FormLabel>Nome</FormLabel>
									<FormControl>
										<Input type="text" placeholder="Nome" v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="cpf">
								<FormItem v-auto-animate>
									<FormLabel>CPF</FormLabel>
									<FormControl>
										<Input type="text" v-maska inputmode="numeric" data-maska="###.###.###-##" placeholder="CPF"
											   v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField, value }" name="birthdate">
								<FormItem v-auto-animate class="flex flex-col pt-2.5">
									<FormLabel>Data de nascimento</FormLabel>
									<Popover>
										<PopoverTrigger as-child>
											<FormControl>
												<Button variant="outline"
														:class="cn('w-full ps-3 text-start font-normal', !value && 'text-muted-foreground')">
													<span>{{ value ? inputDate(value) : "Selecione uma data" }}</span>
													<CalendarIcon class="ms-auto h-4 w-4 opacity-50"/>
												</Button>
											</FormControl>
										</PopoverTrigger>
										<PopoverContent class="p-0">
											<Calendar v-bind="componentField"/>
										</PopoverContent>
									</Popover>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>

						<Separator class="my-6"/>

						<div class="grid grid-cols-1 gap-8">
							<FormField v-slot="{ value, handleChange }" type="checkbox" name="hasGuardian">
								<FormItem v-auto-animate class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
									<FormControl>
										<Checkbox :checked="value" @update:checked="handleChange"/>
									</FormControl>
									<div class="space-y-1 leading-none">
										<FormLabel>Possui responsável?</FormLabel>
										<FormDescription>
											Caso o cliente tenha um responsável, marque esta opção e preencha os campos sobre ele.
										</FormDescription>
										<FormMessage/>
									</div>
								</FormItem>
							</FormField>
						</div>

						<div v-if="values.hasGuardian">
							<Separator class="my-6"/>

							<div class="grid grid-cols-3 gap-8">
								<FormField v-slot="{ componentField }" name="guardianName">
									<FormItem v-auto-animate>
										<FormLabel>Nome do responsável</FormLabel>
										<FormControl>
											<Input type="text" placeholder="Nome" v-bind="componentField" key="guardianName"/>
										</FormControl>
										<FormMessage/>
									</FormItem>
								</FormField>

								<FormField v-slot="{ componentField }" name="guardianCPF">
									<FormItem v-auto-animate>
										<FormLabel>CPF do responsável</FormLabel>
										<FormControl>
											<Input type="text" v-maska inputmode="numeric" data-maska="###.###.###-##" placeholder="CPF"
												   v-bind="componentField" key="guardianCPF"/>
										</FormControl>
										<FormMessage/>
									</FormItem>
								</FormField>

								<FormField v-slot="{ componentField, value }" name="guardianBirthdate">
									<FormItem v-auto-animate class="flex flex-col pt-2.5">
										<FormLabel>Data de nascimento do responsável</FormLabel>
										<Popover>
											<PopoverTrigger as-child>
												<FormControl>
													<Button variant="outline"
															:class="cn('w-full ps-3 text-start font-normal', !value && 'text-muted-foreground')"
															key="guardianBirthdate">
														<span>{{ value ? inputDate(value) : "Selecione uma data" }}</span>
														<CalendarIcon class="ms-auto h-4 w-4 opacity-50"/>
													</Button>
												</FormControl>
											</PopoverTrigger>
											<PopoverContent class="p-0">
												<Calendar v-bind="componentField"/>
											</PopoverContent>
										</Popover>
										<FormMessage/>
									</FormItem>
								</FormField>
							</div>
						</div>

						<Separator class="my-6"/>

						<div class="grid grid-cols-1 gap-8">
							<FormField v-slot="{ value, handleChange }" type="checkbox" name="hasDiscounts">
								<FormItem v-auto-animate class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
									<FormControl>
										<Checkbox :checked="value" @update:checked="handleChange"/>
									</FormControl>
									<div class="space-y-1 leading-none">
										<FormLabel>Possui descontos?</FormLabel>
										<FormDescription>
											Caso o cliente tenha desconto em um ou mais <strong>serviços</strong>, marque esta opção e
											defina os descontos.
										</FormDescription>
										<FormMessage/>
									</div>
								</FormItem>
							</FormField>
						</div>

						<div v-if="values.hasDiscounts">
							<Separator class="my-6"/>

							<AddDiscountForm :services="servicesRef"
											 :get-services-without-discount="getServicesWithoutDiscount"
											 :update-services="updateServicesRef"
											 :push-discount="discountsPushValue"
											 :remove-discount="discountsRemoveValue"
											 :key="servicesRef" />

							<FieldArray name="discounts" v-slot="{ fields, push, remove }">
								<div v-for="(field, idx) in fields" :key="field.key" class="grid grid-cols-5 gap-8 mt-6">
									<div class="col-span-2">
										<FormField v-slot="{ componentField }" :name="`discounts[${idx}].service`">
											<FormItem v-auto-animate>
												<FormLabel>Serviço</FormLabel>
												<FormControl>
													<Input type="text" v-bind="componentField" disabled/>
												</FormControl>
												<FormMessage/>
											</FormItem>
										</FormField>
									</div>

									<div class="col-span-2">
										<FormField v-slot="{ componentField }" :name="`discounts[${idx}].discount`">
											<FormItem v-auto-animate>
												<FormLabel>Desconto</FormLabel>
												<div class="flex items-center gap-4">
													<FormControl>
														<div class="relative w-full max-w-sm items-center">
															<Input type="number" placeholder="Desconto" v-bind="componentField" class="pl-8"/>
															<span class="absolute start-0 inset-y-0 flex items-center justify-center px-2">
																<Percent class="size-4 text-muted-foreground"/>
															</span>
														</div>
													</FormControl>
													<Button @click="removeDiscount(idx)" type="button" size="icon" variant="destructive">
														<Minus class="size-6"/>
													</Button>
												</div>
												<FormMessage/>
											</FormItem>
										</FormField>
									</div>
								</div>
							</FieldArray>
						</div>
					</div>

					<div class="flex justify-end pt-4">
						<Button type="submit">
							Salvar
						</Button>
					</div>
				</form>
			</div>
		</PageContainer>
	</AppLayout>
</template>