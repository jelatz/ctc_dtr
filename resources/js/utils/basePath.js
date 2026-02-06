export default function {
    const url = new URL("../", import.meta.env.VITE_APP_BASE);
    return url.href;
}
