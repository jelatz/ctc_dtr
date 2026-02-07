// export default function () {
//     return import.meta.env.VITE_APP_BASE;
// }

export default function getBasePath() {
    // Get the base path from meta tag or default to '/'
    const base = document.querySelector('meta[name="asset-url"]')?.content || '';
    return base.endsWith('/') ? base : base + '/';
}