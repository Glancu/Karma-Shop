import React, { Component } from "react";
import axios from 'axios';
import ValidateEmail from './ValidateEmail';

import imgI1 from '../../public/assets/img/i1.jpg';
import imgI2 from '../../public/assets/img/i2.jpg';
import imgI3 from '../../public/assets/img/i3.jpg';
import imgI4 from '../../public/assets/img/i4.jpg';
import imgI5 from '../../public/assets/img/i5.jpg';
import imgI6 from '../../public/assets/img/i6.jpg';
import imgI7 from '../../public/assets/img/i7.jpg';
import imgI8 from '../../public/assets/img/i8.jpg';

class Footer extends Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            email: '',
            dataProcessingAgreement: false,
            errors: {
                name: '',
                email: '',
                dataProcessingAgreement: ''
            },
            errorMessage: '',
            noticeMessage: ''
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    handleChange(event) {
        const { name, value, checked } = event.target;
        this.setState({
            [name]: name !== 'dataProcessingAgreement' ? value : checked
        });

        const errors = this.state.errors;

        errors[name] = '';

        if(name === 'dataProcessingAgreement' && !checked) {
            errors.dataProcessingAgreement = 'You need to approve the regulations before submit.';
        }

        this.setState({errors});
    }

    handleSubmit(event) {
        event.preventDefault();
        const { name, email, dataProcessingAgreement } = this.state;
        let errors = this.state.errors;

        // Clear error message while send form
        this.setState({errorMessage: ''});

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.dataProcessingAgreement = dataProcessingAgreement ?
            '' :
            'You need to approve the regulations before submit.';

        this.setState({errors});

        if(errors.email.length === 0 && errors.dataProcessingAgreement.length === 0) {
            const errorMessageStr = 'Something went wrong. Try again.';

            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('dataProcessingAgreement', dataProcessingAgreement);

            axios.post("/api/newsletter/create", formData).then(result => {
                if (result.status === 201) {
                    const data = result.data;
                    if(!data.error) {
                        this.setState({
                            name: '',
                            email: '',
                            dataProcessingAgreement: false,
                            noticeMessage: 'Your email was added to newsletter.'
                        });
                    } else if(data.error && data.message) {
                        this.setState({errorMessage: data.message, noticeMessage: ''});
                    } else {
                        this.setState({errorMessage: errorMessageStr, noticeMessage: ''});
                    }
                } else {
                    this.setState({errorMessage: errorMessageStr, noticeMessage: ''});
                }
            }).catch(() => {
                this.setState({errorMessage: errorMessageStr, noticeMessage: ''});
            });
        }
    }

    render() {
        const { errors, errorMessage, noticeMessage } = this.state;

        return (
            <footer className="footer-area section_gap">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-3  col-md-6 col-sm-6">
                            <div className="single-footer-widget">
                                <h6>About Us</h6>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore dolore magna aliqua.
                                </p>
                            </div>
                        </div>
                        <div className="col-lg-4  col-md-6 col-sm-6">
                            <div className="single-footer-widget">
                                <h6>Newsletter</h6>
                                <p>Stay update with our latest</p>
                                <div className="" id="mc_embed_signup">
                                    { errorMessage ?
                                        <span className="form-error-message">{errorMessage}</span> :
                                        null
                                    }

                                    { noticeMessage ?
                                        <span className="form-notice-message">{noticeMessage}</span> :
                                        null
                                    }

                                    <form target="_blank"
                                          className="row"
                                          onSubmit={this.handleSubmit}
                                    >
                                        <div className="col-md-12 form-group">
                                            <label htmlFor="name">Name (optional)</label>
                                            <input className="form-control"
                                                   type="name"
                                                   id="name"
                                                   name="name"
                                                   placeholder="Name (optional)"
                                                   onChange={this.handleChange}
                                                   value={this.state.name}
                                            />
                                        </div>

                                        <div className="col-md-12 form-group">
                                            { errors.email ?
                                                <span className='error-message-input'>{errors.email}</span> : null }
                                        </div>

                                        <div className="col-md-12 form-group">
                                            <label htmlFor="email">Email <span className="red-star">*</span></label>
                                            <input className="form-control"
                                                   type="email"
                                                   id="email"
                                                   name="email"
                                                   placeholder="Enter Email"
                                                   required
                                                   onChange={this.handleChange}
                                                   value={this.state.email}
                                            />
                                        </div>

                                        <div className="col-md-12 form-group">
                                            { errors.dataProcessingAgreement ?
                                                <span className='error-message-input'>{errors.dataProcessingAgreement}</span> :
                                                null
                                            }
                                        </div>

                                        <div className="col-md-12 form-group">
                                            <input type="checkbox"
                                                   id="dataProcessingAgreement_newsletter"
                                                   name="dataProcessingAgreement"
                                                   className="form-control"
                                                   onChange={this.handleChange}
                                                   checked={this.state.dataProcessingAgreement}
                                            />
                                            <label htmlFor="dataProcessingAgreement_newsletter">
                                                I accept sales regulations and confirm acquaintance with Privacy Policy
                                            </label>
                                        </div>

                                        <div className="col-md-12 form-group">
                                            <button className="click-btn btn btn-default pull-right">
                                                <i className="fa fa-long-arrow-right" aria-hidden="true" />
                                            </button>
                                        </div>
                                        <div className="info"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div className="col-lg-3  col-md-6 col-sm-6">
                            <div className="single-footer-widget mail-chimp">
                                <h6 className="mb-20">Instragram Feed</h6>
                                <ul className="instafeed d-flex flex-wrap">
                                    <li><img src={imgI1} alt=""/></li>
                                    <li><img src={imgI2} alt=""/></li>
                                    <li><img src={imgI3} alt=""/></li>
                                    <li><img src={imgI4} alt=""/></li>
                                    <li><img src={imgI5} alt=""/></li>
                                    <li><img src={imgI6} alt=""/></li>
                                    <li><img src={imgI7} alt=""/></li>
                                    <li><img src={imgI8} alt=""/></li>
                                </ul>
                            </div>
                        </div>
                        <div className="col-lg-2 col-md-6 col-sm-6">
                            <div className="single-footer-widget">
                                <h6>Follow Us</h6>
                                <p>Let us be social</p>
                                <div className="footer-social d-flex align-items-center">
                                    <a href="#"><i className="fa fa-facebook"/></a>
                                    <a href="#"><i className="fa fa-twitter"/></a>
                                    <a href="#"><i className="fa fa-dribbble"/></a>
                                    <a href="#"><i className="fa fa-behance"/></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div className="footer-bottom d-flex justify-content-center align-items-center flex-wrap">
                        <p className="footer-text m-0">
                            Copyright &copy;
                            ({new Date().getFullYear()})
                            All rights reserved | This template is made with
                            <i className="fa fa-heart-o" aria-hidden="true"/> by
                            <a href="https://colorlib.com" target="_blank">Colorlib</a>
                        </p>
                    </div>
                </div>
            </footer>
        )
    }
}

export default Footer
