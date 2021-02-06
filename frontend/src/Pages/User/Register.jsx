import React, { Component } from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';
import BaseTemplate from '../../Components/BaseTemplate';
import ValidateEmail from '../../Components/ValidateEmail';

class Register extends Component {
    constructor(props) {
        super(props);
        this.state = {
            firstName: '',
            lastName: '',
            email: '',
            password: '',
            phoneNumber: '',
            street: '',
            postalCode: '',
            city: '',
            country: '',
            errors: {
                firstName: '',
                lastName: '',
                email: '',
                password: '',
                phoneNumber: '',
                street: '',
                postalCode: '',
                city: '',
                country: '',
                errorMessage: ''
            }
        }

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(event) {
        const fields = [
            'firstName',
            'lastName',
            'email',
            'password',
            'phoneNumber',
            'street',
            'postalCode',
            'city',
            'country'
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
        const { email, firstName, lastName, phoneNumber, street, postalCode, city, country, password } = this.state;
        let errors = this.state.errors;

        // Clear error message while send form
        errors.message = '';
        this.setState({errors});

        errors.firstName = firstName.length < 3 ?
            'First name must be 3 characters long!' :
            '';

        errors.lastName = lastName.length < 3 ?
            'Last name must be 3 characters long!' :
            '';

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.password = password.length < 5 ?
            'Password must be 5 charasters long!' :
            '';

        errors.phoneNumber = phoneNumber.length < 3 ?
            'Phone number must be 3 characters long!' :
            '';

        errors.street = street.length < 3 ?
            'Street must be 3 characters long!' :
            '';

        errors.postalCode = postalCode.length < 3 ?
            'Postal code must be 3 characters long!' :
            '';

        errors.city = city.length < 3 ?
            'City must be 3 characters long!' :
            '';

        errors.country = country.length < 3 ?
            'Country must be 3 characters long!' :
            '';

        this.setState({errors});

        let errorsCount = 0;

        for (const [key, value] of Object.entries(errors)) {
            if(value) {
                errorsCount++;
            }
        }

        if(errorsCount === 0) {
            const formData = new FormData();
            formData.append('firstName', firstName);
            formData.append('lastName', lastName);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('phoneNumber', phoneNumber);
            formData.append('street', street);
            formData.append('postalCode', postalCode);
            formData.append('city', city);
            formData.append('country', country);

            axios.post("/api/user/create", formData).then(result => {
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
                                            { errors.firstName ?
                                                <span className='error-message-input'>{errors.firstName}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="firstName"
                                                   name="firstName"
                                                   placeholder="First name"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-6 form-group">
                                            { errors.lastName ?
                                                <span className='error-message-input'>{errors.lastName}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="lastName"
                                                   name="lastName"
                                                   placeholder="Last name"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>

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

                                        <div className="col-md-6 form-group">
                                            { errors.phoneNumber ?
                                                <span className='error-message-input'>{errors.phoneNumber}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="phoneNumber"
                                                   name="phoneNumber"
                                                   placeholder="Phone number"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>

                                        <div className="col-md-6 form-group">
                                            { errors.street ?
                                                <span className='error-message-input'>{errors.street}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="street"
                                                   name="street"
                                                   placeholder="Street"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-6 form-group">
                                            { errors.postalCode ?
                                                <span className='error-message-input'>{errors.postalCode}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="postalCode"
                                                   name="postalCode"
                                                   placeholder="Postal code"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>

                                        <div className="col-md-6 form-group">
                                            { errors.city ?
                                                <span className='error-message-input'>{errors.city}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="city"
                                                   name="city"
                                                   placeholder="City"
                                                   value={this.state.value}
                                                   onChange={this.handleChange}
                                            />
                                        </div>
                                        <div className="col-md-12 form-group">
                                            { errors.country ?
                                                <span className='error-message-input'>{errors.country}</span> :
                                                null
                                            }
                                            <input type="text"
                                                   className="form-control"
                                                   id="country"
                                                   name="country"
                                                   placeholder="Country"
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
