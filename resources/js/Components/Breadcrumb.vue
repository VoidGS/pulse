<script lang="ts" setup>
import { Home, ChevronRight, Settings, UsersRound, UserRound } from "lucide-vue-next";
</script>

<template>
	<nav class="hidden md:flex justify-between px-3.5 py-1 border rounded-lg bg-background shadow-sm mb-8">
		<ol class="inline-flex items-center mb-3 space-x-1 text-xs text-neutral-500 [&_.active-breadcrumb]:text-neutral-600 [&_.active-breadcrumb]:font-bold [&_.active-breadcrumb]:cursor-default sm:mb-0">
			<li>
				<a href="#_"
				   class="inline-flex items-center px-2 py-1.5 space-x-1.5 font-medium rounded-md hover:text-neutral-900 hover:bg-neutral-100">
					<Home class="w-4 h-4" strokeWidth="2.8"/>
				</a>
			</li>
			<ChevronRight class="w-4 h-4 text-gray-400" strokeWidth="3"/>
			<li>
				<a href="#_"
				   class="inline-flex items-center px-2 py-1.5 space-x-1.5 font-medium rounded-md hover:bg-neutral-100 hover:text-neutral-900 focus:outline-none active-breadcrumb">
					<UsersRound class="w-4 h-4" strokeWidth="2.8"/>
					<span>Usuários</span>
				</a>
			</li>
			<!--<ChevronRight class="w-4 h-4 text-gray-400" strokeWidth="3"/>-->
			<!--<li>-->
			<!--	<a class="inline-flex items-center px-2 py-1.5 space-x-1.5 font-medium rounded-md hover:bg-neutral-100 hover:text-neutral-900 focus:outline-none active-breadcrumb">-->
			<!--		<UserRound class="w-4 h-4" strokeWidth="2.8"/>-->
			<!--		<span>Adam Wathan</span>-->
			<!--	</a>-->
			<!--</li>-->
		</ol>
	</nav>
</template>