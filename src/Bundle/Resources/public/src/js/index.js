import React from 'react';
import ReactDOM from 'react-dom';
import Application from './components/Application';

ReactDOM.render(
    <Application loadPath={appConfig.loadPath} linkPath={appConfig.linkPath} solvePath={appConfig.solvePath} />,
    document.getElementById('application')
);
