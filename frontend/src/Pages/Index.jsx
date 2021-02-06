import React, { Component } from "react";
import BaseTemplate from "../Components/BaseTemplate";
import { withRouter } from "react-router-dom";
import $ from 'jquery';
import OwlCarousel from 'react-owl-carousel';
import '../../public/assets/js/jquery.magnific-popup.min';
import '../../public/assets/js/countdown';

// Images
import imgBanner from '../../public/assets/img/banner/banner-img.png';

import imgFeature1 from '../../public/assets/img/features/f-icon1.png';
import imgFeature2 from '../../public/assets/img/features/f-icon2.png';
import imgFeature3 from '../../public/assets/img/features/f-icon3.png';
import imgFeature4 from '../../public/assets/img/features/f-icon4.png';

import imgCategory1 from '../../public/assets/img/category/c1.jpg';
import imgCategory2 from '../../public/assets/img/category/c2.jpg';
import imgCategory3 from '../../public/assets/img/category/c3.jpg';
import imgCategory4 from '../../public/assets/img/category/c4.jpg';
import imgCategory5 from '../../public/assets/img/category/c5.jpg';

import imgProduct1 from '../../public/assets/img/product/p1.jpg';
import imgProduct2 from '../../public/assets/img/product/p2.jpg';
import imgProduct3 from '../../public/assets/img/product/p3.jpg';
import imgProduct4 from '../../public/assets/img/product/p4.jpg';
import imgProduct5 from '../../public/assets/img/product/p5.jpg';
import imgProduct6 from '../../public/assets/img/product/p6.jpg';
import imgProduct7 from '../../public/assets/img/product/p7.jpg';
import imgProduct8 from '../../public/assets/img/product/p8.jpg';
import imgProductE1 from '../../public/assets/img/product/e-p1.png';

import imgBrand1 from '../../public/assets/img/brand/1.png';
import imgBrand2 from '../../public/assets/img/brand/2.png';
import imgBrand3 from '../../public/assets/img/brand/3.png';
import imgBrand4 from '../../public/assets/img/brand/4.png';
import imgBrand5 from '../../public/assets/img/brand/5.png';

import imgRelatedProduct1 from '../../public/assets/img/r1.jpg';
import imgRelatedProduct2 from '../../public/assets/img/r2.jpg';
import imgRelatedProduct3 from '../../public/assets/img/r3.jpg';
import imgRelatedProduct5 from '../../public/assets/img/r5.jpg';
import imgRelatedProduct6 from '../../public/assets/img/r6.jpg';
import imgRelatedProduct7 from '../../public/assets/img/r7.jpg';
import imgRelatedProduct9 from '../../public/assets/img/r9.jpg';
import imgRelatedProduct10 from '../../public/assets/img/r10.jpg';
import imgRelatedProduct11 from '../../public/assets/img/r11.jpg';
import DealsRelatedProducts from '../Components/DealsRelatedProducts';

class Index extends Component {
    constructor(props) {
        super(props);
    }

    componentDidMount() {
        $(document).ready(function() {
            const window_height = window.innerHeight,
                  header_height = $(".default-header").height(),
                  fitscreen = window_height - header_height;

            $(".fullscreen").css("height", window_height)
            $(".fitscreen").css("height", fitscreen);

            if (document.getElementById("js-countdown")) {
                var countdown = new Date("October 17, 2018");

                function getRemainingTime(endtime) {
                    var milliseconds = Date.parse(endtime) - Date.parse(new Date());
                    var seconds = Math.floor(milliseconds / 1000 % 60);
                    var minutes = Math.floor(milliseconds / 1000 / 60 % 60);
                    var hours = Math.floor(milliseconds / (1000 * 60 * 60) % 24);
                    var days = Math.floor(milliseconds / (1000 * 60 * 60 * 24));

                    return {
                        'total': milliseconds,
                        'seconds': seconds,
                        'minutes': minutes,
                        'hours': hours,
                        'days': days
                    };
                }

                function initClock(id, endtime) {
                    var counter = document.getElementById(id);
                    var daysItem = counter.querySelector('.js-countdown-days');
                    var hoursItem = counter.querySelector('.js-countdown-hours');
                    var minutesItem = counter.querySelector('.js-countdown-minutes');
                    var secondsItem = counter.querySelector('.js-countdown-seconds');

                    function updateClock() {
                        var time = getRemainingTime(endtime);

                        daysItem.innerHTML = time.days;
                        hoursItem.innerHTML = ('0' + time.hours).slice(-2);
                        minutesItem.innerHTML = ('0' + time.minutes).slice(-2);
                        secondsItem.innerHTML = ('0' + time.seconds).slice(-2);

                        if (time.total <= 0) {
                            clearInterval(timeinterval);
                        }
                    }

                    updateClock();
                    var timeinterval = setInterval(updateClock, 1000);
                }

                initClock('js-countdown', countdown);
            }

            $('.img-pop-up').magnificPopup({
                type: 'image',
                gallery: {
                    enabled: true
                }
            });
        });
    }

