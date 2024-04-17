<script lang="ts" setup>
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from "@/Components/ui/dialog";
import { Input } from "@/Components/ui/input";
import { Separator } from "@/Components/ui/separator";
import { Button } from "@/Components/ui/button";
import { Plus } from "lucide-vue-next";
import type { Guardian } from "@/Pages/Customers/data/schema";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { unmaskCPF, validateCPF, validatePhone, validateServerGuardianCPF } from "@/Utilities/utils";
import { formatDate } from "@/Utilities/date";
import { vMaska } from "maska";
import { useForm } from "vee-validate";
import { ref } from "vue";

const props = defineProps<{
	pushGuardian: (guardian: Guardian) => void,
}>()

const openCreateGuardian = ref(false)

const guardianFormSchema = toTypedSchema(z.object({
	name: z.string().min(2).max(255),
	cpf: z.string().refine((cpf: string) => validateCPF(cpf), "Digite um CPF válido.").refine(async (cpf: string) => validateServerGuardianCPF(cpf), "Este CPF já está em uso.").transform((cpf: string) => unmaskCPF((cpf))),
	birthdate: z.preprocess((date) => formatDate(date), z.coerce.date({ required_error: "Data de nascimento obrigatória." }).max(new Date())),
	phone: z.string().refine((cellphone) => validatePhone(cellphone), "Digite um telefone válido."),
	email: z.string().email(),
}).partial()
	.superRefine(({ name, cpf, birthdate, phone, email }, ctx) => {
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

		if (!cpf) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "O CPF é obrigatório.",
				path: ['cpf']
			})
		}

		if (!phone) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "O telefone é obrigatório.",
				path: ['phone']
			})
		}

		if (!email) {
			ctx.addIssue({
				code: z.ZodIssueCode.custom,
				message: "O email é obrigatório.",
				path: ['email']
			})
		}
	}))

const { values, setValues, handleSubmit, resetForm } = useForm({
	validationSchema: guardianFormSchema
})

const guardianOnSubmit = handleSubmit((formValues) => {
	console.log(formValues.cpf, values.cpf)

	props.pushGuardian(formValues)
	openCreateGuardian.value = false;
	resetForm()
})
</script>
<template>
	<Dialog v-model:open="openCreateGuardian">
		<DialogTrigger as-child>
			<Button variant="secondary" type="button">
				<Plus class="w-5 h-5 mr-2"/> Adicionar responsável
			</Button>
		</DialogTrigger>
		<DialogContent class="max-w-5xl">
			<DialogHeader>
				<DialogTitle>Adicionar responsável</DialogTitle>
				<DialogDescription>
					Ao cadastrar um responsável, você poderá vincula-lo em outros clientes, caso necessário.
				</DialogDescription>
			</DialogHeader>

			<Separator />

			<form @submit="guardianOnSubmit" class="space-y-4">
				<div class="grid gap-6 py-4">
					<div class="grid grid-cols-3 gap-6">
						<FormField v-slot="{ componentField }" name="name">
							<FormItem>
								<FormLabel>Nome do responsável</FormLabel>
								<FormControl>
									<Input type="text" placeholder="Nome" v-bind="componentField" key="guardian_name"/>
								</FormControl>
								<FormMessage/>
							</FormItem>
						</FormField>

						<FormField v-slot="{ componentField }" name="cpf">
							<FormItem>
								<FormLabel>CPF do responsável</FormLabel>
								<FormControl>
									<Input type="text" v-maska inputmode="numeric" data-maska="###.###.###-##" placeholder="CPF"
										   v-bind="componentField" key="guardian_cpf"/>
								</FormControl>
								<FormMessage/>
							</FormItem>
						</FormField>

						<FormField v-slot="{ componentField }" name="birthdate">
							<FormItem class="flex flex-col pt-2.5">
								<FormLabel>Data de nascimento do responsável</FormLabel>
								<FormControl>
									<Input type="text" placeholder="dd/mm/aaaa" v-maska
										   data-maska="##/##/####" v-bind="componentField"/>
								</FormControl>
								<FormMessage/>
							</FormItem>
						</FormField>
					</div>

					<div class="grid grid-cols-2 gap-6">
						<FormField v-slot="{ componentField }" name="phone">
							<FormItem>
								<FormLabel>Telefone do responsável</FormLabel>
								<FormControl>
									<Input type="text" placeholder="(99) 99999-9999" v-maska inputmode="numeric" data-maska="(##) #####-####"
										   v-bind="componentField" key="guardian_phone"/>
								</FormControl>
								<FormMessage/>
							</FormItem>
						</FormField>

						<FormField v-slot="{ componentField }" name="email">
							<FormItem>
								<FormLabel>Email do responsável</FormLabel>
								<FormControl>
									<Input type="text" placeholder="email@gmail.com" v-bind="componentField" key="guardian_email"/>
								</FormControl>
								<FormMessage/>
							</FormItem>
						</FormField>
					</div>
				</div>

				<Separator />

				<DialogFooter>
					<Button type="submit">
						Salvar
					</Button>
				</DialogFooter>
			</form>
		</DialogContent>
	</Dialog>
</template>