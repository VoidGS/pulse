<script lang="ts" setup>
import { MoreHorizontal } from "lucide-vue-next";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuTrigger } from "@/Components/ui/dropdown-menu";
import { Button } from "@/Components/ui/button";
import { type Component, type HTMLAttributes, ref } from "vue";
import {
	Dialog,
	DialogClose,
	DialogContent,
	DialogFooter,
	DialogHeader,
	DialogTrigger
} from "@/Components/ui/dialog";
import PoliceCarLight from "@/Components/Emojis/PoliceCarLight.vue";

export interface DeleteDialogOptions {
	title: string
	description?: string
	content?: Component
	deleteActionName: string
	deleteAction: () => void
}

export interface DataTableActionItem {
	label: string
	href: string
	deleteDialog?: DeleteDialogOptions
	icon?: Component
	class?: HTMLAttributes['class']
	onClick?: () => void
}

const props = defineProps<{
	items: DataTableActionItem[]
}>()

const openDropdownMenu = ref(false)
const openDeleteDialog = ref(false)
const closeDropDown = (item: DataTableActionItem) => {
	if (item.deleteDialog) return
	openDropdownMenu.value = false
}

const deleteDialogAction = (item: DataTableActionItem) => {
	if (!item.deleteDialog?.deleteAction) return

	item.deleteDialog.deleteAction()
	openDeleteDialog.value = false
	openDropdownMenu.value = false
}

function copy(id: string) {
	navigator.clipboard.writeText(id)
}
</script>

<template>
	<Dialog v-model:open="openDeleteDialog">
		<DropdownMenu v-model:open="openDropdownMenu">
			<DropdownMenuTrigger as-child>
				<Button variant="ghost" class="w-8 h-8 p-0">
					<span class="sr-only">Abrir menu</span>
					<MoreHorizontal class="w-4 h-4"/>
				</Button>
			</DropdownMenuTrigger>
			<DropdownMenuContent align="end">
				<DropdownMenuLabel>Ações</DropdownMenuLabel>
				<template v-for="item in items" :key="item.label">
					<DropdownMenuItem as-child @select.prevent="closeDropDown(item)" class="flex items-center space-x-1.5 cursor-pointer">
						<template v-if="!item.deleteDialog">
							<a :href="item.href" :class="item.class"
							   @click="item.onClick ? item.onClick() : null">
								<component v-if="item.icon" :is="item.icon" class="w-3 h-3" strokeWidth="2.8"/>
								<span>{{ item.label }}</span>
							</a>
						</template>

						<template v-else>
							<DialogTrigger as-child>
								<a href="#" :class="item.class">
									<component v-if="item.icon" :is="item.icon" class="w-3 h-3" strokeWidth="2.8"/>
									<span>{{ item.label }}</span>
								</a>
							</DialogTrigger>

							<DialogContent>
								<DialogHeader>
									<div class="md:flex md:items-center">
										<div
											class="mx-auto shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 md:mx-0 md:h-10 md:w-10">
											<PoliceCarLight class="w-6 h-6"/>
										</div>

										<div class="mt-3 text-center md:mt-0 md:ms-4 md:text-start">
											<h2 class="text-lg font-semibold leading-none tracking-tight">
												{{ item.deleteDialog.title }}
											</h2>
										</div>
									</div>

									<div class="md:ml-14">
										<div class="mt-3 md:mt-0 text-sm text-gray-600 dark:text-gray-400">
											<p class="text-sm text-muted-foreground">{{ item.deleteDialog.description }}</p>
										</div>
									</div>
								</DialogHeader>
								<DialogFooter class="grid mt-4 gap-3 md:flex">
									<DialogClose as-child>
										<Button type="button" variant="secondary">
											Cancelar
										</Button>
									</DialogClose>
									<Button type="button" class="bg-red-600" variant="destructive" @click="deleteDialogAction(item)">
										{{ item.deleteDialog.deleteActionName }}
									</Button>
								</DialogFooter>
							</DialogContent>
						</template>
					</DropdownMenuItem>
				</template>
			</DropdownMenuContent>
		</DropdownMenu>
	</Dialog>
</template>