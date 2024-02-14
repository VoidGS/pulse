<script lang="ts" setup>

import AppLayout from "@/Layouts/AppLayout.vue";
import PageContainer from "@/Components/PageContainer.vue";
import { Button } from "@/Components/ui/button";
import { Plus } from "lucide-vue-next";
import { columns } from "@/Pages/Services/data/columns";
import { columnsView } from "@/Pages/Services/data/schema";
import DataTable from "@/Components/DataTable/DataTable.vue";
import { onMounted, ref } from "vue";
import type { User } from "@/Pages/Users/data/schema";

const props = defineProps(['services'])

const data = ref<User[]>([])

async function getData(): Promise<User[]> {
	return props.services
}

onMounted(async () => {
	data.value = await getData();
})
</script>

<template>
	<AppLayout title="Serviços">
		<PageContainer>
			<div>
				<img src="/emojis/briefcase.png" class="w-10 h-10 mb-6" alt="Emoji de maleta">

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Serviços
						</h2>

						<p class="text-muted-foreground">
							Listagem de serviços.
						</p>
					</div>

					<div>
						<Button as-child>
							<a>
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