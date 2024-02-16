<script setup>
import { ref } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import Container from '@/Components/Container.vue';
import Breadcrumb from '@/Components/Breadcrumb.vue';

defineProps({
	title: String,
});

const showingNavigationDropdown = ref(false);

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

const menu = [
	{
		name: 'Dashboard',
		url: route('dashboard'),
		route: 'dashboard',
		when: () => usePage().props.auth.user,
	},
	{
		name: 'Usuários',
		url: route('users.index'),
		route: 'users.index',
		when: () => usePage().props.permissions.see_users,
	},
];
</script>

<template>
	<div>
		<Head :title="title"/>

		<Banner/>

		<div class="min-h-screen dark:bg-gray-900">
			<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
				<!-- Primary Navigation Menu -->
				<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
					<div class="flex justify-between h-16">
						<div class="flex">
							<!-- Logo -->
							<div class="shrink-0 flex items-center">
								<Link :href="route('dashboard')">
									<ApplicationMark class="block h-9 w-auto"/>
								</Link>
							</div>

							<!-- Navigation Links -->
							<div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
								<template v-for="item in menu" :key="item.name">
									<NavLink v-if="item.when ? item.when() : true" :href="item.url" :active="route().current(item.route)">
										{{ item.name }}
									</NavLink>
								</template>
							</div>
						</div>

						<div class="hidden sm:flex sm:items-center sm:ms-6">
							<div class="ms-3 relative">
								<!-- Teams Dropdown -->
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

						<!-- Hamburger -->
						<div class="-me-2 flex items-center sm:hidden">
							<button
								class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out"
								@click="showingNavigationDropdown = ! showingNavigationDropdown">
								<svg
									class="h-6 w-6"
									stroke="currentColor"
									fill="none"
									viewBox="0 0 24 24"
								>
									<path
										:class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M4 6h16M4 12h16M4 18h16"
									/>
									<path
										:class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
										stroke-linecap="round"
										stroke-linejoin="round"
										stroke-width="2"
										d="M6 18L18 6M6 6l12 12"
									/>
								</svg>
							</button>
						</div>
					</div>
				</div>

				<!-- Responsive Navigation Menu -->
				<div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden">
					<div class="pt-2 pb-3 space-y-1">
						<template v-for="item in menu" :key="item.name">
							<ResponsiveNavLink v-if="item.when ? item.when() : true" :href="item.url" :active="route().current(item.route)">
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

							<ResponsiveNavLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')"
											   :active="route().current('api-tokens.index')">
								API Tokens
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
			</nav>

			<!-- Page Heading -->
			<header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
				<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
					<slot name="header"/>
				</div>
			</header>

			<!-- Page Content -->
			<main>
				<Container>
					<Breadcrumb class=""/>
					<slot/>
				</Container>
			</main>
		</div>
	</div>
</template>
