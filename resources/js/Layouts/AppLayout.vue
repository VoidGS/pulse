<script lang="ts" setup>
import { type Component, h, markRaw, onMounted, ref, watch, watchEffect } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Container from '@/Components/Container.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';
import { route } from "momentum-trail";
import { Briefcase, Home, UsersRound, UserRoundPlus, Plus, Settings, Wrench, Pencil, BookUser, CalendarDays } from "lucide-vue-next";
import { filledIcons, Notification, Notivue, NotivueSwipe, pastelTheme } from "notivue";
import PoliceCarLight from "@/Components/Emojis/PoliceCarLight.vue";
import Bell from "@/Components/Emojis/Bell.vue";
import ClappingHands from "@/Components/Emojis/ClappingHands.vue";
import Information from "@/Components/Emojis/Information.vue";
import { showToast } from "@/Utilities/toast";

defineProps({
	title: String,
});

const page = usePage()
const showingNavigationDropdown = ref(false)

// Toast
const toastStyle = ref('')
const toastMessage = ref('')
const toastIcons = {
	...filledIcons,
	success: markRaw(ClappingHands),
	error: markRaw(PoliceCarLight),
	info: markRaw(Information),
	warning: markRaw(Bell)
}

watchEffect(async () => {
	toastStyle.value = page.props.jetstream.flash?.toastStyle || page.props.flash?.toastStyle || 'default';
	toastMessage.value = page.props.jetstream.flash?.toast || page.props.flash?.toast || '';
});

watch(toastMessage, (message) => {
	showToast(toastMessage.value, toastStyle.value)
})

onMounted(() => {
	showToast(toastMessage.value, toastStyle.value)
})

const switchToTeam = (team) => {
	router.put(route('current-team.update'), {
		team_id: team.id,
	}, {
		preserveState: false,
	});
};

const logout = () => {
	router.post(route('logout'));
};

// Menu
const generateRoute = (routes: string[]) => {
	let formattedRoute = location.pathname
	routes.forEach((route) => {
		formattedRoute = formattedRoute.replace(route, '')
	})
	return formattedRoute
}

export interface Menu {
	name: string
	url: string
	route?: string
	breadcrumbName?: string
	breadcrumbRoute?: string
	breadcrumbIcon?: Component
	showOnMenu?: boolean
	showOnBreadcrumb?: boolean
	when?: boolean
}

const menu: Menu[] = [
	{
		name: 'Dashboard',
		url: route('dashboard'),
		route: 'dashboard',
		breadcrumbName: 'Dashboard',
		breadcrumbRoute: '/',
		breadcrumbIcon: Home,
		showOnMenu: true,
		showOnBreadcrumb: true,
		when: page.props.auth.user,
	},

	// Profile
	{
		name: 'Configurações',
		url: route('profile.show'),
		breadcrumbRoute: '/user/profile',
		breadcrumbIcon: Settings,
		showOnBreadcrumb: true
	},

	// Teams
	{
		name: 'Setores',
		url: route('teams.show', generateRoute(['/teams/'])),
		breadcrumbRoute: '/teams/' + generateRoute(['/teams/']),
		breadcrumbIcon: Wrench,
		showOnBreadcrumb: true
	},
	{
		name: 'Criar setor',
		url: route('teams.create'),
		breadcrumbRoute: '/teams/create',
		breadcrumbIcon: Plus,
		showOnBreadcrumb: true
	},

	// Users
	{
		name: 'Usuários',
		url: route('users.index'),
		route: 'users.*',
		breadcrumbRoute: '/users',
		breadcrumbIcon: UsersRound,
		showOnMenu: true,
		showOnBreadcrumb: true,
		when: page.props.user_permissions.see_users,
	},
	{
		name: 'Cadastrar usuário',
		url: route('users.create'),
		breadcrumbRoute: '/users/create',
		breadcrumbIcon: UserRoundPlus,
		showOnBreadcrumb: true
	},
	{
		name: 'Editar usuário',
		url: route('users.edit', generateRoute(['/users/', '/edit'])),
		breadcrumbRoute: '/users/' + generateRoute(['/users/', '/edit']) + '/edit',
		breadcrumbIcon: Pencil,
		showOnBreadcrumb: true
	},

	// Services
	{
		name: 'Serviços',
		url: route('services.index'),
		route: 'services.*',
		breadcrumbRoute: '/services',
		breadcrumbIcon: Briefcase,
		showOnMenu: true,
		showOnBreadcrumb: true,
		when: page.props.user_permissions.see_services,
	},
	{
		name: 'Cadastrar serviço',
		url: route('services.create'),
		breadcrumbRoute: '/services/create',
		breadcrumbIcon: Plus,
		showOnBreadcrumb: true,
	},
	{
		name: 'Editar serviço',
		url: route('services.edit', generateRoute(['/services/', '/edit'])),
		breadcrumbRoute: '/services/' + generateRoute(['/services/', '/edit']) + '/edit',
		breadcrumbIcon: Pencil,
		showOnBreadcrumb: true
	},

	// Customer
	{
		name: 'Clientes',
		url: route('customers.index'),
		route: 'customers.*',
		breadcrumbRoute: '/customers',
		breadcrumbIcon: BookUser,
		showOnMenu: true,
		showOnBreadcrumb: true,
		when: page.props.user_permissions.see_customers,
	},
	{
		name: 'Cadastrar cliente',
		url: route('customers.create'),
		breadcrumbRoute: '/customers/create',
		breadcrumbIcon: Plus,
		showOnBreadcrumb: true,
	},
	{
		name: 'Editar cliente',
		url: route('customers.edit', generateRoute(['/customers/', '/edit'])),
		breadcrumbRoute: '/customers/' + generateRoute(['/customers/', '/edit']) + '/edit',
		breadcrumbIcon: Pencil,
		showOnBreadcrumb: true
	},

	{
		name: 'Agendamentos',
		url: route('schedules.index'),
		route: 'schedules.*',
		breadcrumbRoute: '/schedules',
		breadcrumbIcon: CalendarDays,
		showOnMenu: true,
		showOnBreadcrumb: true,
		when: true,
	}
];
</script>

