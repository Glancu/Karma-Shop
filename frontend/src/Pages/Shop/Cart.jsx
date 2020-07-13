import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';

import imgCart from '../../../public/assets/img/cart.jpg';

class Cart extends Component {
    increaseItemCountPlus(e) {
        const sstEl = e.target.parentNode.parentNode.querySelector('input');
        const sttValue = sstEl.value;
        if(!isNaN(sttValue))  {
            sstEl.value++;
        }
    }

    increaseItemCountMinus(e) {
        const sstEl = e.target.parentNode.parentNode.querySelector('input');
        const sttValue = sstEl.value;
        if(!isNaN(sttValue) && sttValue > 1)  {
            sstEl.value--;
        }
    }

    render() {
        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Shopping Cart</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <a href="#">Cart</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="cart_area">
                    <div className="container">
                        <div className="cart_inner">
                            <div className="table-responsive">
                                <table className="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div className="media">
                                                <div className="d-flex">
                                                    <img src={imgCart} alt="" />
                                                </div>
                                                <div className="media-body">
                                                    <p>Minimalistic shop for multipurpose use</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$360.00</h5>
                                        </td>
                                        <td>
                                            <div className="product_count">
                                                <input type="text" name="qty" id="sst" maxLength="12" defaultValue="1"
                                                       title="Quantity:"
                                                       className="input-text qty" />
                                                    <button
                                                        // onClick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;"
                                                        onClick={this.increaseItemCountPlus}
                                                        className="increase items-count" type="button"
                                                    >
                                                        <i className="lnr lnr-chevron-up"/>
                                                    </button>
                                                    <button
                                                        onClick={this.increaseItemCountMinus}
                                                        // onClick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;"
                                                        className="reduced items-count" type="button"
                                                    >
                                                        <i className="lnr lnr-chevron-down"/>
                                                    </button>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$720.00</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div className="media">
                                                <div className="d-flex">
                                                    <img src={imgCart} alt="" />
                                                </div>
                                                <div className="media-body">
                                                    <p>Minimalistic shop for multipurpose use</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$360.00</h5>
                                        </td>
                                        <td>
                                            <div className="product_count">
                                                <input type="text" name="qty" id="sst" maxLength="12" defaultValue="1"
                                                       title="Quantity:"
                                                       className="input-text qty" />
                                                    <button
                                                        onClick={this.increaseItemCountPlus}
                                                        // onClick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;"
                                                        className="increase items-count" type="button"
                                                    >
                                                        <i className="lnr lnr-chevron-up"/>
                                                    </button>
                                                    <button
                                                        onClick={this.increaseItemCountMinus}
                                                        // onClick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;"
                                                        className="reduced items-count" type="button"
                                                    >
                                                        <i className="lnr lnr-chevron-down"/>
                                                    </button>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$720.00</h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div className="media">
                                                <div className="d-flex">
                                                    <img src={imgCart} alt="" />
                                                </div>
                                                <div className="media-body">
                                                    <p>Minimalistic shop for multipurpose use</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$360.00</h5>
                                        </td>
                                        <td>
                                            <div className="product_count">
                                                <input type="text" name="qty" id="sst" maxLength="12" defaultValue="1"
                                                       title="Quantity:"
                                                       className="input-text qty" />
                                                    <button
                                                        onClick={this.increaseItemCountPlus}
                                                        // onClick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst )) result.value++;return false;"
                                                        className="increase items-count" type="button"
                                                    >
                                                        <i className="lnr lnr-chevron-up"/>
                                                    </button>
                                                    <button
                                                        onClick={this.increaseItemCountMinus}
                                                        // onClick="var result = document.getElementById('sst'); var sst = result.value; if( !isNaN( sst ) &amp;&amp; sst > 0 ) result.value--;return false;"
                                                        className="reduced items-count" type="button"
                                                    >
                                                        <i className="lnr lnr-chevron-down"/>
                                                    </button>
                                            </div>
                                        </td>
                                        <td>
                                            <h5>$720.00</h5>
                                        </td>
                                    </tr>
                                    <tr className="bottom_button">
                                        <td>
                                            <a className="gray_btn" href="#">Update Cart</a>
                                        </td>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <div className="cupon_text d-flex align-items-center">
                                                <input type="text" placeholder="Coupon Code" />
                                                <a className="primary-btn" href="#">Apply</a>
                                                <a className="gray_btn" href="#">Close Coupon</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <h5>Subtotal</h5>
                                        </td>
                                        <td>
                                            <h5>$2160.00</h5>
                                        </td>
                                    </tr>
                                    <tr className="shipping_area">
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <h5>Shipping</h5>
                                        </td>
                                        <td>
                                            <div className="shipping_box">
                                                <ul className="list">
                                                    <li><a href="#">Flat Rate: $5.00</a></li>
                                                    <li><a href="#">Free Shipping</a></li>
                                                    <li><a href="#">Flat Rate: $10.00</a></li>
                                                    <li className="active"><a href="#">Local Delivery: $2.00</a></li>
                                                </ul>
                                                <h6>
                                                    Calculate Shipping
                                                    <i className="fa fa-caret-down" aria-hidden="true"/>
                                                </h6>
                                                <select className="shipping_select">
                                                    <option value="1">Bangladesh</option>
                                                    <option value="2">India</option>
                                                    <option value="4">Pakistan</option>
                                                </select>
                                                <select className="shipping_select">
                                                    <option value="1">Select a State</option>
                                                    <option value="2">Select a State</option>
                                                    <option value="4">Select a State</option>
                                                </select>
                                                <input type="text" placeholder="Postcode/Zipcode" />
                                                <a className="gray_btn" href="#">Update Details</a>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr className="out_button_area">
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>

                                        </td>
                                        <td>
                                            <div className="checkout_btn_inner d-flex align-items-center">
                                                <a className="gray_btn" href="#">Continue Shopping</a>
                                                <a className="primary-btn" href="#">Proceed to checkout</a>
                                            </div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Cart
