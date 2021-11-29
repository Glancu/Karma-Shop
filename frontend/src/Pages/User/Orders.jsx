import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import { UserPanelHeader } from '../../Components/User/UserPanelHeader';
import BaseTemplate from '../../Components/BaseTemplate';
import axios from 'axios';
import { getUserToken } from '../../Components/User/UserData';
import SetPageTitle from '../../Components/SetPageTitle';

class Orders extends Component {
    constructor(props) {
        super(props);

        this.state = {
            orders: []
        }
    }

    componentDidMount() {
        SetPageTitle('Orders - Panel');

        const userToken = getUserToken();

        const config = {
            headers: {
                "Content-type": "application/json",
                "Authorization": `Bearer ${userToken}`,
            },
        };

        axios.get('/api/shop/user-orders', config)
            .then(result => {
                if(result && result.data) {
                    this.setState({orders: result.data});
                }
            })
            .catch(err => {
                console.error(err);
            })
    }

    render() {
        const {orders} = this.state;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Panel</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Orders</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="login_box_area section_gap">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-12">
                                <UserPanelHeader />
                                <div className="login_form_inner">
                                    <h3>Orders:</h3>

                                    <div className="container">
                                        <div className="cart_inner">
                                            <div className="table-responsive">
                                                {
                                                    orders && orders.length > 0 &&
                                                    orders.map(order => {
                                                        return (
                                                            <div key={order.uuid}>
                                                                <h3>Order <b>{order.orderNumber}</b></h3>

                                                                <table className="table">
                                                                    <thead>
                                                                    <tr>
                                                                        <th scope="col">Created at</th>
                                                                        <th scope="col">Method payment</th>
                                                                        <th scope="col">Status</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    <tr>
                                                                        <td>{order.createdAt}</td>
                                                                        <td>{order.methodPayment}</td>
                                                                        <td>{order.status}</td>
                                                                    </tr>
                                                                    </tbody>
                                                                </table>

                                                                <h4>Products:</h4>

                                                                <table className="table">
                                                                    <thead>
                                                                    <tr>
                                                                        <th scope="col">Image</th>
                                                                        <th scope="col">Product name</th>
                                                                        <th scope="col">Quantity</th>
                                                                        <th scope="col">Price gross</th>
                                                                        <th scope="col">Total</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    {
                                                                        order.products.map(product => {
                                                                            const productQuantity = product.quantity;
                                                                            const priceGross = product.priceGross;
                                                                            const total = priceGross * productQuantity;

                                                                            return (
                                                                                <tr key={product.uuid}>
                                                                                    <td>
                                                                                        <img src={product.images[0].url} alt="" height="50px"/>
                                                                                    </td>
                                                                                    <td>{product.name}</td>
                                                                                    <td>{productQuantity}</td>
                                                                                    <td>{priceGross}</td>
                                                                                    <td>{total}</td>
                                                                                </tr>
                                                                            )
                                                                        })
                                                                    }
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        )
                                                    })
                                                }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Orders;
