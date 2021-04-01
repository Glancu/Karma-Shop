import React, { Component } from 'react';

class SingleProductList extends Component {
    render() {
        const item = this.props.item;

        return (
            item &&
                <div className="col-lg-4 col-md-6" data-price={item.priceGross} data-brand={item.shopBrand.title} data-category={item.shopCategory.title}>
                    <div className="single-product">
                        { item.images && item.images.length > 0 &&
                            <img className="img-fluid" src={item.images[0].url} alt={item.images[0].name} />
                        }
                        <div className="product-details">
                            <h6>{item.name}</h6>
                            <div className="price" data-price={item.priceGross}>
                                <h6>{item.priceGross}</h6>
                            </div>
                            <div className="prd-bottom">
                                <a href="#" className="social-info">
                                    <span className="ti-bag"/>
                                    <p className="hover-text">add to bag</p>
                                </a>
                                <a href="#" className="social-info">
                                    <span className="lnr lnr-heart"/>
                                    <p className="hover-text">Wishlist</p>
                                </a>
                                <a href="#" className="social-info">
                                    <span className="lnr lnr-move"/>
                                    <p className="hover-text">view more</p>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
        )
    }
}

export default SingleProductList;
