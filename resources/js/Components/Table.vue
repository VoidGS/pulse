<template>
	<div class="overflow-hidden bg-white shadow rounded-lg">
		<ul role="list" class="divide-y divide-gray-200">
			<li v-for="user in users.data" :key="user.id">
				<Link href="#" class="block hover:bg-gray-50">
					<div class="flex items-center justify-between px-4 py-4 sm:px-6">
						<div class="flex gap-3 items-center">
							<div>
								<span
									class="block rounded-full w-12 h-12 bg-cover bg-no-repeat bg-center"
									:style="'background-image: url(\'' + user.profile_photo_url + '\');'"
								/>
							</div>
							<div class="flex flex-col">
								<div class="truncate text-lg m-0 p-0 font-bold text-gray-800">{{ user.name }}</div>
								<div class="mr-6 flex items-center text-base font-medium text-gray-500">
									<svg class="mr-1.5 h-5 w-5 flex-shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
										 aria-hidden="true">
										<path
											d="M7 8a3 3 0 100-6 3 3 0 000 6zM14.5 9a2.5 2.5 0 100-5 2.5 2.5 0 000 5zM1.615 16.428a1.224 1.224 0 01-.569-1.175 6.002 6.002 0 0111.908 0c.058.467-.172.92-.57 1.174A9.953 9.953 0 017 18a9.953 9.953 0 01-5.385-1.572zM14.5 16h-.106c.07-.297.088-.611.048-.933a7.47 7.47 0 00-1.588-3.755 4.502 4.502 0 015.874 2.636.818.818 0 01-.36.98A7.465 7.465 0 0114.5 16z"></path>
									</svg>
									Fono, Terapia
								</div>
							</div>
						</div>
						<div>
							<div class="ml-2">
								<span
									class="rounded-full bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20">
									Ativo
								</span>
							</div>
						</div>
					</div>
				</Link>
			</li>
		</ul>

		<Pagination :meta="users.meta"/>
	</div>
</template>
<script setup>
import { Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
	users: {
		type: Object,
		required: true,
	},
	only: {
		type: Array,
		default: () => [],
	},
});
</script>