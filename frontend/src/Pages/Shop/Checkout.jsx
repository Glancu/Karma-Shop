import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link, Redirect } from 'react-router-dom';
import ShoppingCart from '../../Components/Shop/ShoppingCart';
import { windowScrollTo }  from '../../Components/WindowScroll';
import isEqual from 'react-fast-compare';
import { userLoggedIn, userEmail } from '../../Components/User/UserData';
import axios from 'axios';
import ValidateEmail from '../../Components/ValidateEmail';
import CONFIG from '../../config';
import $ from 'jquery';
import SetPageTitle from '../../Components/SetPageTitle';

import imgProductCard from '../../../public/assets/img/product/card.jpg';

class Checkout extends Component {
    constructor(props) {
        super(props);

        this.state = {
            form: {
                inputs: {
                    firstName: null,
                    lastName: null,
                    companyName: null,
                    phoneNumber: null,
                    email: null,
                    addressLineFirst: null,
                    addressLineSecond: null,
                    city: null,
                    postalCode: null,
                    additionalInformation: null,
                    firstNameCorrespondence: null,
                    lastNameCorrespondence: null,
                    companyNameCorrespondence: null,
                    addressLineFirstCorrespondence: null,
                    addressLineSecondCorrespondence: null,
                    cityCorrespondence: null,
                    postalCodeCorrespondence: null
                },
                customCorrespondence: false,
                methodPayment: null,
                dataProcessingAgreement: false
            },
            isUserLoggedIn: false,
            userEmail: '',
            userLoginForm: {
                inputs: {
                    email: '',
                    password: ''
                },
                errors: {
                    email: '',
                    password: ''
                },
                errorMessage: ''
            },
            successRedirect: false,
            payPalUrl: null
        };

        this.submitForm = this.submitForm.bind(this);
        this.handleChange = this.handleChange.bind(this);
        this.toggleDifferentAddressElements = this.toggleDifferentAddressElements.bind(this);
        this.setMethodPayment = this.setMethodPayment.bind(this);
        this.updateLocalStorageDataInputs = this.updateLocalStorageDataInputs.bind(this);
        this.toggleAcceptDataProcessingAgreement = this.toggleAcceptDataProcessingAgreement.bind(this);
        this.handleChangeUserLogin = this.handleChangeUserLogin.bind(this);
        this.loginUser = this.loginUser.bind(this);
    }

    componentDidMount() {
        SetPageTitle('Checkout - Shop');

        $('select').niceSelect();

        this.updateLocalStorageDataInputs(true);

        userLoggedIn().then((data) => {
            this.setState({isUserLoggedIn: data})
        });

        userEmail().then((data) => {
            this.setState({userEmail: data})
        });
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        this.updateLocalStorageDataInputs();
    }

    updateLocalStorageDataInputs(setDataFromLocalStorageToState = false) {
        const {form} = this.state;

        let localStorageShop = window.localStorage.getItem(ShoppingCart.localStorageShopKeyName);
        if(setDataFromLocalStorageToState === true && (JSON.parse(localStorageShop) &&
            JSON.parse(localStorageShop).hasOwnProperty('form') &&
            JSON.parse(localStorageShop).form)
        ) {
            const formLocalStorage = JSON.parse(localStorageShop).form;
            formLocalStorage.dataProcessingAgreement = false;

            this.setState({form: formLocalStorage});
            return true;
        }
        if(!JSON.parse(localStorageShop)) {
            window.localStorage.setItem(ShoppingCart.localStorageShopKeyName, JSON.stringify({products: []}));
            localStorageShop = window.localStorage.getItem(ShoppingCart.localStorageShopKeyName);
        }

        const parsedLocalStorageShop = JSON.parse(localStorageShop);
        if(!parsedLocalStorageShop.hasOwnProperty('form') || (parsedLocalStorageShop.hasOwnProperty('form')
            && !isEqual(parsedLocalStorageShop.form, form))
        ) {
            parsedLocalStorageShop['form'] = form;

            window.localStorage.setItem(ShoppingCart.localStorageShopKeyName, JSON.stringify(parsedLocalStorageShop));
        }
    }

    toggleDifferentAddressElements() {
        const {form} = this.state;
        const differentAddressEl = document.getElementById('different-address');
        if(differentAddressEl) {
            let setRequired = false;

            if(differentAddressEl.classList.contains('hide')) {
                differentAddressEl.classList.remove('hide');

                form.customCorrespondence = true;

                setRequired = true;
            } else {
                differentAddressEl.classList.add('hide');

                form.customCorrespondence = false;

                setRequired = false;
            }

            this.setState({form});

            differentAddressEl.querySelectorAll('.p_star input').forEach((input) => {
                input.required = setRequired;
                input.minLength = 3;
            });
        }
    }

