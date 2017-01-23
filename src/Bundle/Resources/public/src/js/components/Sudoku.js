import React from 'react';
import Cell from './Cell';

class Sudoku extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        let tbodyArray = [];
        for (let tbodyIndex = 0; tbodyIndex < 3; tbodyIndex++) {
            let trArray = [];
            for (let trIndex = 0; trIndex < 3; trIndex++) {
                let tdArray = [];
                for (let tdIndex = 0; tdIndex < 9; tdIndex++) {
                    let rowIndex = tbodyIndex * 3 + trIndex;
                    let key = rowIndex + "" + tdIndex;
                    tdArray.push(<td key={tdIndex}><Cell name={key} value={this.props.cells[key].value} options={this.props.cells[key].options} onChange={this.props.onChange} /></td>);
                }
                trArray.push(<tr key={trIndex}>{tdArray}</tr>);
            }
            tbodyArray.push(<tbody key={tbodyIndex}>{trArray}</tbody>);
        }

        let valid = this.props.valid ? 'valid' : 'invalid';

        return (
            <table id="sudoku" className={valid}>
                <colgroup><col></col><col></col><col></col></colgroup>
                <colgroup><col></col><col></col><col></col></colgroup>
                <colgroup><col></col><col></col><col></col></colgroup>
                {tbodyArray}
            </table>
        );
    }
}

export default Sudoku;