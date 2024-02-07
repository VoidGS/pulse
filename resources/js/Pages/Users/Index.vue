<template>
	<AppLayout title="Usuários">
		<Container>
			<Table :users="users" />
		</Container>
	</AppLayout>
</template>
<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Container from '@/Components/Container.vue';
import Pagination from '@/Components/Pagination.vue';
import Table from '@/Components/Table.vue';

defineProps(['users']);
</script>