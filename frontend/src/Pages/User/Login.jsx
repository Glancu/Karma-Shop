import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import axios from 'axios';
import ValidateEmail from '../../Components/ValidateEmail';
import CONFIG from '../../config';

import imgLogin from '../../../public/assets/img/login.jpg';

const userStorageLoginToken = CONFIG.user.storage_login_token;
const userStorageLoginRefreshToken = CONFIG.user.storage_login_refresh_token;

class Login extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            password: '',
            keepLoggedIn: false,
            errors: {
                email: '',
                password: ''
            },
            errorMessage: '',
            noticeMessage: '',
            locationReferrer: ''
        };

        this.handleChange = this.handleChange.bind(this);
        this.toggleLoggedIn = this.toggleLoggedIn.bind(this);
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

        let token = localStorage.getItem(userStorageLoginToken);
        if(!token) {
            token = sessionStorage.getItem(userStorageLoginToken);
        }
        if(token) {
            axios.post("/api/user/validate-token", {'token': token}, {
                headers: { 'Content-Type': 'application/json' }
            }).then(result => {
                if(result.data.error === false) {
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

    toggleLoggedIn(e) {
        this.setState({keepLoggedIn: e.target.checked});
    }

    addUserTokenToStorage(token, refreshToken = null) {
        const {keepLoggedIn} = this.state;

        if(keepLoggedIn) {
            localStorage.setItem(userStorageLoginToken, token);
            localStorage.setItem(userStorageLoginRefreshToken, refreshToken);
        } else {
            sessionStorage.setItem(userStorageLoginToken, token);
            sessionStorage.setItem(userStorageLoginRefreshToken, refreshToken);
        }
    }

    handleSubmit(e) {
        e.preventDefault();
        const { email, password } = this.state;
        let errors = this.state.errors;

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

            axios.post("/api/user/generate-token", {
                email,
                password
            }).then(result => {
                const token = result.data ? result.data.token : null;
                if (result.status === 200 && token) {
                    this.addUserTokenToStorage(token, result.data.refresh_token);

                    const locationReferrer = this.state.locationReferrer;

                    return locationReferrer ?
                        this.props.history.push(locationReferrer) :
                        this.props.history.push('/');

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
                                    { errorMessage &&
                                        <span className="form-error-message">{errorMessage}</span>
                                    }

                                    { noticeMessage &&
                                        <span className="form-notice-message">{noticeMessage}</span>
                                    }

                                    <form className="row login_form"
                                          id="contactForm"
                                          onSubmit={this.handleSubmit}
                                    >
                                        <div className="col-md-12 form-group">
                                            {errors.email &&
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
                                            {errors.password &&
                                                <span className='error-message-input'>{errors.password}</span>}
                                            <input type="password"
                                                   className="form-control"
                                                   id="password"
                                                   name="password"
                                                   placeholder="Password"
                                                   value={this.state.value}
                                                   defaultValue='current-password'
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <div className="creat_account">
                                                <input type="checkbox"
                                                       id="f-option2"
                                                       name="selector"
                                                       onClick={this.toggleLoggedIn}
                                                />
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
