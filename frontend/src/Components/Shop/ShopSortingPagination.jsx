import React, { Component } from 'react';
import Pagination from '../Pagination';
import CONFIG from '../../config';

const localStorageKey = CONFIG.shop.localStorageKey;
const sortPerPage = CONFIG.shop.sortPerPage;

class ShopSortingPagination extends Component {
    constructor(props) {
        super(props);
        this.state = {
            sorting: localStorage.getItem(localStorageKey) ? JSON.parse(localStorage.getItem(localStorageKey)).sorting : 1,
            perPage: localStorage.getItem(localStorageKey) ? JSON.parse(localStorage.getItem(localStorageKey)).perPage : sortPerPage[1]
        }

        this.onSubmitSort = this.onSubmitSort.bind(this);
    }

    componentDidMount() {
        const {sorting, perPage} = this.state;

        this.props.sortingSetPerPage(sortPerPage[perPage]);

        const itemsSortSelects = document.querySelectorAll('.sorting.items-sort select');
        const itemPerPageSelects = document.querySelectorAll('.sorting.items-per-page select');

        Array.from(itemsSortSelects).map((itemSortSelect) => {
            const itemSortSelectEl = itemSortSelect.querySelector(`option[value='${sorting}']`);
            itemSortSelectEl ?
                itemSortSelectEl.setAttribute('selected', 'selected') :
                itemSortSelect.querySelectorAll('option')[0].setAttribute('selected', 'selected');
        });

        Array.from(itemPerPageSelects).map((itemPerPageSelect) => {
            const itemsPerPageEl = itemPerPageSelect.querySelector(`option[value='${perPage}']`);
            itemsPerPageEl ?
                itemsPerPageEl.setAttribute('selected', 'selected') :
                itemPerPageSelect.querySelectorAll('option')[0].setAttribute('selected', 'selected');
        });
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const {sorting, itemsPerPage} = this.state;

        const itemsSortSelects = document.querySelectorAll('.sorting.items-sort select');
        const itemPerPageSelects = document.querySelectorAll('.sorting.items-per-page select');

        this.updateSelectsSelected(itemsSortSelects, sorting, 'items-sort')
        this.updateSelectsSelected(itemPerPageSelects, itemsPerPage, 'items-per-page')
    }

    updateSelectsSelected(selects, value, querySelectorClass) {
        Array.from(selects).map((select) => {
            const element = select.querySelector(`option[value='${value}']`);
            if(element) {
                select.querySelector('option[selected="selected"]').removeAttribute('selected');

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

    onSubmitSort(e) {
        e.preventDefault();

        const {sorting, itemsPerPage} = this.state;

        const currentValueItemsSort = parseInt(document.querySelector('.sorting.items-sort > div > ul > li.selected').getAttribute('data-value'));
        const currentValueItemsPerPage = parseInt(document.querySelector('.sorting.items-per-page > div > ul > li.selected').getAttribute('data-value'));

        if(sorting !== currentValueItemsSort) {
            this.updateLocalStorageInfo(localStorageKey, 'sorting', currentValueItemsSort);
        }

        if(itemsPerPage !== currentValueItemsPerPage) {
            this.updateLocalStorageInfo(localStorageKey, 'perPage', currentValueItemsPerPage);
            this.props.sortingSetPerPage(sortPerPage[currentValueItemsPerPage], true);
        }
    }

    updateLocalStorageInfo(type, key, value) {
        let currentLocalStorageValues = localStorage.getItem(type);
        currentLocalStorageValues = currentLocalStorageValues ? JSON.parse(currentLocalStorageValues) : {};

        currentLocalStorageValues[key] = value;

        localStorage.setItem(type, JSON.stringify(currentLocalStorageValues));
    }

    render() {
        const {perPage} = this.state;
        const perPageOptions = [];

        for(const i in sortPerPage) {
            perPageOptions.push(
                <option key={i} value={i}>Show {sortPerPage[i]}</option>
            )
        }

        return (
            <div className="filter-bar d-flex flex-wrap align-items-center">
                <div className="sorting items-sort">
                    <select>
                        <option value="1">Newest</option>
                        <option value="2">Price: Low to High</option>
                        <option value="3">Price: High to Low</option>
                    </select>
                </div>
                <div className="sorting items-per-page">
                    <select>
                        {perPageOptions}
                    </select>
                </div>
                <div className="sorting mr-auto">
                    <button onClick={this.onSubmitSort} className="genric-btn primary">Apply</button>
                </div>
                <div className="pagination">
                    <Pagination
                        itemsPerPage={perPage}
                        countItems={this.props.paginationCountItems}
                        subPagePrefix={this.props.paginationSubPagePrefix}
                        setCurrentPage={this.props.paginationSetCurrentPage}
                    />
                </div>
            </div>
        )
    }
}

export default ShopSortingPagination;
