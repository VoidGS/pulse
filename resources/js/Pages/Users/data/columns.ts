// @ts-ignore
import DropdownAction from "@/Components/DataTable/DataTableDropdown.vue";
// @ts-ignore
import DataTableColumnHeader from "@/Components/DataTable/DataTableColumnHeader.vue";
import { h } from 'vue';
import type { ColumnDef } from "@tanstack/vue-table";
import { Checkbox } from "@/Components/ui/checkbox";
import { Badge } from "@/Components/ui/badge";
import { Avatar, AvatarImage } from "@/Components/ui/avatar";
import type { Team, User } from "@/Pages/Users/data/schema";
import { relativeDate } from "@/Utilities/date";

export const columns: ColumnDef<User>[] = [
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
            return h('div', { class: 'flex items-center space-x-2 font-medium' }, [
                h(Avatar, { size: 'xs' }, () => [
                    h(AvatarImage, { 'src': row.original.profile_photo_url })
                ]),
                h('span', row.getValue('name'))
            ])
        },
    },
    {
        accessorKey: 'teams',
        header: ({ column }) => {
            return h(DataTableColumnHeader, {
                column: column,
                title: 'Setores',
            })
        },
        cell: ({ row }) => {
            const teams = row.getValue<Team[]>('teams')

            if (teams.length === 0) return h('div', { class: 'text-muted-foreground' }, 'Sem setor.')

            let teamBadges = teams.map((team) => h(Badge, { variant: 'outline' }, () => team.name))

            return h('div', { class: 'flex items-center space-x-1' }, [teamBadges])
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
    // {
    //     id: 'actions',
    //     enableHiding: false,
    //     cell: ({ row }) => {
    //         const user = row.original
    //
    //         return h('div', { class: 'relative' }), h(DropdownAction, {
    //             user,
    //         })
    //     }
    // }
]