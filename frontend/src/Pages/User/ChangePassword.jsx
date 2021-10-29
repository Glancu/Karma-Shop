import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import removeUserTokensStorage, { userData } from '../../Components/User/UserData';
import axios from 'axios';
import CONFIG from '../../config';
import { toast } from 'react-toastify';

class ChangePassword extends Component {
    constructor(props) {
        super(props);

        this.state = {
            userEmail: null,
            userUuid: null,
            form: {
                oldPassword: '',
                newPassword: '',
                newPasswordRepeat: ''
            },
            errorMessage: null,
            noticeMessage: null
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        userData().then(data => {
            this.setState({userEmail: data.email, userUuid: data.uuid});
        });
    }

    handleChange(e) {
        const {form} = this.state;

        form[e.target.name] = e.target.value;

        this.setState({form});
    }

    handleSubmit(e) {
        e.preventDefault();

        const {form, userUuid} = this.state;

        if(!form.oldPassword) {
            this.setState({errorMessage: 'Old password cannot be null.'});
            return;
        }

        if(!form.newPassword) {
            this.setState({errorMessage: 'New password cannot be null.'});
            return;
        }

        if(form.newPassword.length < 5) {
            this.setState({errorMessage: 'Minimum length of new password is 5 characters.'});
            return;
        }

        if(!form.newPasswordRepeat) {
            this.setState({errorMessage: 'New password repeat cannot be null.'});
            return;
        }

        if(form.newPasswordRepeat.length < 5) {
            this.setState({errorMessage: 'Minimum length of repeat password is 5 characters.'});
            return;
        }

        if(form.newPassword !== form.newPasswordRepeat) {
            this.setState({errorMessage: 'New passwords do not match.'});
            return;
        }

        const userStorageLoginToken = CONFIG.user.storage_login_token;
        let userToken = localStorage.getItem(userStorageLoginToken);
        if(!userToken) {
            userToken = sessionStorage.getItem(userStorageLoginToken);
        }

        const config = {
            headers: {
                "Content-type": "application/json",
                "Authorization": `Bearer ${userToken}`,
            },
        };

        const data = {
            oldPassword: form.oldPassword,
            newPassword: form.newPassword,
            newPasswordRepeat: form.newPasswordRepeat
        }

        axios.patch(`/api/user/change-password/${userUuid}`, data, config)
            .then(result => {
                if(result.status === 200 && result.data.uuid) {
                    removeUserTokensStorage();

                    toast.info('Password was changed. Login in again.', {autoClose: 4000});

                    setTimeout(() => {
                        return this.props.history.push('/');
                    }, 4000);
                }
            })
            .catch(err => {
                console.error(err);
            });

        form.oldPassword = '';
        form.newPassword = '';
        form.newPasswordRepeat = '';

        this.setState({form, errorMessage: null});

        document.getElementById('changePassword').reset();
    }

    render() {
        const {userEmail, form, errorMessage, noticeMessage} = this.state;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Panel</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Change password</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="login_box_area section_gap">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-12">
                                <div className="header">
                                    <ul>
                                        <Link to='/user/panel'><li>Change password</li></Link>
                                    </ul>
                                </div>
                                <div className="login_form_inner">
                                    <h3>Change password</h3>

                                    <p>Email: <b>{userEmail}</b></p>

                                    { errorMessage &&
                                    <span className="form-error-message">{errorMessage}</span>
                                    }

                                    { noticeMessage &&
                                    <span className="form-notice-message">{noticeMessage}</span>
                                    }

                                    <form className="row login_form"
                                          id="changePassword"
                                          onSubmit={this.handleSubmit}
                                    >
                                        <div className="col-md-12 form-group">
                                            <input type="password"
                                                   className="form-control"
                                                   id="oldPassword"
                                                   name="oldPassword"
                                                   placeholder="Old password"
                                                   required={true}
                                                   value={form.oldPassword}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <input type="password"
                                                   className="form-control"
                                                   id="newPassword"
                                                   name="newPassword"
                                                   placeholder="New password"
                                                   required={true}
                                                   value={form.newPassword}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <input type="password"
                                                   className="form-control"
                                                   id="newPasswordRepeat"
                                                   name="newPasswordRepeat"
                                                   placeholder="Repeat new password"
                                                   required={true}
                                                   value={form.newPasswordRepeat}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <button type="submit" value="submit" className="primary-btn">
                                                Change password
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

export default ChangePassword;
