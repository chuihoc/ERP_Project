import { createStore, applyMiddleware } from "redux";
import reducer from "./../reducers";
import { createLogger } from "redux-logger";
import thunk from "redux-thunk";

//const sagaMiddleware = createSagaMiddleware()
//const middleware = applyMiddleware(sagaMiddleware)
let store;
let middleware = applyMiddleware(thunk);
if (process.env.NODE_ENV === "development") {
  middleware = applyMiddleware(createLogger(), thunk);
  store = createStore(reducer, middleware);
} else {
  store = createStore(reducer, middleware);
}
export default store;
