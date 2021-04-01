import React, { Component } from "react";
import PropTypes from 'prop-types';
import UrlParams from "./UrlParams";
import GetPage from './GetPage';

const PREFIX_PAGE = 'page';
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
            paginationSubPagePrefix: props.paginationSubPagePrefix,
            pager: {},
            totalPages: 1,
            currentPage: GetPage.getSubPage() || 1,
            pageNeighbours: 1,
        };
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(prevProps.countPaginationLength !== this.props.countPaginationLength) {
            this.updatePage();
        }

        if(GetPage.getSubPage() !== this.state.currentPage) {
            this.setPage(GetPage.getSubPage(PREFIX_PAGE));
        }
    }

    updatePage() {
        const paginationLength = this.props.countPaginationLength;
        const currentPage = GetPage.getSubPage(PREFIX_PAGE);
        const page = (paginationLength < currentPage) || (currentPage < 1) ? 1 : currentPage;
        const paginationSubPagePrefix = this.props.paginationSubPagePrefix;

        if(paginationSubPagePrefix !== this.state.paginationSubPagePrefix) {
            this.setState({paginationSubPagePrefix});
        }

        this.setPage(page);
    }

    setPage(page, setFromPagination = false) {
        const items = this.props.items;
        const countPaginationLength = this.props.countPaginationLength;

        if(countPaginationLength !== 0) {
            if(countPaginationLength !== this.state.totalPages) {
                this.setState({totalPages: countPaginationLength});
            }

            if(page < 1 || page > countPaginationLength) {
                return;
            }
        }

        const pager = items ? this.getPager(items.length, page) : null;

        if(pager && page) {
            const currentUrl = window.location.href;
            let newUrl = '';

            if(page > 1) {
                newUrl = UrlParams.updateURLParameter(currentUrl, PREFIX_PAGE, page)
            }

            if(page === 1) {
                const splittedUrl = currentUrl.split(PREFIX_PAGE);

                if(splittedUrl) {
                    newUrl = currentUrl.replace(PREFIX_PAGE + splittedUrl[1], '', currentUrl);
                }
            }

            if(newUrl) {
                if(setFromPagination || !history.state || (history.state && (!history.state.page || history.state.page < page))) {
                    history.pushState({page}, null, newUrl);
                }
            }

            this.setState({pager: pager, currentPage: page});

            this.props.paginationSetCurrentPage(page);
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
        const totalPages = parseInt(this.state.totalPages);
        const currentPage = parseInt(this.state.currentPage);
        const pageNeighbours = parseInt(this.state.pageNeighbours);

        /**
         * totalNumbers: the total page numbers to show on the control
         * totalBlocks: totalNumbers + 2 to cover for the left(<) and right(>) controls
         */
        const totalNumbers = (pageNeighbours * 2) + 1;
        const runFullPagination = totalPages > 5;
        const startPage = Math.max(2, currentPage - pageNeighbours);
        const endPage = runFullPagination ? Math.min(totalPages - 1, currentPage + pageNeighbours) : totalPages - 1;
        let pages = range(startPage, endPage);

        if(runFullPagination) {
            /**
             * hasLeftSpill: has hidden pages to the left
             * hasRightSpill: has hidden pages to the right
             * spillOffset: number of hidden pages either to the left or to the right
             */
            const hasLeftSpill = startPage > 2;
            const hasRightSpill = (totalPages - endPage) > 1;
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

        return [1, ...pages, totalPages];
    }

    changePaginationClick(page, evt) {
        evt.preventDefault();
        this.setPage(page, true);
    }

    render() {
        const {currentPage, totalPages, paginationSubPagePrefix} = this.state;
        const prefixPage = `${paginationSubPagePrefix}/${PREFIX_PAGE}/`;
        const pages = this.fetchPageNumbers();
        const isLastPage = currentPage === totalPages;
        const classes = 'link-prevent-default';
        const activeClasses = `${classes} active`;

        return (
            <>
                { pages.map((page, index) => {
                    if (page === LEFT_PAGE) return (
                        <a key={index}
                           className={classes}
                           href={(parseInt(currentPage) - 1) > 1 ?  prefixPage + (parseInt(currentPage) - 1) : paginationSubPagePrefix}
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
                           href={!isLastPage ? prefixPage + (parseInt(currentPage) + 1) : prefixPage + totalPages}
                           aria-label="Next"
                           onClick={(e) => this.changePaginationClick(parseInt(currentPage) + 1, e)}>
                            <span aria-hidden="true">&raquo;</span>
                            <span className="sr-only">Next</span>
                        </a>
                    );

                    return (
                        <a key={index}
                           className={parseInt(currentPage) === parseInt(page) ? activeClasses : classes}
                           href={page > 1 ? prefixPage + page : paginationSubPagePrefix}
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
    items: PropTypes.array.isRequired,
    itemsPerPage: PropTypes.number.isRequired,
    countPaginationLength: PropTypes.number.isRequired,
    paginationSubPagePrefix: PropTypes.string.isRequired,
    paginationSetCurrentPage: PropTypes.func.isRequired
};

export default Pagination;

