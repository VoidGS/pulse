<script lang="ts" setup>
import { ChevronRight } from "lucide-vue-next";
import type { Menu } from "@/Layouts/AppLayout.vue";
import { cn } from "@/lib/utils";

const props = defineProps<{
	menu: Menu[]
}>()

const includeRoute = (route: string | undefined) => {
	return route ? location.pathname.includes(route) : false
}

const menuList = props.menu.filter((item) => {
	return item.showOnBreadcrumb && includeRoute(item.breadcrumbRoute)
})

const lastElement = (index: number, array: any[]) => {
	return index + 1 === array.length
}

const displayName = (item: Menu) => {
	return item.breadcrumbName ?? item.name
}

const displayNameBool = (item: Menu) => {
	return item.breadcrumbName !== undefined ? (item.breadcrumbName !== '') : (item.name !== '')
}

const itemUrl = (index: number, array: any[], item: Menu) => {
	return lastElement(index, array) ? '#' : item.url
}
</script>

<template>
	<nav class="hidden md:flex justify-between px-3.5 py-1 border rounded-lg bg-background shadow-sm mb-8">
		<ol class="inline-flex items-center mb-3 space-x-1 text-xs text-neutral-500 [&_.active-breadcrumb]:text-neutral-600 [&_.active-breadcrumb]:font-bold [&_.active-breadcrumb]:cursor-default md:mb-0">
			<template v-for="(item, index) in menuList" :key="item.name">
				<template class="flex items-center">
					<li>
						<a :href="itemUrl(index, menuList, item)" :class="cn('inline-flex items-center px-2 py-1.5 space-x-1.5 font-medium rounded-md hover:text-neutral-900 hover:bg-neutral-100 focus:outline-none', lastElement(index, menuList) ? 'active-breadcrumb' : '')">
							<component v-if="item.breadcrumbIcon" :is="item.breadcrumbIcon" class="w-4 h-4" strokeWidth="2.8" />
							<span v-if="displayNameBool(item)">{{ displayName(item) }}</span>
						</a>
					</li>
					<ChevronRight v-if="!lastElement(index, menuList)" class="w-4 h-4 text-gray-400" strokeWidth="3"/>
				</template>
			</template>
		</ol>
	</nav>
</template>