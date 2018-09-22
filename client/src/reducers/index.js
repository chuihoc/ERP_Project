import { combineReducers } from "redux";
import mainReducer from "./mainReducer";
import uiReducer from "./uiReducer";
import reportReducer from "./reportReducer";
import trelloReducer from "./trelloReducer";

import undoable from "redux-undo";
import actionTypes from "actions/ACTION_TYPES";

let includeActions = [actionTypes.UPDATE_STATE_DATA];
let customFilter = (action, currentState, previousHistory) => {
  if (includeActions.indexOf(action.type) !== -1) {
    return true;
  }
  return false;
};
const allReducers = combineReducers({
  main: undoable(mainReducer, {
    filter: customFilter,
    limit: 100
  }),
  ui: uiReducer,
  report: reportReducer,
  trelloReducer: trelloReducer
});

export default allReducers;
