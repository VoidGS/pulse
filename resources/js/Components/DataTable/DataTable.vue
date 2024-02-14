<script lang="ts" setup>
import type { ColumnDef, SortingState, ColumnFiltersState, VisibilityState } from "@tanstack/vue-table";
import { valueUpdater } from "@/lib/utils";
import {
	FlexRender,
	getCoreRowModel,
	getPaginationRowModel,
	getSortedRowModel,
	getFilteredRowModel,
	useVueTable
} from '@tanstack/vue-table'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from "@/Components/ui/table";
import { ref } from "vue";
import DataTablePagination from "@/Components/DataTable/DataTablePagination.vue";
import DataTableViewOptions, { type ViewColumnsOptions } from "@/Components/DataTable/DataTableViewOptions.vue";
import DataTableFilter from "@/Components/DataTable/DataTableFilter.vue";

const props = defineProps<{
	columns: ColumnDef<any>[]
	data: [{}]
	filterColumn?: {
		value: string
		name: string
	}
	viewColumns?: ViewColumnsOptions[]
}>()

const sorting = ref<SortingState>([])
const columnFilters = ref<ColumnFiltersState>([])
const columnVisibility = ref<VisibilityState>({})
const rowSelection = ref({})

const table = useVueTable({
	get data() {
		return props.data
	},
	get columns() {
		return props.columns
	},
	getCoreRowModel: getCoreRowModel(),
	getPaginationRowModel: getPaginationRowModel(),
	getSortedRowModel: getSortedRowModel(),
	getFilteredRowModel: getFilteredRowModel(),
	onSortingChange: updaterOrValue => valueUpdater(updaterOrValue, sorting),
	onColumnFiltersChange: updaterOrValue => valueUpdater(updaterOrValue, columnFilters),
	onColumnVisibilityChange: updaterOrValue => valueUpdater(updaterOrValue, columnVisibility),
	onRowSelectionChange: updaterOrValue => valueUpdater(updaterOrValue, rowSelection),
	state: {
		get sorting() {
			return sorting.value
		},
		get columnFilters() {
			return columnFilters.value
		},
		get columnVisibility() {
			return columnVisibility.value
		},
		get rowSelection() {
			return rowSelection.value
		}
	}
})
</script>

<template>
	<div>
		<div class="flex items-center py-4">
			<DataTableFilter v-if="filterColumn" :column-id="filterColumn.value" :column-name="filterColumn.name" :table="table" />
			<DataTableViewOptions :table="table" :view-columns-options="viewColumns" />
		</div>
		<div class="border rounded-md">
			<Table>
				<TableHeader>
					<TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
						<TableHead v-for="header in headerGroup.headers" :key="header.id">
							<FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
										:props="header.getContext()"/>
						</TableHead>
					</TableRow>
				</TableHeader>
				<TableBody>
					<template v-if="table.getRowModel().rows?.length">
						<TableRow v-for="row in table.getRowModel().rows" :key="row.id"
								  :data-state="row.getIsSelected() ? 'selected' : undefined">
							<TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
								<FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()"/>
							</TableCell>
						</TableRow>
					</template>
					<template v-else>
						<TableRow>
							<TableCell :colSpan="columns.length" class="h-24 text-center">
								Sem dados
							</TableCell>
						</TableRow>
					</template>
				</TableBody>
			</Table>
		</div>
		<DataTablePagination :table="table" />
	</div>
</template>