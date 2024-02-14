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