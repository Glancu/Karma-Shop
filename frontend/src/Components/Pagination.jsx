import React, { Component } from "react";
import PropTypes from 'prop-types';
import UrlAddressBar from "./UrlAddressBar";
import GetPage from './GetPage';
import CONFIG from '../config';

const PREFIX_PAGE = CONFIG.shop.prefixPage;
const LEFT_PAGE = 'LEFT';
const RIGHT_PAGE = 'RIGHT';

const range = (from, to, step = 1) => {
    const rangeCount = [];
    let i = from;

    while (i <= to) {
        rangeCount.push(i);
        i += step;
    }

    return rangeCount;
}

class Pagination extends Component {
    constructor(props) {
        super(props);
        this.state = {
            subPagePrefix: props.subPagePrefix,
            currentPage: GetPage.getSubPage() || 1,
            pageNeighbours: 1,
            pager: {}
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(prevProps.countPages !== this.props.countPages) {
            this.updatePage();
        }

        if(GetPage.getSubPage() !== this.state.currentPage) {
            this.setPage(GetPage.getSubPage(PREFIX_PAGE));
        }
    }

    updatePage() {
        const countPages = this.props.countPages;
        const currentPage = GetPage.getSubPage(PREFIX_PAGE);
        const page = (countPages < currentPage) || (currentPage < 1) ? 1 : currentPage;
        const subPagePrefix = this.props.subPagePrefix;

        if(subPagePrefix !== this.state.subPagePrefix) {
            this.setState({subPagePrefix});
        }

        this.setPage(page);
    }

    setPage(page, setFromPagination = false) {
        const countPages = this.props.countPages;

        if(countPages > 0 && (page < 0 || page > countPages)) {
            return;
        }

        const pager = countPages ? this.getPager(countPages, page) : null;

        if(pager && page) {
            const currentUrl = window.location.href;
            let newUrl = UrlAddressBar.updateOrRemovePageFromStringURL(currentUrl, '/' + PREFIX_PAGE + '/', page);

            if(newUrl && newUrl !== currentUrl && (setFromPagination || !history.state || (history.state &&
                (!history.state.page || history.state.page < page)))
            ) {
                UrlAddressBar.pushAddressUrl({page}, null, newUrl)
            }

            this.setState({pager: pager, currentPage: page});
            if(newUrl !== currentUrl) {
                this.props.setCurrentPage(page);
            }
        }
    }

    getPager(totalItems, currentPage, pageSize) {
        const itemsPerPage = this.props.itemsPerPage;
        currentPage = currentPage || 1;

        pageSize = pageSize || itemsPerPage;

        const totalPages = Math.ceil(totalItems / pageSize);

        let startPage, endPage;
        if(totalPages <= 10) {
            startPage = 1;
            endPage = totalPages;
        } else {
            if(currentPage <= 6) {
                startPage = 1;
                endPage = itemsPerPage;
            } else if(currentPage + 4 >= totalPages) {
                startPage = totalPages - 9;
                endPage = totalPages;
            } else {
                startPage = currentPage - 5;
                endPage = currentPage + 4;
            }
        }

        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize - 1, totalItems - 1);

        const pages = [...Array((endPage + 1) - startPage).keys()].map(i => startPage + i);

        return {
            totalItems: totalItems,
            currentPage: currentPage,
            pageSize: pageSize,
            totalPages: totalPages,
            startPage: startPage,
            endPage: endPage,
            startIndex: startIndex,
            endIndex: endIndex,
            pages: pages
        };
    }

