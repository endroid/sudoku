import React from 'react';
import ReactDOM from 'react-dom';
import Application from './components/Application';

ReactDOM.render(
    <Application values={appConfig.values} loadPath={appConfig.loadPath} linkPath={appConfig.linkPath} solvePath={appConfig.solvePath} />,
    document.getElementById('application')
);
