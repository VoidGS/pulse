<script lang="ts" setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import { Button } from "@/Components/ui/button";
import { Plus } from "lucide-vue-next";
import { columns } from "@/Pages/Services/data/columns";
import { columnsView, type Service } from "@/Pages/Services/data/schema";
import DataTable from "@/Components/DataTable/DataTable.vue";
import { onMounted, ref } from "vue";
import { usePage } from "@inertiajs/vue3";
import { route } from "momentum-trail";
import Briefcase from "@/Components/Emojis/Briefcase.vue";

const props = defineProps<{
	services: Service[]
}>()

const data = ref<Service[]>([])

async function getData(): Promise<Service[]> {
	return props.services
}

onMounted(async () => {
	data.value = await getData();
})

const canCreateService = usePage().props.user_permissions.create_services
const createServiceRoute = route('services.create');
</script>

<template>
	<AppLayout title="Serviços">
		<PageContainer>
			<div>
				<Briefcase class="w-10 h-10 mb-6" />

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Serviços
						</h2>

						<p class="text-muted-foreground">
							Listagem de serviços.
						</p>
					</div>

					<div v-if="canCreateService">
						<Button as-child>
							<a :href="createServiceRoute">
								<Plus class="w-5 h-5 mr-2"/>
								Cadastrar serviço
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