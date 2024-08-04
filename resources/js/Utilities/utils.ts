import axios from "axios";
import { route } from "momentum-trail";
import type { Schedule, ScheduleFilter } from "@/Pages/Schedules/Data/schema";

function formatCPF(cpf: string) {
    cpf = cpf.replace(/\D/g, "")

    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, "$1.$2.$3-$4")
}

function validateCPF(cpf: string) {
    cpf = cpf.replace(/\D+/g, "");
    if (cpf.length !== 11 || !!cpf.match(/(\d)\1{10}/)) return false
    const cpfDigits = cpf.split("").map((el) => +el)
    const rest = (count: number): number => {
        return (((cpfDigits.slice(0, count - 12).reduce((soma, el, index) => soma + el * (count - index), 0) * 10) % 11) % 10)
    }
    return rest(10) === cpfDigits[9] && rest(11) === cpfDigits[10]
}

async function validateServerGuardianCPF(cpf: string) {
    if (!validateCPF(cpf)) return false
    let validCpf = false

    await axios.get(route('guardians.index') + '?cpf=' + unmaskCPF(cpf)).then((response) => {
        if (typeof response !== 'object') return true

        validCpf = response.data.length < 1
    })

    return validCpf
}

function unmaskCPF(cpf: string) {
    return cpf.replace(/\D+/g, "");
}

function validatePhone(phone: string) {
    phone = phone.replace(/\D+/g, '')
    return phone.match('^((1[1-9])|([2-9][0-9]))((3[0-9]{3}[0-9]{4})|(9[0-9]{3}[0-9]{5}))$')
}

async function filterSchedules(filterOption: ScheduleFilter): Promise<Schedule[]> {
    return await axios.get(route('schedules.filter'), {
        params: filterOption
    }).then((response) => {
        console.log(response.data)
        return response.data
    }).catch((error) => {
        console.log(error)
    })
}

export {
    formatCPF,
    validateCPF,
    validateServerGuardianCPF,
    unmaskCPF,
    validatePhone,
    filterSchedules
}