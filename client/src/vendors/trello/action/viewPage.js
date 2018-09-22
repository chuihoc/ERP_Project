import actionTypes from "./../../../actions/ACTION_TYPES";

export const updateData = data => {
  return {
    type: actionTypes.UPDATE_DATA,
    payload: data
  };
};

export const deleteItem = dataItem => (dispatch, getState) => {
  const state = getState();
  const temp = [...state.trelloReducer.data];
  const result = temp.filter(item => item !== dataItem);
  dispatch({
    type: actionTypes.DELETE_ITEM,
    payload: result
  });
};

export const addItem = dataItem => (dispatch, getState) => {
  const state = getState();
  const temp = [dataItem, ...state.trelloReducer.data];
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
