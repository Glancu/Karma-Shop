import React, { Component } from 'react';
import ShopCategoriesSidebar from './ShopCategoriesSidebar';
import axios from 'axios';

class ShopSidebar extends Component {
    constructor(props) {
        super(props);
        this.state = {
            brands: [],
            colors: []
        };
    }

    componentDidMount() {
        // Get brands
        axios.get("/api/brands/list", null)
            .then(result => {
                if (result.status === 200) {
                    this.setState({brands: result.data});
                }
            })
            .catch((err) => {
                console.log(err);
            });

        axios.get('/api/colors/list', null)
            .then(result => {
                if(result.status === 200) {
                    this.setState({colors: result.data});
                }
            })
            .catch((err) => {
                console.log(err);
            });
    }

    render() {
        const {brands, colors} = this.state;

        return (
            <div className="col-xl-3 col-lg-4 col-md-5">
                <ShopCategoriesSidebar />
                <div className="sidebar-filter mt-50">
                    <div className="top-filter-head">Product Filters</div>
                    <div className="common-filter">
                        <div className="head">Brands</div>
                        <form action="#">
                            <ul>
                                {brands && brands.length > 0 &&
                                    brands.map((brand) => (
                                        <li className="filter-list" key={brand.slug}>
                                            <input className="pixel-radio" type="radio" id={brand.slug} name="brand"/>
                                            <label htmlFor={brand.slug}>{brand.title} <span>({brand.countProducts})</span></label>
                                        </li>
                                    ))
                                }
                            </ul>
                        </form>
                    </div>
                    <div className="common-filter">
                        <div className="head">Color</div>
                        <form action="#">
                            <ul>
                                {colors && colors.length > 0 &&
                                    colors.map((color) => (
                                        <li className="filter-list" key={color.slug}>
                                            <input className="pixel-radio" type="radio" id={color.slug} name="color"/>
                                            <label htmlFor={color.slug}>{color.name} <span>({color.countProducts})</span></label>
                                        </li>
                                    ))
                                }
                            </ul>
                        </form>
                    </div>
                    <div className="common-filter">
                        <div className="head">Price</div>
                        <div className="price-range-area">
                            <div id="price-range"/>
                            <div className="value-wrapper d-flex">
                                <div className="price">Price:</div>
                                <span>$</span>
                                <div id="lower-value"/>
                                <div className="to">to</div>
                                <span>$</span>
                                <div id="upper-value"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}

export default ShopSidebar;
