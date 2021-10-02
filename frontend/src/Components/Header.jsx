import React, { Component } from "react";
import { NavLink, Link } from "react-router-dom";
import $ from 'jquery';
import ShoppingCart from './Shop/ShoppingCart';
import { userLoggedIn } from './User/UserInfo';
import axios from 'axios';
import '../../public/assets/js/jquery.sticky';

// CSS IMPORT
import '../../public/assets/css/linearicons.css';
import '../../public/assets/css/font-awesome.min.css';
import '../../public/assets/css/themify-icons.css';
import '../../public/assets/css/bootstrap.css';
import '../../public/assets/css/owl.carousel.css';
import '../../public/assets/css/nice-select.css';
import '../../public/assets/css/nouislider.min.css';
import '../../public/assets/css/ion.rangeSlider.css';
import '../../public/assets/css/ion.rangeSlider.skinFlat.css';
import '../../public/assets/css/magnific-popup.css';
import '../../public/assets/css/main.css';

import imgLogoHeader from '../../public/assets/img/logo.png';

class Header extends Component {
    constructor(props) {
        super(props);

        this.state = {
            userLoggedIn: false,
            productsSearchBox: [],
            timer: null
        };

        this.handleSearchInputChange = this.handleSearchInputChange.bind(this);
        this.getProducts = this.getProducts.bind(this);
    }

    componentDidMount() {
        $('.sticky-header').sticky();

        $('.collapse').on('shown.bs.collapse', function() {
            $(this)
                .parent()
                .find(".lnr-arrow-right")
                .removeClass("lnr-arrow-right")
                .addClass("lnr-arrow-left");
        })
            .on('hidden.bs.collapse', function() {
                $(this)
                    .parent()
                    .find(".lnr-arrow-left")
                    .removeClass("lnr-arrow-left")
                    .addClass("lnr-arrow-right");
            });

        $("#search_input_box").hide();
        $("#search").on("click", function() {
            $("#search_input_box")
                .slideToggle();
            $("#search_input")
                .focus();
        });
        $("#close_search").on("click", function() {
            $('#search_input_box')
                .slideUp(500);
        });

        $('.navbar-nav li.dropdown').hover(function() {
            $(this)
                .find('.dropdown-menu')
                .stop(true, true)
                .delay(200)
                .fadeIn(500);
        }, function() {
            $(this)
                .find('.dropdown-menu')
                .stop(true, true)
                .delay(200)
                .fadeOut(500);
        });

        userLoggedIn().then((data) => {
            this.setState({userLoggedIn: data});
        });
    }

    getProducts(value) {
        const _this = this;
        axios.get(`/api/products/search/${value}`)
            .then(result => {
                if(result.status === 200 && result.data.length > 0) {
                    _this.setState({productsSearchBox: result.data})
                } else {
                    _this.setState({productsSearchBox: []})
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    handleSearchInputChange(e) {
        let {timer} = this.state;
        const _this = this;
        const searchInputValue = e.target.value.replaceAll(' ', '');
        if(searchInputValue && searchInputValue.length >= 3) {
            if(timer) {
                clearTimeout(timer);
            }

            timer = setTimeout(() => {
                _this.getProducts(searchInputValue);
            }, 500);

            this.setState({timer});
        } else {
            _this.setState({productsSearchBox: []})
        }
    }

    render() {
        const {productsSearchBox} = this.state;

        const renderLoginLogout = () => {
            if(this.state.userLoggedIn) {
                return (
                    <li className="nav-item">
                        <NavLink className="nav-link" to={'/logout'}>Logout</NavLink>
                    </li>
                )
            }

            return (
                <li className="nav-item">
                    <NavLink className="nav-link" to={'/login'}>Login</NavLink>
                </li>
            )
        }

        const renderProductsSearchBox = () => {
            return productsSearchBox.map((product) => {
                return (
                    <div className="single-item" key={product.uuid}>
                        <img src={product.image.url} alt="" height="75"/>

                        <div className="text">
                            <p><Link to={`/shop/product/${product.slug}`}>{product.name}</Link></p>
                            <p>€ {product.priceGross}</p>
                        </div>
                    </div>
                )
            })
        }

        return (
            <header className="header_area sticky-header">
                <div className="main_menu">
                    <nav className="navbar navbar-expand-lg navbar-light main_box">
                        <div className="container">
                            <NavLink className="navbar-brand logo_h" exact to={'/'}><img src={imgLogoHeader} alt=""/></NavLink>
                            <button className="navbar-toggler" type="button" data-toggle="collapse"
                                    data-target="#navbarSupportedContent"
                                    aria-controls="navbarSupportedContent" aria-expanded="false"
                                    aria-label="Toggle navigation">
                                <span className="icon-bar"/>
                                <span className="icon-bar"/>
                                <span className="icon-bar"/>
                            </button>
                            <div className="collapse navbar-collapse offset" id="navbarSupportedContent">
                                <ul className="nav navbar-nav menu_nav ml-auto">
                                    <li className="nav-item">
                                        <NavLink exact className="nav-link" to={'/'}>
                                            Home
                                        </NavLink>
                                    </li>
                                    <li className="nav-item">
                                        <NavLink className="nav-link" to={'/shop'}>Shop</NavLink>
                                    </li>
                                    <li className="nav-item submenu dropdown">
                                        <NavLink className="nav-link dropdown-toggle"
                                                 data-toggle="dropdown"
                                                 role="button"
                                                 aria-haspopup="true"
                                                 aria-expanded="false"
                                                 to={'/blog'}>
                                            Blog
                                        </NavLink>
                                        <ul className="dropdown-menu">
                                            <li className="nav-item">
                                                <NavLink className="nav-link" to={'/blog'}>Blog</NavLink>
                                            </li>
                                            <li className="nav-item">
                                                <NavLink className="nav-link" to={'/blog/id'}>Blog Details</NavLink>
                                            </li>
                                        </ul>
                                    </li>
                                    <li className="nav-item">
                                        <NavLink className="nav-link" to={'/contact'}>Contact</NavLink>
                                    </li>
                                    {
                                        renderLoginLogout()
                                    }
                                </ul>
                                <ul className="nav navbar-nav navbar-right">
                                    <li className="nav-item">
                                        <Link to={'/shop/cart'} className="cart"><span className="ti-bag"/></Link>
                                        <span className="cart-count-products">{ShoppingCart.getCountProducts()}</span>
                                    </li>
                                    <li className="nav-item">
                                        <button className="search">
                                            <span className="lnr lnr-magnifier" id="search"/>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                </div>
                <div className="search-box">
                    <div className="search_input" id="search_input_box">
                        <div className="container">
                            <form className="d-flex justify-content-between">
                                <input type="text"
                                       className="form-control"
                                       id="search_input"
                                       placeholder="Search Here"
                                       onKeyUp={this.handleSearchInputChange}/>
                                <button type="submit" className="btn"/>
                                <span className="lnr lnr-cross" id="close_search" title="Close Search"/>
                            </form>
                        </div>

                        <div className={productsSearchBox.length === 0 ? "search-list hide" : "search-list"}>
                            <h6 className="text-left">Products:</h6>
                            {renderProductsSearchBox()}
                        </div>
                    </div>
                </div>
            </header>
        )
    }
}

export default Header