<template>
	<div>
		<Head :title="title"/>

		<Notivue v-slot="item">
			<NotivueSwipe :item="item">
				<Notification :item="item" :icons="toastIcons" :theme="pastelTheme" />
			</NotivueSwipe>
		</Notivue>

		<div class="flex min-h-screen flex-col bg-background">
			<!--<Banner/>-->

			<header class="sticky z-40 top-0 bg-background/80 backdrop-blur-lg border-b border-border shadow-md">
				<div class="container flex justify-between h-16 max-w-7xl mx-auto items-center">
					<div class="flex mr-4">
						<Link :href="route('dashboard')" class="mr-6 flex items-center space-x-2">
							<ApplicationMark class="h-9 w-9"/>
							<!--<span class="font-bold">Pulse</span>-->
						</Link>

						<nav class="items-center space-x-6 font-medium hidden md:flex">
							<template v-for="item in menu" :key="item.name">
								<NavLink v-if="item.showOnMenu && item.when" :href="item.url" :active="route().current(item.route)">
									{{ item.name }}
								</NavLink>
							</template>
						</nav>
					</div>

					<div class="flex items-center justify-end space-x-4">
						<div class="hidden md:flex md:items-center md:ms-6">
							<!-- Teams Dropdown -->
							<div class="ms-3 relative">
								<Dropdown v-if="$page.props.jetstream.hasTeamFeatures && $page.props.auth.user.all_teams.length > 0"
										  align="right" width="60">
									<template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
													class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.current_team.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
													 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
														  d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                                </svg>
                                            </button>
                                        </span>
									</template>

									<template #content>
										<div class="w-60">
											<!-- Team Management -->
											<div class="block px-4 py-2 text-xs text-gray-400">
												Gerenciar setores
											</div>

											<!-- Team Settings -->
											<DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
												Configurações do setor
											</DropdownLink>

											<DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
												Criar novo setor
											</DropdownLink>

											<!-- Team Switcher -->
											<template v-if="$page.props.auth.user.all_teams.length > 1">
												<div class="border-t border-gray-200 dark:border-gray-600"/>

												<div class="block px-4 py-2 text-xs text-gray-400">
													Trocar de setor
												</div>

												<template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
													<form @submit.prevent="switchToTeam(team)">
														<DropdownLink as="button">
															<div class="flex items-center">
																<svg v-if="team.id == $page.props.auth.user.current_team_id"
																	 class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg"
																	 fill="none" viewBox="0 0 24 24" stroke-width="1.5"
																	 stroke="currentColor">
																	<path stroke-linecap="round" stroke-linejoin="round"
																		  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
																</svg>

																<div>{{ team.name }}</div>
															</div>
														</DropdownLink>
													</form>
												</template>
											</template>
										</div>
									</template>
								</Dropdown>

								<Dropdown v-else>
									<template #trigger>
										<span class="inline-flex rounded-md">
                                            <button type="button"
													class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                Setores

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
													 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
														  d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9"/>
                                                </svg>
                                            </button>
                                        </span>
									</template>

									<template #content>
										<!-- Team Management -->
										<div class="block px-4 py-2 text-xs text-gray-400">
											Gerenciar setores
										</div>

										<DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
											Criar novo setor
										</DropdownLink>
									</template>
								</Dropdown>
							</div>

							<!-- Settings Dropdown -->
							<div class="ms-3 relative">
								<Dropdown align="right" width="48">
									<template #trigger>
										<button v-if="$page.props.jetstream.managesProfilePhotos"
												class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
											<img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
												 :alt="$page.props.auth.user.name">
										</button>

										<span v-else class="inline-flex rounded-md">
                                            <button type="button"
													class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none"
													 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                                </svg>
                                            </button>
                                        </span>
									</template>

									<template #content>
										<!-- Account Management -->
										<div class="block px-4 py-2 text-xs text-gray-400">
											Gerenciar conta
										</div>

										<DropdownLink :href="route('profile.show')">
											Perfil
										</DropdownLink>

										<DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
											API Tokens
										</DropdownLink>

										<div class="border-t border-gray-200 dark:border-gray-600"/>

										<!-- Authentication -->
										<form @submit.prevent="logout">
											<DropdownLink as="button">
												Deslogar
											</DropdownLink>
										</form>
									</template>
								</Dropdown>
							</div>
						</div>

						<!--Hamburguer-->
						<div class="-me-2 flex items-center md:hidden">
							<button
								class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
								@click="showingNavigationDropdown = ! showingNavigationDropdown">
								<svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
									<path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
										  stroke-linecap="round"
										  stroke-linejoin="round"
										  stroke-width="2"
										  d="M4 6h16M4 12h16M4 18h16"/>
									<path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
										  stroke-linecap="round"
										  stroke-linejoin="round"
										  stroke-width="2"
										  d="M6 18L18 6M6 6l12 12"/>
								</svg>
							</button>
						</div>
					</div>
				</div>

				<!--Responsive Menu-->
				<div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="md:hidden">
					<div class="pt-2 pb-3 space-y-1">
						<template v-for="item in menu" :key="item.name">
							<ResponsiveNavLink v-if="item.showOnMenu && item.when" :href="item.url" :active="route().current(item.route)">
								{{ item.name }}
							</ResponsiveNavLink>
						</template>
					</div>

					<!-- Responsive Settings Options -->
					<div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
						<div class="flex items-center px-4">
							<div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
								<img class="h-10 w-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url"
									 :alt="$page.props.auth.user.name">
							</div>

							<div>
								<div class="font-medium text-base text-gray-800 dark:text-gray-200">
									{{ $page.props.auth.user.name }}
								</div>
								<div class="font-medium text-sm text-gray-500">
									{{ $page.props.auth.user.email }}
								</div>
							</div>
						</div>

						<div class="mt-3 space-y-1">
							<ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
								Perfil
							</ResponsiveNavLink>

							<!-- Authentication -->
							<form method="POST" @submit.prevent="logout">
								<ResponsiveNavLink as="button">
									Deslogar
								</ResponsiveNavLink>
							</form>

							<!-- Team Management -->
							<template v-if="$page.props.jetstream.hasTeamFeatures && $page.props.auth.user.all_teams.length > 0">
								<div class="border-t border-gray-200 dark:border-gray-600"/>

								<div class="block px-4 py-2 text-xs text-gray-400">
									Gerenciar setor
								</div>

								<!-- Team Settings -->
								<ResponsiveNavLink :href="route('teams.show', $page.props.auth.user.current_team)"
												   :active="route().current('teams.show')">
									Configurações do setor
								</ResponsiveNavLink>

								<ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')"
												   :active="route().current('teams.create')">
									Criar novo setor
								</ResponsiveNavLink>

								<!-- Team Switcher -->
								<template v-if="$page.props.auth.user.all_teams.length > 1">
									<div class="border-t border-gray-200 dark:border-gray-600"/>

									<div class="block px-4 py-2 text-xs text-gray-400">
										Trocar de setor
									</div>

									<template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
										<form @submit.prevent="switchToTeam(team)">
											<ResponsiveNavLink as="button">
												<div class="flex items-center">
													<svg v-if="team.id == $page.props.auth.user.current_team_id"
														 class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none"
														 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
														<path stroke-linecap="round" stroke-linejoin="round"
															  d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
													</svg>
													<div>{{ team.name }}</div>
												</div>
											</ResponsiveNavLink>
										</form>
									</template>
								</template>
							</template>

							<template v-else>
								<div class="border-t border-gray-200 dark:border-gray-600"/>

								<div class="block px-4 py-2 text-xs text-gray-400">
									Gerenciar setor
								</div>

								<ResponsiveNavLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')"
												   :active="route().current('teams.create')">
									Criar novo setor
								</ResponsiveNavLink>
							</template>
						</div>
					</div>
				</div>
			</header>

			<main>
				<Container>
					<Breadcrumb :menu="menu"/>
					<slot/>
				</Container>
			</main>
		</div>
	</div>
</template>
