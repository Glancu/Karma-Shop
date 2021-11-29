import React, { Component } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import BaseTemplate from '../../Components/BaseTemplate';
import ValidateEmail from '../../Components/ValidateEmail';
import SetPageTitle from '../../Components/SetPageTitle';

class Register extends Component {
    constructor(props) {
        super(props);
        this.state = {
            email: '',
            password: '',
            errors: {
                email: '',
                password: ''
            }
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        SetPageTitle('Register');
    }

    handleChange(event) {
        const fields = [
            'email',
            'password'
        ];
        const { name, value } = event.target;

        if(fields.includes(name)) {
            this.setState({
                [name]: value
            });

            const errors = this.state.errors;
            errors.message = '';

            errors[name] = '';

            this.setState({errors});
        }
    }

    handleSubmit(event) {
        event.preventDefault();
        const { email, password } = this.state;
        let errors = this.state.errors;

        // Clear error message while send form
        errors.message = '';
        this.setState({errors});

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.password = password.length < 5 ?
            'Password must be 5 characters long!' :
            '';

        this.setState({errors});

        let errorsCount = 0;

        for (const [key, value] of Object.entries(errors)) {
            if(value) {
                errorsCount++;
            }
        }

        if(errorsCount === 0) {
            axios.post("/api/user/create", {email, password}).then(result => {
                if(result && result.data) {
                    const data = result.data;
                    if(data.error === true) {
                        errors.errorMessage = data.message ?? 'Something went wrong. Try again.';

                        this.setState({errors});
                    } else {
                        return this.props.history.push({
                            pathname: '/login',
                            state: {
                                referrer: '/',
                                message: 'Your account was created. You can log in now.'
                            }
                        });
                    }
                }
            }).catch(() => {
                errors.errorMessage = 'Something went wrong. Try again.';

                this.setState({errors});
            });
        }
    }

    render() {
        const { errors } = this.state;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Register</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Register</p>
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
                                    <h3>Register Account</h3>
                                    { errors.errorMessage ?
                                        <span className="form-error-message">{errors.errorMessage}</span> :
                                        null
                                    }

                                    <form className="row register_form"
                                          onSubmit={this.handleSubmit}
                                    >
                                        <div className="col-md-6 form-group">
                                            { errors.email ?
                                                <span className='error-message-input'>{errors.email}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="email"
                                                   name="email"
                                                   placeholder="E-mail"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>

                                        <div className="col-md-6 form-group">
                                            { errors.password ?
                                                <span className='error-message-input'>{errors.password}</span> :
                                                null
                                            }
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
                                            <button type="submit" value="submit" className="primary-btn">
                                                Register
                                            </button>
                                            <Link to='/login'>Have Account? Log in</Link>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        );
    }
}

export default Register;
