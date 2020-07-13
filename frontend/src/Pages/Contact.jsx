import React, { Component } from "react";
import { Link } from "react-router-dom";
import BaseTemplate from "../Components/BaseTemplate";
import GMaps from '../../public/assets/js/gmaps.min';

class Contact extends Component {
    componentDidMount() {
        const mapBox = document.getElementById('mapBox');

        var $lat = mapBox.getAttribute('data-lat');
        var $lon = mapBox.getAttribute('data-lon');
        var $zoom = parseInt(mapBox.getAttribute('data-zoom'));

        new GMaps({
            el: '#mapBox',
            lat: $lat,
            lng: $lon,
            scrollwheel: false,
            scaleControl: true,
            streetViewControl: false,
            panControl: true,
            disableDoubleClickZoom: true,
            mapTypeControl: false,
            zoom: $zoom,
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

    render() {
        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Contact Us</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <a href="category.html">Contact</a>
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
                                <form className="row contact_form" method="post"
                                      id="contactForm">
                                    {/* <form className="row contact_form" action="contact_process.php" method="post" */}

                                    <div className="col-md-6">
                                        <div className="form-group">
                                            <input type="text"
                                                   className="form-control"
                                                   id="name"
                                                   name="name"
                                                   placeholder="Enter your name"
                                                // onfocus="this.placeholder = ''"
                                                // onblur="this.placeholder = 'Enter your name'"
                                            />
                                        </div>
                                        <div className="form-group">
                                            <input type="email"
                                                   className="form-control"
                                                   id="email"
                                                   name="email"
                                                   placeholder="Enter email address"
                                                // onfocus="this.placeholder = ''"
                                                // onblur="this.placeholder = 'Enter email address'"
                                            />
                                        </div>
                                        <div className="form-group">
                                            <input type="text"
                                                   className="form-control"
                                                   id="subject"
                                                   name="subject"
                                                   placeholder="Enter Subject"
                                                // onfocus="this.placeholder = ''"
                                                // onblur="this.placeholder = 'Enter Subject'"
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-6">
                                        <div className="form-group">
                                            <textarea className="form-control"
                                                      name="message"
                                                      id="message"
                                                      rows="1"
                                                      placeholder="Enter Message"
                                                // onfocus="this.placeholder = ''"
                                                // onblur="this.placeholder = 'Enter Message'"
                                            />
                                        </div>
                                    </div>
                                    <div className="col-md-12 text-right">
                                        <button type="submit" value="submit" className="primary-btn">Send Message
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