    inputsValidationWithEventListener(inputs, errorsInput) {
        inputs.forEach((input) => {
            if(input) {
                if(input.value.length < 3) {
                    input.classList.add('error-input');
                    errorsInput = true;
                } else {
                    input.classList.remove('error-input');
                }

                input.addEventListener('input', function(inputEvent) {
                    const inputTarget = inputEvent.target;
                    const inputValue = inputTarget.value;
                    if(!inputValue || inputValue.length < 3) {
                        inputTarget.classList.add('error-input');

                        errorsInput = true;
                    } else {
                        inputTarget.classList.remove('error-input');
                    }
                });
            }
        });

        return errorsInput;
    }

    submitForm(e) {
        const _this = this;
        const { form } = this.state;

        const formEl = document.getElementById('shopping-form');
        if(formEl && formEl.checkValidity()) {
            e.preventDefault();

            e.target.disabled = true;

            const products = [];
            let errorsInput = false;

            errorsInput = this.inputsValidationWithEventListener(
                formEl.querySelectorAll(':scope > .col-lg-8 > .p_star input'),
                errorsInput
            );
            if(errorsInput === true) {
                windowScrollTo(formEl.offsetTop - 40);

                e.target.disabled = false;
            }

            if(form.customCorrespondence === true && (form.inputs.firstNameCorrespondence === null ||
                form.inputs.lastNameCorrespondence === null || form.inputs.addressLineFirstCorrespondence === null ||
                form.inputs.addressLineSecondCorrespondence === null || form.inputs.cityCorrespondence === null ||
                form.inputs.postalCodeCorrespondence === null)
            ) {
                errorsInput = this.inputsValidationWithEventListener(
                    formEl.querySelectorAll('#different-address .p_star input'),
                    errorsInput,
                    true
                );

                if(errorsInput === true) {
                    e.target.disabled = false;
                }
            }

            if(errorsInput === false && form.methodPayment === null) {
                errorsInput = true;

                const paymentsItems = document.getElementById('payments-items');
                if(paymentsItems) {
                    paymentsItems.classList.add('error-input');
                }

                if(errorsInput === true) {
                    e.target.disabled = false;
                }
            }

            const acceptTermsInputEl = document.getElementById('accept-terms');
            if(acceptTermsInputEl && acceptTermsInputEl.parentElement.classList.contains('error-input')) {
                acceptTermsInputEl.parentElement.classList.remove('error-input');
            }

            if(errorsInput === false && form.dataProcessingAgreement !== true) {
                errorsInput = true;

                if(acceptTermsInputEl) {
                    acceptTermsInputEl.parentElement.classList.add('error-input');

                    acceptTermsInputEl.addEventListener('click', function(inputEvent) {
                        inputEvent.target.checked ?
                            acceptTermsInputEl.parentElement.classList.remove('error-input') :
                            acceptTermsInputEl.parentElement.classList.add('error-input');
                    });
                }

                if(errorsInput === true) {
                    e.target.disabled = false;
                }
            }

            if(errorsInput === false) {
                ShoppingCart.getProducts().map(product => {
                    products.push({uuid: product.uuid, quantity: parseInt(product.quantity)});
                });

                const userStorageLoginToken = CONFIG.user.storage_login_token;

                const errorMessageStr = 'Something went wrong. Try again.';

                const responseDataMessage = document.getElementById('response-data-message');
                if(responseDataMessage) {
                    let token = localStorage.getItem(userStorageLoginToken);
                    if(!token) {
                        token = sessionStorage.getItem(userStorageLoginToken);
                    }

                    axios.post("/api/shop/create-order", {
                        'personalData': form.inputs,
                        'methodPayment': form.methodPayment,
                        'isCustomCorrespondence': form.customCorrespondence,
                        'products': products,
                        'dataProcessingAgreement': form.dataProcessingAgreement,
                        'userToken': token ?? null
                    }).then(result => {
                        if (result.status === 201) {
                            if(result.data && result.data.uuid) {
                                responseDataMessage.classList.add('success-message');
                                responseDataMessage.innerText = 'Success';

                                if(result.data.payPalUrl) {
                                    _this.state.payPalUrl = result.data.payPalUrl;
                                }

                                setTimeout(function() {
                                    _this.setState({successRedirect: true});
                                }, 500);
                            } else {
                                responseDataMessage.classList.add('error-message');
                                responseDataMessage.innerText = errorMessageStr;
                            }
                        } else if(result.data && result.data.error && result.data.message) {
                            responseDataMessage.classList.add('error-message');
                            responseDataMessage.innerText = result.data.message;
                        } else {
                            responseDataMessage.classList.add('error-message');
                            responseDataMessage.innerText = errorMessageStr;
                        }
                    }).catch((err) => {
                        console.error(err)

                        if(err.response.data && err.response.data.error && err.response.data.message) {
                            responseDataMessage.classList.add('error-message');
                            responseDataMessage.innerText = err.response.data.message;
                        }
                    });
                }
            }
        }
    }

