export function time(datetime) {
    if (!datetime) return "";
    const d = new Date(datetime);
    return d.toLocaleTimeString([], {
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
}

export function convertToLocalDate(dateTime) {
    if (!dateTime) return "";
    const d = new Date(dateTime);
    return d.toLocaleDateString([], {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });
}
