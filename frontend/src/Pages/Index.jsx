import React, { Component } from "react";
import BaseTemplate from "../Components/BaseTemplate";
import { Link, withRouter } from 'react-router-dom';
import $ from 'jquery';
import OwlCarousel from 'react-owl-carousel';
import '../../public/assets/js/jquery.magnific-popup.min';
import '../../public/assets/js/countdown';
import axios from 'axios';
import CONFIG from '../config';
import ShoppingCart from '../Components/Shop/ShoppingCart';
import { toast } from 'react-toastify';
import SetPageTitle from '../Components/SetPageTitle';

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

import imgBrand1 from '../../public/assets/img/brand/1.png';
import imgBrand2 from '../../public/assets/img/brand/2.png';
import imgBrand3 from '../../public/assets/img/brand/3.png';
import imgBrand4 from '../../public/assets/img/brand/4.png';
import imgBrand5 from '../../public/assets/img/brand/5.png';

class Index extends Component {
    constructor(props) {
        super(props);

        this.state = {
            latestProducts: []
        }
    }

    componentDidMount() {
        SetPageTitle('Index');

        $(document).ready(function() {
            const window_height = window.innerHeight,
                  header_height = $(".default-header").height(),
                  fitscreen = window_height - header_height;

            $(".fullscreen").css("height", window_height)
            $(".fitscreen").css("height", fitscreen);

            if (document.getElementById("js-countdown")) {
                const countdown = new Date("October 17, 2018");

                function getRemainingTime(endtime) {
                    const milliseconds = Date.parse(endtime) - Date.parse(new Date());
                    const seconds = Math.floor(milliseconds / 1000 % 60);
                    const minutes = Math.floor(milliseconds / 1000 / 60 % 60);
                    const hours = Math.floor(milliseconds / (1000 * 60 * 60) % 24);
                    const days = Math.floor(milliseconds / (1000 * 60 * 60 * 24));

                    return {
                        'total': milliseconds,
                        'seconds': seconds,
                        'minutes': minutes,
                        'hours': hours,
                        'days': days
                    };
                }

                function initClock(id, endtime) {
                    const counter = document.getElementById(id);
                    const daysItem = counter.querySelector('.js-countdown-days');
                    const hoursItem = counter.querySelector('.js-countdown-hours');
                    const minutesItem = counter.querySelector('.js-countdown-minutes');
                    const secondsItem = counter.querySelector('.js-countdown-seconds');

                    function updateClock() {
                        const time = getRemainingTime(endtime);

                        daysItem.innerHTML = time.days;
                        hoursItem.innerHTML = ('0' + time.hours).slice(-2);
                        minutesItem.innerHTML = ('0' + time.minutes).slice(-2);
                        secondsItem.innerHTML = ('0' + time.seconds).slice(-2);

                        if (time.total <= 0) {
                            clearInterval(timeinterval);
                        }
                    }

                    updateClock();
                    const timeinterval = setInterval(updateClock, 1000);
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

        axios.get('/api/shop/products/latest')
            .then(result => {
                if(result.status === 200) {
                    if(result.data) {
                        this.setState({latestProducts: result.data.items});
                    }
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    addProductToCart(e, product) {
        e.preventDefault();
        const data = ShoppingCart.addProductToCart(product);

        if(data.newProductInCart === true) {
            toast.info('Product was add to cart!', {autoClose: 2000});
        } else if(data.newProductInCart === false && data.quantity <= 9) {
            toast.info('Product quantity was increased to ' + data.quantity + '!', {autoClose: 2000});
        } else {
            toast.info('Reached maximum quantity of product!', {autoClose: 2000});
        }

        const cartCountProductsEl = document.querySelector('header.header_area .cart-count-products');
        if(cartCountProductsEl) {
            cartCountProductsEl.innerText = ShoppingCart.getCountProducts();
        }
    }

    render() {
        const {latestProducts} = this.state;

        const currencySymbol = CONFIG.shop.currencySymbol;

        const latestProductsHTML = () => {
            return latestProducts.map((product) => {
                return (
                    <div className="col-lg-3 col-md-6" key={product.uuid}>
                        <div className="single-product">
                            { product.images && product.images.length > 0 &&
                                <Link to={`/shop/product/${product.slug}`}>
                                    <img className="img-fluid" src={product.images[0].url} alt={product.images[0].name} />
                                </Link>
                            }
                            <div className="product-details">
                                <h6><Link to={`/shop/product/${product.slug}`} className="product-title">{product.name}</Link></h6>
                                <div className="price">
                                    <h6>{currencySymbol} {product.priceGross}</h6>
                                </div>
                                <div className="prd-bottom">
                                    <a href="#" className="social-info" onClick={(e) => {this.addProductToCart(e, product) }}>
                                        <span className="ti-bag"/>
                                        <p className="hover-text">add to bag</p>
                                    </a>
                                    <Link to={`/shop/product/${product.slug}`} className="social-info">
                                        <span className="lnr lnr-move"/>
                                        <p className="hover-text">view more</p>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            });
        };

        const exclusiveProductsFromLatestProducts = () => {
            return latestProducts.map((product) => {
                return (
                    <div className="single-exclusive-slider" key={product.uuid}>
                        <Link to={`/shop/product/${product.slug}`}>
                            <img className="img-fluid" src={product.images[0].url} alt={product.images[0].name}/>
                        </Link>
                        <div className="product-details">
                            <div className="price">
                                <h6>{currencySymbol} {product.priceGross}</h6>
                            </div>
                            <Link to={`/shop/product/${product.slug}`}>
                                <h4>{product.name}</h4>
                            </Link>
                            <div
                                className="add-bag d-flex align-items-center justify-content-center">
                                <Link to={`/shop/product/${product.slug}`} className="add-btn">
                                    <span className="lnr lnr-move"/>
                                </Link>

                                <Link to={`/shop/product/${product.slug}`} className="">
                                    <span className="add-text text-uppercase">View more</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                )
            });
        }

        const firstOfLatestProducts = this.state.latestProducts[0];

        return (
            <BaseTemplate>
                <section className="banner-area">
                    <div className="container">
                        <div className="row fullscreen align-items-center justify-content-start">
                            <div className="col-lg-12">
                                {firstOfLatestProducts &&
                                    <div className="row single-slide align-items-center d-flex" key={firstOfLatestProducts.uuid}>
                                        <div className="col-lg-5 col-md-6">
                                            <div className="banner-content">
                                                <h1>New products!</h1>
                                                <p>Check our new products in shop!</p>
                                                <div className="add-bag d-flex align-items-center">
                                                    <Link to={'/shop'} className="add-btn">
                                                            <span className="lnr lnr-cross"/>
                                                    </Link>

                                                    <Link to={'/shop'}>
                                                        <span className="add-text text-uppercase">Shop</span>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="col-lg-7">
                                            <div className="banner-img">
                                                <img className="img-fluid" src={imgBanner} alt=""/>
                                            </div>
                                        </div>
                                    </div>
                                }
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
                                {latestProductsHTML()}
                            </div>
                        </div>
                    </div>
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

                                <Link to={'/shop'} className='primary-btn'>Shop now</Link>

                            </div>
                            <div className="col-lg-6 no-padding exclusive-right">
                                {
                                    latestProducts.length &&

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
                                        {exclusiveProductsFromLatestProducts()}
                                    </OwlCarousel>
                                }
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
            </BaseTemplate>
        );
    }
}

export default withRouter(Index);
