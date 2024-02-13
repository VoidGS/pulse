export interface User {
    id: bigint
    name: string
    email: string
    profile_photo_url: string
    teams: Team[]
    created_at: string
}

export interface Team {
    created_at: string
    id: number
    membership?: Membership
    name: string
    personal_team: boolean
    updated_at: string
    user_id: number
}

export interface Membership {
    created_at: string
    role: string
    team_id: number
    updated_at: string
    user_id: number
}

export const columnsView = [
    {
        id: 'created_at',
        label: 'Cadastro'
    }
]