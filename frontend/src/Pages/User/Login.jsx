import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import axios from 'axios';
import ValidateEmail from '../../Components/ValidateEmail';

import imgLogin from '../../../public/assets/img/login.jpg';

class Login extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            password: '',
            errors: {
                email: '',
                password: ''
            },
            errorMessage: '',
            noticeMessage: '',
            locationReferrer: ''
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        const locationState = this.props.location.state;
        if(locationState && locationState.referrer) {
            this.setState({
                noticeMessage: locationState.message ?? 'Your session was expired. Login in again.',
                locationReferrer: locationState.referrer
            });
        }

        const token = localStorage.getItem(process.env.LOGIN_TOKEN_STORAGE_PREFIX);

        const formData = new FormData();
        formData.append('token', token);

        if(token) {
            axios.post("/api/user/validate_token", formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            }).then(result => {
                if(result.data.success) {
                    return this.props.history.push('/');
                }
            }).catch(e => {
                console.error(e)
            });
        }
    }

    handleChange(event) {
        const { name, value } = event.target;
        if(name === 'email' || name === 'password') {
            this.setState({
                [name]: value
            });

            this.setState({errorMessage: ''});
        }
    }

    handleSubmit(event) {
        event.preventDefault();
        const { email, password } = this.state;
        let errors = this.state.errors;

        // Clear error message while send form
        this.setState({errorMessage: ''});

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.password = password.length < 3 ?
            'Password must be 3 characters long!' :
            '';

        this.setState({errors});

        if(errors.email.length === 0 && errors.password.length === 0) {
            const errorMessageStr = 'Bad email or password. Try again.';

            // getting token to login with email and password
            axios.post("/api/login_check", {
                email,
                password
            }).then(result => {
                const token = result.data ? result.data.token : null;
                if (result.status === 200 && token) {
                    localStorage.setItem(process.env.LOGIN_TOKEN_STORAGE_PREFIX, token);

                    const locationReferrer = this.state.locationReferrer;
                    if(locationReferrer) {
                        return this.props.history.push(locationReferrer)
                    }

                    return this.props.history.push('/');
                } else {
                    this.setState({errorMessage: errorMessageStr, noticeMessage: ''});
                }
            }).catch(() => {
                this.setState({errorMessage: errorMessageStr, noticeMessage: ''});
            });
        }
    }

    render() {
        const {errors, errorMessage, noticeMessage} = this.state;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Login/Register</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Login/Register</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="login_box_area section_gap">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-6">
                                <div className="login_box_img">
                                    <img className="img-fluid" src={imgLogin} alt="" />
                                        <div className="hover">
                                            <h4>New to our website?</h4>
                                            <p>There are advances being made in science and technology everyday, and a
                                                good example of this is the</p>
                                            <Link to='/register' className='primary-btn'>Create an Account</Link>
                                        </div>
                                </div>
                            </div>
                            <div className="col-lg-6">
                                <div className="login_form_inner">
                                    <h3>Log in to enter</h3>
                                    { errorMessage ?
                                        <span className="form-error-message">{errorMessage}</span> :
                                        null
                                    }

                                    { noticeMessage ?
                                        <span className="form-notice-message">{noticeMessage}</span> :
                                        null
                                    }

                                    <form className="row login_form"
                                          id="contactForm"
                                          onSubmit={this.handleSubmit}
                                    >
                                        <div className="col-md-12 form-group">
                                            {errors.email > 0 &&
                                                <span className='error-message-input'>{errors.email}</span>}
                                            <input type="text"
                                                   className="form-control"
                                                   id="email"
                                                   name="email"
                                                   placeholder="E-mail"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            {errors.password > 0 &&
                                                <span className='error-message-input'>{errors.password}</span>}
                                            <input type="password"
                                                   className="form-control"
                                                   id="password"
                                                   name="password"
                                                   placeholder="Password"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <div className="creat_account">
                                                <input type="checkbox" id="f-option2" name="selector" />
                                                <label htmlFor="f-option2">Keep me logged in</label>
                                            </div>
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <button type="submit" value="submit" className="primary-btn">
                                                Log In
                                            </button>
                                            <a href="#">Forgot Password?</a>
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

export default Login