    handleChange(e) {
        const {form} = this.state;
        form.inputs[e.target.name] = e.target.value;

        this.setState({form});
    }

    setMethodPayment(paymentType) {
        const {form} = this.state;
        form.methodPayment = paymentType;

        this.setState({form});

        const paymentsItems = document.getElementById('payments-items');
        if(paymentsItems && paymentsItems.classList.contains('error-input')) {
            paymentsItems.classList.remove('error-input');
        }
    }

    toggleAcceptDataProcessingAgreement(e) {
        const {form} = this.state;
        form.dataProcessingAgreement = e.target.checked;

        this.setState({form});
    }

    handleChangeUserLogin(e) {
        const userLoginForm = this.state.userLoginForm;
        userLoginForm.inputs[e.target.name] = e.target.value;

        this.setState({userLoginForm});
    }

    loginUser(event) {
        event.preventDefault();
        const userLoginForm = this.state.userLoginForm;
        const { email, password } = userLoginForm.inputs;
        let errors = userLoginForm.errors;

        userLoginForm.errorMessage = '';

        // Clear error message while send form
        this.setState({userLoginForm});

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.password = password.length < 3 ?
            'Password must be 3 characters long!' :
            '';

        userLoginForm.errors = errors;

        this.setState({userLoginForm});

        if(errors.email.length === 0 && errors.password.length === 0) {
            const errorMessageStr = 'Bad email or password. Try again.';

            axios.post("/api/user/generate-token", {
                email,
                password
            }).then(result => {
                const token = result.data ? result.data.token : null;
                if (result.status === 200 && token) {
                    localStorage.setItem(CONFIG.user.storage_login_token, token);

                    window.location.reload(true);
                } else {
                    userLoginForm.errorMessage = errorMessageStr;
                    this.setState({userLoginForm});
                }
            }).catch(() => {
                userLoginForm.errorMessage = errorMessageStr;
                this.setState({userLoginForm});
            });
        }
    }

