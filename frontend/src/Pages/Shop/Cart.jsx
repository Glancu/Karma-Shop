import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import ShoppingCart from '../../Components/Shop/ShoppingCart';
import $ from 'jquery';
import SetPageTitle from '../../Components/SetPageTitle';

class Cart extends Component {
    constructor(props) {
        super(props);

        this.increaseQuantityItem = this.increaseQuantityItem.bind(this);
        this.decreaseQuantityItem = this.decreaseQuantityItem.bind(this);
        this.modalAcceptToRemoveProduct = this.modalAcceptToRemoveProduct.bind(this);
        this.onInputProductQuantityChange = this.onInputProductQuantityChange.bind(this);
    }

    componentDidMount() {
        SetPageTitle('Cart - Shop');

        const removeProductAlertModalEl = $('#removeProductAlertModal');
        removeProductAlertModalEl.on('show.bs.modal', (event) => {
            const product = event.relatedTarget.product;
            if(product) {
                const modal = event.target;
                modal.querySelector('.modal-title').innerText = 'Remove ' + product.name + ' from cart';
                modal.querySelector('.modal-body').innerText = 'Are you sure to remove this product from your cart?';

                const inputEl = modal.querySelector('input[type="hidden"][name="product_uuid"]');
                if(!inputEl) {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'hidden');
                    input.setAttribute('name', 'product_uuid');
                    input.setAttribute('value', product.uuid);
                    modal.append(input);
                } else {
                    inputEl.value = product.uuid;
                }
            }
        });

        removeProductAlertModalEl.on('hidden.bs.modal', () => {
            const modal = document.getElementById('removeProductAlertModal');
            if(modal) {
                const productUuidEl = modal.querySelector('input[type="hidden"][name="product_uuid"]');
                if(productUuidEl) {
                    productUuidEl.value = null;
                }
            }
        });
    }

    increaseQuantityItem(e, product) {
        const sstEl = e.target.parentNode.parentNode.querySelector('input');
        const sttValue = sstEl.value;
        if(!isNaN(sttValue) && parseInt(sttValue) < 10)  {
            sstEl.value++;

            ShoppingCart.increaseQuantityOfProduct(product);

            this.forceUpdate();
        }
    }

    decreaseQuantityItem(e, product) {
        const quantityInputEl = e.target.parentNode.parentNode.querySelector('input');
        const quantityInputElValue = quantityInputEl.value;
        if(!isNaN(quantityInputElValue) && quantityInputElValue > 1)  {
            quantityInputEl.value--;
        }

        if(parseInt(quantityInputElValue) === 1) {
            $('#removeProductAlertModal').modal('show', {'product': product});
            return false;
        }

        ShoppingCart.decreaseQuantityOfProduct(product);

        this.forceUpdate();
    }

    modalAcceptToRemoveProduct() {
        const modal = document.getElementById('removeProductAlertModal');
        if(modal) {
            const productUuidEl = modal.querySelector('input[type="hidden"][name="product_uuid"]');
            if(productUuidEl) {
                const productUuidValue = productUuidEl.value;
                if(productUuidValue) {
                    const product = ShoppingCart.findProductByProductUuid(productUuidValue);
                    if(product) {
                        ShoppingCart.removeProductFromCart(product);

                        this.forceUpdate();
                    }
                }
            }
        }
    }

    onInputProductQuantityChange(e, product) {
        const target = e.target;
        const targetValue = target.value;
        if(!targetValue || parseInt(targetValue) < 1) {
            target.value = 1;
        }

        if(parseInt(targetValue) > 10) {
            target.value = 10;
        }

        if(target.value) {
            if(product.quantity !== target.value) {
                ShoppingCart.updateQuantityOfProduct(product, target.value);

                this.forceUpdate();
            }
        }
    }

    removeProductFromCart(e,product) {
        e.preventDefault();

        ShoppingCart.removeProductFromCart(product);

        this.forceUpdate();
    }

    render() {
        const shoppingCartProducts = ShoppingCart.getProducts();
        const isCartEmpty = shoppingCartProducts.length === 0;

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Shopping Cart</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <Link to={'/shop'}>Shop<span className="lnr lnr-arrow-right"/></Link>
                                    <p>Cart</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                {
                    isCartEmpty &&
                    <section className="cart_area">
                        <div className="container">
                            <div className="cart_inner text-center">
                                <h2>Cart is empty!</h2>
                            </div>
                        </div>
                    </section>
                }

                {
                    !isCartEmpty &&
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
                                            {
                                                shoppingCartProducts && shoppingCartProducts.length > 0 &&
                                                shoppingCartProducts.map(product => {
                                                    const priceGross = product.priceGross;
                                                    const priceGrossSum = (parseFloat(priceGross) * product.quantity).toFixed(2);

                                                    return (
                                                        <tr key={product.uuid}>
                                                            <td>
                                                                <div className="media">
                                                                    {
                                                                        product.image &&
                                                                            <div className="d-flex">
                                                                                <img src={product.image.url}
                                                                                     alt="" height="100"/>
                                                                            </div>
                                                                    }
                                                                    <div className="media-body">
                                                                        <p>{product.name}</p>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <h5>{priceGross}</h5>
                                                            </td>
                                                            <td>
                                                                <div className="product_count">
                                                                    <input type="number" name="qty"
                                                                           id={`product_quantity[${product.slug}]`}
                                                                           minLength="0" maxLength="10"
                                                                           defaultValue={product.quantity}
                                                                           title="Quantity:" className="input-text qty"
                                                                           onChange={(e) => this.onInputProductQuantityChange(e, product)}/>
                                                                    <button
                                                                        onClick={(e) => this.increaseQuantityItem(e, product)}
                                                                        className="increase items-count" type="button"
                                                                    >
                                                                        <i className="lnr lnr-chevron-up"/>
                                                                    </button>
                                                                    <button
                                                                        onClick={(e) => this.decreaseQuantityItem(e, product)}
                                                                        className="reduced items-count" type="button"
                                                                    >
                                                                        <i className="lnr lnr-chevron-down"/>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <h5>{priceGrossSum}</h5>
                                                            </td>
                                                        </tr>
                                                    )
                                                })
                                            }
                                            <tr>
                                                <td />
                                                <td />
                                                <td>
                                                    <h5>Subtotal</h5>
                                                </td>
                                                <td style={{whiteSpace: 'nowrap'}}>
                                                    <h5>{ShoppingCart.getTotalPrice()}</h5>
                                                </td>
                                            </tr>
                                            <tr className="out_button_area">
                                                <td />
                                                <td />
                                                <td />
                                                <td style={{whiteSpace: 'nowrap'}}>
                                                    <div className="checkout_btn_inner d-flex align-items-center">
                                                        <Link to={'/shop'} className="gray_btn">Continue Shopping</Link>
                                                        <Link to={'/shop/checkout'} className="primary-btn">
                                                            Proceed to checkout
                                                        </Link>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </section>
                }

                <div className="modal fade" id="removeProductAlertModal" tabIndex="-1" role="dialog"
                     aria-labelledby="removeProductAlertModalLabel" aria-hidden="true">
                    <div className="modal-dialog" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title" id="removeProductAlertModalLabel" />
                                <button type="button" className="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div className="modal-body" />
                            <div className="modal-footer">
                                <button type="button" className="btn btn-secondary" data-dismiss="modal">
                                    Cancel
                                </button>
                                <button type="button" className="btn btn-primary" data-dismiss="modal"
                                        onClick={this.modalAcceptToRemoveProduct}
                                >
                                    Remove product
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </BaseTemplate>
        )
    }
}

export default Cart
