import React from 'react';
import Request from 'superagent';
import Alert from 'react-s-alert';
import 'react-s-alert/dist/s-alert-default.css';
import _ from 'lodash';

class Application extends React.Component {

    constructor(props) {
        super(props);

        this.onChange = this.onChange.bind(this);

        this.loadState();

        this.state = { sudoku: null };
    }

    loadState() {
        Request.get(this.props.loadPath).then((response) => {
            console.log(response.body);
            this.setState(response.body);
        });
    }

    onChange(event) {
        console.log(event.target.value);
    }

    render() {

        if (this.state.sudoku == null) {
            return <div>No sudoku loaded</div>
        }

        let sudoku = this;

        let cells = [];
        _.each(this.state.sudoku, function(cell, index) {
            cells.push(
                <div key={index}>
                    <input type="text" value={cell.value} onChange={sudoku.onChange} />
                </div>
            )
        });

        return (
            <div className="row">
                {cells}
            </div>
        )



        // let targetEntity = null;
        // let sourceEntities = [];
        // for (let entity of this.state.entities) {
        //     if (this.state.sources.includes(entity.id)) {
        //         sourceEntities.push(entity);
        //     }
        //     if (this.state.target == entity.id) {
        //         targetEntity = entity;
        //     }
        // }
        //
        // return (
        //     <div className="row">
        //         <Alert stack={{limit: 3}} />
        //         <div className="col-md-4">
        //             <SourceEntitySelect entities={this.state.entities} fields={this.state.fields} onChange={this.onSourceEntityChange} />
        //         </div>
        //         <div className="col-md-4">
        //             <TargetEntitySelect entities={sourceEntities} fields={this.state.fields} onChange={this.onTargetEntityChange} />
        //         </div>
        //         <div className="col-md-4">
        //             <TargetEntity entity={targetEntity} fields={this.state.fields} onClick={this.onMergeClick} />
        //         </div>
        //     </div>
        // );
    }
}

export default Application;
