<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DeleteTeamForm from '@/Pages/Teams/Partials/DeleteTeamForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TeamMemberManager from '@/Pages/Teams/Partials/TeamMemberManager.vue';
import UpdateTeamNameForm from '@/Pages/Teams/Partials/UpdateTeamNameForm.vue';
import PageContainer from '@/Components/PageContainer.vue';

defineProps({
    team: Object,
    availableRoles: Array,
    permissions: Object,
});
</script>

<template>
    <AppLayout title="Configurações do setor">
		<div>
			<UpdateTeamNameForm :team="team" :permissions="permissions" />

			<TeamMemberManager
				class="mt-10 md:mt-0"
				:team="team"
				:available-roles="availableRoles"
				:user-permissions="permissions"
			/>

			<template v-if="permissions.canDeleteTeam && ! team.personal_team">
				<SectionBorder />

				<DeleteTeamForm class="mt-10 md:mt-0" :team="team" />
			</template>
		</div>
    </AppLayout>
</template>
