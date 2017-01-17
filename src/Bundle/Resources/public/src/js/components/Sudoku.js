import React from 'react';
import Cell from './Cell';

class Sudoku extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        let sudoku = this;

        let rows = [];
        for (let rowIndex = 0; rowIndex < 9; rowIndex++) {
            let columns = [];
            for (let colIndex = 0; colIndex < 9; colIndex++) {
                let key = rowIndex + "" + colIndex;
                columns.push(<Cell key={key} name={key} value={this.props.cells[key].value} options={this.props.cells[key].options} onChange={this.props.onChange} />)
            }
            rows.push(<div key={rowIndex} className="row">{columns}</div>)
        }

        return (
            <div id="sudoku">
                {rows}
            </div>
        );
    }
}

export default Sudoku;