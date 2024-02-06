<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import ActionSection from '@/Components/ActionSection.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    team: Object,
});

const confirmingTeamDeletion = ref(false);
const form = useForm({});

const confirmTeamDeletion = () => {
    confirmingTeamDeletion.value = true;
};

const deleteTeam = () => {
    form.delete(route('teams.destroy', props.team), {
        errorBag: 'deleteTeam',
    });
};
</script>

<template>
    <ActionSection>
        <template #title>
            Remover setor
        </template>

        <template #description>
			Apague permanentemente este setor.
        </template>

        <template #content>
            <div class="max-w-xl text-sm text-gray-600 dark:text-gray-400">
				Quando um setor é removido, todos os seus dados serão perdidos. Apenas setores sem <strong>serviços</strong> ativos vinculados podem ser removidos.
            </div>

            <div class="mt-5">
                <DangerButton @click="confirmTeamDeletion">
                    Remover setor
                </DangerButton>
            </div>

            <!-- Delete Team Confirmation Modal -->
            <ConfirmationModal :show="confirmingTeamDeletion" @close="confirmingTeamDeletion = false">
                <template #title>
                    Remover setor
                </template>

                <template #content>
					Você tem certeza que deseja remover esse setor? Quando um setor é removido, todos os seus dados serão perdidos.
                </template>

                <template #footer>
                    <SecondaryButton @click="confirmingTeamDeletion = false">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        class="ms-3"
                        :class="{ 'opacity-25': form.processing }"
                        :disabled="form.processing"
                        @click="deleteTeam"
                    >
                        Remover setor
                    </DangerButton>
                </template>
            </ConfirmationModal>
        </template>
    </ActionSection>
</template>
