import React, { Component } from 'react';
import BlogBaseTemplate from '../../Components/Blog/BlogBaseTemplate';

class Tag extends Component {
    constructor(props) {
        super(props);

        this.state = {
            slug: props.match.params.slug
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {slug} = this.state;
        if(this.props.match.params.slug !== slug) {
            this.setState({slug: this.props.match.params.slug});
        }
    }

    render() {
        return (
            <BlogBaseTemplate tag={this.state.slug} />
        )
    }
}

export default Tag
