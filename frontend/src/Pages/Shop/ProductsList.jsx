import React, { Component } from 'react';
import axios from 'axios';
import ShopBaseTemplate from '../../Components/Shop/ShopBaseTemplate';

class ProductsList extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: []
        };
    }

    componentDidMount() {
        document.body.id = 'products-list';

        // Get products
        axios.get("/api/products/list", null)
            .then(result => {
                if (result.status === 200) {
                    this.setState({items: result.data});
                }
            })
            .catch((err) => {
                console.log(err);
            });
    }

    render() {
        const { items } = this.state;

        return (
            <ShopBaseTemplate items={items} />
        )
    }
}

export default ProductsList
