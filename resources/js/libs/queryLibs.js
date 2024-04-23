export const parseQueryFilters = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const filters = {};
  
    for (const [key, value] of urlParams) {
      let parsedValue;  
      if (value.startsWith('[') && value.endsWith(']')) {
        const content = value.substring(1, value.length - 1);
        parsedValue = content.split(',');
      } else {
        parsedValue = value;
      }  
      if(parsedValue)
        filters[key] = parsedValue;
    }
  
    return filters;
  };
  
  export const updateQueryFilters = (filters) => {
    let queryString = '';
  
    Object.keys(filters).forEach(key => {
      const value = filters[key];
  
      if (Array.isArray(value) && value.length > 0) {
        queryString += `${key}=[${value.join(',')}]&`;
      } else {
        queryString += `${key}=${value}&`;
      }
    });
  
    const newQueryString = queryString.slice(0, -1);
    const newUrl = `${window.location.pathname}?${newQueryString}`;
    
    window.history.replaceState(null, '', newUrl);
  };
  
  

  
