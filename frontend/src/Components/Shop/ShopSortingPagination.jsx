import React, { Component } from 'react';
import Pagination from '../Pagination';
import CONFIG from '../../config';
import PropTypes from 'prop-types';
import UrlAddressBar from '../UrlAddressBar';
import { windowScrollTo } from '../WindowScroll';
import '../../../public/assets/js/jquery.nice-select.min';

const sortItems = CONFIG.shop.sortItems;
const sortPerPage = CONFIG.shop.sortPerPage;

class ShopSortingPagination extends Component {
    constructor(props) {
        super(props);
        this.state = {
            sorting: 1,
            perPage: 1
        }

        this.onSubmitSort = this.onSubmitSort.bind(this);
        this.updateDefaultValues = this.updateDefaultValues.bind(this);
        this.setSortingSelectValue = this.setSortingSelectValue.bind(this);
        this.setPerPageSelectValue = this.setPerPageSelectValue.bind(this);
    }

    componentDidMount() {
        const _this = this;
        this.updateDefaultValues(true);

        $('select').niceSelect();

        window.addEventListener('popstate', () => {
            const sortingValue = _this.setSortingSelectValue(true);
            if(sortingValue) {
                _this.setState({sorting: sortingValue});
            }

            const perPageValue = _this.setPerPageSelectValue(true);
            if(perPageValue) {
                _this.setState({perPage: perPageValue});
            }
        });
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        this.props.paginationCountPages > 5 && $('select').niceSelect();

        const {sorting, perPage} = this.state;

        if(sorting !== prevState.sorting) {
            const itemsSortSelects = document.querySelectorAll('.sorting.items-sort select');
            this.updateSelectsSelected(itemsSortSelects, sorting, 'items-sort')
        }

        if(perPage !== prevState.perPage) {
            const itemPerPageSelects = document.querySelectorAll('.sorting.items-per-page select');
            this.updateSelectsSelected(itemPerPageSelects, perPage, 'items-per-page')
        }
    }

    setSortingSelectValue(returnValue = false, sendUpdateSortingFunction = true) {
        const sortItemsConfig = CONFIG.shop.sortItems;
        const {sorting} = this.state;
        let sortingValue = sorting;

        const sortingFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('sorting');
        if(sortingFromURL) {
            const splittedSortingFromURL = sortingFromURL.split('_');
            const sortingValueFromURL = splittedSortingFromURL[0]
            const sortingOrderFromURL = splittedSortingFromURL[1].toUpperCase();

            const valuesSortingConfig = Object
                .values(sortItemsConfig)
                .filter(i => i.value === sortingValueFromURL && i.order === sortingOrderFromURL);
            const valueSortingConfig = valuesSortingConfig.length > 0 ? valuesSortingConfig[0] : null;
            if(valueSortingConfig) {
                Object.keys(sortItemsConfig).map((i) => {
                    if(sortItemsConfig[i] && (sortItemsConfig[i] === valueSortingConfig)) {
                        sortingValue = i;
                    }
                });
            }
        } else {
            sortingValue = 1;
        }

        const itemsSortSelects = document.querySelectorAll('.sorting.items-sort select');
        this.updateSelectsSelected(itemsSortSelects, sortingValue, 'items-sort')

        if(sendUpdateSortingFunction) {
            this.props.sortingUpdateSorting(null, sortItems[sortingValue]);
        }

        if(returnValue) {
            return parseInt(sortingValue);
        }
    }

    setPerPageSelectValue(returnValue = false, sendUpdateSortingFunction = true) {
        const sortPerPageConfig = CONFIG.shop.sortPerPage;
        const {perPage} = this.state;
        let perPageValue = perPage;

        const perPageFromURL = UrlAddressBar.getGetValueOfKeyFromAddressURL('per_page');
        if(perPageFromURL) {
            const perPageFromURLValue = Object.keys(sortPerPageConfig).find(i => sortPerPageConfig[i] === parseInt(perPageFromURL));
            if(perPageFromURLValue) {
                perPageValue = perPageFromURLValue;
            }
        } else {
            perPageValue = 1;
        }

        const itemPerPageSelects = document.querySelectorAll('.sorting.items-per-page select');
        this.updateSelectsSelected(itemPerPageSelects, perPageValue, 'items-per-page')

        if(sendUpdateSortingFunction) {
            this.props.sortingUpdateSorting(sortPerPage[perPageValue]);
        }

        if(returnValue) {
            return parseInt(perPageValue);
        }
    }

    updateDefaultValues(updateStateValue = false) {
        const sortingValue = this.setSortingSelectValue(true, false);
        const perPageValue = this.setPerPageSelectValue(true, false);

        if(this.props.firstComponent) {
            this.props.sortingUpdateSorting(sortPerPage[perPageValue], sortItems[sortingValue]);
        }

        if(updateStateValue) {
            this.setState({sorting: sortingValue, perPage: perPageValue})
        }
    }

