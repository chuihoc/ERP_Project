import React from 'react';
import ReactDOM from 'react-dom';
import './index.css';
import 'assets/semantic/semantic.min.css'
import 'assets/styles/style.css'
import App from './App';
import { LocaleProvider } from 'antd';
import vi_VN from 'antd/lib/locale-provider/vi_VN';
import 'moment/locale/vi';

ReactDOM.render(
<LocaleProvider locale={vi_VN}>
    <App />
</LocaleProvider>,
    document.getElementById('root')
);

