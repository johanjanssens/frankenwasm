export default {};
export const pathToFileURL = (p) => ({ href: 'file://' + p, toString: () => 'file://' + p });
export const fileURLToPath = (u) => u.replace('file://', '');
