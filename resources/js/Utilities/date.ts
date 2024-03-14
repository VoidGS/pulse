import 'moment/dist/locale/pt-br'
import moment from 'moment-timezone'

moment.tz.setDefault('America/Sao_Paulo')
const relativeDate = (date: string) => moment(date).fromNow();
const calendarDate = (date: string) => moment(date).format('L');

const inputDate = (date: string) => moment(date).format('L');

export {
    relativeDate, calendarDate, inputDate
}