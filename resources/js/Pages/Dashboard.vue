<script lang="ts" setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { DollarSign, Users, CreditCard, Activity, SquareArrowOutUpRight } from 'lucide-vue-next'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/Components/ui/table';
import { Button } from "@/Components/ui/button";
import { Calendar } from "@/Components/ui/calendar";
import { type Ref, ref } from "vue";
import { type DateValue, getLocalTimeZone, today } from "@internationalized/date";

const calendarValue = ref(today(getLocalTimeZone())) as Ref<DateValue>
</script>

<template>
	<AppLayout title="Dashboard">
		<main class="flex flex-1 flex-col gap-8">
			<div class="grid gap-4 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
				<Card>
					<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle class="text-sm font-medium">
							Total Revenue
						</CardTitle>
						<DollarSign class="h-4 w-4 text-muted-foreground"/>
					</CardHeader>
					<CardContent>
						<div class="text-2xl font-bold">
							$45,231.89
						</div>
						<p class="text-xs text-muted-foreground">
							+20.1% from last month
						</p>
					</CardContent>
				</Card>
				<Card>
					<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle class="text-sm font-medium">
							Subscriptions
						</CardTitle>
						<Users class="h-4 w-4 text-muted-foreground"/>
					</CardHeader>
					<CardContent>
						<div class="text-2xl font-bold">
							+2350
						</div>
						<p class="text-xs text-muted-foreground">
							+180.1% from last month
						</p>
					</CardContent>
				</Card>
				<Card>
					<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle class="text-sm font-medium">
							Sales
						</CardTitle>
						<CreditCard class="h-4 w-4 text-muted-foreground"/>
					</CardHeader>
					<CardContent>
						<div class="text-2xl font-bold">
							+12,234
						</div>
						<p class="text-xs text-muted-foreground">
							+19% from last month
						</p>
					</CardContent>
				</Card>
				<Card>
					<CardHeader class="flex flex-row items-center justify-between space-y-0 pb-2">
						<CardTitle class="text-sm font-medium">
							Active Now
						</CardTitle>
						<Activity class="h-4 w-4 text-muted-foreground"/>
					</CardHeader>
					<CardContent>
						<div class="text-2xl font-bold">
							+573
						</div>
						<p class="text-xs text-muted-foreground">
							+201 since last hour
						</p>
					</CardContent>
				</Card>
			</div>
			<div class="grid gap-4 md:gap-8 lg:grid-cols-2 xl:grid-cols-3">
				<Card class="xl:col-span-2">
					<CardHeader class="flex flex-row items-center">
						<div class="grid gap-2">
							<CardTitle>Próximos agendamentos</CardTitle>
						</div>
						<Button as-child size="sm" class="ml-auto gap-2">
							<a href="#">
								Ver todos os agendamentos
								<SquareArrowOutUpRight class="h-4 w-4"/>
							</a>
						</Button>
					</CardHeader>
					<CardContent>
						<Table>
							<TableHeader>
								<TableRow>
									<TableHead>Cliente</TableHead>
									<TableHead>Serviço</TableHead>
									<TableHead>Data/Hora</TableHead>
									<TableHead>Profissional</TableHead>
								</TableRow>
							</TableHeader>
							<TableBody>
								<TableRow>
									<TableCell>
										<div class="font-medium">
											Guilherme Santos
										</div>
										<div class="text-sm text-muted-foreground">
											Resp. Silvia Carla
										</div>
									</TableCell>
									<TableCell>
										Sessão de Fono
									</TableCell>
									<TableCell>
										18/04/2024 16:00 - 17:00
									</TableCell>
									<TableCell>
										Camila Timbó
									</TableCell>
								</TableRow>
							</TableBody>
						</Table>
					</CardContent>
				</Card>
				<Card>
					<CardHeader>
						{{ calendarValue }}
						<CardTitle>Calendário</CardTitle>
					</CardHeader>
					<CardContent class="grid gap-8">
						<Calendar v-model="calendarValue" locale="pt-BR" />
					</CardContent>
				</Card>
			</div>
		</main>
	</AppLayout>
</template>
