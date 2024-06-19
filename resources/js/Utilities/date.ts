import 'moment/dist/locale/pt-br'
import moment from 'moment-timezone'

moment.tz.setDefault('America/Sao_Paulo')

const formatDate = (date: any) => {
    if (date === undefined) return
    return moment(date, 'DD/MM/YYYY').format()
}
const formatDateTime = (date: string) => moment(date).toISOString()
const formatServerDate = (date: any) => {
    return moment(date).format('DD/MM/YYYY')
}
const relativeDate = (date: string) => moment(date).fromNow();
const calendarDate = (date: string) => moment(date).format('L');

const displayScheduleDates = (startDate: string, endDate: string) => {
    return moment(startDate).format('DD/MM/YYYY HH:mm') + ' - ' + moment(endDate).format('HH:mm')
};

export {
    relativeDate, calendarDate, formatDate, formatServerDate, formatDateTime, displayScheduleDates
}