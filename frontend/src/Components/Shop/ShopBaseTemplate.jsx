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
import Pagination from '../Pagination';
import UrlParams from '../UrlParams';
import CONFIG from '../../config';
import Loader from '../Loader';
import '../../../public/assets/js/jquery.nice-select.min';

class ShopBaseTemplate extends Component {
    constructor(props) {
        super(props);
        this.state = {
            itemsUrl: '',
            items: [],
            pageOfItems: props.pageOfItems ?? [],
            pagination: {
                perPage: 1,
                currentPage: GetPage.getSubPage(),
                countItems: 0
            },
            sorting: {
                sortBy: null,
                sortOrder: null
            },
            loader: true,
            noticeMessage: '',
            errorMessage: ''
        };

        this.setCurrentPage = this.setCurrentPage.bind(this);
        this.setPerPage = this.setPerPage.bind(this);
        this.getItems = this.getItems.bind(this);
        this.sortItems = this.sortItems.bind(this);
    }

    componentDidMount() {
        $('select').niceSelect();

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
        const _this = this;
        if(this.props && this.props.itemsUrl) {
            const itemsUrl = this.props.itemsUrl;
            const {pagination} = this.state;
            const currentPage = GetPage.getSubPage('page');
            const limit = pagination.perPage;
            const offset = currentPage > 0 ? (currentPage - 1) * pagination.perPage : 0;

            let urlGetValues = `?limit=${limit}&offset=${offset}`;

            if(this.state.sorting.sortBy && this.state.sorting.sortOrder) {
                const sortBy = this.state.sorting.sortBy;
                const sortOrder = this.state.sorting.sortOrder;

                urlGetValues += `&sortBy=${sortBy}&sortOrder=${sortOrder}`;
            }

            const itemsUrlWithGetValues = itemsUrl + urlGetValues;
            // Get items
            axios.get(itemsUrlWithGetValues, null)
                .then(result => {
                    if(result.status === 200) {
                        if(result.data) {
                            if(result.data.items && result.data.items.length > 0) {
                                pagination.countItems = result.data.countItems;

                                this.setState({items: result.data.items, pagination});

                                setTimeout(() => {
                                    _this.setState({loader: false});
                                }, 500);
                            } else if(result.data.errorMessage) {
                                setTimeout(() => {
                                    const errorMessage = result.data.errorMessage;
                                    _this.setState({errorMessage, loader: false});
                                }, 1500);
                            }
                        }
                    } else {
                        setTimeout(() => {
                            const errorMessage = 'Error while load products. Try again.';
                            _this.setState({errorMessage, loader: false});
                        }, 1500);
                    }
                })
                .catch((err) => {
                    console.log(err);
                    setTimeout(() => {
                        const errorMessage = 'Error while load products. Try again.';
                        _this.setState({errorMessage, loader: false});
                    }, 1500);
                });

            if(this.state.itemsUrl !== itemsUrl) {
                this.setState({itemsUrl})
            }
        }
    }

    setCurrentPage(page) {
        const {pagination} = this.state;
        if(pagination.currentPage !== page) {
            pagination.currentPage = page;

            this.getItems();
        }
    }

    setPerPage(perPage, overwriteAddressUrl = false) {
        const {pagination, sorting} = this.state;
        if(pagination.perPage !== perPage) {
            pagination.perPage = perPage;
            this.setState({pagination, loader: true});

            if(overwriteAddressUrl) {
                const currentUrl = window.location.href;
                const shopPrefixPage = CONFIG.shop.prefixPage;
                const newUrl = UrlParams.updateURLParameter(currentUrl, shopPrefixPage, 1)
                    .replace(shopPrefixPage + '/1', '');

                Pagination.updateAddressUrl({}, null, newUrl)
            }

            if(sorting.sortBy !== null && sorting.sortOrder !== null) {
                this.getItems();
            }
        }
    }

    sortItems(optionType) {
        const {pagination, sorting} = this.state;
        const {sortBy, sortOrder} = sorting;

        this.setState({loader: true});

        const newSorting = {
            sortBy: optionType.value,
            sortOrder: optionType.order
        };

        if(sortBy !== optionType.value || sortOrder !== optionType.order) {
            pagination.perPage = 1;
            this.setState({pagination, sorting: newSorting}, this.getItems);
        }
    }

    render() {
        const {items, pagination, loader, noticeMessage, errorMessage} = this.state;
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
                                sortingSortItems={this.sortItems}
                            />

                            <Loader isLoading={loader} />

                            {!loader && noticeMessage && (
                                <div className="message notice">{noticeMessage}</div>
                            )}

                            {!loader && errorMessage && (
                                <div className="message error">{errorMessage}</div>
                            )}

                            {!loader && !noticeMessage && (
                                <section className="lattest-product-area pb-40 category-list">
                                    <div className="row">
                                        {items.map((item) => (
                                            <SingleProductList key={item.uuid} item={item} />
                                        ))}
                                    </div>
                                </section>
                            )}

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
