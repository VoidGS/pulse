<script lang="ts" setup>
import type { Column } from "@tanstack/vue-table";
import { ArrowDown, ArrowUp, ChevronsUpDown, EyeOff } from "lucide-vue-next";
import { cn } from '@/lib/utils'
import { Button } from "@/Components/ui/button";
import { DropdownMenu, DropdownMenuTrigger, DropdownMenuContent, DropdownMenuItem, DropdownMenuSeparator } from "@/Components/ui/dropdown-menu";
interface DataTableColumnHeaderProps {
	column: Column<any>
	title: string
}

const props = defineProps<DataTableColumnHeaderProps>()
</script>

<template>
	<div v-if="props.column.getCanSort()" :class="cn('flex items-center space-x-2',  <string>$attrs.class ?? '')">
		<DropdownMenu>
			<DropdownMenuTrigger as-child>
				<Button variant="ghost" size="sm" class="-ml-3 h-8 data-[state=open]:bg-accent">
					<span>{{ title }}</span>
					<ArrowDown v-if="props.column.getIsSorted() === 'desc'" class="w-4 h-4 ml-2"/>
					<ArrowUp v-else-if=" props.column.getIsSorted() === 'asc'" class="w-4 h-4 ml-2"/>
					<ChevronsUpDown v-else class="w-4 h-4 ml-2"/>
				</Button>
			</DropdownMenuTrigger>
			<DropdownMenuContent align="start">
				<DropdownMenuItem @click="props.column.toggleSorting(false)">
					<ArrowUp class="mr-2 h-3.5 w-3.5 text-muted-foreground/70"/>
					Asc
				</DropdownMenuItem>
				<DropdownMenuItem @click="props.column.toggleSorting(true)">
					<ArrowDown class="mr-2 h-3.5 w-3.5 text-muted-foreground/70"/>
					Desc
				</DropdownMenuItem>
				<DropdownMenuSeparator/>
				<DropdownMenuItem @click="props.column.toggleVisibility(false)">
					<EyeOff class="mr-2 h-3.5 w-3.5 text-muted-foreground/70"/>
					Hide
				</DropdownMenuItem>
			</DropdownMenuContent>
		</DropdownMenu>
	</div>

	<div v-else :class="$attrs.class">
		{{ title }}
	</div>
</template>