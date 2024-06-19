<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import Stethoscope from "@/Components/Emojis/Stethoscope.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { unmaskCPF, validateCPF, validatePhone } from "@/Utilities/utils";
import { FieldArray, useFieldArray, useForm } from "vee-validate";
import { useForm as inertiaUseForm } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import { FormControl, FormDescription, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { Input } from "@/Components/ui/input";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import { vMaska } from "maska";
import { Button } from "@/Components/ui/button";
import { formatDate } from "@/Utilities/date";
import { Minus, Percent } from "lucide-vue-next";
import { Checkbox } from "@/Components/ui/checkbox";
import { Separator } from "@/Components/ui/separator";
import type { Service } from "@/Pages/Services/Data/schema";
import AddDiscountForm from "@/Pages/Customers/Partials/AddDiscountForm.vue";
import type { Discount, Guardian } from "@/Pages/Customers/Data/schema";
import { ref } from "vue";
import CreateGuardianDialog from "@/Pages/Customers/Partials/CreateGuardianDialog.vue";
import LinkGuardianDialog from "@/Pages/Customers/Partials/LinkGuardianDialog.vue";
import { showToast } from "@/Utilities/toast";

const props = defineProps<{
	services: Service[]
}>()

const formSchema = toTypedSchema(z.object({
	name: z.string().min(2).max(255),
	cpf: z.string().refine((cpf: string) => validateCPF(cpf), "Digite um CPF válido.").transform((cpf: string) => unmaskCPF((cpf))).optional(),
	birthdate: z.preprocess((date) => formatDate(date), z.coerce.date({ required_error: "Data de nascimento obrigatória." }).max(new Date())),
	phone: z.string().refine((cellphone) => validatePhone(cellphone), "Digite um telefone válido.").optional(),
	email: z.string().email().optional(),
	hasGuardians: z.boolean().default(false),
	guardians: z.array(z.object({
		id: z.number().optional(),
		name: z.string().min(2).max(255),
		cpf: z.string(),
		birthdate: z.coerce.date().max(new Date()),
		phone: z.string(),
		email: z.string().email(),
	})),
	hasDiscounts: z.boolean().default(false),
	discounts: z.array(z.object({
		service: z.number().min(1),
		discount: z.coerce.number().min(1).max(100)
	})),
}).partial()
	.superRefine(({ name, cpf, birthdate, phone, email, hasGuardians, guardians }, ctx) => {
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
				message: "A data de nascimento é obritagória.",
				path: ['birthdate']
			})
		}

		if (!hasGuardians && !cpf) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "CPF obrigatório caso não tenha responsável.",
				path: ['cpf']
			})
		}

		if (!hasGuardians && !phone) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "Telefone obrigatório caso não tenha responsável.",
				path: ['phone']
			})
		}

		if (!hasGuardians && !email) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "Email obrigatório caso não tenha responsável.",
				path: ['email']
			})
		}
	}))

const { values, setValues, handleSubmit, setErrors } = useForm({
	validationSchema: formSchema
})

const onSubmit = handleSubmit((formValues) => {
	// console.log(formValues)

	const inertiaForm = inertiaUseForm(formValues)

	inertiaForm.post(route('customers.store'), {
		onError: (errors) => {
			setErrors(errors)
		}
	})
})

const discountsArray = useFieldArray('discounts')
const guardiansArray = useFieldArray('guardians')
const discountsPushValue = (discount: Discount) => discountsArray.push(discount)
const guardiansPushValue = (guardian: Guardian) => {
	let isDuplicated = guardiansArray.fields.value.some((guardianElement) => {
		return guardianElement.value.cpf === guardian.cpf
	})

	if (isDuplicated) return showToast("Este responsável já está vinculado neste cliente.", 'warn')

	guardiansArray.push(guardian)
}
const removeDiscount = (index: number) => {
	discountsArray.remove(index)
	updateServicesRef(getServicesWithoutDiscount(props.services))
}
const removeGuardian = (index: number) => guardiansArray.remove(index)

