export const preventStringInputWithNumber = (value) => {
  return value.toString().replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');
};