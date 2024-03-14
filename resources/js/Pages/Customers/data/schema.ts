export interface Customer {
    id: number
    name: string
    cpf: string
    gender: string
    birthdate: Date
    active: boolean
    updated_at: string
    created_at: string
}

export interface Discount {
    service: string,
    discount: number
}

export const columnsView = [
    {
        id: 'name',
        label: 'nome'
    },
    {
        id: 'cpf',
        label: 'cpf'
    },
    {
        id: 'gender',
        label: 'gênero'
    },
    {
        id: 'birthdate',
        label: 'data de nascimento'
    },
    {
        id: 'created_at',
        label: 'cadastro'
    },
]