import React, { Component } from 'react';
import ShoppingCart from './ShoppingCart';
import { toast } from 'react-toastify';
import { Link } from 'react-router-dom';
import CONFIG from '../../config';

class SingleProductList extends Component {
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
        const {item} = this.props;
        const currencySymbol = CONFIG.shop.currencySymbol;

        return (
            item &&
                <div className="col-lg-4 col-md-6">
                    <div className="single-product">
                        { item.images && item.images.length > 0 &&
                            <Link to={`/shop/product/${item.slug}`}>
                                <img className="img-fluid" src={item.images[0].url} alt={item.images[0].name} />
                            </Link>
                        }
                        <div className="product-details">
                            <h6>
                                <Link to={`/shop/product/${item.slug}`} className="product-title">{item.name}</Link>
                            </h6>
                            <div className="price">
                                <h6>{currencySymbol} {item.priceGross}</h6>
                            </div>
                            <div className="prd-bottom">
                                <a href="#" className="social-info" onClick={(e) => {this.addProductToCart(e, item) }}>
                                    <span className="ti-bag"/>
                                    <p className="hover-text">add to bag</p>
                                </a>
                                <Link to={`/shop/product/${item.slug}`} className="social-info">
                                    <span className="lnr lnr-move"/>
                                    <p className="hover-text">view more</p>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
        )
    }
}

export default SingleProductList;
