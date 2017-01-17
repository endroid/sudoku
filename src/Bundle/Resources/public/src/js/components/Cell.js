import React from 'react';

class Cell extends React.Component {

    constructor(props) {
        super(props);

        this.state = { value: this.props.value };

        this.onTextChange = this.onTextChange.bind(this);
    }

    onTextChange(event) {
        this.setState({ value: event.target.value })
        this.props.onChange(event);
    }

    render() {
        return (
            <div width="11%">
                <input name={this.props.name} type="number" min="1" max="9" value={this.state.value} onChange={this.onTextChange} />
            </div>
        )
    }
}

export default Cell;