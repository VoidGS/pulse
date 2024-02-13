<script lang="ts" setup>
import { type Table } from "@tanstack/vue-table";
import { ChevronsRight, ChevronRight, ChevronsLeft, ChevronLeft } from "lucide-vue-next";
import { Button } from "@/Components/ui/button";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/Components/ui/select";

interface DataTablePaginationProps {
	table: Table<any>
}

defineProps<DataTablePaginationProps>()
</script>

<template>
	<div class="flex items-center justify-between py-4">
		<div class="flex-1 text-sm text-muted-foreground">
			{{ table.getFilteredSelectedRowModel().rows.length }} de
			{{ table.getFilteredRowModel().rows.length }} linha(s) selecionadas.
		</div>
		<div class="flex items-center space-x-6 lg:space-x-8">
			<div class="flex items-center space-x-2">
				<p class="text-sm font-medium">
					Linhas por página
				</p>
				<Select :model-value="`${table.getState().pagination.pageSize}`" @update:model-value="table.setPageSize">
					<SelectTrigger class="h-8 w-[70px]">
						<SelectValue :placeholder="`${table.getState().pagination.pageSize}`"/>
					</SelectTrigger>
					<SelectContent side="top">
						<SelectItem v-for="pageSize in [10, 20, 30, 40, 50]" :key="pageSize" :value="`${pageSize}`">
							{{ pageSize }}
						</SelectItem>
					</SelectContent>
				</Select>
			</div>
			<div class="flex w-[100px] items-center justify-center text-sm font-medium">
				Página {{ table.getState().pagination.pageIndex + 1 }} de
				{{ table.getPageCount() }}
			</div>
			<div class="flex items-center space-x-2">
				<Button variant="outline" class="hidden w-8 h-8 p-0 lg:flex" :disabled="!table.getCanPreviousPage()"
						@click="table.setPageIndex(0)">
					<span class="sr-only">Primeira página</span>
					<ChevronsLeft class="w-4 h-4"/>
				</Button>
				<Button variant="outline" class="w-8 h-8 p-0" :disabled="!table.getCanPreviousPage()" @click="table.previousPage()">
					<span class="sr-only">Página anterior</span>
					<ChevronLeft class="w-4 h-4"/>
				</Button>
				<Button variant="outline" class="w-8 h-8 p-0" :disabled="!table.getCanNextPage()" @click="table.nextPage()">
					<span class="sr-only">Próxima página</span>
					<ChevronRight class="w-4 h-4"/>
				</Button>
				<Button variant="outline" class="hidden w-8 h-8 p-0 lg:flex" :disabled="!table.getCanNextPage()"
						@click="table.setPageIndex(table.getPageCount() - 1)">
					<span class="sr-only">Última página</span>
					<ChevronsRight class="w-4 h-4"/>
				</Button>
			</div>
		</div>
	</div>
</template>