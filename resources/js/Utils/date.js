import dayjs from 'dayjs';
import utc from 'dayjs/plugin/utc';
import timezone from 'dayjs/plugin/timezone';
import isSameOrBefore from 'dayjs/plugin/isSameOrBefore';

dayjs.extend(utc);
dayjs.extend(timezone);
dayjs.extend(isSameOrBefore);

const userTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone;

export { dayjs };

export function isTodayOrPast(date){
    if (!date) return false;

    const input = dayjs(date).tz(userTimezone).startOf('day');
    const today = dayjs().tz(userTimezone).startOf('day');

    return input.isSameOrBefore(today);
}

export function formatUTC(isoDate, format){
    if (!isoDate) return null;
    return dayjs.utc(isoDate).tz(userTimezone).format(format);
}

export function formatDate(isoDate, format){
    if (!isoDate) return null;
    return dayjs(isoDate).tz(userTimezone).format(format);
}