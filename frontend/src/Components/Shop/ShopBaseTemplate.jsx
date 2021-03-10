import React, { Component } from 'react';
import { Link } from 'react-router-dom';
import ShopSidebar from './ShopSidebar';
import SingleProductList from './SingleProductList';
import BaseTemplate from '../BaseTemplate';
import noUiSlider from '../../../public/assets/js/nouislider.min';
import ShopSortingPagination from './ShopSortingPagination';
import '../../../public/assets/js/jquery.nice-select.min';

class ShopBaseTemplate extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: [],
            pageOfItems: props.pageOfItems ?? []
        };

        this.onChangePage = this.onChangePage.bind(this);
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
                    'min': [0],
                    '10%': [500, 500],
                    '50%': [4000, 1000],
                    'max': [10000]
                }
            }, true);

            const nodes = [
                document.getElementById('lower-value'), // 0
                document.getElementById('upper-value')  // 1
            ];

            // Display the slider value and how far the handle moved
            // from the left edge of the slider.
            nonLinearSlider.noUiSlider.on('update', function(values, handle, unencoded, isTap, positions) {
                nodes[handle].innerHTML = values[handle];
            });
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(prevProps.items !== this.state.items) {
            this.setState({items: prevProps.items})
        }
    }

    onChangePage(pageOfItems) {
        const _this = this;
        setTimeout(function() {
            _this.setState({pageOfItems: pageOfItems});
        }, 800)
    }

    getSubPage() {
        const splittedText = window.location.href.split('page/');
        return (splittedText && splittedText[1]) ? splittedText[1] : 1;
    }

    render() {
        const {pageOfItems, items} = this.state;

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
                        <ShopSidebar />

                        <div className="col-xl-9 col-lg-8 col-md-7">
                            <ShopSortingPagination
                                onPageChange={this.handlePageClick}
                                paginationItems={items}
                                paginationOnChangePage={this.onChangePage}
                                paginationInitialPage={this.getSubPage}
                                paginationSubPagePrefix='/shop'
                            />

                            <section className="lattest-product-area pb-40 category-list">
                                <div className="row">
                                    {pageOfItems.map(item => (
                                        <SingleProductList key={item.uuid} item={item} />
                                    ))}
                                </div>
                            </section>

                            {/*<ShopSortingPagination onPageChange={this.handlePageClick} />*/}
                        </div>
                    </div>
                </div>
            </BaseTemplate>
        )
    }
}

export default ShopBaseTemplate;
