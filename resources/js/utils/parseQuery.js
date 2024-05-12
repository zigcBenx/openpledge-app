export const parseQueryFilters = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const filters = [];

    for (const [key, value] of urlParams) {
      if (value.indexOf(',') !== -1) {
        const labels = value.split(',');
        labels.map((value) => {
          filters.push({key,value})
        });
      } else if(key == 'date' && value != "null"){
        let parsedDate = value.split('-');
        filters.push({key: 'date', value: {month: parsedDate[0], year: parsedDate[1]}});
      } else if(key == 'range' && value != "null"){
        let parsedRange = value.split('-');
        filters.push({key: 'range', value: {start: parsedRange[0], end: parsedRange[1]}});
      }
    }  
    return filters;
  };
  
  export const updateQueryFilters = (filters) => {
    let prepareFilters = [];
    let queryString = '';

    filters.map(({key,value}) => {
      if(key == 'date' && value != null){
        if(value.month && value.year)
          queryString += `${key}=${value.month}-${value.year}&`;
      } else if(key == 'range' && value != null){
        if(value.start && value.end)
          queryString += `${key}=${value.start}-${value.end}&`;
      }
      else if(!prepareFilters[key]) {
        prepareFilters[key] =  `${value}`;
      }
      else {
        prepareFilters[key] += `,${value}`;
      }
    });

    for (const key in prepareFilters) { 
      queryString += `${key}=${prepareFilters[key]}&`;
    }
  
    const newUrl = `${window.location.pathname}?${queryString.slice(0, -1)}`;
    window.history.replaceState(null, '', newUrl);
  };