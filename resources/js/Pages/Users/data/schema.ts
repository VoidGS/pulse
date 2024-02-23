import type { Team } from "@/Pages/Teams/data/schema";

export interface User {
    id: number
    name: string
    email: string
    profile_photo_url: string
    teams?: Team[]
    updated_at: string
    created_at: string
}

export const columnsView = [
    {
        id: 'name',
        label: 'nome'
    },
    {
        id: 'teams',
        label: 'setores'
    },
    {
        id: 'created_at',
        label: 'cadastro'
    },
]