<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from "momentum-trail";
import { Button } from "@/Components/ui/button";
import { Plus } from "lucide-vue-next";
import { ref, onMounted } from "vue";
import { columns } from "@/Pages/Users/Data/columns";
import { columnsView, type User } from "@/Pages/Users/Data/schema";
import DataTable from "@/Components/DataTable/DataTable.vue";
import PageContainer from "@/Components/PageContainer.vue";
import { usePage } from "@inertiajs/vue3";
import OpenFileFolder from "@/Components/Emojis/OpenFileFolder.vue";

const props = defineProps(['users']);

const data = ref<User[]>([])

async function getData(): Promise<User[]> {
	return props.users
}

onMounted(async () => {
	data.value = await getData();
})

const canCreateUser = usePage().props.user_permissions.create_users
const createUserRoute = route('users.create');
</script>
<template>
	<AppLayout title="Usuários">
		<PageContainer>
			<div>
				<OpenFileFolder class="w-10 h-10 mb-6" />

				<div class="flex items-center justify-between">
					<div>
						<h2 class="text-2xl font-bold tracking-tight">
							Usuários
						</h2>

						<p class="text-muted-foreground">
							Listagem de usuários.
						</p>
					</div>

					<div v-if="canCreateUser">
						<Button as-child>
							<a :href="createUserRoute">
								<Plus class="w-5 h-5 mr-2"/>
								Cadastrar usuário
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