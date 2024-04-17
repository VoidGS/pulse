import type { Team } from "@/Pages/Teams/data/schema";
import type { User } from "@/Pages/Users/data/schema";

export interface Service {
    id: number
    name: string
    price: number
    team: Team
    user: User
    active: boolean
    created_at: string
}

export const columnsView = [
    {
        id: 'name',
        label: 'nome'
    },
    {
        id: 'price',
        label: 'valor'
    },
    {
        id: 'team',
        label: 'setor'
    },
    {
        id: 'user',
        label: 'responsável'
    },
    {
        id: 'created_at',
        label: 'cadastro'
    },
]