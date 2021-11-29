import React, { Component } from 'react';
import ShopBaseTemplate from '../../Components/Shop/ShopBaseTemplate';
import SetPageTitle from '../../Components/SetPageTitle';

class ProductsList extends Component {
    componentDidMount() {
        SetPageTitle('Shop');
    }

    render() {
        return (
            <ShopBaseTemplate itemsUrl="/api/shop/products/list" />
        )
    }
}

export default ProductsList
