<script lang="ts" setup>
import { type Table } from "@tanstack/vue-table";
import { Input } from "@/Components/ui/input";

interface DataTableFilterProps {
	table: Table<any>
	columnId: string
	columnName: string
}

defineProps<DataTableFilterProps>()
</script>

<template>
	<Input class="max-w-sm"
		   :placeholder="'Filtrar por ' + columnName + '...'"
		   :model-value="table.getColumn(columnId)?.getFilterValue() as string"
		   @update:model-value="table.getColumn(columnId)?.setFilterValue($event)"/>
</template>