export interface Team {
    id: number
    name: string
    user_id: number
    membership?: Membership
    personal_team?: boolean
    active: boolean
    updated_at: string
    created_at: string
}

export interface Membership {
    created_at: string
    role: string
    team_id: number
    updated_at: string
    user_id: number
}