<script lang="ts" setup>
import { reactive, ref, watch } from "vue";
import axios from "axios";
import { route } from "momentum-trail";
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle, DialogTrigger } from "@/Components/ui/dialog";
import { Button } from "@/Components/ui/button";
import { Link } from "lucide-vue-next";
import { Separator } from "@/Components/ui/separator";
import { FormControl, FormField, FormItem, FormLabel, FormMessage } from "@/Components/ui/form";
import { Input } from "@/Components/ui/input";
import type { Guardian } from "@/Pages/Customers/data/schema";
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from "@/Components/ui/table";
import { Info } from 'lucide-vue-next'
import { formatCPF } from "@/Utilities/utils";
import { vMaska } from "maska";

const props = defineProps<{
	pushGuardian: (guardian: Guardian) => void,
}>()

const openSearchGuardian = ref(false)
const guardians = ref<Guardian[]>()
const maskedSearchCpf = ref('')
const cpfBoundObject = reactive({})

const searchGuardian = async (searchParam: string) => {
	await axios.get(route('guardians.index') + '?cpf=' + searchParam).then((response) => {
		if (typeof response !== 'object') return guardians.value = []

		guardians.value = response.data
	})
}

const selectGuardian = (guardian: Guardian) => {
	props.pushGuardian(guardian)
	maskedSearchCpf.value = ''
	openSearchGuardian.value = false
}

watch(cpfBoundObject, async (newSearchCpf) => {
	console.log(newSearchCpf['unmasked'])
	if (newSearchCpf['unmasked'].length < 3) return guardians.value = []

	await searchGuardian(newSearchCpf['unmasked'])
})

</script>
<template>
	<Dialog v-model:open="openSearchGuardian">
		<DialogTrigger as-child>
			<Button variant="default" type="button">
				<Link class="w-5 h-5 mr-2"/> Vincular responsável
			</Button>
		</DialogTrigger>
		<DialogContent class="max-w-3xl">
			<DialogHeader>
				<DialogTitle>Vincular responsável</DialogTitle>
				<DialogDescription>
					Busca um responsável já existente e o vincula ao cliente.
				</DialogDescription>
			</DialogHeader>

			<Separator />

			<form class="space-y-4 mb-4">
				<div class="grid gap-6 py-4">
					<div class="grid grid-cols-1 gap-6">
						<FormField v-slot="{ componentField }" name="search_cpf">
							<FormItem>
								<FormLabel>Busque pelo CPF</FormLabel>
								<FormControl>
									<Input type="text" v-maska="cpfBoundObject" inputmode="numeric" data-maska="###.###.###-##" placeholder="CPF"
										   v-bind="componentField" v-model="maskedSearchCpf" key="search_cpf"/>
								</FormControl>
								<div class="flex items-center gap-1">
									<Info color="#fff" fill="#4F94E3" />
									<span class="text-sm text-muted-foreground">
										<strong>Clique</strong> no responsável que deseja vincular
									</span>
								</div>
								<FormMessage/>
							</FormItem>
						</FormField>
					</div>
				</div>
			</form>

			<Table class="mb-4">
				<TableCaption>Lista de responsáveis encontrados</TableCaption>
				<TableHeader>
					<TableRow>
						<TableHead>Nome</TableHead>
						<TableHead>CPF</TableHead>
					</TableRow>
				</TableHeader>
				<TableBody>
					<TableRow v-for="guardian in guardians" :key="guardian.id" class="cursor-pointer" @click="selectGuardian(guardian)">
						<TableCell class="font-medium">{{ guardian.name }}</TableCell>
						<TableCell class="font-medium">{{ formatCPF(guardian.cpf) }}</TableCell>
					</TableRow>
				</TableBody>
			</Table>

			<Separator />

			<DialogFooter>
				<Button type="submit">
					Salvar
				</Button>
			</DialogFooter>
		</DialogContent>
	</Dialog>
</template>