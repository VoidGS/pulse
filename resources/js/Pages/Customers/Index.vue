<script lang="ts" setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import Stethoscope from "@/Components/Emojis/Stethoscope.vue";
import { Plus } from "lucide-vue-next";
import { Button } from "@/Components/ui/button";
import { usePage } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import DataTable from "@/Components/DataTable/DataTable.vue";
import { onMounted, ref } from "vue";
import { columnsView, type Customer } from "@/Pages/Customers/data/schema";
import { columns } from "@/Pages/Customers/data/columns";

const props = defineProps(['customers']);

const data = ref<Customer[]>([])

async function getData(): Promise<Customer[]> {
	return props.customers
}

onMounted(async () => {
	data.value = await getData();
})

const canCreateCustomer = usePage().props.user_permissions.create_customers
const createCustomerRoute = route('customers.create');
</script>
<template>
	<AppLayout title="Clientes">
		<PageContainer>
			<div>
				<Stethoscope class="w-10 h-10 mb-6" />

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Clientes
						</h2>

						<p class="text-muted-foreground">
							Listagem de clientes.
						</p>
					</div>

					<div v-if="canCreateCustomer">
						<Button as-child>
							<a :href="createCustomerRoute">
								<Plus class="w-5 h-5 mr-2"/>
								Cadastrar cliente
							</a>
						</Button>
					</div>
				</div>
			</div>

			<div class="space-y-4">
				<DataTable :data="data" :columns="columns" :filter-column="{value: 'name', name: 'nome'}" :view-columns="columnsView" />
			</div>
		</PageContainer>
	</AppLayout>
</template>