const servicesRef = ref<Service[]>(props.services)
const updateServicesRef = (services: Service[]) => {
	servicesRef.value = services
	console.log(services)
}
const getServicesWithoutDiscount = (services: Service[]) => {
	return services.filter((service) => {
		return !discountsArray.fields.value.some((field) => field.value.service === service.id)
	})
}

const getServiceName = (serviceId: number) => {
	return props.services.find((service) => service.id === serviceId)?.name
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
									<FormControl>
										<Input type="text" placeholder="dd/mm/aaaa" v-maska inputmode="numeric" data-maska="##/##/####"
											   v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>

						<div class="grid grid-cols-2 gap-8 mt-6">
							<FormField v-slot="{ componentField }" name="phone">
								<FormItem v-auto-animate>
									<FormLabel>Telefone</FormLabel>
									<FormControl>
										<Input type="text" placeholder="(99) 99999-9999" v-maska inputmode="numeric"
											   data-maska="(##) #####-####"
											   v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="email">
								<FormItem v-auto-animate>
									<FormLabel>Email</FormLabel>
									<FormControl>
										<Input type="text" placeholder="email@gmail.com" v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>

						<Separator class="my-6"/>

						<div class="grid grid-cols-1 gap-8">
							<FormField v-slot="{ value, handleChange }" type="checkbox" name="hasGuardians">
								<FormItem v-auto-animate class="flex flex-row items-start space-x-3 space-y-0 rounded-md border p-4">
									<FormControl>
										<Checkbox :checked="value" @update:checked="handleChange"/>
									</FormControl>
									<div class="space-y-1 leading-none">
										<FormLabel>Possui responsáveis?</FormLabel>
										<FormDescription>
											Caso o cliente tenha responsáveis, marque esta opção e preencha os campos sobre eles.
										</FormDescription>
										<FormMessage/>
									</div>
								</FormItem>
							</FormField>
						</div>

						<div v-if="values.hasGuardians">
							<Separator class="my-6"/>

							<div class="grid grid-cols-3 lg:grid-cols-4 gap-4">
								<LinkGuardianDialog :push-guardian="guardiansPushValue"/>

								<CreateGuardianDialog :push-guardian="guardiansPushValue"/>
							</div>

							<FieldArray name="guardians" v-slot="{ fields, push, remove }">
								<div v-for="(field, idx) in fields" :key="field.key" class="grid grid-cols-5 gap-8 mt-6">
									<div class="col-span-2">
										<FormField v-slot="{ componentField, value }" :name="`guardians[${idx}].name`">
											<FormItem v-auto-animate>
												<FormLabel>Nome do responsável</FormLabel>
												<FormControl>
													<Input type="text" v-bind="componentField" disabled/>
												</FormControl>
												<FormMessage/>
											</FormItem>
										</FormField>
									</div>

									<div class="col-span-2">
										<FormField v-slot="{ componentField }" :name="`guardians[${idx}].cpf`">
											<FormItem v-auto-animate>
												<FormLabel>CPF do responsável</FormLabel>
												<div class="flex items-center gap-4">
													<FormControl>
														<div class="relative w-full max-w-sm items-center">
															<Input type="text" v-maska data-maska="###.###.###-##" v-bind="componentField"
																   disabled/>
														</div>
													</FormControl>
													<Button @click="removeGuardian(idx)" type="button" size="icon" variant="destructive">
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
											 :key="servicesRef"/>

							<FieldArray name="discounts" v-slot="{ fields, push, remove }">
								<div v-for="(field, idx) in fields" :key="field.key" class="grid grid-cols-5 gap-8 mt-6">
									<div class="col-span-2">
										<FormField v-slot="{ componentField, value }" :name="`discounts[${idx}].service`">
											<FormItem v-auto-animate>
												<FormLabel>Serviço</FormLabel>
												<FormControl>
													<Input type="hidden" v-bind="componentField"/>
													<input
														class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
														type="text" :value="getServiceName(value)" disabled/>
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
															<Input type="number" placeholder="Desconto" v-bind="componentField"
																   class="pl-8"/>
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