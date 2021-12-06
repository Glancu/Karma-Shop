import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import BaseTemplate from '../../../Components/BaseTemplate';

class Notify extends Component {
    render() {
        const urlSearch = window.location.search;

        const urlParamsArr = new URLSearchParams(urlSearch);

        let error = false;
        let message = '';

        if(urlParamsArr.has('error')) {
            error = urlParamsArr.get('error');
        }

        if(urlParamsArr.has('message')) {
            message = urlParamsArr.get('message');
        }

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Notify</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Payment notify</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="login_box_area section_gap">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-12">
                                <div className="login_form_inner">
                                    <h2 className={error ? 'form-error-message' : 'form-notice-message'}>{message}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Notify;
