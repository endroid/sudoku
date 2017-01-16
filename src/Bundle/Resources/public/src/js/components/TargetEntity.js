import React from 'react';

class TargetEntity extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {

        let display = this;

        if (!this.props.entity) {
            return (
                <div className="box-header with-border">
                    <h3 className="box-title">Target data</h3>
                </div>
            )
        }

        let values = [];
        _.each(this.props.fields, function(field, index) {
            if (field != 'id') {
                values.push(
                    <tr key={index}>
                        <td><strong>{field}</strong></td>
                        <td width="100%">{display.props.entity[field]}</td>
                    </tr>
                )
            }
        });

        return (
            <div className="box">
                <div className="box-header with-border">
                    <h3 className="box-title">Target data</h3>
                </div>
                <div className="box-body">
                    <table className="table table-bordered">
                        <tbody>
                            <tr>
                                <td><strong>id</strong></td>
                                <td width="100%">{this.props.entity.id}</td>
                            </tr>
                            {values}
                            <tr>
                                <td>&nbsp;</td>
                                <td><input type="submit" value="Merge" onClick={this.props.onClick} /></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        )
    }
}

export default TargetEntity;