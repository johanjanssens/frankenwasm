export default {};
export const resolve = (...args) => args[args.length - 1] || '';
export const join = (...args) => args.join('/');
export const dirname = (p) => p.replace(/\/[^/]*$/, '');
export const basename = (p) => p.replace(/^.*\//, '');
export const extname = (p) => { const m = p.match(/\.[^.]*$/); return m ? m[0] : ''; };
export const isAbsolute = () => false;
export const sep = '/';
