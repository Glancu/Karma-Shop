import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import axios from 'axios';
import SinglePostList from '../../Components/Blog/SinglePostList';
import Pagination from '../../Components/Pagination';
import GetPage from '../../Components/GetPage';
import Loader from '../../Components/Loader';
import { windowScrollTo } from '../WindowScroll';
import BlogSidebar from '../../Components/Blog/BlogSidebar';
import BlogListCategoriesLatest from '../../Components/Blog/BlogListCategoriesLatest';
import PropTypes from 'prop-types';

class BlogBaseTemplate extends Component {
    constructor(props) {
        super(props);

        this.state = {
            blogPosts: [],
            blogPostsCountItems: 0,
            currentPage: GetPage.getSubPage(),
            loader: true,
            categorySlug: props.category,
            tagSlug: props.tag
        }

        this.paginationSetCurrentPage = this.paginationSetCurrentPage.bind(this);
        this.getBlogPosts = this.getBlogPosts.bind(this);
    }

    getBlogPosts() {
        const _this = this;
        const {currentPage} = this.state;
        const offset = currentPage > 1 ? (currentPage - 1) * 12 : 0;

        let itemsUrl = `/api/blog/posts/list?offset=${offset}`;

        if(this.props.category) {
            itemsUrl += '&category=' + this.props.category;
        }

        if(this.props.tag) {
            itemsUrl += '&tag=' + this.props.tag;
        }

        axios.get(itemsUrl)
            .then(result => {
                if(result && result.data.countItems && result.data.countItems > 0 && result.data.items && result.data.items.length > 0) {
                    this.setState({blogPosts: result.data.items, countItems: result.data.countItems});

                    setTimeout(() => {
                        _this.setState({loader: false});
                    }, 500);
                }
            })
            .catch(err => {
                console.error(err)
            });
    }

    scrollToTop(timeout = 0) {
        const contentEl = document.querySelector('.blog_categorie_area');
        const headerAreaEl = document.querySelector('.header_area');

        setTimeout(() => {
            windowScrollTo(contentEl.offsetTop - headerAreaEl.offsetHeight);
        }, timeout)
    }

    componentDidMount() {
        this.getBlogPosts();
    }

    componentDidUpdate(prevProps, prevState, snapshot) {
        if(this.props.category && this.props.category !== this.state.categorySlug) {
            this.setState({categorySlug: this.props.category});

            this.getBlogPosts();
        }

        if(this.props.tag && this.props.tag !== this.state.tagSlug) {
            this.setState({tagSlug: this.props.tag});

            this.getBlogPosts();
        }
    }

    paginationSetCurrentPage(page) {
        const _this = this;
        let {currentPage} = this.state;
        if(currentPage !== page) {
            this.state.currentPage = page;
            this.state.loader = true;

            this.scrollToTop(100);

            setTimeout(() => {
                _this.getItems();
            }, 500);
        }
    }

    render() {
        const {
            blogPosts,
            blogPostsCountItems,
            loader
        } = this.state;

        const itemsPerPage = 12;
        const countPagesPagination = Math.ceil(blogPostsCountItems / itemsPerPage);

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Blog posts</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}/>
                                    <Link to={'/blog'}>Blog</Link>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="blog_categorie_area">
                    <div className="container">
                        <div className="row">
                            <BlogListCategoriesLatest />
                        </div>
                    </div>
                </section>

                <section className="blog_area">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-8">
                                <Loader isLoading={loader}/>
                            </div>

                            {!loader &&
                            <div className="col-lg-8">
                                <div className="blog_left_sidebar">
                                    {blogPosts && blogPosts.length > 0 &&
                                    blogPosts.map(item => (
                                        <SinglePostList item={item} key={item.uuid}/>
                                    ))
                                    }

                                    <nav className="blog-pagination justify-content-center d-flex">
                                        <ul className="pagination">
                                            <Pagination
                                                itemsPerPage={itemsPerPage}
                                                countPages={countPagesPagination}
                                                setCurrentPage={this.paginationSetCurrentPage}
                                                paginationType="blog"
                                            />
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            }
                            <BlogSidebar />
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

BlogBaseTemplate.propTypes = {
    category: PropTypes.string,
    tag: PropTypes.string,
}

export default BlogBaseTemplate
