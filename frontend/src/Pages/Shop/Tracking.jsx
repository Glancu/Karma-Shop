import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';

class Tracking extends Component {
    render() {
        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Order Tracking</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}/>
                                    <a href="#">Fashon Category</a>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="tracking_box_area section_gap">
                    <div className="container">
                        <div className="tracking_box_inner">
                            <p>To track your order please enter your Order ID in the box below and press the "Track"
                                button. This
                                was given to you on your receipt and in the confirmation email you should have
                                received.</p>
                            <form className="row tracking_form" action="#" method="post" noValidate="novalidate">
                                <div className="col-md-12 form-group">
                                    <input type="text"
                                           className="form-control"
                                           id="order"
                                           name="order"
                                           placeholder="Order ID"
                                           // onFocus="this.placeholder = ''"
                                           // onBlur="this.placeholder = 'Order ID'"
                                    />
                                </div>
                                <div className="col-md-12 form-group">
                                    <input type="email"
                                           className="form-control"
                                           id="email"
                                           name="email"
                                           placeholder="Billing Email Address"
                                           // onFocus="this.placeholder = ''"
                                           // onBlur="this.placeholder = 'Billing Email Address'"
                                    />
                                </div>
                                <div className="col-md-12 form-group">
                                    <button type="submit" value="submit" className="primary-btn">Track Order</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Tracking
