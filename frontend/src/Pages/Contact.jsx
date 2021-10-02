import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import BaseTemplate from '../Components/BaseTemplate';
import GMaps from '../../public/assets/js/gmaps.min';
import axios from 'axios';
import ValidateEmail from '../Components/ValidateEmail';

class Contact extends Component {
    constructor(props) {
        super(props);
        this.state = {
            name: '',
            email: '',
            subject: '',
            message: '',
            dataProcessingAgreement: false,
            errors: {
                name: '',
                email: '',
                subject: '',
                message: '',
                dataProcessingAgreement: ''
            },
            noticeMessage: '',
            errorMessage: ''
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentDidMount() {
        const mapBox = document.getElementById('mapBox');
        if(mapBox) {
            const lat = mapBox.getAttribute('data-lat');
            const lon = mapBox.getAttribute('data-lon');
            const zoom = parseInt(mapBox.getAttribute('data-zoom'));

            new GMaps({
                el: '#mapBox',
                lat: lat,
                lng: lon,
                scrollWheel: false,
                scaleControl: true,
                streetViewControl: false,
                panControl: true,
                disableDoubleClickZoom: true,
                mapTypeControl: false,
                zoom: zoom,
                styles: [
                    {
                        featureType: 'water',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                color: '#dcdfe6'
                            }
                        ]
                    },
                    {
                        featureType: 'transit',
                        stylers: [
                            {
                                color: '#808080'
                            },
                            {
                                visibility: 'off'
                            }
                        ]
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'geometry.stroke',
                        stylers: [
                            {
                                visibility: 'on'
                            },
                            {
                                color: '#dcdfe6'
                            }
                        ]
                    },
                    {
                        featureType: 'road.highway',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                color: '#ffffff'
                            }
                        ]
                    },
                    {
                        featureType: 'road.local',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                visibility: 'on'
                            },
                            {
                                color: '#ffffff'
                            },
                            {
                                weight: 1.8
                            }
                        ]
                    },
                    {
                        featureType: 'road.local',
                        elementType: 'geometry.stroke',
                        stylers: [
                            {
                                color: '#d7d7d7'
                            }
                        ]
                    },
                    {
                        featureType: 'poi',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                visibility: 'on'
                            },
                            {
                                color: '#ebebeb'
                            }
                        ]
                    },
                    {
                        featureType: 'administrative',
                        elementType: 'geometry',
                        stylers: [
                            {
                                color: '#a7a7a7'
                            }
                        ]
                    },
                    {
                        featureType: 'road.arterial',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                color: '#ffffff'
                            }
                        ]
                    },
                    {
                        featureType: 'road.arterial',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                color: '#ffffff'
                            }
                        ]
                    },
                    {
                        featureType: 'landscape',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                visibility: 'on'
                            },
                            {
                                color: '#efefef'
                            }
                        ]
                    },
                    {
                        featureType: 'road',
                        elementType: 'labels.text.fill',
                        stylers: [
                            {
                                color: '#696969'
                            }
                        ]
                    },
                    {
                        featureType: 'administrative',
                        elementType: 'labels.text.fill',
                        stylers: [
                            {
                                visibility: 'on'
                            },
                            {
                                color: '#737373'
                            }
                        ]
                    },
                    {
                        featureType: 'poi',
                        elementType: 'labels.icon',
                        stylers: [
                            {
                                visibility: 'off'
                            }
                        ]
                    },
                    {
                        featureType: 'poi',
                        elementType: 'labels',
                        stylers: [
                            {
                                visibility: 'off'
                            }
                        ]
                    },
                    {
                        featureType: 'road.arterial',
                        elementType: 'geometry.stroke',
                        stylers: [
                            {
                                color: '#d6d6d6'
                            }
                        ]
                    },
                    {
                        featureType: 'road',
                        elementType: 'labels.icon',
                        stylers: [
                            {
                                visibility: 'off'
                            }
                        ]
                    },
                    {},
                    {
                        featureType: 'poi',
                        elementType: 'geometry.fill',
                        stylers: [
                            {
                                color: '#dadada'
                            }
                        ]
                    }
                ]
            });
        }
    }

    handleChange(event) {
        const {name, value, checked} = event.target;
        this.setState({
            [name]: name !== 'dataProcessingAgreement' ? value : checked
        });

        const errors = this.state.errors;

        errors[name] = '';

        if(name === 'dataProcessingAgreement' && !checked) {
            errors.dataProcessingAgreement = 'You need to approve the regulations before submit.';
        }

        this.setState({errors, errorsMessage: ''});
    }

    handleSubmit(event) {
        event.preventDefault();

        const {name, email, subject, message, dataProcessingAgreement} = this.state;
        let errors = this.state.errors;

        // Clear error message while send form
        this.setState({errorMessage: ''});

        errors.name = name.length < 3 ?
            'Name must be 3 characters long!' :
            '';

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.subject = subject.length < 3 ?
            'Subject must be 3 characters long!' :
            '';

        errors.message = message.length < 3 ?
            'Message must be 3 characters long!' :
            '';

        errors.dataProcessingAgreement = dataProcessingAgreement ?
            '' :
            'You need to approve the regulations before submit.';

        this.setState({errors});

        if(!errors.name && !errors.email && !errors.subject && !errors.message && !errors.dataProcessingAgreement) {
            const errorMessageCustom = 'Something went wrong. Try again.';

            const formData = new FormData();
            formData.append('name', name);
            formData.append('email', email);
            formData.append('subject', subject);
            formData.append('message', message);
            formData.append('dataProcessingAgreement', dataProcessingAgreement);

            axios.post('/api/contact/create', formData)
                .then(result => {
                    if(result.status === 201) {
                        const data = result.data;
                        if(!data.error) {
                            this.setState({
                                name: '',
                                email: '',
                                subject: '',
                                message: '',
                                dataProcessingAgreement: false,
                                noticeMessage: 'Your message was sent to administration.'
                            });
                        } else if(data.error && data.message) {
                            this.setState({errorMessage: data.message, noticeMessage: ''});
                        } else {
                            this.setState({errorMessage: errorMessageCustom, noticeMessage: ''});
                        }
                    } else {
                        this.setState({errorMessage: errorMessageCustom, noticeMessage: ''});
                    }
                }).catch(() => {
                this.setState({errorMessage: errorMessageCustom, noticeMessage: ''});
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
                                <h1>Contact Us</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <Link to={'/contact'}>Contact</Link>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="contact_area section_gap_bottom">
                    <div className="container">
                        <div id="mapBox" className="mapBox" data-lat="40.701083" data-lon="-74.1522848" data-zoom="13"
                             data-info="PO Box CT16122 Collins Street West, Victoria 8007, Australia."
                             data-mlat="40.701083" data-mlon="-74.1522848">
                        </div>
                        <div className="row">
                            <div className="col-lg-3">
                                <div className="contact_info">
                                    <div className="info_item">
                                        <i className="lnr lnr-home"/>
                                        <h6>California, United States</h6>
                                        <p>Santa monica bullevard</p>
                                    </div>
                                    <div className="info_item">
                                        <i className="lnr lnr-phone-handset"/>
                                        <h6><a href="#">00 (440) 9865 562</a></h6>
                                        <p>Mon to Fri 9am to 6 pm</p>
                                    </div>
                                    <div className="info_item">
                                        <i className="lnr lnr-envelope"/>
                                        <h6><a href="#">support@colorlib.com</a></h6>
                                        <p>Send us your query anytime!</p>
                                    </div>
                                </div>
                            </div>
                            <div className="col-lg-9">
                                {errorMessage ?
                                    <span className="form-error-message">{errorMessage}</span> :
                                    null
                                }

                                {noticeMessage ?
                                    <span className="form-notice-message">{noticeMessage}</span> :
                                    null
                                }

                                <form className="row contact_form"
                                      method="post"
                                      id="contactForm"
                                      onSubmit={this.handleSubmit}
                                >
                                    <div className="col-md-6">
                                        <div className="form-group">
                                            {errors.name ?
                                                <span className='error-message-input'>{errors.name}</span> :
                                                null
                                            }

                                            <input type="text"
                                                   className="form-control"
                                                   id="name"
                                                   name="name"
                                                   placeholder="Enter your name"
                                                   onChange={this.handleChange}
                                                   required={true}
                                                   value={this.state.name}
                                            />
                                        </div>
                                        <div className="form-group">
                                            {errors.email ?
                                                <span className='error-message-input'>{errors.email}</span> :
                                                null
                                            }

                                            <input type="email"
                                                   className="form-control"
                                                   id="email"
                                                   name="email"
                                                   placeholder="Enter email address"
                                                   onChange={this.handleChange}
                                                   required={true}
                                                   value={this.state.email}
                                            />
                                        </div>
                                        <div className="form-group">
                                            {errors.subject ?
                                                <span className='error-message-input'>{errors.subject}</span> :
                                                null
                                            }

                                            <input type="text"
                                                   className="form-control"
                                                   id="subject"
                                                   name="subject"
                                                   placeholder="Enter Subject"
                                                   onChange={this.handleChange}
                                                   required={true}
                                                   value={this.state.subject}
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="form-group">
                                            {errors.message ?
                                                <span className='error-message-input'>{errors.message}</span> :
                                                null
                                            }

                                            <textarea className="form-control"
                                                      name="message"
                                                      id="message"
                                                      rows="1"
                                                      placeholder="Enter Message"
                                                      onChange={this.handleChange}
                                                      required={true}
                                                      value={this.state.message}
                                            />
                                        </div>
                                    </div>

                                    <div className="col-md-6 form-group"/>

                                    <div className="col-md-6 form-group">
                                        {errors.dataProcessingAgreement ?
                                            <span className='error-message-input'>
                                                {errors.dataProcessingAgreement}
                                            </span> :
                                            null
                                        }

                                        <input type="checkbox"
                                               id="dataProcessingAgreement"
                                               name="dataProcessingAgreement"
                                               className="form-control"
                                               onChange={this.handleChange}
                                               required={true}
                                               checked={this.state.dataProcessingAgreement}
                                        />
                                        <label htmlFor="dataProcessingAgreement">
                                            I accept sales regulations and confirm acquaintance with Privacy Policy
                                        </label>
                                    </div>

                                    <div className="col-md-12 text-right">
                                        <button type="submit" value="submit" className="primary-btn">
                                            Send Message
                                        </button>
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

export default Contact
