import React, { Component } from 'react';
import Pagination from '../Pagination';

const localStorageKey = 'shop_pagination';
const sortPerPage = {
    1: 12,
    2: 21,
    3: 48
}

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

        Array.from(itemsSortSelects).map((itemSortSelect) => {
            const itemSortSelectEl = itemSortSelect.querySelector(`option[value='${sorting}']`);
            if(itemSortSelectEl) {
                itemSortSelect.querySelector('option[selected="selected"]').removeAttribute('selected');

                const currentSelects = document.querySelectorAll('.sorting.items-sort div.nice-select');
                Array.from(currentSelects).map((currentSelect) => {
                    currentSelect.querySelector('span').textContent = itemSortSelectEl.textContent;

                    currentSelect.querySelector('ul li.selected').classList.remove('selected');
                    currentSelect.querySelector(`ul li[data-value='${sorting}']`).classList.add('selected');
                });

                itemSortSelectEl.setAttribute('selected', 'selected');
            } else {
                itemSortSelect.querySelectorAll('option')[0].setAttribute('selected', 'selected');
            }
        });

        Array.from(itemPerPageSelects).map((itemPerPageSelect) => {
            const itemsPerPageEl = itemPerPageSelect.querySelector(`option[value='${itemsPerPage}']`);
            if(itemsPerPageEl) {
                itemPerPageSelect.querySelector('option[selected="selected"]').removeAttribute('selected');

                const currentPerPageItems = document.querySelectorAll('.sorting.items-per-page div.nice-select');
                Array.from(currentPerPageItems).map((currentPerPageItem) => {
                    currentPerPageItem.querySelector('span').textContent = itemsPerPageEl.textContent;

                    currentPerPageItem.querySelector('ul li.selected').classList.remove('selected');
                    currentPerPageItem.querySelector(`ul li[data-value='${itemsPerPage}']`).classList.add('selected');
                });

                itemsPerPageEl.setAttribute('selected', 'selected');
            } else {
                itemPerPageSelect.querySelectorAll('option')[0].setAttribute('selected', 'selected');
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
            this.props.sortingSetPerPage(sortPerPage[currentValueItemsPerPage]);
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
