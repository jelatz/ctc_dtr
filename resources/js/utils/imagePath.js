export function getImagePath(name) {
    return new URL(`../images/${name}`, import.meta.url).href;
}