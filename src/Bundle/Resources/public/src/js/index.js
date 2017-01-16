import React from 'react';
import ReactDOM from 'react-dom';
import Application from './components/Application';

ReactDOM.render(
    <Application loadPath={appConfig.loadPath} solvePath={appConfig.solvePath} />,
    document.getElementById('application')
);
