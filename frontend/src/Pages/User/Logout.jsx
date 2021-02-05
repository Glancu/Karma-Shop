import React, { Component } from 'react';
import { Redirect } from 'react-router-dom';

class Logout extends Component {
    componentDidMount() {
        const token = localStorage.getItem(process.env.LOGIN_TOKEN_STORAGE_PREFIX);
        if(token) {
            localStorage.removeItem(process.env.LOGIN_TOKEN_STORAGE_PREFIX);
        }
    }

    render() {
        return <Redirect to="/" />
    }
}

export default Logout;
