import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import ShopSidebar from './ShopSidebar';
import SingleProductList from './SingleProductList';
import BaseTemplate from '../BaseTemplate';
import noUiSlider from '../../../public/assets/js/nouislider.min';
import ShopSortingPagination from './ShopSortingPagination';
import axios from 'axios';
import PropTypes from 'prop-types';
import GetPage from '../GetPage';
import '../../../public/assets/js/jquery.nice-select.min';

class ShopBaseTemplate extends Component {
    constructor(props) {
        super(props);
        this.state = {
            itemsUrl: '',
            items: [],
            pageOfItems: props.pageOfItems ?? [],
            pagination: {
                perPage: 12,
                currentPage: GetPage.getSubPage(),
                countItems: 0
            }
        };

        this.setCurrentPage = this.setCurrentPage.bind(this);
        this.setPerPage = this.setPerPage.bind(this);
        this.getItems = this.getItems.bind(this);
    }

    componentDidMount() {
        $('select').niceSelect();

        this.getItems();

        if(document.getElementById('price-range')) {
            const nonLinearSlider = document.getElementById('price-range');

            noUiSlider.create(nonLinearSlider, {
                connect: true,
                behaviour: 'tap',
                start: [500, 4000],
                range: {
                    // Starting at 500, step the value by 500,
                    // until 4000 is reached. From there, step by 1000.
                    'min': [50],
                    '10%': [500, 500],
                    '50%': [4000, 1000],
                    'max': [10000]
                }
            }, true);

            const nodes = {
                0: document.getElementById('lower-value'),
                1: document.getElementById('upper-value')
            };

            // Display the slider value and how far the handle moved
            // from the left edge of the slider.
            nonLinearSlider.noUiSlider.on('update', function(values, handle) {
                nodes[handle].innerHTML = values[handle];
            });
        }
    }

    getItems() {
        if(this.props && this.props.itemsUrl) {
            const itemsUrl = this.props.itemsUrl;
            const {pagination} = this.state;
            const currentPage = GetPage.getSubPage('page');
            const limit = pagination.perPage;
            const offset = parseInt(currentPage) > 0 ? (currentPage - 1) * pagination.perPage : 0;

            // Get items
            axios.get(itemsUrl + `?limit=${limit}&offset=${offset}`, null)
                .then(result => {
                    if(result.status === 200 && result.data && result.data.items) {
                        pagination.countItems = result.data.countItems;

                        this.setState({items: result.data.items, pagination});
                    }
                })
                .catch((err) => {
                    console.log(err);
                });

            this.setState({itemsUrl})
        }
    }

    setCurrentPage(page) {
        const {pagination} = this.state;
        if(pagination.currentPage !== page) {
            pagination.currentPage = page;
            this.setState({pagination});

            this.getItems();
        }
    }

    setPerPage(perPage) {
        const {pagination} = this.state;
        if(pagination.perPage !== perPage) {
            pagination.perPage = perPage;
            this.setState({pagination});
        }
    }

    render() {
        const {items, pagination} = this.state;
        const itemsPerPage = pagination.perPage;
        const paginationCountItems = Math.ceil(pagination.countItems / itemsPerPage);

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Products list</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <Link to={'/shop'}>Shop</Link>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <div className="container">
                    <div className="row">
                        <ShopSidebar/>

                        <div className="col-xl-9 col-lg-8 col-md-7">
                            <ShopSortingPagination
                                paginationCountItems={paginationCountItems}
                                paginationSubPagePrefix='/shop'
                                paginationSetCurrentPage={this.setCurrentPage}
                                sortingSetPerPage={this.setPerPage}
                            />

                            <section className="lattest-product-area pb-40 category-list">
                                <div className="row">
                                    {items.map(item => (
                                        <SingleProductList key={item.uuid} item={item}/>
                                    ))}
                                </div>
                            </section>

                            {/*// @TODO Add a second sort with pagination*/}
                            {/*<ShopSortingPagination />*/}
                        </div>
                    </div>
                </div>
            </BaseTemplate>
        )
    }
}

ShopBaseTemplate.propTypes = {
    itemsUrl: PropTypes.string.isRequired
}

export default ShopBaseTemplate;
