import React, { Component } from 'react';
import ShopCategoriesSidebar from './ShopCategoriesSidebar';
import axios from 'axios';
import CONFIG from '../../config';
import noUiSlider from '../../../public/assets/js/nouislider.min';
import PropTypes from 'prop-types';
import UrlAddressBar from '../UrlAddressBar';

class ShopSidebar extends Component {
    constructor(props) {
        super(props);
        this.state = {
            brands: [],
            colors: [],
            filters: {
                brand: CONFIG.shop.filters.brand,
                color: CONFIG.shop.filters.color,
                priceFrom: CONFIG.shop.filters.priceFrom,
                priceTo: CONFIG.shop.filters.priceTo
            }
        };

        this.onFilterApply = this.onFilterApply.bind(this);
        this.onFilterReset = this.onFilterReset.bind(this);
        this.updateFilters = this.updateFilters.bind(this);
    }

    componentDidMount() {
        const _this = this;
        const {filters} = this.state;

        axios.all([
            axios.get("/api/brands/list", {type: 'brands'}),
            axios.get("/api/colors/list", {type: 'colors'})
        ])
            .then(responseArr => {
                responseArr.map(response => {
                    const data = response.data;
                    const responseType = response.config.type;
                    let valueFromURL = null;
                    let responseTypeState = null;

                    if(responseType) {
                        switch(responseType) {
                            case 'brands':
                                responseTypeState = 'brand';
                                break;
                            case 'colors':
                                responseTypeState = 'color';
                                break;
                        }

                        if(responseTypeState) {
                            valueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL(responseTypeState);

                            let isExistInItems = false;
                            data.map(item => {
                                if(item.slug === valueFromURL) {
                                    isExistInItems = true;
                                }
                            });

                            filters[responseTypeState] = isExistInItems ? valueFromURL : CONFIG.shop.filters[responseTypeState];

                            this.setState({[responseType]: data, filters});
                        }
                    }
                });
            })
            .catch((err) => {
                console.error(err);
            });

        const nonLinearSlider = document.getElementById('price-range');
        if(nonLinearSlider) {
            const priceFromValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('priceFrom');
            filters.priceFrom = priceFromValueFromURL || filters.priceFrom;

            const priceToValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('priceTo');
            filters.priceFrom = priceToValueFromURL || filters.priceFrom;

            const configPriceFrom = parseInt(priceFromValueFromURL) || CONFIG.shop.filters.priceFrom;
            const configPriceTo = parseInt(priceToValueFromURL) || CONFIG.shop.filters.priceTo;

            if(priceFromValueFromURL || priceToValueFromURL) {
                this.setState({filters});
                this.props.updateFilters(false);
            }

            noUiSlider.create(nonLinearSlider, {
                connect: true,
                behaviour: 'tap',
                start: [configPriceFrom, configPriceTo],
                range: {
                    // Starting at 500, step the value by 500,
                    // until 4000 is reached. From there, step by 1000.
                    'min': [0],
                    '1%': [0, 10],
                    'max': [CONFIG.shop.filters.priceTo]
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

        window.addEventListener('popstate', function () {
            const priceFromValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('priceFrom');
            const priceToValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('priceTo');

            nonLinearSlider.noUiSlider.updateOptions({
                start: [
                    priceFromValueFromURL || CONFIG.shop.filters.priceFrom,
                    priceToValueFromURL || CONFIG.shop.filters.priceTo
                ]
            });

            _this.updateFilters();
        });

        if(UrlAddressBar.getGetValueOfKeyFromAddressURL('brand') ||
            UrlAddressBar.getGetValueOfKeyFromAddressURL('color')
        ) {
            this.sendUpdateFilters(false);
        }
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {filters} = this.state;

        if((filters.brand !== prevState.filters.brand || filters.color !== prevState.filters.color ||
            filters.priceFrom !== prevState.filters.priceFrom || filters.priceTo !== prevState.filters.priceTo)
        ) {
            this.sendUpdateFilters();
        }
    }

    sendUpdateFilters(setFirstPagePagination = true) {
        this.props.updateFilters(setFirstPagePagination);
    }

    updateFilters() {
        const {filters} = this.state;
        let updateFilters = false;

        const brandValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('brand');
        const brandEl = document.querySelector(`.brands-list input[type="radio"][id="${brandValueFromURL}"]`);
        if(brandEl) {
            brandEl.checked = true;

            filters.brand = brandValueFromURL;

            updateFilters = true;
        } else {
            const checkedBrandEl = document.querySelector('.brands-list input[type="radio"]:checked');
            if(checkedBrandEl) {
                checkedBrandEl.checked = false;

                filters.brand = CONFIG.shop.filters.brand;

                updateFilters = true;
            }
        }

        const colorValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('color');
        const colorEl = document.querySelector(`.colors-list input[type="radio"][id="${colorValueFromURL}"]`);
        if(colorEl) {
            colorEl.checked = true;

            filters.color = colorValueFromURL;

            updateFilters = true;
        } else {
            const checkedColorEl = document.querySelector('.colors-list input[type="radio"]:checked');
            if(checkedColorEl) {
                checkedColorEl.checked = false;

                filters.color = CONFIG.shop.filters.color;

                updateFilters = true;
            }
        }

        const priceFromValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('priceFrom');
        if(priceFromValueFromURL) {
            filters.priceFrom = priceFromValueFromURL;

            updateFilters = true;
        }

        const priceToValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('priceTo');
        if(priceToValueFromURL) {
            filters.priceFrom = priceToValueFromURL;

            updateFilters = true;
        }

        const nonLinearSlider = document.getElementById('price-range');
        nonLinearSlider.noUiSlider.updateOptions({
            start: [
                priceFromValueFromURL || CONFIG.shop.filters.priceFrom,
                priceToValueFromURL || CONFIG.shop.filters.priceTo
            ]
        });

        if(updateFilters) {
            this.setState({filters}, () => this.sendUpdateFilters())
        }
    }

    onFilterApply() {
        const {filters} = this.state;
        let changeState = false;
        let newUrl = window.location.href;

        const brandEl = document.querySelector('.brands-list input[type="radio"]:checked');
        const brandValue = brandEl ? brandEl.getAttribute('id'): null;
        if(brandValue && brandValue !== filters.brand) {
            filters.brand = brandValue;

            changeState = true;

            newUrl = UrlAddressBar.addParameterToStringURL(newUrl, 'brand', brandValue);
        }

        const colorEl = document.querySelector('.colors-list input[type="radio"]:checked');
        const colorValue = colorEl ? colorEl.getAttribute('id') : null;
        if(colorValue && colorValue !== filters.color) {
            filters.color = colorValue;

            changeState = true;

            newUrl = UrlAddressBar.addParameterToStringURL(newUrl, 'color', colorValue);
        }

        const priceEl = document.querySelector('.price-range-area');

        const priceFromVal = priceEl.querySelector('#lower-value').textContent;
        const priceToVal = priceEl.querySelector('#upper-value').textContent;

        if(!isNaN(parseFloat(priceFromVal)) && parseFloat(priceFromVal) !== filters.priceFrom) {
            filters.priceFrom = parseFloat(priceFromVal);

            changeState = true;

            newUrl = UrlAddressBar.addParameterToStringURL(newUrl, 'priceFrom', priceFromVal);
        }

        if(!isNaN(parseFloat(priceToVal)) && parseFloat(priceToVal) !== filters.priceTo) {
            filters.priceTo = parseFloat(priceToVal);

            changeState = true;

            newUrl = UrlAddressBar.addParameterToStringURL(newUrl, 'priceTo', priceToVal);
        }

        if(changeState) {
            // Update state only when minimum one of value was changed
            this.setState({filters}, () => this.sendUpdateFilters());

            const PREFIX_PAGE = CONFIG.shop.prefixPage;

            newUrl = UrlAddressBar.updateOrRemovePageFromStringURL(newUrl, '/' + PREFIX_PAGE + '/', 1);

            UrlAddressBar.pushAddressUrl({}, null, newUrl);
        }
    }

    onFilterReset() {
        const {filters} = this.state;
        const configShopFilters = CONFIG.shop.filters;
        const shopPrefixPage = CONFIG.shop.prefixPage;
        let newUrl = window.location.href;

        const isFiltersAreTheSameAsDefault = Object.keys(filters).every((key) =>  filters[key] === configShopFilters[key]);
        if(isFiltersAreTheSameAsDefault === true) {
            if(UrlAddressBar.getGetValueOfKeyFromAddressURL('priceFrom') ||
                UrlAddressBar.getGetValueOfKeyFromAddressURL('priceTo')
            ) {
                newUrl = UrlAddressBar.removeParameterToStringURL(newUrl, ['priceFrom', 'priceTo']);
                UrlAddressBar.replaceAddressUrl({},"", newUrl);
            }
            return;
        }

        newUrl = UrlAddressBar.removeParameterToStringURL(newUrl, ['color', 'brand', 'priceFrom', 'priceTo']);

        newUrl = UrlAddressBar.updateOrRemovePageFromStringURL(newUrl, '/' + shopPrefixPage + '/', 1)
            .replace(shopPrefixPage + '/1', '');

        UrlAddressBar.replaceAddressUrl({},"", newUrl);

        const brandEl = document.querySelector('.brands-list input[type="radio"]:checked');
        if(brandEl) {
            filters.brand = null;

            brandEl.checked = false;
        }

        const colorEl = document.querySelector('.colors-list input[type="radio"]:checked');
        if(colorEl) {
            filters.color = null;

            colorEl.checked = false;
        }

        const nonLinearSlider = document.getElementById('price-range');
        nonLinearSlider.noUiSlider.updateOptions({
            start: [configShopFilters.priceFrom, configShopFilters.priceTo]
        });

        const priceEl = document.querySelector('.price-range-area');

        const priceFromVal = priceEl.querySelector('#lower-value').textContent;
        const priceToVal = priceEl.querySelector('#upper-value').textContent;

        if(!isNaN(parseFloat(priceFromVal)) && parseFloat(priceFromVal) !== filters.priceFrom) {
            filters.priceFrom = parseFloat(priceFromVal);
        }

        if(!isNaN(parseFloat(priceToVal)) && parseFloat(priceToVal) !== filters.priceTo) {
            filters.priceTo = parseFloat(priceToVal);
        }

        this.setState({filters}, () => this.sendUpdateFilters());
    }

    render() {
        const {brands, colors} = this.state;

        const brandValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('brand');
        const colorValueFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('color');

        return (
            <div className="col-xl-3 col-lg-4 col-md-5">
                <ShopCategoriesSidebar categorySlug={this.props.categorySlug} />
                <div className="sidebar-filter mt-50">
                    <div className="top-filter-head">Product Filters</div>
                    <div className="common-filter">
                        <div className="head">Brands</div>
                            <ul className="brands-list">
                                {brands && brands.length > 0 &&
                                    brands.map((brand) => (
                                        <li className="filter-list" key={brand.slug}>
                                            <input className="pixel-radio" type="radio" id={brand.slug} name="brand" defaultChecked={brandValueFromURL === brand.slug}/>
                                            <label htmlFor={brand.slug}>{brand.title} <span>({brand.countProducts})</span></label>
                                        </li>
                                    ))
                                }
                            </ul>
                    </div>
                    <div className="common-filter">
                        <div className="head">Color</div>
                        <form action="#">
                            <ul className="colors-list">
                                {colors && colors.length > 0 &&
                                    colors.map((color) => (
                                        <li className="filter-list" key={color.slug}>
                                            <input className="pixel-radio" type="radio" id={color.slug} name="color" defaultChecked={colorValueFromURL === color.slug} />
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
                    <div className="sorting mr-auto">
                        <button onClick={this.onFilterApply} className="genric-btn primary d-inline-block">Apply</button>
                        <button onClick={this.onFilterReset} className="genric-btn primary ml-1 d-inline-block">Reset</button>
                    </div>
                </div>
            </div>
        )
    }
}

ShopSidebar.propTypes = {
    updateFilters: PropTypes.func.isRequired,
    categorySlug: PropTypes.string
}

export default ShopSidebar;
