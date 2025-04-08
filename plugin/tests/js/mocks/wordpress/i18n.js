export const __ = (text) => text;
export const _n = (single, plural, count) => (count === 1 ? single : plural);
export const sprintf = (format, ...args) => {
  let i = 0;
  return format.replace(/%s/g, () => args[i++]);
};

export default {
  __,
  _n,
  sprintf,
};
