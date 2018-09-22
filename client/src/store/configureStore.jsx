import { applyMiddleware, createStore } from "redux";
import { createLogger } from "redux-logger";
import reducer from "./../reducers";
import thunk from "redux-thunk";
//import createSagaMiddleware, {END} from 'redux-saga'

//const sagaMiddleware = createSagaMiddleware()
const middleware = applyMiddleware(createLogger(), thunk);
const store = createStore(reducer, middleware);
//store.runSaga = sagaMiddleware.run
//store.close = () => store.dispatch(END)
export default store;
