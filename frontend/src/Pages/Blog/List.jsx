import React, { Component } from 'react';
import BlogBaseTemplate from '../../Components/Blog/BlogBaseTemplate';
import SetPageTitle from '../../Components/SetPageTitle';

class List extends Component {
    componentDidMount() {
        SetPageTitle('Blog');
    }

    render() {
        return (
            <BlogBaseTemplate />
        )
    }
}

export default List
