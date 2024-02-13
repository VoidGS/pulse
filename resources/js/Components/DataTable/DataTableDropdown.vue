<script lang="ts" setup>
import { MoreHorizontal } from "lucide-vue-next";
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuLabel, DropdownMenuTrigger } from "@/Components/ui/dropdown-menu";
import { Button } from "@/Components/ui/button";

defineProps<{
	payment: {
		id: string
	}
}>()

function copy(id: string) {
	navigator.clipboard.writeText(id)
}
</script>

<template>
	<DropdownMenu>
		<DropdownMenuTrigger as-child>
			<Button variant="ghost" class="w-8 h-8 p-0">
				<span class="sr-only">Open menu</span>
				<MoreHorizontal class="w-4 h-4" />
			</Button>
		</DropdownMenuTrigger>
		<DropdownMenuContent align="end">
			<DropdownMenuLabel>Actions</DropdownMenuLabel>
			<DropdownMenuItem>View customer</DropdownMenuItem>
			<DropdownMenuItem>View payment details</DropdownMenuItem>
		</DropdownMenuContent>
	</DropdownMenu>
</template>