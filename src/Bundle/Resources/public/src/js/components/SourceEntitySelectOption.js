import React from 'react';
import _ from 'lodash';

class SourceEntitySelectOption extends React.Component {

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
                <td width="20"><input type="checkbox" name="selected[]" onChange={this.props.onChange} value={this.props.entity.id} /></td>
                {values}
            </tr>
        )
    }
}

export default SourceEntitySelectOption;