    /**
     * Let's say we have 10 pages and we set pageNeighbours to 2
     * Given that the current page is 6
     * The pagination control will look like the following:
     *
     * (1) < {4 5} [6] {7 8} > (10)
     *
     * (x) => terminal pages: first and last page(always visible)
     * [x] => represents current page
     * {...x} => represents page neighbours
     */
    fetchPageNumbers() {
        const countPages = this.props.countPages;
        const currentPage = parseInt(this.state.currentPage);
        const pageNeighbours = parseInt(this.state.pageNeighbours);

        /**
         * totalNumbers: the total page numbers to show on the control
         * totalBlocks: totalNumbers + 2 to cover for the left(<) and right(>) controls
         */
        const totalNumbers = (pageNeighbours * 2) + 1;
        const runFullPagination = countPages > 5;
        const startPage = Math.max(2, currentPage - pageNeighbours);
        const endPage = runFullPagination ? Math.min(countPages - 1, currentPage + pageNeighbours) : countPages - 1;
        let pages = range(startPage, endPage);

        if(runFullPagination) {
            /**
             * hasLeftSpill: has hidden pages to the left
             * hasRightSpill: has hidden pages to the right
             * spillOffset: number of hidden pages either to the left or to the right
             */
            const hasLeftSpill = startPage > 2;
            const hasRightSpill = (countPages - endPage) > 1;
            const spillOffset = totalNumbers - (pages.length + 1);

            switch (true) {
                // handle: (1) < {5 6} [7] {8 9} (10)
                case (hasLeftSpill && !hasRightSpill): {
                    const extraPages = range(startPage - spillOffset, startPage - 1);
                    pages = [LEFT_PAGE, ...extraPages, ...pages];
                    break;
                }

                // handle: (1) {2 3} [4] {5 6} > (10)
                case (!hasLeftSpill && hasRightSpill): {
                    const extraPages = range(endPage + 1, endPage + spillOffset);
                    pages = [...pages, ...extraPages, RIGHT_PAGE];
                    break;
                }

                // handle: (1) < {4 5} [6] {7 8} > (10)
                case (hasLeftSpill && hasRightSpill):
                default: {
                    pages = [LEFT_PAGE, ...pages, RIGHT_PAGE];
                    break;
                }
            }
        }

        if(pages.length === 0 || countPages < 2) {
            return [];
        }

        return [1, ...pages, countPages];
    }

    changePaginationClick(page, evt) {
        evt.preventDefault();
        this.setPage(page, true);
    }

    render() {
        const {currentPage} = this.state;
        const pages = this.fetchPageNumbers();
        const classes = 'link-prevent-default';
        const activeClasses = `${classes} active`;

        return (
            <>
                { pages.map((page, index) => {
                    if (page === LEFT_PAGE) return (
                        <a key={index}
                           className={classes}
                           href={UrlAddressBar.setPageAfterPrefix(PREFIX_PAGE, parseInt(currentPage) - 1)}
                           aria-label="Previous"
                           onClick={(e) => this.changePaginationClick(parseInt(currentPage) - 1, e)}
                        >
                            <span aria-hidden="true">&laquo;</span>
                            <span className="sr-only">Previous</span>
                        </a>
                    );

                    if (page === RIGHT_PAGE) return (
                        <a key={index}
                           className={classes}
                           href={UrlAddressBar.setPageAfterPrefix(PREFIX_PAGE, parseInt(currentPage) + 1)}
                           aria-label="Next"
                           onClick={(e) => this.changePaginationClick(parseInt(currentPage) + 1, e)}>
                            <span aria-hidden="true">&raquo;</span>
                            <span className="sr-only">Next</span>
                        </a>
                    );

                    return (
                        <a key={index}
                           className={parseInt(currentPage) === parseInt(page) ? activeClasses : classes}
                           href={UrlAddressBar.setPageAfterPrefix(PREFIX_PAGE, page)}
                           onClick={(e) => this.changePaginationClick(page, e) }
                        >{ page }
                        </a>
                    );
                }) }
            </>
        );
    }
}

Pagination.propTypes = {
    countPages: PropTypes.number.isRequired,
    itemsPerPage: PropTypes.number.isRequired,
    subPagePrefix: PropTypes.string.isRequired,
    setCurrentPage: PropTypes.func.isRequired
};

export default Pagination;
