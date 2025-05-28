export function isPastDate(date){
    if (!date) return false

    const input = new Date(date)
    const today = new Date()

    input.setHours(0, 0, 0, 0)
    today.setHours(0, 0, 0, 0)

    return input < today
}
