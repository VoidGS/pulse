<script lang="ts" setup>
import { type Component, computed, ref } from 'vue';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import { Button } from "@/Components/ui/button";
import { Command, CommandEmpty, CommandGroup, CommandItem, CommandInput, CommandList } from "@/Components/ui/command";
import { Popover, PopoverContent, PopoverTrigger } from "@/Components/ui/popover";
import { FormControl } from "@/Components/ui/form";

interface ModelOptions {
	selectMessage?: string
	searchMessage?: string
	emptyMessage?: string
}

interface ModelKeys {
	id: string
	label: string
	icon?: string
}

const props = defineProps<{
	items: [{}],
	itemsKeys: ModelKeys
	itemSelected: string
	itemSetValue: (value: any) => void
	options?: ModelOptions
}>()

interface ModelValue {
	id: string
	label: string
	icon?: Component | undefined
}

function remapArray(values: [{}], keys: ModelKeys): ModelValue[] {
	return values.map((item) => {
		const { id, label, icon } = keys;
		return {
			id: item[id].toString(),
			label: item[label].toString(),
			icon: item[icon] ?? undefined
		}
	})
}

const itemsValues = remapArray(props.items, props.itemsKeys);

const selectMessage = computed(() => props.options?.selectMessage ?? "Selecione...")
const searchMessage = computed(() => props.options?.searchMessage ?? "Pesquise...")
const emptyMessage = computed(() => props.options?.emptyMessage ?? "Sem dados.")

const open = ref(false)

const filterFunction = (list: typeof itemsValues, search: string) => list.filter(i => i.label.toLowerCase().includes(search.toLowerCase()))
</script>

<template>
	<Popover v-model:open="open">
		<PopoverTrigger as-child>
			<FormControl>
				<Button variant="outline" role="combobox" :aria-expanded="open"
						:class="cn('w-full justify-between', props.itemSelected ? '' : 'text-muted-foreground')">
					{{ props.itemSelected ? itemsValues.find((model) => model.id === props.itemSelected)?.label : selectMessage }}
					<ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50"/>
				</Button>
			</FormControl>
		</PopoverTrigger>
		<PopoverContent class="p-0">
			<Command :filter-function="filterFunction">
				<CommandInput class="border-none p-0 focus:ring-0" :placeholder="searchMessage"/>
				<CommandEmpty>{{ emptyMessage }}</CommandEmpty>
				<CommandList>
					<CommandGroup>
						<CommandItem v-for="model in itemsValues"
									 :key="model.id"
									 :value="model"
									 @select="(ev) => {
										 props.itemSetValue(ev.detail.value.id)
										 open = false
									 }">
							{{ model.label }}
							<Check :class="cn('ml-auto h-4 w-4', props.itemSelected === model.id ? 'opacity-100' : 'opacity-0')"/>
						</CommandItem>
					</CommandGroup>
				</CommandList>
			</Command>
		</PopoverContent>
	</Popover>
</template>