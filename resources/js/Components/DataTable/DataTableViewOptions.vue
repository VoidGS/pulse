<script lang="ts" setup>
import { type Column, type Table } from "@tanstack/vue-table";
import { computed } from "vue";
import { SlidersHorizontal } from "lucide-vue-next";
import { Button } from "@/Components/ui/button";
import {
	DropdownMenu,
	DropdownMenuCheckboxItem,
	DropdownMenuContent,
	DropdownMenuLabel,
	DropdownMenuSeparator,
	DropdownMenuTrigger
} from "@/Components/ui/dropdown-menu";

export interface ViewColumnsOptions {
	id: string
	label: string
}

interface DataTableViewOptionsProps {
	table: Table<any>
	viewColumnsOptions?: ViewColumnsOptions[]
}

const props = defineProps<DataTableViewOptionsProps>()

const columns = computed(() => props.table.getAllColumns()
	.filter(column => typeof column.accessorFn !== 'undefined' && column.getCanHide()))

const columnLabel = (column: Column<any>) => {
	if (!props.viewColumnsOptions) return column.id

	const label = props.viewColumnsOptions.find(label => label.id === column.id)
	return label ? label.label : column.id
}
</script>

<template>
	<DropdownMenu>
		<DropdownMenuTrigger as-child>
			<Button variant="outline" size="sm" class="hidden h-8 ml-auto lg:flex">
				<SlidersHorizontal class="w-4 h-4 mr-2"/>
				Colunas
			</Button>
		</DropdownMenuTrigger>
		<DropdownMenuContent align="end" class="w-[150px]">
			<DropdownMenuLabel>Alternar colunas</DropdownMenuLabel>
			<DropdownMenuSeparator/>

			<DropdownMenuCheckboxItem
				v-for="column in columns"
				:key="column.id"
				class="capitalize"
				:checked="column.getIsVisible()"
				@update:checked="(value) => column.toggleVisibility(!!value)">
				{{ columnLabel(column) }}
			</DropdownMenuCheckboxItem>
		</DropdownMenuContent>
	</DropdownMenu>
</template>