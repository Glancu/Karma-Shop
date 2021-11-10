import React, { Component } from "react";
import { NavLink, Link } from "react-router-dom";
import $ from 'jquery';
import ShoppingCart from './Shop/ShoppingCart';
import { userLoggedIn } from './User/UserData';
import '../../public/assets/js/jquery.sticky';
import '../../public/assets/js/jquery.autocomplete.min';

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
            userLoggedIn: false
        };
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
            $("#autocomplete")
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

        userLoggedIn().then((isUserLoggedIn) => {
            this.setState({userLoggedIn: isUserLoggedIn});
        });

        $('#autocomplete').autocomplete({
            serviceUrl: '/api/shop/products/search',
            onSelect: function (suggestion) {
                if(suggestion && suggestion.url) {
                    window.location.href = suggestion.url;
                }
            }
        });
    }

    render() {
        const renderLoginLogout = () => {
            if(this.state.userLoggedIn) {
                return (
                    <>
                        <li className="nav-item">
                            <NavLink className="nav-link" to={'/user/panel'}>Profile</NavLink>
                        </li>
                        <li className="nav-item">
                            <NavLink className="nav-link" to={'/logout'}>Logout</NavLink>
                        </li>
                    </>
                )
            }

            return (
                <li className="nav-item">
                    <NavLink className="nav-link" to={'/login'}>Login</NavLink>
                </li>
            )
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
                                       id="autocomplete"
                                       placeholder="Search Here" />
                                <button type="submit" className="btn"/>
                                <span className="lnr lnr-cross" id="close_search" title="Close Search"/>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
        )
    }
}

export default Header
