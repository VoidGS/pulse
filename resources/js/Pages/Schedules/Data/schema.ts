import type { Customer } from "@/Pages/Customers/Data/schema";
import type { Service } from "@/Pages/Services/Data/schema";

export interface Schedule {
    id: number
    customer: Customer
    service: Service
    start_date: string
    end_date: string
    status: ScheduleStatus
    active: boolean
    event_id: string
    recurrence_id?: string
    updated_at: string
    created_at: string
    can: {
        delete: boolean
    }
}

export interface ScheduleFilter {
    scheduleDate?: Date
    month?: number
    year?: number
    customerId?: number
    serviceId?: number
    status?: ScheduleStatus
}

export enum ScheduleStatus {
    PENDENTE = "pendente",
    CONFIRMADO = "confirmado",
    CANCELADO = "cancelado",
    REMARCADO = "remarcado",
    FINALIZADO = "finalizado",
    FALTOU = "faltou"
}

export enum ScheduleBorderStatusColor {
    PENDENTE = "border-gray-400",
    CONFIRMADO = "border-lime-400",
    CANCELADO = "border-red-300",
    REMARCADO = "border-orange-400",
    FINALIZADO = "border-emerald-400",
    FALTOU = "border-red-600"
}

export enum ScheduleSelectStatusColor {
    PENDENTE = "#a1a1aa",
    CONFIRMADO = "#a3e635",
    CANCELADO = "#fca5a5",
    REMARCADO = "#fb923c",
    FINALIZADO = "#34d399",
    FALTOU = "#dc2626"
}

export interface CalendarPopover {
    description: string,
    isComplete: boolean,
    dates: Date[],
    color: string
}