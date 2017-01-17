import React from 'react';
import Request from 'superagent';
import Alert from 'react-s-alert';
import 'react-s-alert/dist/s-alert-default.css';
import Sudoku from './Sudoku';

class Application extends React.Component {

    constructor(props) {
        super(props);

        this.onChange = this.onChange.bind(this);

        this.loadState();

        this.state = { sudoku: null };
    }

    loadState() {
        let loadPath = this.props.loadPath;
        if (this.state) {
            loadPath += '?values=' + this.state.sudoku.string;
        }
        Request.get(loadPath).then((response) => {
            this.setState(response.body);
        });
    }

    onChange(event) {
        this.state.sudoku.cells[event.target.name].value = event.target.value;
        this.updateStringRepresentation();
        this.setState(this.state);
        this.loadState();
    }

    updateStringRepresentation() {
        let representation = "";
        for (let rowIndex = 0; rowIndex < 9; rowIndex++) {
            for (let colIndex = 0; colIndex < 9; colIndex++) {
                let value = this.state.sudoku.cells[rowIndex + "" + colIndex].value;
                if (value == '') {
                    value = 0;
                }
                representation += "" + value;
            }
        }
        this.state.sudoku.string = representation;
    }

    render() {

        if (this.state.sudoku == null) {
            return <div>No sudoku loaded</div>
        }

        let url = this.props.linkPath + "?values=" + this.state.sudoku.string;

        return (
            <div>
                <Sudoku cells={this.state.sudoku.cells} onChange={this.onChange} />
                <a href={url}>Permalink</a>
            </div>
        );
    }
}

export default Application;
