import 'moment/dist/locale/pt-br'
import moment from 'moment-timezone'

moment.tz.setDefault('America/Sao_Paulo')
const relativeDate = (date: string) => moment(date).fromNow();

export {
    relativeDate
}