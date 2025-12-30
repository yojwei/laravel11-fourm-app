import { DateTime } from "luxon"

const relativeDate = (date) => { return DateTime.fromISO(date).toRelative({ locale: 'en' }); };

export { relativeDate };