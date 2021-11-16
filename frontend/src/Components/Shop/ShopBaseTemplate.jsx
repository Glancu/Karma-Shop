import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import ShopSidebar from './ShopSidebar';
import SingleProductList from './SingleProductList';
import BaseTemplate from '../BaseTemplate';
import ShopSortingPagination from './ShopSortingPagination';
import axios from 'axios';
import PropTypes from 'prop-types';
import GetPage from '../GetPage';
import Loader from '../Loader';
import { windowScrollTo } from '../WindowScroll';
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
                sortBy: 'newset',
                sortOrder: 'DESC'
            },
            loader: true,
            noticeMessage: '',
            errorMessage: ''
        };

        this.setCurrentPage = this.setCurrentPage.bind(this);
        this.updateSorting = this.updateSorting.bind(this);
        this.getItems = this.getItems.bind(this);
        this.updateSidebarFilters = this.updateSidebarFilters.bind(this);
    }

    componentDidMount() {
        const _this = this;
        $('select').niceSelect();

        if(!window.location.search) {
            this.getItems();
        }

        window.addEventListener('popstate', function () {
            _this.scrollToTop(1000);

            _this.setCurrentPage(GetPage.getSubPage('page'));
        });
    }

    scrollToTop(timeout = 0) {
        const contentEl = document.querySelector('.col-xl-9.col-lg-8.col-md-7');
        const headerAreaEl = document.querySelector('.header_area');

        setTimeout(() => {
            windowScrollTo(contentEl.offsetTop - headerAreaEl.offsetHeight);
        }, timeout)
    }

    getItems() {
        const _this = this;
        if(this.props && this.props.itemsUrl) {
            const itemsUrl = this.props.itemsUrl;
            const {pagination} = this.state;
            const currentPage = pagination.currentPage;
            const limit = pagination.perPage;
            const offset = currentPage > 0 ? (currentPage - 1) * pagination.perPage : 0;

            let itemsUrlWithGetValues = itemsUrl;

            const windowLocationSearch = window.location.search;

            itemsUrlWithGetValues += itemsUrlWithGetValues.includes('?') ?
                windowLocationSearch.replace('?', '&') :
                windowLocationSearch;

            itemsUrlWithGetValues = itemsUrlWithGetValues.replaceAll(`&per_page=${limit}`, '');

            let startParameterCharacter = windowLocationSearch || itemsUrlWithGetValues.includes('?') ? '&' : '?';

            let urlGetValues = `${startParameterCharacter}limit=${limit}&offset=${offset}`;

            const sortBy = this.state.sorting.sortBy;
            const sortOrder = this.state.sorting.sortOrder;
            if(sortBy && sortOrder) {
                const sortOrderLowerCase = sortOrder.toLowerCase();

                urlGetValues += `&sortBy=${sortBy}&sortOrder=${sortOrder}`;

                itemsUrlWithGetValues = itemsUrlWithGetValues
                    .replaceAll(`&sorting=${sortBy}_${sortOrderLowerCase}`, '');
            }

            itemsUrlWithGetValues += urlGetValues;

            this.setState({items: [], noticeMessage: null, errorMessage: null});

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
                            } else {
                                setTimeout(() => {
                                    const errorMessage = 'Error while load products. Try again.';
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
                .catch(() => {
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

    updateSorting(perPageValue = null, sortingValue = null) {
        const {pagination, sorting} = this.state;
        let getNewItems = false;
        let newPaginationValue = pagination;
        let newSortingValue = sorting;

        if(perPageValue && pagination.perPage !== perPageValue) {
            pagination.perPage = perPageValue;
            pagination.currentPage = GetPage.getSubPage('page') || 1;

            getNewItems = true;
            newPaginationValue = pagination;
        }

        if(sortingValue) {
            const {sortBy, sortOrder} = this.state.sorting;

            const newSorting = {
                sortBy: sortingValue.value,
                sortOrder: sortingValue.order
            };

            if(sortBy !== sortingValue.value || sortOrder !== sortingValue.order) {
                getNewItems = true;
                newSortingValue = newSorting;
            }
        }

        if(getNewItems) {
            this.setState({pagination: newPaginationValue, sorting: newSortingValue, loader: true}, this.getItems);
        }
    }

    updateSidebarFilters(setFirstPagePagination) {
        const {pagination} = this.state;
        if(setFirstPagePagination) {
            pagination.currentPage = 1;
        }

        this.setState({loader: true, pagination}, () => {
            this.scrollToTop();
            this.getItems();
        });
    }

    render() {
        const {items, pagination, loader, noticeMessage, errorMessage} = this.state;
        const paginationCountPages = Math.ceil(pagination.countItems / pagination.perPage);

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

                <div className="container margin-bottom-40">
                    <div className="row">
                        <ShopSidebar updateFilters={this.updateSidebarFilters} categorySlug={this.props.categorySlug} />

                        <div className="col-xl-9 col-lg-8 col-md-7">
                            <ShopSortingPagination
                                paginationCountPages={paginationCountPages}
                                paginationSubPagePrefix='/shop'
                                paginationSetCurrentPage={this.setCurrentPage}
                                sortingUpdateSorting={this.updateSorting}
                                firstComponent={true}
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

                            <ShopSortingPagination
                                paginationCountPages={paginationCountPages}
                                paginationSubPagePrefix='/shop'
                                paginationSetCurrentPage={this.setCurrentPage}
                                sortingUpdateSorting={this.updateSorting}
                                firstComponent={false}
                            />
                        </div>
                    </div>
                </div>
            </BaseTemplate>
        )
    }
}

ShopBaseTemplate.propTypes = {
    itemsUrl: PropTypes.string.isRequired,
    categorySlug: PropTypes.string
}

export default ShopBaseTemplate;
