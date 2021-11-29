import React, { Component } from 'react';
import { Redirect } from 'react-router-dom';
import CONFIG from '../../config';

class Logout extends Component {
    componentDidMount() {
        const userStorageLoginToken = CONFIG.user.storage_login_token;
        const userStorageLoginRefreshToken = CONFIG.user.storage_login_refresh_token;

        let token = localStorage.getItem(userStorageLoginToken);
        if(!token) {
            token = sessionStorage.getItem(userStorageLoginToken);
        }
        if(token) {
            localStorage.removeItem(userStorageLoginToken);
            localStorage.removeItem(userStorageLoginRefreshToken);

            sessionStorage.removeItem(userStorageLoginToken);
            sessionStorage.removeItem(userStorageLoginRefreshToken);
        }
    }

    render() {
        return <Redirect to="/" />
    }
}

export default Logout;
