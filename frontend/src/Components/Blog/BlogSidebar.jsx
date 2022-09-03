import React, { Component } from 'react';
import axios from 'axios';
import imgBlogAdd from '../../../public/assets/img/blog/add.jpg';
import ValidateEmail from '../ValidateEmail';
import { Link } from 'react-router-dom';
import $ from 'jquery';
import '../../../public/assets/js/jquery.autocomplete.min';

class BlogSidebar extends Component {
    constructor(props) {
        super(props);

        this.state = {
            popularPosts: [],
            categoriesList: [],
            tagsList: [],
            newsletter: {
                form: {
                    name: '',
                    email: '',
                    dataProcessingAgreement: false,
                },
                errors: {
                    name: '',
                    email: '',
                    dataProcessingAgreement: ''
                },
                errorMessage: '',
                noticeMessage: ''
            }
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    getPopularPosts() {
        axios.get('/api/blog/posts/popular')
            .then(result => {
                if(result && result.data) {
                    this.setState({popularPosts: result.data});
                }
            })
            .catch(err => {
                console.error(err)
            });
    }

    getCategoriesList() {
        axios.get('/api/blog/categories/list')
            .then(result => {
                if(result && result.data) {
                    this.setState({categoriesList: result.data});
                }
            })
            .catch(err => {
                console.error(err)
            });
    }

    getTagsList() {
        axios.get('/api/blog/tags/list')
            .then(result => {
                if(result && result.data) {
                    this.setState({tagsList: result.data});
                }
            })
            .catch(err => {
                console.error(err)
            });
    }

    componentDidMount() {
        this.getPopularPosts();
        this.getCategoriesList();
        this.getTagsList();

        $('#search_blog').autocomplete({
            serviceUrl: '/api/blog/posts/search-by-title',
            onSelect: function (suggestion) {
                if(suggestion && suggestion.slug) {
                    window.location.href = `/blog/${suggestion.slug}`;
                }
            }
        });
    }

    handleChange(event) {
        const { name, value, checked } = event.target;

        const {newsletter} = this.state;
        const {form} = newsletter;

        form[name] = name !== 'dataProcessingAgreement' ? value : checked;

        newsletter.form = form;

        const errors = newsletter.errors;

        errors[name] = '';

        if(name === 'dataProcessingAgreement' && !checked) {
            errors.dataProcessingAgreement = 'You need to approve the regulations before submit.';
        }

        newsletter.errors = errors;

        this.setState({newsletter});
    }

    handleSubmit(event) {
        event.preventDefault();
        const { name, email, dataProcessingAgreement } = this.state.newsletter.form;
        let errors = this.state.newsletter.errors;

        // Clear error message while send form
        this.setState({errorMessage: ''});

        errors.email = ValidateEmail(email) ?
            '' :
            'Email is not valid!';

        errors.dataProcessingAgreement = dataProcessingAgreement ?
            '' :
            'You need to approve the regulations before submit.';

        this.setState({errors});

        if(errors.email.length === 0 && errors.dataProcessingAgreement.length === 0) {
            const errorMessageStr = 'Something went wrong. Try again.';

            const {newsletter} = this.state;

            axios.post("/api/newsletter/create",
                {'name': name, 'email': email, 'dataProcessingAgreement': dataProcessingAgreement}
            ).then(result => {
                if (result.status === 201) {
                    const data = result.data;
                    if(!data.error) {
                        const {form} = newsletter;

                        form.name = '';
                        form.email = '';
                        form.dataProcessingAgreement = false;

                        newsletter.form = form;

                        newsletter.noticeMessage = 'Your email was added to the newsletter.';

                        this.setState({newsletter});
                    } else if(data.error && data.message) {
                        newsletter.errorMessage = data.message;
                        newsletter.noticeMessage = '';

                        this.setState({newsletter});
                    } else {
                        newsletter.errorMessage = errorMessageStr;
                        newsletter.noticeMessage = '';

                        this.setState({newsletter});
                    }
                } else {
                    newsletter.errorMessage = errorMessageStr;
                    newsletter.noticeMessage = '';

                    this.setState({newsletter});
                }
            }).catch((err) => {
                const responseMessage = err.response.data.message;
                newsletter.errorMessage = responseMessage ? '' : errorMessageStr;
                newsletter.noticeMessage = responseMessage ? responseMessage : '';

                this.setState({newsletter});
            });
        }
    }

    render() {
        const {
            popularPosts,
            categoriesList,
            tagsList
        } = this.state;

        const {errors, errorMessage, noticeMessage} = this.state.newsletter;

        const renderPopularPostsHTML = () => {
            return popularPosts.map(item => {
                return (
                    <div className="media post_item" key={item.uuid}>
                        <Link to={`/blog/${item.slug}`}>
                            <img src={item.image.url} alt={item.title} width="100px"/>
                        </Link>
                        <div className="media-body">
                            <Link to={`/blog/${item.slug}`}>
                                <h3>{item.title}</h3>
                            </Link>
                            <p>{item.views} views</p>
                        </div>
                    </div>
                )
            });
        };

        const renderCategoriesListHTML = () => {
            return categoriesList.map(item => (
                <li key={item.uuid}>
                    <Link to={`/blog/category/${item.slug}`} className="d-flex justify-content-between">
                        <p>{item.name}</p>
                        <p>{item.countPosts}</p>
                    </Link>
                </li>
            ))
        };

        const renderTagsListHTML = () => {
            return tagsList.map(item => (
                <li key={item.uuid}><Link to={`/blog/tag/${item.slug}`}>{item.name}</Link></li>
            ));
        }

        return (
            <div className="col-lg-4">
                <div className="blog_right_sidebar">
                    <aside className="single_sidebar_widget search_widget">
                        <div className="input-group">
                            <input type="text"
                                   className="form-control"
                                   id="search_blog"
                                   placeholder="Search Posts"
                            />
                            <span className="input-group-btn">
                                <button className="btn btn-default" type="button">
                                    <i className="lnr lnr-magnifier"/>
                                </button>
                            </span>
                        </div>
                        <div className="br"/>
                    </aside>
                    <aside className="single_sidebar_widget popular_post_widget">
                        <h3 className="widget_title">Popular Posts</h3>
                        {renderPopularPostsHTML()}
                        <div className="br"/>
                    </aside>
                    <aside className="single_sidebar_widget ads_widget">
                        <a href="#"><img className="img-fluid" src={imgBlogAdd} alt=""/></a>
                        <div className="br"/>
                    </aside>
                    <aside className="single_sidebar_widget post_category_widget">
                        <h4 className="widget_title">Post Catgories</h4>
                        <ul className="list cat-list">
                            {renderCategoriesListHTML()}
                        </ul>
                        <div className="br"/>
                    </aside>
                    <aside className="single-sidebar-widget newsletter_widget">
                        <h4 className="widget_title">Newsletter</h4>
                        <p>
                            Here, I focus on a range of items and features that we use in life without
                            giving them a second thought.
                        </p>
                        <div className="form-group d-flex flex-row">
                            <form target="_blank"
                                  className="row"
                                  onSubmit={this.handleSubmit}
                            >
                                <div className="input-group">
                                    <div className="input-group-prepend">
                                        <div className="input-group-text">
                                            <i className="fa fa-user" aria-hidden="true"/>
                                        </div>
                                    </div>
                                    <input type="text"
                                           className="form-control"
                                           id="name"
                                           name="name"
                                           placeholder="Name"
                                           onChange={this.handleChange}
                                           value={this.state.newsletter.form.name}
                                    />
                                </div>
                                <div className="input-group">
                                    <div className="input-group-prepend">
                                        <div className="input-group-text">
                                            <i className="fa fa-envelope" aria-hidden="true"/>
                                        </div>
                                    </div>
                                    <input type="text"
                                           className="form-control"
                                           id="email"
                                           name="email"
                                           placeholder="Enter email"
                                           required={true}
                                           onChange={this.handleChange}
                                           value={this.state.newsletter.form.email}
                                    />
                                </div>
                                <div className="col-md-12 form-group">
                                    <input type="checkbox"
                                           id="dataProcessingAgreement_sidebar_newsletter"
                                           name="dataProcessingAgreement"
                                           className="form-control"
                                           required={true}
                                           onChange={this.handleChange}
                                           checked={this.state.newsletter.form.dataProcessingAgreement}
                                    />
                                    <label htmlFor="dataProcessingAgreement_sidebar_newsletter">
                                        I accept sales regulations and confirm acquaintance with Privacy Policy
                                    </label>
                                </div>

                                <div className="col-md-12 form-group">
                                    { errors.email ?
                                        <span className='error-message-input'>{errors.email}</span> : null
                                    }

                                    { errors.dataProcessingAgreement ?
                                        <span className='error-message-input'>{errors.dataProcessingAgreement}</span> :
                                        null
                                    }

                                    { errorMessage ?
                                        <span className="form-error-message">{errorMessage}</span> :
                                        null
                                    }

                                    { noticeMessage ?
                                        <span className="form-notice-message">{noticeMessage}</span> :
                                        null
                                    }
                                </div>

                                <div className="col-md-12 form-group">
                                    <button className="click-btn btn btn-default pull-right">
                                        <i className="fa fa-long-arrow-right" aria-hidden="true" />
                                    </button>
                                </div>
                            </form>
                        </div>
                        <p className="text-bottom">You can unsubscribe at any time</p>
                        <div className="br"/>
                    </aside>
                    <aside className="single-sidebar-widget tag_cloud_widget">
                        <h4 className="widget_title">Tag Clouds</h4>
                        <ul className="list">
                            {renderTagsListHTML()}
                        </ul>
                    </aside>
                </div>
            </div>
        )
    }
}

export default BlogSidebar;
