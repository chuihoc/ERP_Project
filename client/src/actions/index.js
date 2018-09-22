import actionTypes from "./ACTION_TYPES";

export const startApp = defaultProps => ({
  type: actionTypes.START_APP,
  defaultProps
});

/**
 *
 * @param {object} updateData
 */
export const updateStateData = updateData => ({
  type: actionTypes.UPDATE_STATE_DATA,
  updateData
});

export const updateReportStateData = updateData => ({
  type: actionTypes.UPDATE_REPORT_STATE_DATA,
  updateData
});

/**
 * Update trello task actions
 */

export const updateData = data => {
  return {
    type: actionTypes.UPDATE_DATA,
    payload: data
  };
};

export const deleteItem = dataItem => (dispatch, getState) => {
  const state = getState();
  const temp = [...state.viewPage.data];
  const result = temp.filter(item => item !== dataItem);
  dispatch({
    type: actionTypes.DELETE_ITEM,
    payload: result
  });
};

export const addItem = dataItem => (dispatch, getState) => {
  const state = getState();
  const temp = [dataItem, ...state.viewPage.data];
  dispatch({
    type: actionTypes.ADD_ITEM,
    payload: temp
  });
};

export const editItem = newData => (dispatch, getState) => {
  dispatch({
    type: actionTypes.EDIT_ITEM,
    payload: newData
  });
};
