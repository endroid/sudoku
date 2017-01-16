import React from 'react';
import _ from 'lodash';

class TargetEntitySelectOption extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        let option = this;

        let values = [];
        _.each(this.props.fields, function(field, index) {
            values.push(
                <td key={index}>{option.props.entity[field]}</td>
            )
        });

        return (
            <tr>
                <td width="20px"><input type="radio" name="target" onChange={this.props.onChange} value={this.props.entity.id} /></td>
                {values}
            </tr>
        )
    }
}

export default TargetEntitySelectOption;