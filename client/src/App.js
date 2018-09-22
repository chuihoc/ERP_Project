import React from 'react'
import MainComponent from './components/MainComponent'
import {Provider} from 'react-redux'
import store from './store/configureStoreProduction'
import createBrowserHistory from 'history/createBrowserHistory';
import { Router } from 'react-router-dom';
import 'ISD_API';
import 'antd/dist/antd.css'

const history = createBrowserHistory();

const App = () => {
    return (

        <Provider store={store}>
            <Router history={history}>
                <MainComponent />
            </Router>
        </Provider>
    )
};

export default App;

