<script lang="ts" setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import type { Service } from "@/Pages/Services/Data/schema";
import { Button } from "@/Components/ui/button";
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { Input } from "@/Components/ui/input";
import Combobox from "@/Components/Combobox.vue";
import CurrencyInput from "@/Components/CurrencyInput.vue";
import { toTypedSchema } from "@vee-validate/zod";
import { z } from "@/lib/pt-zod";
import { vAutoAnimate } from "@formkit/auto-animate/vue";
import { useForm } from "vee-validate";
import { useForm as inertiaUseForm } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import type { Team } from "@/Pages/Teams/Data/schema";
import type { User } from "@/Pages/Users/Data/schema";
import { onMounted } from "vue";
import Pencil from "@/Components/Emojis/Pencil.vue";

const props = defineProps<{
	service: Service,
	teams: Team[],
	users: User[]
}>()

const formSchema = toTypedSchema(z.object({
	name: z.string().min(2).max(255),
	price: z.number().min(1).max(99999),
	team: z.coerce.number({ invalid_type_error: "Escolha um setor" }).positive('Escolha um setor'),
	user: z.coerce.number({ invalid_type_error: "Escolha um responsável" }).positive('Escolha um responsável')
}))

const { values, setValues, handleSubmit, resetForm } = useForm({
	initialValues: {
		name: '',
		price: 0,
		team: 0,
		user: 0
	},
	validationSchema: formSchema
})

const onSubmit = handleSubmit((formValues) => {
	const form = inertiaUseForm(formValues)
	form.put(route('services.update', props.service))
})

const teamsSetValue = (value) => setValues({ team: value })
const teamsComboboxArrayKeys = { id: 'id', label: 'name' }
const teamsComboboxOptions = { searchMessage: 'Pesquise um setor...', selectMessage: 'Selecione um setor...' }

const usersSetValue = (value) => setValues({ user: value })
const usersComboboxArrayKeys = { id: 'id', label: 'name' }
const usersComboboxOptions = { searchMessage: 'Pesquise um responsável...', selectMessage: 'Selecione um responsável...' }

onMounted(() => {
	setValues({
		name: props.service.name,
		price: props.service.price,
		team: props.service.team.id,
		user: props.service.user.id
	})
})
</script>

<template>
	<AppLayout title="Editar serviço">
		<PageContainer>
			<div>
				<Pencil class="w-10 h-10 mb-6" />

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Serviços
						</h2>

						<p class="text-muted-foreground">
							Editar serviço.
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
										<Input type="text" placeholder="Nome do serviço" v-bind="componentField"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="price">
								<FormItem v-auto-animate>
									<FormLabel>Valor</FormLabel>
									<FormControl>
										<CurrencyInput name="price"/>
									</FormControl>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>

						<div class="grid grid-cols-2 gap-8">
							<FormField v-slot="{ componentField }" name="team">
								<FormItem v-auto-animate>
									<FormLabel>Setor</FormLabel>
									<Combobox :items="props.teams"
											  :items-keys="teamsComboboxArrayKeys"
											  :options="teamsComboboxOptions"
											  :item-selected="values.team?.toString()"
											  :item-set-value="teamsSetValue"/>
									<FormMessage/>
								</FormItem>
							</FormField>

							<FormField v-slot="{ componentField }" name="user">
								<FormItem v-auto-animate>
									<FormLabel>Responsável</FormLabel>
									<Combobox :items="props.users"
											  :items-keys="usersComboboxArrayKeys"
											  :options="usersComboboxOptions"
											  :item-selected="values.user?.toString()"
											  :item-set-value="usersSetValue"/>
									<FormMessage/>
								</FormItem>
							</FormField>
						</div>
					</div>

					<div class="flex justify-end pt-4">
						<Button type="submit">
							Editar
						</Button>
					</div>
				</form>
			</div>
		</PageContainer>
	</AppLayout>
</template>