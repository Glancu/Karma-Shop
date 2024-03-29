import React, { Component } from 'react';
import Header from './Header';
import Footer from './Footer';
import '../../public/assets/js/vendor/bootstrap.min';

class BaseTemplate extends Component {
    render() {
        return (
            <div>
                <Header/>

                {this.props.children}

                <Footer/>
            </div>
        )
    }
}

export default BaseTemplate
