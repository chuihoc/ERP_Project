import axios from 'axios';

//Get host name
function getHostName(url) {
  var match = url.match(/:\/\/(www[0-9]?\.)?(.[^/:]+)/i);
  if (match !== null && match.length > 2 && typeof match[2] === 'string' && match[2].length > 0) {
  return match[2];
  }
  else {
      return null;
  }
}
//Global config 
if(!window.ISD_BASE_URL ||  window.ISD_BASE_URL === '') {
  if (process.env.NODE_ENV === 'development') {
      window.ISD_BASE_URL = 'http://localhost/erp/public/';
  } else {
      window.ISD_BASE_URL = window.location.protocol + '//' + getHostName(document.location.href) + '/api/public/';
  }

}

// Will add more code later
export function bootstrapApp() {

}

//JSON string return an object contain objects, we need to covert them to array
export function convertObjectsToArray(objects) {
  let objectsArr = [];
  Object.keys(objects).forEach((objectId) => {
    objectsArr.push({
      id: objectId,
      ...objects[objectId]
    })
  });
  return objectsArr;
}
export function convertArrayObjectToObject(objectArr, propId) {
  let objects = {};
  propId = propId || 'id';
  if(objectArr.length) {
    objectArr.forEach((object) => {
      objects[object[propId]] = object;
    })
  }
  return objects;
}
//Sort an array by property
export const sortArrayByProp = (prop, unsortArr) => {
  var sortedArray = unsortArr.sort((a, b) => {
    //Sort by prop
    if(a.hasOwnProperty(prop) && b.hasOwnProperty(prop)) {
      return a[prop] - b[prop];
    }
    //Try to sort by id
    if(a.hasOwnProperty('id') && b.hasOwnProperty('id')) {
      return a['id'] - b['id'];
    }
    //Will not sort invalid format array
    return 0;
  });
  return sortedArray;
};

export const getTokenHeader = () => {
  let token = sessionStorage.getItem('ISD_TOKEN');
  if(token !== "" && token !== null) {
    return {
      'Accept': 'application/json',
      'Content-Type': 'application/json',
      'Authorization': 'Bearer ' + token,
    };
  }
}
export const getUploadTokenHeader = () => {
  let token = sessionStorage.getItem('ISD_TOKEN');
  if(token !== "" && token !== null) {
    return {
      'Authorization': 'Bearer ' + token,
    };
  }
}

export const statusOptions = [
  {value: '0', text: 'Ngừng kích hoạt'},
  {value: '1', text: 'Kích hoạt'},
];

export const trangThaiPhieu = [
  {
    id: '0',
    value: '0',
    text: 'Không duyệt'
  },
  {
    id: '1',
    value: '1',
    text: 'Đã duyệt'
  },
  {
    id: '2',
    value: '2',
    text: 'Chưa duyệt'
  },
];

export const qcQAStatus = [
  {
    id: '2',
    value: '2',
    text: 'Chờ duyệt'
  },
  {
    id: '0',
    value: '0',
    text: 'Không đạt'
  },
  {
    id: '1',
    value: '1',
    text: 'Đạt'
  },
];

export const blankGanttData = {
  data: [],
  links: []
}

// setup axios

axios.defaults.baseURL = window.ISD_BASE_URL;

export function setAxiosAuthHeader() {
    const token = sessionStorage.getItem('ISD_TOKEN');

    if (token) {
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }
}

setAxiosAuthHeader();

export const getYearsDropdown = () => {
  const range = (start, end) => (
    Array.from(Array(end - start + 1).keys()).map(i => i + start)
  );
  let dynamicsYear = range(2018, (new Date()).getFullYear());
  
  let yearOptions = dynamicsYear.map((year) => {
    return {
      key: year,
      value: year,
      text: 'Năm ' + year
    }
  });
  return yearOptions;
}

export const getMonthOptions = () => {
  const monthOptions = [];

  for(let i = 1; i <= 12; i++) {
    monthOptions.push({
      key: i,
      value: i,
      text: `Tháng ${i}`
    });
  }
  return monthOptions;
}

export const fetchWithToken = (method = 'GET', url, cb = ()=>{}) => {
  fetch(url, {
    method: method,
    headers: getTokenHeader()
  })
    .then((response) => {
      return response.json();
    }).then((json) => {
      if(json.status && json.status == "error") {
        console.warn(json.message);
        return false;
      }
        cb(json);
    }).catch((error) => {
      console.log('parsing failed', error)
    });
}

export const axiosWithToken = axios.create({
  baseURL: window.ISD_BASE_URL,
  headers:  getTokenHeader()
});


