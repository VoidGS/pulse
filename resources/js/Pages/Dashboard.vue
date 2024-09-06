<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { PiggyBank, Users, CalendarCheck, ChartSpline } from 'lucide-vue-next'
import PageContainer from "@/Components/PageContainer.vue";
import BarChart from "@/Components/Emojis/BarChart.vue";
import { BarChart as Chart } from '@/Components/ui/chart-bar';
import { translateMonth } from "@/Utilities/utils";

const props = defineProps<{
	chartData: any[],
	projection: number,
	newCustomers: number
}>()

const translatedChartData = props.chartData.map((month) => {
	return { ...month, name: translateMonth(month.name) }
})

const chartCategories: any[] = Array.from(translatedChartData.reduce((acc, item) => {
	const keys = Object.keys(item).filter(key => key !== 'name')

	keys.forEach(key => acc.add(key))

	return acc
}, new Set()))
</script>

<template>
	<AppLayout title="Dashboard">
		<PageContainer>
			<main class="flex flex-1 flex-col gap-8">
				<div>
					<BarChart class="w-10 h-10 mb-6" />

					<div class="space-y-4 md:space-y-0 md:flex md:items-center justify-between">
						<div>
							<h2 class="text-2xl font-bold tracking-tight">
								Dashboard
							</h2>

							<p class="text-muted-foreground">
								Listagem de métricas.
							</p>
						</div>
					</div>
				</div>

				<div class="grid gap-4 md:grid-cols-3 md:gap-8 lg:grid-cols-3">
					<Card>
						<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
							<CardTitle class="text-sm font-medium">
								Projeção mensal
							</CardTitle>
							<PiggyBank class="h-4 w-4 text-muted-foreground"/>
						</CardHeader>
						<CardContent>
							<div class="text-2xl font-bold">
								{{ props.projection.toLocaleString('pt-BR', { style: "currency", currency: "BRL" }) }}
							</div>
						</CardContent>
					</Card>
					<Card>
						<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
							<CardTitle class="text-sm font-medium">
								Novos pacientes mensais
							</CardTitle>
							<Users class="h-4 w-4 text-muted-foreground"/>
						</CardHeader>
						<CardContent>
							<div class="text-2xl font-bold">
								{{ props.newCustomers }}
							</div>
						</CardContent>
					</Card>
					<Card>
						<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
							<CardTitle class="text-sm font-medium">
								Atendimentos mensais
							</CardTitle>
							<CalendarCheck class="h-4 w-4 text-muted-foreground"/>
						</CardHeader>
						<CardContent>
							<div class="text-2xl font-bold">
								12,234
							</div>
						</CardContent>
					</Card>
				</div>

				<div class="grid gap-4 md:gap-8">
					<Card>
						<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
							<CardTitle class="text-sm font-medium">
								Atendimentos mensais por setor
							</CardTitle>
							<ChartSpline class="h-4 w-4 text-muted-foreground"/>
						</CardHeader>
						<CardContent>
							<Chart index="name"
								   :data="translatedChartData"
								   :type="'stacked'"
								   :categories="chartCategories"
								   :rounded-corners="4"
							/>
						</CardContent>
					</Card>
				</div>
			</main>
		</PageContainer>
	</AppLayout>
</template>