    updateSelectsSelected(selects, value, querySelectorClass) {
        if(selects) {
            Array.from(selects).map((select) => {
                const element = select.querySelector(`option[value='${value}']`);
                if(element) {
                    const currentSelectedOption = select.querySelector('option[selected="selected"]');
                    if(currentSelectedOption) {
                        currentSelectedOption.removeAttribute('selected');
                    }

                    const items = document.querySelectorAll(`.sorting.${querySelectorClass} div.nice-select`);
                    Array.from(items).map((item) => {
                        item.querySelector('span').textContent = element.textContent;

                        item.querySelector('ul li.selected').classList.remove('selected');
                        item.querySelector(`ul li[data-value='${value}']`).classList.add('selected');
                    });

                    element.setAttribute('selected', 'selected');
                } else {
                    select.querySelectorAll('option')[0].setAttribute('selected', 'selected');
                }
            });
        }
    }

    onSubmitSort(e) {
        e.preventDefault();

        const {sorting, perPage} = this.state;

        const filterWrap = e.target.parentElement.parentElement;
        const contentEl = document.querySelector('.col-xl-9.col-lg-8.col-md-7');
        const headerAreaEl = document.querySelector('.header_area');

        const currentValueItemsSort = parseInt(filterWrap.querySelector('.sorting.items-sort > div > ul > li.selected').getAttribute('data-value'));
        const currentValueItemsPerPage = filterWrap.querySelector('.sorting.items-per-page') && parseInt(filterWrap.querySelector('.sorting.items-per-page > div > ul > li.selected').getAttribute('data-value'));

        const currentUrl = window.location.href;
        let newUrl = window.location.href;

        if(sorting !== currentValueItemsSort) {
            const newValue = sortItems[currentValueItemsSort].value;
            const newOrder = sortItems[currentValueItemsSort].order.toLocaleLowerCase();

            if(newValue !== 'newset' && newOrder !== 'DESC') {
                newUrl = UrlAddressBar.addParameterToStringURL(newUrl, 'sorting', newValue + '_' + newOrder);
            } else {
                newUrl = UrlAddressBar.removeParameterToStringURL(newUrl, 'sorting');
            }

            this.setState({sorting: currentValueItemsSort});

            windowScrollTo(contentEl.offsetTop - headerAreaEl.offsetHeight);
        }

        if(currentValueItemsPerPage && (perPage !== currentValueItemsPerPage)) {
            const perPageValue = parseInt(sortPerPage[currentValueItemsPerPage]);
            if(perPageValue > 1) {
                newUrl = UrlAddressBar.addParameterToStringURL(newUrl, 'per_page', perPageValue);
            } else {
                newUrl = UrlAddressBar.removeParameterToStringURL(newUrl, 'per_page');
            }

            this.setState({perPage: currentValueItemsPerPage});

            windowScrollTo(contentEl.offsetTop - headerAreaEl.offsetHeight);
        }

        if(currentUrl !== newUrl) {
            const shopPrefixPage = CONFIG.shop.prefixPage;

            newUrl = UrlAddressBar.updateOrRemovePageFromStringURL(newUrl, '/' + shopPrefixPage + '/', 1)
                .replace(shopPrefixPage + '/1', '');

            UrlAddressBar.pushAddressUrl({},null, newUrl);

            this.updateDefaultValues();
        }
    }

    render() {
        const {perPage} = this.state;
        const sortOptions = [];
        const perPageOptions = [];
        const paginationCountPages = this.props.paginationCountPages;

        for(const i in sortItems) {
            sortOptions.push(
                <option key={i} value={i}>{sortItems[i].title}</option>
            )
        }

        for(const i in sortPerPage) {
            perPageOptions.push(
                <option key={i} value={i}>Show {sortPerPage[i]}</option>
            )
        }

        return (
            <div className="filter-bar d-flex flex-wrap align-items-center">
                <div className="sorting items-sort">
                    <select>
                        {sortOptions}
                    </select>
                </div>

                {paginationCountPages > 5 &&
                    <div className="sorting items-per-page">
                        <select>
                            {perPageOptions}
                        </select>
                    </div>
                }

                <div className="sorting mr-auto">
                    <button onClick={this.onSubmitSort} className="genric-btn primary">Apply</button>
                </div>
                <div className="pagination">
                    <Pagination
                        itemsPerPage={perPage}
                        countPages={paginationCountPages}
                        subPagePrefix={this.props.paginationSubPagePrefix}
                        setCurrentPage={this.props.paginationSetCurrentPage}
                    />
                </div>
            </div>
        )
    }
}

ShopSortingPagination.propTypes = {
    paginationCountPages: PropTypes.number.isRequired,
    paginationSubPagePrefix: PropTypes.string.isRequired,
    paginationSetCurrentPage: PropTypes.func.isRequired,
    sortingUpdateSorting: PropTypes.func.isRequired,
    firstComponent: PropTypes.bool.isRequired
}

export default ShopSortingPagination;
