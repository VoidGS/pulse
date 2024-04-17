export interface Customer {
    id: number
    name: string
    cpf?: string
    phone?: string
    email?: string
    birthdate: string
    active: boolean
    updated_at: string
    created_at: string
}

export interface Discount {
    id?: number,
    service_id?: number,
    service: number,
    discount: number
}

export interface Guardian {
    id: number
    name: string
    cpf: string
    phone: string
    email: string
    birthdate: Date
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
        id: 'phone',
        label: 'Telefone'
    },{
        id: 'email',
        label: 'Email'
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