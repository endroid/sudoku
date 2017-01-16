import React from 'react';
import TargetEntitySelectOption from './TargetEntitySelectOption';
import _ from 'lodash';

class TargetEntitySelect extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        let select = this;

        let headers = [];
        _.each(this.props.fields, function(field, index) {
            headers.push(
                <th key={index}>{field}</th>
            )
        });

        let options = [];
        _.each(this.props.entities, function(entity) {
            options.push(
                <TargetEntitySelectOption entity={entity} fields={select.props.fields} onChange={select.props.onChange} key={entity.id} />
            );
        });

        return (
            <div className="box">
                <div className="box-header with-border">
                    <h3 className="box-title">Choose target</h3>
                </div>
                <div className="box-body">
                    <table className="table table-bordered">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                {headers}
                            </tr>
                        </thead>
                        <tbody>
                            {options}
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}

export default TargetEntitySelect;