    render() {
        const {isUserLoggedIn, userLoginForm, successRedirect, payPalUrl} = this.state;

        const shoppingCartProducts = ShoppingCart.getProducts();
        const localStorageShop = window.localStorage.getItem(ShoppingCart.localStorageShopKeyName);
        let form = {};
        if(localStorageShop && JSON.parse(localStorageShop).hasOwnProperty('form') &&
            JSON.parse(localStorageShop).form.hasOwnProperty('inputs')
        ) {
            form = JSON.parse(localStorageShop).form;
        }

        if(successRedirect) {
            return <Redirect to={{pathname: '/shop/confirmation', state: {payPalUrl: payPalUrl}}}/>
        }

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Checkout</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <Link to={'/shop'}>Shop<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Checkout</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>
                <section className="checkout_area section_gap">
                    <div className="container">
                        {!isUserLoggedIn &&
                            <div className="returning_customer">
                                <div className="check_title">
                                    <h2>Returning Customer? <Link to="/login">Click here to login</Link></h2>
                                </div>
                                <p>If you have shopped with us before, please enter your details in the boxes below. If you
                                    are a new
                                    customer, please proceed to the Billing & Shipping section.</p>
                                <form className="row contact_form" method="post" onSubmit={this.loginUser}>
                                    <div className="col-md-6 form-group p_star">
                                        {userLoginForm.errors.email &&
                                            <span className='error-message-input'>{userLoginForm.errors.email}</span>
                                        }
                                        <input type="text" className="form-control" id="user-name" name="email"
                                               required={true} autoComplete="email"
                                               onChange={this.handleChangeUserLogin}/>
                                        <span className="placeholder" data-placeholder="Email"/>
                                    </div>
                                    <div className="col-md-6 form-group p_star">
                                        {userLoginForm.errors.password &&
                                            <span className='error-message-input'>{userLoginForm.errors.password}</span>
                                        }
                                        <input type="password" className="form-control" id="user-password" name="password"
                                               required={true} autoComplete="current-password"
                                               onChange={this.handleChangeUserLogin}/>
                                        <span className="placeholder" data-placeholder="Password"/>
                                    </div>
                                    <div className="col-md-12 form-group">
                                        <button type="submit" value="submit" className="primary-btn">login</button>
                                        <div className="creat_account">
                                            <input type="checkbox" id="f-option" name="selector"/>
                                            <label htmlFor="f-option">Remember me</label>
                                        </div>
                                    </div>
                                </form>

                                {userLoginForm.errorMessage &&
                                    <span className="form-error-message">{userLoginForm.errorMessage}</span>
                                }
                            </div>
                        }
                        <div className="billing_details">
                            <div className="row">
                                <form id="shopping-form" className="row contact_form">

                                <div className="col-lg-8">
                                    <h3>Billing Details</h3>
                                        <div className="col-md-6 form-group p_star">
                                            <input type="text" className="form-control" name="firstName"
                                                   onChange={this.handleChange} required={true} minLength={3}
                                                   defaultValue={form && form.inputs && form.inputs.firstName}/>
                                            <span className="placeholder" data-placeholder="First name"/>
                                        </div>
                                        <div className="col-md-6 form-group p_star">
                                            <input type="text" className="form-control" name="lastName"
                                                   onChange={this.handleChange} required={true} minLength={3}
                                                   defaultValue={form && form.inputs && form.inputs.lastName}/>
                                            <span className="placeholder" data-placeholder="Last name"/>
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <input type="text" className="form-control" name="companyName"
                                                   placeholder="Company name" onChange={this.handleChange}
                                                   defaultValue={form && form.inputs && form.inputs.companyName}/>
                                        </div>
                                        <div className="col-md-6 form-group p_star">
                                            <input type="text" className="form-control" name="phoneNumber"
                                                   onChange={this.handleChange} required={true} minLength={3}
                                                   defaultValue={form && form.inputs && form.inputs.phoneNumber}/>
                                            <span className="placeholder" data-placeholder="Phone number"/>
                                        </div>
                                        <div className="col-md-6 form-group p_star">
                                            <input type="email" className="form-control" name="email"
                                                   onChange={this.handleChange} required={true} minLength={3}
                                                   defaultValue={this.state.userEmail ? this.state.userEmail : form && form.inputs && form.inputs.email}
                                                   disabled={this.state.userEmail}/>
                                            <span className="placeholder" data-placeholder="Email Address"/>
                                        </div>
                                        <div className="col-md-12 form-group p_star">
                                            <input type="text" className="form-control" name="addressLineFirst"
                                                   onChange={this.handleChange} required={true} minLength={3}
                                                   defaultValue={form && form.inputs && form.inputs.addressLineFirst}/>
                                            <span className="placeholder" data-placeholder="Address line 01"/>
                                        </div>
                                    <div className="col-md-12 form-group">
                                        <input type="text" className="form-control" name="addressLineSecond"
                                               onChange={this.handleChange} minLength={3}
                                               defaultValue={form && form.inputs && form.inputs.addressLineSecond}
                                               placeholder="Address line 02"/>
                                    </div>
                                    <div className="col-md-12 form-group p_star">
                                        <input type="text" className="form-control" name="city"
                                               onChange={this.handleChange} required={true} minLength={3}
                                               defaultValue={form && form.inputs && form.inputs.city}/>
                                        <span className="placeholder" data-placeholder="Town/City"/>
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <input type="text" className="form-control" name="postalCode"
                                                   placeholder="Postcode/ZIP" onChange={this.handleChange}
                                                   defaultValue={form && form.inputs && form.inputs.postalCode}/>
                                        </div>
                                        <div className="col-md-12 form-group">
                                            <div className="creat_account">
                                                <h3>Shipping Details</h3>
                                                <input type="checkbox" id="different-address-input" name="selector"
                                                       onClick={this.toggleDifferentAddressElements}
                                                       defaultChecked={form && form.customCorrespondence}/>
                                                <label htmlFor="different-address-input">
                                                    Ship to a different address?
                                                </label>
                                            </div>
                                            <textarea className="form-control" name="additionalInformation" rows="1"
                                                      placeholder="Order Notes" onChange={this.handleChange}/>
                                        </div>
                                        <div id="different-address" className={!form || (form && !form.customCorrespondence) ? 'different-address hide' : 'different-address'}>
                                            <div className="col-md-6 form-group p_star">
                                                <input type="text" className="form-control" name="firstNameCorrespondence"
                                                    onChange={this.handleChange} defaultValue={form && form.inputs && form.inputs.firstNameCorrespondence}/>
                                                <span className="placeholder" data-placeholder="First name"/>
                                            </div>
                                            <div className="col-md-6 form-group p_star">
                                                <input type="text" className="form-control" name="lastNameCorrespondence"
                                                       onChange={this.handleChange} defaultValue={form && form.inputs && form.inputs.lastNameCorrespondence}/>
                                                <span className="placeholder" data-placeholder="Last name"/>
                                            </div>
                                            <div className="col-md-12 form-group">
                                                <input type="text" className="form-control" name="companyNameCorrespondence"
                                                       placeholder="Company name" onChange={this.handleChange}
                                                       defaultValue={form && form.inputs && form.inputs.companyNameCorrespondence}/>
                                            </div>
                                            <div className="col-md-12 form-group p_star">
                                                <input type="text" className="form-control" name="addressLineFirstCorrespondence"
                                                       onChange={this.handleChange} defaultValue={form && form.inputs && form.inputs.addressLineFirstCorrespondence}/>
                                                <span className="placeholder" data-placeholder="Address line 01"/>
                                            </div>
                                            <div className="col-md-12 form-group p_star">
                                                <input type="text" className="form-control" name="addressLineSecondCorrespondence"
                                                       onChange={this.handleChange} defaultValue={form && form.inputs && form.inputs.addressLineSecondCorrespondence}/>
                                                <span className="placeholder" data-placeholder="Address line 02"/>
                                            </div>
                                            <div className="col-md-12 form-group p_star">
                                                <input type="text" className="form-control" name="cityCorrespondence"
                                                       onChange={this.handleChange} defaultValue={form && form.inputs && form.inputs.cityCorrespondence}/>
                                                <span className="placeholder" data-placeholder="Town/City"/>
                                            </div>
                                            <div className="col-md-12 form-group">
                                                <input type="text" className="form-control" name="postalCodeCorrespondence"
                                                       placeholder="Postcode/ZIP" onChange={this.handleChange}
                                                       defaultValue={form && form.inputs && form.inputs.postalCodeCorrespondence}/>
                                            </div>
                                        </div>
                                </div>
                                <div className="col-lg-4">
                                    <div className="order_box">
                                        <h2>Your Order</h2>
                                        <ul className="list">
                                            <li>Products: <span>Total</span></li>
                                            {
                                                shoppingCartProducts.map(product => {
                                                    const priceGross = product.priceGross;
                                                    const priceGrossSum = (parseFloat(priceGross) * product.quantity).toFixed(2);
                                                    const productQuantity = (Math.round(product.quantity * 100) / 100).toFixed(2);

                                                    return (
                                                        <li key={product.uuid}>
                                                            {product.name}
                                                            <span className="middle">x {productQuantity}</span>
                                                            <span className="last">{priceGrossSum}</span>
                                                        </li>
                                                    )
                                                })
                                            }
                                        </ul>
                                        <ul className="list list_2">
                                            <li>Total <span>{ShoppingCart.getTotalPrice()}</span></li>
                                        </ul>
                                        <div id="payments-items">
                                            <div className="payment_item">
                                                <div className="radion_btn">
                                                    <input type="radio" id="payment-online" name="selector"
                                                           onClick={() => {this.setMethodPayment('Online')}}
                                                           defaultChecked={form && form.methodPayment === 'Online'}
                                                    />
                                                    <label htmlFor="payment-online">Online</label>
                                                    <div className="check"/>
                                                </div>
                                                <p>Pay via online payment. You can choose your bank account and pay with it.</p>
                                            </div>
                                            <div className="payment_item">
                                                <div className="radion_btn">
                                                    <input type="radio" id="payment-paypal" name="selector"
                                                           onClick={() => {this.setMethodPayment('PayPal')}}
                                                           defaultChecked={form && form.methodPayment === 'PayPal'}
                                                    />
                                                    <label htmlFor="payment-paypal">Paypal </label>
                                                    <img src={imgProductCard} alt=""/>
                                                    <div className="check"/>
                                                </div>
                                                <p>Pay via PayPal; you can pay with your credit card if you don’t have a
                                                    PayPal
                                                    account.</p>
                                            </div>
                                        </div>
                                        <div className="creat_account">
                                            <input type="checkbox" id="accept-terms" name="selector"
                                                   onClick={this.toggleAcceptDataProcessingAgreement}/>
                                            <label htmlFor="accept-terms">I’ve read and accept the </label>
                                            <a href="#" onClick={(e) => {e.preventDefault()}}>
                                                terms & conditions*
                                            </a>
                                        </div>
                                        <button type="submit"
                                                className="primary-btn border-0"
                                                onClick={(e) => this.submitForm(e)}
                                        >
                                            Submit order
                                        </button>

                                        <p id="response-data-message" className="margin-p-normalize"/>
                                    </div>
                                </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Checkout
