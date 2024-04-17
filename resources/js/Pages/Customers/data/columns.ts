// @ts-ignore
import DropdownAction, { type DataTableActionItem } from "@/Components/DataTable/DataTableDropdown.vue";
// @ts-ignore
import DataTableColumnHeader from "@/Components/DataTable/DataTableColumnHeader.vue";
import { h } from 'vue';
import type { ColumnDef } from "@tanstack/vue-table";
import { Checkbox } from "@/Components/ui/checkbox";
import { calendarDate, relativeDate } from "@/Utilities/date";
import { route } from "momentum-trail";
import { Pencil, Trash } from "lucide-vue-next";
import { useForm, usePage } from "@inertiajs/vue3";
import type { Customer } from "@/Pages/Customers/data/schema";
import { formatCPF } from "@/Utilities/utils";

const form = useForm({});

export const columns: ColumnDef<Customer>[] = [
    {
        id: 'select',
        header: ({ table }) => h(Checkbox, {
            'checked': table.getIsAllPageRowsSelected(),
            'onUpdate:checked': (value: boolean) => table.toggleAllPageRowsSelected(!!value),
            'ariaLabel': 'Selecionar todos',
        }),
        cell: ({ row }) => h(Checkbox, {
            'checked': row.getIsSelected(),
            'onUpdate:checked': (value: boolean) => row.toggleSelected(!!value),
            'ariaLabel': 'Selecionar linha',
        }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Nome',
            })
        },
        cell: ({ row }) => {
            return h('div', { class: 'font-medium' }, [
                h('span', row.getValue('name'))
            ])
        },
    },
    {
        accessorKey: 'cpf',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'CPF',
            })
        },
        cell: ({ row }) => {
            const cpf = row.getValue<string>('cpf')

            return h('div', cpf ? formatCPF(cpf) : 'Sem CPF.')
        },
    },
    {
        accessorKey: 'phone',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Telefone',
            })
        },
        cell: ({ row }) => {
            const phone = row.getValue<string>('phone')

            return h('div', phone ?? 'Sem telefone.')
        },
    },
    {
        accessorKey: 'email',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Email',
            })
        },
        cell: ({ row }) => {
            const email = row.getValue<string>('email')

            return h('div', email ?? 'Sem email.')
        },
    },
    {
        accessorKey: 'birthdate',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Data de nascimento',
            })
        },
        cell: ({ row }) => {
            const date = row.getValue<string>('birthdate')
            const formatted = calendarDate(date)

            return h('div', formatted)
        },
    },
    {
        accessorKey: 'created_at',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Cadastro',
            })
        },
        cell: ({ row }) => {
            const date = row.getValue<string>('created_at')
            const formatted = relativeDate(date)

            return h('div', formatted)
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const user = row.original
            const actions: DataTableActionItem[] = [
                {
                    label: 'Editar cliente',
                    href: route('customers.edit', user),
                    icon: Pencil,
                    show: usePage<any>().props.user_permissions.edit_customers
                },
                {
                    label: 'Inativar cliente',
                    href: route('customers.destroy', user),
                    icon: Trash,
                    class: 'text-red-500 focus:text-red-500',
                    deleteDialog: {
                        title: 'Inativar cliente',
                        description: 'Tem certeza que deseja inativar este cliente?',
                        deleteActionName: 'Inativar',
                        deleteAction: () => form.delete(route('customers.destroy', user), { preserveState: false })
                    },
                    show: usePage<any>().props.user_permissions.delete_customers
                }
            ]

            return h('div', { class: 'relative' }, [h(DropdownAction, { items: actions })])
        }
    }
]