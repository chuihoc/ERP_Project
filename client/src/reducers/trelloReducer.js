import actionTypes from "actions/ACTION_TYPES";

let data = [
  {
    cardId: 0,
    id: 1,
    text: "Project #1",
    start_date: "01-04-2018",
    due_time: "02-09-2018",
    duration: 15,
    order: 10,
    progress: 0,
    open: true,
    parent: 1,
    assigned: [1, 2, 3]
  },
  {
    cardId: 1,
    id: 2,
    text: "Task #1",
    start_date: "02-04-2018",
    due_time: "02-09-2018",
    duration: 10,
    order: 10,
    progress: 0.9,
    open: true,
    parent: 1,
    assigned: []
  },
  {
    cardId: 1,
    id: 6,
    text: "Task #3",
    start_date: "02-05-2018",
    due_time: "02-09-2018",
    duration: 1,
    order: 10,
    progress: 0.5,
    open: true,
    parent: 1,
    assigned: []
  },
  {
    cardId: 2,
    id: 16,
    text: "Project #7",
    start_date: "25-04-2018",
    due_time: "02-09-2018",
    duration: 10,
    order: 20,
    progress: 0,
    open: true,
    parent: 1,
    assigned: []
  },
  {
    cardId: 0,
    id: 9,
    text: "Project #12",
    start_date: "01-04-2018",
    due_time: "02-09-2018",
    duration: 1,
    order: 10,
    progress: 0,
    open: true,
    parent: 1,
    assigned: []
  }
];

let initialState = {
  data: data,
  isFetching: false
};

const viewPage = (state = initialState, action) => {
  switch (action.type) {
    case actionTypes.UPDATE_DATA:
      return {
        ...state,
        data: action.payload,
        isFetching: !state.isFetching
      };
    case actionTypes.DELETE_ITEM:
      return {
        ...state,
        data: action.payload
      };
    case actionTypes.ADD_ITEM:
      return {
        ...state,
        data: action.payload
      };
    case actionTypes.EDIT_ITEM:
      return {
        ...state,
        data: action.payload
      };
    default:
      return state;
  }
};
export default viewPage;
