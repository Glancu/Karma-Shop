import React, { Component } from 'react';
import ShopBaseTemplate from '../../Components/Shop/ShopBaseTemplate';

class Category extends Component {
    constructor(props) {
        super(props);
        this.state = {
            slug: this.props.match.params.slug
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(this.state.slug !== this.props.match.params.slug) {
            this.setState({slug: this.props.match.params.slug});
        }
    }

    render() {
        const {slug} = this.state;

        return (
            <ShopBaseTemplate itemsUrl={`/api/shop/products/list?categorySlug=${slug}`} categorySlug={slug} />
        )
    }
}

export default Category
