import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import BaseTemplate from '../../Components/BaseTemplate';
import ValidateEmail from '../../Components/ValidateEmail';
import axios from 'axios';

class ForgotPassword extends Component {
    constructor(props) {
        super(props);

        this.state = {
            email: null,
            errorMessage: null,
            noticeMessage: null
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(e) {
        this.setState({email: e.target.value, errorMessage: null, noticeMessage: null});
    }

    handleSubmit(e) {
        e.preventDefault();

        const {email, errorMessage} = this.state;

        if(!ValidateEmail(email)) {
            this.setState({errorMessage: 'Email is not valid.'});
            return;
        }

        if(errorMessage || noticeMessage) {
            return;
        }

        axios.post('/api/user/forgot-password', {email})
            .then(result => {
                if(result.data) {
                    if(result.data.error === true && result.data.message) {
                        this.setState({errorMessage: result.data.message})
                    } else if(result.data.error === false && result.data.message) {
                        this.setState({noticeMessage: result.data.message})
                    } else {
                        this.setState({errorMessage: 'Something went wrong. Try again.'});
                    }
                } else {
                    this.setState({errorMessage: 'Something went wrong. Try again.'});
                }
            })
            .catch(err => {
                if(err.response) {
                    const response = err.response;
                    if(response.status === 400 && response.data.error === true && response.data.message) {
                        this.setState({errorMessage: response.data.message});
                    }
                } else {
                    console.log('err', err);
                }
            })
    }

    render() {
        const {email, errorMessage, noticeMessage} = this.state;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Panel</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Forgot password</p>
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
                                    <h3>Forgot password</h3>

                                    {errorMessage &&
                                    <span className="form-error-message">{errorMessage}</span>
                                    }

                                    {noticeMessage &&
                                    <span className="form-notice-message">{noticeMessage}</span>
                                    }

                                    <form className="row login_form"
                                          id="changePassword"
                                          onSubmit={this.handleSubmit}
                                    >
                                        <div className="col-md-12 form-group">
                                            <input type="email"
                                                   className="form-control"
                                                   id="email"
                                                   name="email"
                                                   placeholder="Email"
                                                   defaultValue={email}
                                                   required={true}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <button type="submit" value="submit" className="primary-btn">
                                                Forgot password
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default ForgotPassword;
