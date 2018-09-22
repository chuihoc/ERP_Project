import actionTypes from 'actions/ACTION_TYPES'
import {cloneDeep} from 'lodash'
import moment from 'moment'

var today = new Date();

let _designDefault = {
    filter: {
      year: (new Date()).getFullYear(),
      product: 'all',
      area: 'all',
      quarter: moment().quarter(),
      month: today.getMonth(),
      week: 1,
      province: '1',//Hanoi = 1
      district: '', // Quan Ba Dinh
      store_id: '',
      delivery_id: '',
      fromdate: '',
      todate: '',
      doanhthu: '',
      doanhso: '',
      limit: '10',
      tdv: ''
    },
    products: [],
    provinces: [],
    districts: [],
    stores: [],
    delivery: [],
    tdvs: [],
    reportBy: window.ISD_CURRENT_PAGE ? window.ISD_CURRENT_PAGE : '',
    showLogin: true,
  },
  cloneState;

export default (state = _designDefault, action) => {
  switch (action.type) {
    case actionTypes.UPDATE_REPORT_STATE_DATA:
      cloneState = cloneDeep(state);
      if(action.updateData && action.updateData.showLogin) {
        //Clean localStore
        sessionStorage.setItem('ISD_TOKEN', '');
      }
      cloneState = {
        ...cloneState,
        ...action.updateData
      }
      return cloneState;
      break;
  }
  return state;
}
