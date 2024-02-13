<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { route } from "momentum-trail";
import { Button } from "@/Components/ui/button";
import { Plus } from "lucide-vue-next";
import { ref, onMounted, computed } from "vue";
import { columns } from "@/Pages/Users/data/columns";
import { columnsView, type User } from "@/Pages/Users/data/schema";
import DataTable from "@/Components/DataTable/DataTable.vue";

const props = defineProps(['users']);

const data = ref<User[]>([])

async function getData(): Promise<User[]> {
	return props.users
}

onMounted(async () => {
	data.value = await getData();
})

const createUserRoute = route('users.create');
const canCreateUser = computed(() => props.users[0].can?.create)
</script>
<template>
	<AppLayout title="Usuários">
		<div class="overflow-hidden rounded-lg border bg-background shadow-md md:shadow-xl">
			<div class="hidden h-full flex-1 flex-col space-y-2 p-8 md:flex">
				<div>
					<img src="/emojis/open-file-folder.png" class="w-10 h-10 mb-6" alt="Emoji pasta aberta">
					<!--<FolderOpen class="w-10 h-10 mb-6" />-->

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
			</div>
		</div>
	</AppLayout>
</template>