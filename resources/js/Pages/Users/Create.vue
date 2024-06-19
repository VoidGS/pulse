<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import IdentificationCard from "@/Components/Emojis/IdentificationCard.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { useForm } from "vee-validate";
import { useForm as inertiaUseForm, usePage } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import type { Role } from "@/Pages/Users/Data/schema";
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import { Input } from "@/Components/ui/input";
import Combobox from "@/Components/Combobox.vue";
import { Button } from "@/Components/ui/button";
import PasswordInput from "@/Components/PasswordInput.vue";

const props = defineProps<{
	roles: Role[],
}>()

const formSchema = toTypedSchema(z.object({
	name: z.string().min(2).max(255),
	email: z.string().email().max(255),
	password: z.string().min(8),
	password_confirmation: z.string().min(8),
	role: z.string().min(1, 'Selecione um cargo'),
}).refine((data) => data.password === data.password_confirmation, {
	message: "As senhas devem ser iguais",
	path: ['password_confirmation']
}))

const { values, setValues, handleSubmit, setErrors } = useForm({
	initialValues: {
		name: '',
		email: '',
		password: '',
		password_confirmation: '',
		role: ''
	},
	validationSchema: formSchema
})

const onSubmit = handleSubmit((formValues) => {
	// console.log(formValues)

	const inertiaForm = inertiaUseForm(formValues)

	inertiaForm.post(route('users.store'), {
		onError: (errors) => {
			setErrors(errors)
		}
	})
})

const rolesSetValue = (value) => setValues({ role: value })
const rolesComboboxArrayKeys = { id: 'name', label: 'name' }
const rolesComboboxOptions = { searchMessage: 'Pesquise um cargo...', selectMessage: 'Selecione um cargo...' }
</script>
<template>
	<AppLayout title="Criar usuário">
		<PageContainer>
			<div>
				<IdentificationCard class="w-10 h-10 mb-6"/>

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Usuários
						</h2>

						<p class="text-muted-foreground">
							Cadastrar um usuário.
						</p>
					</div>
				</div>
			</div>

			<div class="pt-4">
				<form @submit="onSubmit" class="space-y-4">
					<div class="grid gap-6">
						<div class="grid grid-cols-2 gap-8">
							<FormField v-slot="{ componentField }" name="name">
								<FormItem v-auto-animate>
									<FormLabel>Nome</FormLabel>
									<FormControl>
										<Input type="text" placeholder="Nome de usuário" v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="email">
								<FormItem v-auto-animate>
									<FormLabel>Email</FormLabel>
									<FormControl>
										<Input type="text" placeholder="Email do usuário" v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>

						<div class="grid grid-cols-3 gap-8">
							<FormField v-slot="{ componentField }" name="role">
								<FormItem v-auto-animate>
									<FormLabel>Cargo</FormLabel>
									<Combobox :items="props.roles"
											  :items-keys="rolesComboboxArrayKeys"
											  :options="rolesComboboxOptions"
											  :item-selected="values.role.toString()"
											  :item-set-value="rolesSetValue"/>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="password">
								<FormItem v-auto-animate>
									<FormLabel>Senha</FormLabel>
									<FormControl>
										<PasswordInput placeholder="Senha do usuário" :component-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="password_confirmation">
								<FormItem v-auto-animate>
									<FormLabel>Confirmação da senha</FormLabel>
									<FormControl>
										<PasswordInput placeholder="Confirmação da senha" :component-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>
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