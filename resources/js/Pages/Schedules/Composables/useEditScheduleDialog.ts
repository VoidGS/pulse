import { reactive } from "vue";
import type { Schedule } from "@/Pages/Schedules/Data/schema";



export const editScheduleDialogState: { open: boolean, schedule: Schedule | null } = reactive({
	open: false,
	schedule: null
})

export function useEditScheduleDialog() {
    const openDialog = (schedule: Schedule) => {
        editScheduleDialogState.schedule = schedule
        editScheduleDialogState.open = true
    }

    const closeDialog = () => {
        editScheduleDialogState.schedule = null
        editScheduleDialogState.open = false
    }

    return {
        editScheduleDialogState,
        openDialog,
        closeDialog
    }
}