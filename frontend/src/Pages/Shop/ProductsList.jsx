import React, { Component } from 'react';
import ShopBaseTemplate from '../../Components/Shop/ShopBaseTemplate';

class ProductsList extends Component {
    componentDidMount() {
        document.body.id = 'products-list';
    }

    render() {
        return (
            <ShopBaseTemplate itemsUrl="/api/shop/products/list" />
        )
    }
}

export default ProductsList
