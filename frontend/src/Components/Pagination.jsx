import React, { Component } from "react";
import UrlParams from "./UrlParams";

const defaultProps = {
    initialPage: 1
}

const PREFIX_PAGE = 'page';

class Pagination extends Component {
    constructor(props) {
        super(props);
        this.state = {
            subPageName: '',
            items: [],
            pager: {}
        };
    }

    componentWillMount() {
        const _this = this;

        if(this.props.items && this.props.items.length) {
            this.setPage(this.props.initialPage);
        }

        window.addEventListener("popstate", () => {
            const currentPage = UrlParams.getCurrentSubPage(`${this.state.subPageName}/${PREFIX_PAGE}`);
            if(currentPage) {
                if(currentPage > 0) {
                    _this.setPage(currentPage - 1);
                } else {
                    _this.setPage(1);
                }
            } else {
                _this.setPage(1);
            }
        });
    }

    componentDidUpdate(prevProps, prevState) {
        if((this.props.items !== prevProps.items) && (this.props.items && this.props.items.length)) {
            this.setState({
                items: prevProps.items
            });

            this.setPage(this.props.initialPage);
        }
    }

    setPage(page, event = null) {
        if(event) {
            event.preventDefault();
        }

        const items = this.props.items;
        let pager = this.state.pager;

        if(page < 1 || page > pager.totalPages) {
            return;
        }

        pager = this.getPager(items.length, page);

        if(pager.currentPage) {
            if(pager.currentPage > 1) {
                const newUrl = UrlParams.updateURLParameter(window.location.href, PREFIX_PAGE, pager.currentPage)

                window.history.pushState({}, null, newUrl);
            } else if(pager.currentPage === 1) {
                const currentUrl = window.location.href;
                const splittedUrl = currentUrl.split(PREFIX_PAGE);
                let newUrl = '';

                if(splittedUrl) {
                    newUrl = currentUrl.replace(PREFIX_PAGE + splittedUrl[1], '', currentUrl);
                }

                window.history.replaceState({}, null, newUrl);
            }
        }

        const pageOfItems = items.slice(pager.startIndex, pager.endIndex + 1);

        this.setState({pager: pager});

        this.props.onChangePage(pageOfItems);
    }

    getPager(totalItems, currentPage, pageSize) {
        // default to first page
        currentPage = currentPage || 1;

        // default page size is 10
        pageSize = pageSize || 5;

        if(this.props && this.props.itemPerPage && this.props.itemPerPage > 0) {
            pageSize = this.props.itemPerPage;
        }

        // calculate total pages
        const totalPages = Math.ceil(totalItems / pageSize);

        let startPage, endPage;
        if(totalPages <= 10) {
            // less than 10 total pages so show all
            startPage = 1;
            endPage = totalPages;
        } else {
            // more than 10 total pages so calculate start and end pages
            if(currentPage <= 6) {
                startPage = 1;
                endPage = 10;
            } else if(currentPage + 4 >= totalPages) {
                startPage = totalPages - 9;
                endPage = totalPages;
            } else {
                startPage = currentPage - 5;
                endPage = currentPage + 4;
            }
        }

        // calculate start and end item indexes
        const startIndex = (currentPage - 1) * pageSize;
        const endIndex = Math.min(startIndex + pageSize - 1, totalItems - 1);

        // create an array of pages to ng-repeat in the pager control
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

    render() {
        const pager = this.state.pager;

        if(!pager.pages || pager.pages.length <= 1) {
            return null;
        }

        const prefixPage = `${this.state.subPageName}/${PREFIX_PAGE}/`;
        const classesPaginationA = "link-prevent-default";

        let isLastPage = parseInt(pager.currentPage) === parseInt(pager.totalPages);

        let classes = '';
        let activeClasses = '';

        if(this.props) {
            if(this.props.defaultClasses) {
                classes = this.props.defaultClasses;
            }

            if(this.props.activeClasses) {
                activeClasses = this.props.activeClasses;
            }
        }

        classes = classes + ' ' + classesPaginationA;

        const paginationSubPagePrefix = this.props.paginationSubPagePrefix;

        return (
            <>
                <a
                    href={(parseInt(pager.currentPage) - 1) > 1 ? prefixPage + (parseInt(pager.currentPage) - 1) : paginationSubPagePrefix}
                    onClick={(e) => this.setPage(pager.currentPage - 1, e)}
                    className={classes}
                >
                    <i className="fa fa-long-arrow-left" aria-hidden="true"/>
                </a>

                {pager.pages.map((page, index) => (
                    <a
                        key={index}
                        href={page > 1 ? paginationSubPagePrefix + prefixPage + page : paginationSubPagePrefix}
                        onClick={(e) => this.setPage(page, e)}
                        className={parseInt(pager.currentPage) === parseInt(page) ? classes + ' ' + activeClasses : classes}
                    >
                        {page}
                    </a>
                    )
                )}

                <a
                    href={!isLastPage ? paginationSubPagePrefix + prefixPage + (parseInt(pager.currentPage) + 1) : paginationSubPagePrefix + prefixPage + pager.totalPages}
                    onClick={(e) => this.setPage(pager.currentPage + 1, e)}
                    className={classes}
                >
                    <i className="fa fa-long-arrow-right" aria-hidden="true"/>
                </a>
            </>
        );
    }
}

Pagination.defaultProps = defaultProps;

export default Pagination;
