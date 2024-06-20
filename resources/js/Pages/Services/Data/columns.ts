// @ts-ignore
import DataTableColumnHeader from "@/Components/DataTable/DataTableColumnHeader.vue";
// @ts-ignore
import DropdownAction, { type DataTableActionItem } from "@/Components/DataTable/DataTableDropdown.vue";
import type { ColumnDef } from "@tanstack/vue-table";
import type { Service } from "@/Pages/Services/Data/schema";
import { h } from "vue";
import { Checkbox } from "@/Components/ui/checkbox";
import { Avatar, AvatarImage } from "@/Components/ui/avatar";
import { relativeDate } from "@/Utilities/date";
import type { Team } from "@/Pages/Teams/Data/schema";
import { Badge } from "@/Components/ui/badge";
import type { User } from "@/Pages/Users/Data/schema";
import { route } from "momentum-trail";
import { Pencil, Trash } from "lucide-vue-next";
import { useForm, usePage } from '@inertiajs/vue3';

const form = useForm({});

export const columns: ColumnDef<Service>[] = [
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
        cell: ({ row }) => h('div', { class: "font-medium" }, row.getValue('name')),
    },
    {
        accessorKey: 'price',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Valor',
            })
        },
        cell: ({ row }) => h('div', { class: "text-green-700" }, 'R$ ' + row.getValue('price')),
    },
    {
        accessorKey: 'duration',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Duração',
            })
        },
        cell: ({ row }) => h('div', row.getValue('duration') + ' minuto(s)')
    },
    {
        accessorKey: 'team',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Setor',
            })
        },
        cell: ({ row }) => {
            const team = row.getValue<Team>('team')

            let teamBadge = h(Badge, { variant: 'outline' }, () => team.name)

            return h('div', { class: 'flex items-center space-x-1' }, [teamBadge])
        },
    },
    {
        accessorKey: 'user',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Responsável',
            })
        },
        cell: ({ row }) => {
            const user = row.getValue<User>('user')

            return h('div', { class: 'flex items-center space-x-2' }, [
                h(Avatar, { size: 'xs' }, () => [
                    h(AvatarImage, { 'src': user.profile_photo_url })
                ]),
                h('span', user.name)
            ])
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
            const service = row.original
            const actions: DataTableActionItem[] = [
                {
                    label: 'Editar serviço',
                    href: route('services.edit', service),
                    icon: Pencil,
                    show: usePage<any>().props.user_permissions.edit_services
                },
                {
                    label: 'Inativar serviço',
                    href: route('services.destroy', service),
                    icon: Trash,
                    class: 'text-red-500 focus:text-red-500',
                    deleteDialog: {
                        title: 'Inativar serviço',
                        description: 'Tem certeza que deseja inativar este serviço?',
                        deleteActionName: 'Inativar',
                        deleteAction: () => form.delete(route('services.destroy', service), { preserveState: false })
                    },
                    show: usePage<any>().props.user_permissions.delete_services
                }
            ]

            return h('div', { class: 'relative' }, [h(DropdownAction, { items: actions })])
        }
    }
]