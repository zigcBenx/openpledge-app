export const formatExpireDate = (value) => {
    let input = value.replace(/[^\d/]/g, '');
    const slashIndex = input.indexOf('/');
    if (slashIndex !== -1) {
      input = input.slice(0, slashIndex + 1) + input.slice(slashIndex).replace(/\//g, '');
    }
    return input;
}