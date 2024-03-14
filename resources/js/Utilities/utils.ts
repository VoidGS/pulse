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

export {
    formatCPF,
    validateCPF
}