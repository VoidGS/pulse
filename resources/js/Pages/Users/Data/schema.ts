import type { Team } from "@/Pages/Teams/Data/schema";

export interface User {
    id: number
    name: string
    email: string
    profile_photo_url: string
    teams?: Team[]
    role?: string
    active: boolean
    updated_at: string
    created_at: string
}

export interface Role {
    id: number
    name: string
    guard_name: string
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