    render() {
        return (
            <BaseTemplate>
                <section className="banner-area">
                    <div className="container">
                        <div className="row fullscreen align-items-center justify-content-start">
                            <div className="col-lg-12">
                                <OwlCarousel className="active-banner-slider owl-carousel"
                                             items={1}
                                             autoPlay={false}
                                             autoplayTimeout={5000}
                                             loop={true}
                                             nav={true}
                                             dots={false}
                                             navText={[
                                                 "<img src='assets/img/banner/prev.png'>",
                                                 "<img src='assets/img/banner/next.png'>"
                                             ]}
                                             navClass={[
                                                 "owl-prev button-without-background",
                                                 "owl-next button-without-background"
                                             ]}
                                >
                                    <div className="row single-slide align-items-center d-flex">
                                        <div className="col-lg-5 col-md-6">
                                            <div className="banner-content">
                                                <h1>Nike New <br/>Collection!</h1>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                                    eiusmod tempor incididunt ut labore et
                                                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                                    exercitation.</p>
                                                <div className="add-bag d-flex align-items-center">
                                                    <a className="add-btn" href=""><span
                                                        className="lnr lnr-cross"/></a>
                                                    <span className="add-text text-uppercase">Add to Bag</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-lg-7">
                                            <div className="banner-img">
                                                <img className="img-fluid" src={imgBanner} alt=""/>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="row single-slide">
                                        <div className="col-lg-5">
                                            <div className="banner-content">
                                                <h1>Nike New <br/>Collection!</h1>
                                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do
                                                    eiusmod tempor incididunt ut labore et
                                                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud
                                                    exercitation.</p>
                                                <div className="add-bag d-flex align-items-center">
                                                    <a className="add-btn" href=""><span
                                                        className="lnr lnr-cross"/></a>
                                                    <span className="add-text text-uppercase">Add to Bag</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-lg-7">
                                            <div className="banner-img">
                                                <img className="img-fluid" src={imgBanner} alt=""/>
                                            </div>
                                        </div>
                                    </div>
                                </OwlCarousel>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="features-area section_gap">
                    <div className="container">
                        <div className="row features-inner">
                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <div className="single-features">
                                    <div className="f-icon">
                                        <img src={imgFeature1} alt=""/>
                                    </div>
                                    <h6>Free Delivery</h6>
                                    <p>Free Shipping on all order</p>
                                </div>
                            </div>

                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <div className="single-features">
                                    <div className="f-icon">
                                        <img src={imgFeature2} alt=""/>
                                    </div>
                                    <h6>Return Policy</h6>
                                    <p>Free Shipping on all order</p>
                                </div>
                            </div>

                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <div className="single-features">
                                    <div className="f-icon">
                                        <img src={imgFeature3} alt=""/>
                                    </div>
                                    <h6>24/7 Support</h6>
                                    <p>Free Shipping on all order</p>
                                </div>
                            </div>

                            <div className="col-lg-3 col-md-6 col-sm-6">
                                <div className="single-features">
                                    <div className="f-icon">
                                        <img src={imgFeature4} alt=""/>
                                    </div>
                                    <h6>Secure Payment</h6>
                                    <p>Free Shipping on all order</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="category-area">
                    <div className="container">
                        <div className="row justify-content-center">
                            <div className="col-lg-8 col-md-12">
                                <div className="row">
                                    <div className="col-lg-8 col-md-8">
                                        <div className="single-deal">
                                            <div className="overlay"/>
                                            <img className="img-fluid w-100" src={imgCategory1} alt=""/>
                                            <a href={imgCategory1} className="img-pop-up" target="_blank">
                                                <div className="deal-details">
                                                    <h6 className="deal-title">Sneaker for Sports</h6>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div className="col-lg-4 col-md-4">
                                        <div className="single-deal">
                                            <div className="overlay"/>
                                            <img className="img-fluid w-100" src={imgCategory2} alt=""/>
                                            <a href={imgCategory2} className="img-pop-up" target="_blank">
                                                <div className="deal-details">
                                                    <h6 className="deal-title">Sneaker for Sports</h6>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div className="col-lg-4 col-md-4">
                                        <div className="single-deal">
                                            <div className="overlay"/>
                                            <img className="img-fluid w-100" src={imgCategory3} alt=""/>
                                            <a href={imgCategory3} className="img-pop-up" target="_blank">
                                                <div className="deal-details">
                                                    <h6 className="deal-title">Product for Couple</h6>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <div className="col-lg-8 col-md-8">
                                        <div className="single-deal">
                                            <div className="overlay"/>
                                            <img className="img-fluid w-100" src={imgCategory4} alt=""/>
                                            <a href={imgCategory4} className="img-pop-up" target="_blank">
                                                <div className="deal-details">
                                                    <h6 className="deal-title">Sneaker for Sports</h6>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="col-lg-4 col-md-6">
                                <div className="single-deal">
                                    <div className="overlay"/>
                                    <img className="img-fluid w-100" src={imgCategory5} alt=""/>
                                    <a href={imgCategory5} className="img-pop-up" target="_blank">
                                        <div className="deal-details">
                                            <h6 className="deal-title">Sneaker for Sports</h6>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="active-product-area section_gap">
                    <OwlCarousel className="owl-carousel"
                                 items={1}
                                 autoPlay={false}
                                 autoplayTimeout={5000}
                                 loop={true}
                                 nav={true}
                                 dots={false}
                                 navText={[
                                     "<img src='assets/img/product/prev.png'>",
                                     "<img src='assets/img/product/next.png'>"
                                 ]}
                                 navClass={[
                                     "owl-prev button-without-background",
                                     "owl-next button-without-background"
                                 ]}
                    >
                        <div className="single-product-slider">
                            <div className="container">
                                <div className="row justify-content-center">
                                    <div className="col-lg-6 text-center">
                                        <div className="section-title">
                                            <h1>Latest Products</h1>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                                tempor incididunt ut labore et
                                                dolore
                                                magna aliqua.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct1} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct2} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct3} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">
                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct4} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct5} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct6} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct7} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct8} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="single-product-slider">
                            <div className="container">
                                <div className="row justify-content-center">
                                    <div className="col-lg-6 text-center">
                                        <div className="section-title">
                                            <h1>Coming Products</h1>
                                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
                                                tempor incididunt ut labore et
                                                dolore
                                                magna aliqua.</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="row">
                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct6} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct8} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct3} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct5} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct1} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct4} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct1} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="col-lg-3 col-md-6">
                                        <div className="single-product">
                                            <img className="img-fluid" src={imgProduct8} alt=""/>
                                            <div className="product-details">
                                                <h6>addidas New Hammer sole
                                                    for Sports person</h6>
                                                <div className="price">
                                                    <h6>$150.00</h6>
                                                    <h6 className="l-through">$210.00</h6>
                                                </div>
                                                <div className="prd-bottom">

                                                    <a href="" className="social-info">
                                                        <span className="ti-bag"/>
                                                        <p className="hover-text">add to bag</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-heart"/>
                                                        <p className="hover-text">Wishlist</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-sync"/>
                                                        <p className="hover-text">compare</p>
                                                    </a>
                                                    <a href="" className="social-info">
                                                        <span className="lnr lnr-move"/>
                                                        <p className="hover-text">view more</p>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </OwlCarousel>
                </section>

                <section className="exclusive-deal-area">
                    <div className="container-fluid">
                        <div className="row justify-content-center align-items-center">
                            <div className="col-lg-6 no-padding exclusive-left">
                                <div className="row clock_sec clockdiv" id="clockdiv">
                                    <div className="col-lg-12">
                                        <h1>Exclusive Hot Deal Ends Soon!</h1>
                                        <p>Who are in extremely love with eco friendly system.</p>
                                    </div>
                                    <div className="col-lg-12">
                                        <div className="row clock-wrap">
                                            <div className="col clockinner1 clockinner">
                                                <h1 className="days">150</h1>
                                                <span className="smalltext">Days</span>
                                            </div>
                                            <div className="col clockinner clockinner1">
                                                <h1 className="hours">23</h1>
                                                <span className="smalltext">Hours</span>
                                            </div>
                                            <div className="col clockinner clockinner1">
                                                <h1 className="minutes">47</h1>
                                                <span className="smalltext">Mins</span>
                                            </div>
                                            <div className="col clockinner clockinner1">
                                                <h1 className="seconds">59</h1>
                                                <span className="smalltext">Secs</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <a href="" className="primary-btn">Shop Now</a>
                            </div>
                            <div className="col-lg-6 no-padding exclusive-right">
                                <OwlCarousel className="active-exclusive-product-slider"
                                             items={1}
                                             autoPlay={false}
                                             autoplayTimeout={5000}
                                             loop={true}
                                             nav={true}
                                             dots={false}
                                             navText={[
                                                 "<img src='assets/img/product/prev.png'>",
                                                 "<img src='assets/img/product/next.png'>"
                                             ]}
                                             navClass={[
                                                 "owl-prev button-without-background",
                                                 "owl-next button-without-background"
                                             ]}
                                >
                                    <div className="single-exclusive-slider">
                                        <img className="img-fluid" src={imgProductE1} alt=""/>
                                        <div className="product-details">
                                            <div className="price">
                                                <h6>$150.00</h6>
                                                <h6 className="l-through">$210.00</h6>
                                            </div>
                                            <h4>addidas New Hammer sole
                                                for Sports person</h4>
                                            <div
                                                className="add-bag d-flex align-items-center justify-content-center">
                                                <a className="add-btn" href=""><span className="ti-bag"/></a>
                                                <span className="add-text text-uppercase">Add to Bag</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="single-exclusive-slider">
                                        <img className="img-fluid" src={imgProductE1} alt=""/>
                                        <div className="product-details">
                                            <div className="price">
                                                <h6>$150.00</h6>
                                                <h6 className="l-through">$210.00</h6>
                                            </div>
                                            <h4>addidas New Hammer sole
                                                for Sports person</h4>
                                            <div
                                                className="add-bag d-flex align-items-center justify-content-center">
                                                <a className="add-btn" href=""><span className="ti-bag"/></a>
                                                <span className="add-text text-uppercase">Add to Bag</span>
                                            </div>
                                        </div>
                                    </div>
                                </OwlCarousel>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="brand-area section_gap">
                    <div className="container">
                        <div className="row">
                            <a className="col single-img" href="#">
                                <img className="img-fluid d-block mx-auto" src={imgBrand1} alt=""/>
                            </a>
                            <a className="col single-img" href="#">
                                <img className="img-fluid d-block mx-auto" src={imgBrand2} alt=""/>
                            </a>
                            <a className="col single-img" href="#">
                                <img className="img-fluid d-block mx-auto" src={imgBrand3} alt=""/>
                            </a>
                            <a className="col single-img" href="#">
                                <img className="img-fluid d-block mx-auto" src={imgBrand4} alt=""/>
                            </a>
                            <a className="col single-img" href="#">
                                <img className="img-fluid d-block mx-auto" src={imgBrand5} alt=""/>
                            </a>
                        </div>
                    </div>
                </section>

                <DealsRelatedProducts />
            </BaseTemplate>
        );
    }
}

export default withRouter(Index);
