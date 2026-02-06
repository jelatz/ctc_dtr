export default function getBasePath() {
    const url = new URL("../", import.meta.url);
    return url.href;
}
