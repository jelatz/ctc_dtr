const TIMEZONE = "Asia/Manila";

/**
 * Normalize a datetime string from the DB (e.g. "2026-03-18 08:00:00")
 * to an ISO string with the Manila offset so the browser doesn't treat
 * it as local browser time.
 */
function toManilaDate(datetime) {
    if (!datetime) return null;
    // If already has timezone info (Z, +, -) leave it alone
    if (/[Z+\-]\d{2}:?\d{2}$/.test(String(datetime).trim())) {
        return new Date(datetime);
    }
    // Append +08:00 so the browser knows it's Manila time
    // For date-only strings (e.g. "2026-03-18"), we need a full ISO format
    const s = String(datetime).trim();
    const isDateOnly = /^\d{4}-\d{2}-\d{2}$/.test(s);
    return new Date(isDateOnly ? s + "T00:00:00+08:00" : s.replace(" ", "T") + "+08:00");
}

export function time(datetime) {
    if (!datetime) return "";
    const d = toManilaDate(datetime);
    return d.toLocaleTimeString("en-US", {
        timeZone: TIMEZONE,
        hour: "2-digit",
        minute: "2-digit",
        second: "2-digit",
    });
}

export function convertToLocalDate(dateTime) {
    if (!dateTime) return "";
    const d = toManilaDate(dateTime);
    return d.toLocaleDateString("en-US", {
        timeZone: TIMEZONE,
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
    });
}
