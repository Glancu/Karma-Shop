import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link, Redirect } from 'react-router-dom';
import ShoppingCart from '../../Components/Shop/ShoppingCart';
import CONFIG from '../../config';
import SetPageTitle from '../../Components/SetPageTitle';
import { generatePath } from 'react-router';

class Confirmation extends Component {
    componentDidMount() {
        SetPageTitle('Confirmation - Shop');

        window.localStorage.removeItem(ShoppingCart.localStorageShopKeyName);
    }

    render() {
        const currencySymbol = CONFIG.shop.currencySymbol;
        const shoppingCartProducts = ShoppingCart.getProducts();
        const {payPalUrl} = this.props.location.state;

        const localStorageShop = window.localStorage.getItem(ShoppingCart.localStorageShopKeyName);
        if(!localStorageShop || JSON.parse(localStorageShop).products.length === 0 ||
            Object.keys(JSON.parse(localStorageShop).form).length === 0
        ) {
            return <Redirect to="/" />
        }

        let parsedLocalStorageShop = null;
        if(localStorageShop) {
            parsedLocalStorageShop = JSON.parse(localStorageShop);
            if(!parsedLocalStorageShop || !parsedLocalStorageShop.form || !parsedLocalStorageShop.products) {
                return <Redirect to="/" />
            }
        }

        if(!parsedLocalStorageShop) {
            return <Redirect to="/" />
        }

        const {form} = parsedLocalStorageShop;
        const {inputs} = form;

        const renderBillingAddress = () => {
            return (
                <>
                    <li><a href="#"><span>Address first</span>: {inputs.addressLineFirst}</a></li>
                    {inputs.addressLineSecond &&
                    <li><a href="#"><span>Address second</span>: {inputs.addressLineSecond}</a></li>
                    }
                    <li><a href="#"><span>City</span>: {inputs.city}</a></li>
                    {inputs.postalCode &&
                    <li><a href="#"><span>Postcode </span>: {inputs.postalCode}</a></li>
                    }
                </>
            )
        };

        const renderShippingAddress = () => {
            return form.customCorrespondence === true ? (
                    <>
                        <li><a href="#"><span>Address first</span>: {inputs.addressLineFirstCorrespondence}</a></li>
                        <li><a href="#"><span>Address second</span>: {inputs.addressLineSecondCorrespondence}</a></li>
                        <li><a href="#"><span>City</span>: {inputs.cityCorrespondence}</a></li>
                        {inputs.postalCodeCorrespondence &&
                        <li><a href="#"><span>Postcode </span>: {inputs.postalCodeCorrespondence}</a></li>
                        }
                    </>
                )
                : renderBillingAddress();
        };

        const paymentNotifyUrl = window.location.origin + generatePath('/payment/pay-pal/notify');
        const payPalFullUrl = payPalUrl + '?notifyUrl=' + paymentNotifyUrl;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Confirmation</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <Link to={'/shop'}>Shop<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Confirmation</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="order_details section_gap">
                    <div className="container">
                        <h3 className="title_confirmation">Thank you. Your order has been received.</h3>
                        <div className="row order_d_inner">
                            <div className="col-lg-4">
                                <div className="details_item">
                                    <h4>Order Info</h4>
                                    <ul className="list">
                                        <li><a href="#"><span>Total</span>: {currencySymbol} {ShoppingCart.getTotalPrice()}</a></li>
                                        <li><a href="#"><span>Payment method</span>: {form.methodPayment}</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div className="col-lg-4">
                                <div className="details_item">
                                    <h4>Billing Address</h4>
                                    <ul className="list">
                                        {renderBillingAddress()}
                                    </ul>
                                </div>
                            </div>
                            <div className="col-lg-4">
                                <div className="details_item">
                                    <h4>Shipping Address</h4>
                                    <ul className="list">
                                        {renderShippingAddress()}
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div className="order_details_table">
                            <h2>Order Details</h2>
                            <div className="table-responsive">
                                <table className="table">
                                    <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    {
                                        shoppingCartProducts.map(product => {
                                            const priceGross = product.priceGross;
                                            const priceGrossSum = (parseFloat(priceGross) * product.quantity).toFixed(2);
                                            const productQuantity = (Math.round(product.quantity * 100) / 100).toFixed(2);

                                            return (
                                                <tr  key={product.uuid}>
                                                    <td><p>{product.name}</p></td>
                                                    <td><h5>x {productQuantity}</h5></td>
                                                    <td><p>{currencySymbol} {priceGrossSum}</p></td>
                                                </tr>
                                            )
                                        })
                                    }
                                    <tr>
                                        <td><h4>Total</h4></td>
                                        <td><h5/></td>
                                        <td><p>{currencySymbol} {ShoppingCart.getTotalPrice()}</p></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        {payPalUrl &&
                        <div className="shop-payment-pay_pal">
                            <p><b>Payment</b></p>
                            <p>You can pay for this order now, <a href={payPalFullUrl}><b>click here</b></a>.</p>
                            <p>You can also click by PayPal logo</p>
                            <p><a href={payPalFullUrl}><img
                                src="https://www.paypalobjects.com/webstatic/mktg/logo/bdg_payments_by_pp_2line.png"
                                alt="pay-pal"/></a></p>
                        </div>
                        }
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Confirmation
