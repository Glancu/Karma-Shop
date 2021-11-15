import React, { Component } from 'react';
import { userLoggedIn } from '../Components/User/UserData';
import { Redirect, Route } from 'react-router-dom';

class ClientPanelRoute extends Component {
    constructor(props) {
        super(props);

        this.state = {
            userLoggedIn: false,
            redirect: false
        }
    }

    componentDidMount() {
        userLoggedIn().then((isUserLoggedIn) => {
            this.setState({userLoggedIn: isUserLoggedIn, redirect: !isUserLoggedIn});
        });
    }

    render() {
        const {...rest} = this.props;

        if(this.state.userLoggedIn !== true) {
            if(this.state.redirect === false) {
                return <div>loading...</div>;
            }

            return <Redirect to="/"/>;
        }

        return <Route {...rest} render={(props) => (
            <Component {...props} />
        )}/>
    }
}

export default (ClientPanelRoute);
