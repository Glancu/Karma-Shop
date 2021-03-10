import React, { Component } from 'react';
import Pagination from '../Pagination';

class ShopSortingPagination extends Component {
    constructor(props) {
        super(props);
        this.state = {
            sorting: 1,
            itemsPerPage: 1,
            currentPage: 1,
            pages: 1
        }

        this.onSubmitSort = this.onSubmitSort.bind(this);
    }

    componentDidMount() {
        const { sorting, itemsPerPage } = this.state;

        const itemsSortSelects = document.querySelectorAll('.sorting.items-sort select');
        const itemPerPageSelects = document.querySelectorAll('.sorting.items-per-page select');

        Array.from(itemsSortSelects).map((itemSortSelect) => {
            const itemSortSelectEl = itemSortSelect.querySelector(`option[value='${sorting}']`);
            itemSortSelectEl ?
                itemSortSelectEl.setAttribute('selected', 'selected') :
                itemSortSelect.querySelectorAll('option')[0].setAttribute('selected', 'selected');
        });

        Array.from(itemPerPageSelects).map((itemPerPageSelect) => {
            const itemsPerPageEl = itemPerPageSelect.querySelector(`option[value='${itemsPerPage}']`);
            itemsPerPageEl ?
                itemsPerPageEl.setAttribute('selected', 'selected') :
                itemPerPageSelect.querySelectorAll('option')[0].setAttribute('selected', 'selected');
        });
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        const { sorting, itemsPerPage } = this.state;

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

        const { sorting, itemsPerPage } = this.state;

        const currentValueItemsSort = parseInt(document.querySelector('.sorting.items-sort > div > ul > li.selected').getAttribute('data-value'));
        const currentValueItemsPerPage = parseInt(document.querySelector('.sorting.items-per-page > div > ul > li.selected').getAttribute('data-value'));

        if(sorting !== currentValueItemsSort) {
            this.setState({sorting: currentValueItemsSort});
        }

        if(itemsPerPage !== currentValueItemsPerPage) {
            this.setState({itemsPerPage: currentValueItemsPerPage});
        }
    }

    render() {
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
                        <option value="1">Show 12</option>
                        <option value="2">Show 21</option>
                        <option value="3">Show 48</option>
                    </select>
                </div>
                <div className="sorting mr-auto">
                    <button onClick={this.onSubmitSort} className="genric-btn primary">Apply</button>
                </div>
                <div className="pagination">
                    <Pagination
                        items={this.props.paginationItems}
                        onChangePage={this.props.paginationOnChangePage}
                        initialPage={this.props.paginationInitialPage()}
                        activeClasses="active"
                        itemPerPage={6}
                        paginationSubPagePrefix={this.props.paginationSubPagePrefix}/>
                </div>
            </div>
        )
    }
}

export default ShopSortingPagination;
