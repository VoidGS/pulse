import type { ColumnDef } from "@tanstack/vue-table";
import type { Service } from "@/Pages/Services/data/schema";
import { h } from "vue";
import { Checkbox } from "@/Components/ui/checkbox";
import DataTableColumnHeader from "@/Components/DataTable/DataTableColumnHeader.vue";
import { Avatar, AvatarImage } from "@/Components/ui/avatar";
import { relativeDate } from "@/Utilities/date";

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
        cell: ({ row }) => h('div', row.getValue('name')),
    },
    // {
    //     accessorKey: 'team',
    //     header: ({ column }) => {
    //         return h(DataTableColumnHeader, {
    //             column: column,
    //             title: 'Setor',
    //         })
    //     },
    //     cell: ({ row }) => h('div', row.getValue('team')),
    // },
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
]