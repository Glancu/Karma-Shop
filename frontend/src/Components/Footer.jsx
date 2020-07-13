import React, { Component } from "react";

import imgI1 from '../../public/assets/img/i1.jpg';
import imgI2 from '../../public/assets/img/i2.jpg';
import imgI3 from '../../public/assets/img/i3.jpg';
import imgI4 from '../../public/assets/img/i4.jpg';
import imgI5 from '../../public/assets/img/i5.jpg';
import imgI6 from '../../public/assets/img/i6.jpg';
import imgI7 from '../../public/assets/img/i7.jpg';
import imgI8 from '../../public/assets/img/i8.jpg';

class Footer extends Component {
    newsletterOnChange() {
        // @TODO
    }

    render() {
        return (
            <footer className="footer-area section_gap">
                <div className="container">
                    <div className="row">
                        <div className="col-lg-3  col-md-6 col-sm-6">
                            <div className="single-footer-widget">
                                <h6>About Us</h6>
                                <p>
                                    Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor
                                    incididunt ut labore dolore
                                    magna aliqua.
                                </p>
                            </div>
                        </div>
                        <div className="col-lg-4  col-md-6 col-sm-6">
                            <div className="single-footer-widget">
                                <h6>Newsletter</h6>
                                <p>Stay update with our latest</p>
                                <div className="" id="mc_embed_signup">

                                    <form target="_blank" noValidate={true}
                                          action=""
                                        // action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01"
                                          method="get" className="form-inline">

                                        <div className="d-flex flex-row">

                                            <input className="form-control"
                                                   name="EMAIL"
                                                   placeholder="Enter Email"
                                                // onFocus="this.placeholder = ''"
                                                // onBlur="this.placeholder = 'Enter Email '"
                                                   required="" type="email"/>


                                            <button className="click-btn btn btn-default"><i
                                                className="fa fa-long-arrow-right" aria-hidden="true"/>
                                            </button>
                                            <div style={{position: 'absolute', left: '-5000px'}}>
                                                <input name="b_36c4fd991d266f23781ded980_aefe40901a"
                                                       tabIndex="-1"
                                                       value=""
                                                       type="text"
                                                       onChange={this.newsletterOnChange}
                                                />
                                            </div>
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
                            {(new Date().getFullYear())}
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
