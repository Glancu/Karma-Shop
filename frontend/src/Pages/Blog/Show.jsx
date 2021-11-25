import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import axios from 'axios';
import Loader from '../../Components/Loader';
import ValidateEmail from '../../Components/ValidateEmail';
import BlogSidebar from '../../Components/Blog/BlogSidebar';

class Show extends Component {
    constructor(props) {
        super(props);
        this.state = {
            slug: this.props.match.params.slug,
            loader: true,
            post: null,
            commentForm: {
                name: '',
                email: '',
                message: '',
                dataProcessingAgreement: false
            }
        }

        this.setAddCommentValueToState = this.setAddCommentValueToState.bind(this);
        this.submitComment = this.submitComment.bind(this);

        this.addCommentMessageRef = React.createRef();
    }

    componentDidMount() {
        const _this = this;
        const {slug} = this.state;

        axios.get(`/api/blog/post/${slug}`)
            .then(result => {
                if(result && result.data) {
                    this.setState({post: result.data});

                    setTimeout(() => {
                        _this.setState({loader: false});
                    }, 500);
                }
            })
            .catch(err => {
                console.error(err);
            });
    }

    setAddCommentValueToState(e) {
        const {commentForm} = this.state;

        commentForm[e.target.getAttribute('name')] = e.target.value;

        this.state.commentForm = commentForm;
    }

    submitComment(e) {
        e.preventDefault();
        const messageEl = this.addCommentMessageRef.current;

        const {commentForm} = this.state;
        if(!commentForm.name) {
            messageEl.textContent = 'Name cannot be null.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!commentForm.email) {
            messageEl.textContent = 'Email cannot be null.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!ValidateEmail(commentForm.email)) {
            messageEl.textContent = 'Email is not valid.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!commentForm.message) {
            messageEl.textContent = 'Message cannot be null.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!commentForm.dataProcessingAgreement) {
            messageEl.textContent = 'You need to approve the regulations before submit.'
            messageEl.classList = 'form-error-message';
            return;
        }

        const errorMessageCustom = 'Something went wrong. Try again.';

        axios.post('/api/comments/create', {
            'name': commentForm.name,
            'email': commentForm.email,
            'message': commentForm.message,
            'dataProcessingAgreement': commentForm.dataProcessingAgreement === 'on',
            'blogPostUuid': this.state.post.uuid
        })
            .then(result => {
                if(result.status === 201) {
                    const data = result.data;
                    if(!data.error && data.uuid) {
                        this.state.commentForm = {
                            name: '',
                            email: '',
                            message: '',
                            dataProcessingAgreement: false
                        };

                        messageEl.textContent = 'Comment was add, but it must be accepted by administrator.'
                        messageEl.classList = 'form-notice-message';

                        document.getElementById('add_comment_form').reset();
                    } else if(data.error && data.message) {
                        messageEl.textContent = data.message
                        messageEl.classList = 'form-error-message';
                    } else {
                        messageEl.textContent = errorMessageCustom;
                        messageEl.classList = 'form-error-message';
                    }
                } else {
                    messageEl.textContent = errorMessageCustom;
                    messageEl.classList = 'form-error-message';
                }
            }).catch(() => {
            messageEl.textContent = errorMessageCustom;
            messageEl.classList = 'form-error-message';
        });
    }

    render() {
        const {loader, post} = this.state;

        const renderCommentsHTML = () => {
            return post.comments.map(comment => {
                const randomImageKey = Math.floor(Math.random() * (5 - 1 + 1) + 1);
                const image = `/assets/img/blog/c${randomImageKey}.jpg`;
                return (
                    <div className="comment-list" key={comment.uuid}>
                        <div className="single-comment justify-content-between d-flex">
                            <div className="user justify-content-between d-flex">
                                <div className="thumb">
                                    <img src={image} alt=""/>
                                </div>
                                <div className="desc">
                                    <h5><a>{comment.name}</a></h5>
                                    <p className="date">{comment.createdAt}</p>
                                    <p className="comment">
                                        {comment.text}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                )
            })
        };

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Blog Page</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}/>
                                    <Link to={'/blog'}>Blog</Link>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <section className="blog_area single-post-area section_gap">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-8 posts-list">
                                <Loader isLoading={loader} />

                                {!loader && post &&
                                    <>
                                    <div className="single-post row">
                                        <div className="col-lg-12">
                                            <div className="feature-img">
                                                <img className="img-fluid" src={post.image.url} alt=""/>
                                            </div>
                                        </div>
                                        <div className="col-lg-3  col-md-3">
                                            <div className="blog_info text-right">
                                                <div className="post_tag">
                                                    {
                                                        post.tags.map((tag, key) => (
                                                            <Link to={`/blog/tag/${tag.slug}`} key={tag.uuid}>{tag.name}{key + 1 !== post.tags.length && ', '}</Link>
                                                        ))
                                                    }
                                                </div>
                                                <ul className="blog_meta list">
                                                    <li>
                                                        <a>
                                                            {post.createdAt}<i className="lnr lnr-calendar-full"/>
                                                        </a>
                                                    </li>
                                                    <li><a>{post.views} Views<i className="lnr lnr-eye"/></a></li>
                                                    <li><a>{post.comments.length} Comments<i className="lnr lnr-bubble"/></a>
                                                    </li>
                                                </ul>
                                                <ul className="social-links">
                                                    <li><a href="#"><i className="fa fa-facebook"/></a></li>
                                                    <li><a href="#"><i className="fa fa-twitter"/></a></li>
                                                    <li><a href="#"><i className="fa fa-github"/></a></li>
                                                    <li><a href="#"><i className="fa fa-behance"/></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div className="col-lg-9 col-md-9 blog_details">
                                            <h2>{post.title}</h2>
                                            <div className="excert" dangerouslySetInnerHTML={{ __html: post.shortContent }} />
                                        </div>
                                        <div className="col-lg-12">
                                            <div className="row">
                                                <div dangerouslySetInnerHTML={{ __html: post.longContent }} />
                                            </div>
                                        </div>
                                    </div>
                                    <div className="navigation-area">
                                        <div className="row">
                                            {post.previousPost &&
                                                <div
                                                    className="col-lg-6 col-md-6 col-12 nav-left flex-row d-flex justify-content-start align-items-center"
                                                    key={post.previousPost.uuid}
                                                >
                                                    {post.previousPost.imageUrl &&
                                                        <div className="thumb">
                                                            <Link to={`/blog/${post.previousPost.slug}`}>
                                                                <img className="img-fluid" src={post.previousPost.imageUrl} alt=""/>
                                                            </Link>
                                                        </div>
                                                    }
                                                    <div className="arrow">
                                                        <Link to={`/blog/${post.previousPost.slug}`}>
                                                            <span className="lnr text-white lnr-arrow-left"/>
                                                        </Link>
                                                    </div>
                                                    <div className="detials">
                                                        <p>Prev Post</p>
                                                        <Link to={`/blog/${post.previousPost.slug}`}>
                                                            <h4>{post.previousPost.title}</h4>
                                                        </Link>
                                                    </div>
                                                </div>
                                            }

                                            {post.nextPost &&
                                                <div
                                                    className="col-lg-6 col-md-6 col-12 nav-right flex-row d-flex justify-content-end align-items-center"
                                                    key={post.nextPost.uuid}
                                                >
                                                    <div className="detials">
                                                        <p>Next Post</p>
                                                        <Link to={`/blog/${post.nextPost.slug}`}>
                                                            <h4>{post.nextPost.title}</h4>
                                                        </Link>
                                                    </div>
                                                    <div className="arrow">
                                                        <Link to={`/blog/${post.nextPost.slug}`}>
                                                            <span className="lnr text-white lnr-arrow-right"/>
                                                        </Link>
                                                    </div>
                                                    {post.nextPost.imageUrl &&
                                                        <div className="thumb">
                                                            <Link to={`/blog/${post.nextPost.slug}`}>
                                                                <img className="img-fluid" src={post.nextPost.imageUrl} alt=""/>
                                                            </Link>
                                                        </div>
                                                    }
                                                </div>
                                            }
                                        </div>
                                    </div>
                                    <div className="comments-area">
                                        <h4>{post.comments.length} {post.comments.length > 1 ? 'Comments' : 'Comment'}</h4>
                                        {renderCommentsHTML()}
                                    </div>
                                    <div className="comment-form">
                                        <h4>Leave a Reply</h4>
                                        <form onSubmit={this.submitComment}
                                              id="add_comment_form">
                                            <div className="form-group form-inline">
                                                <div className="form-group col-lg-6 col-md-6 name">
                                                    <input type="text"
                                                           className="form-control"
                                                           id="name"
                                                           name="name"
                                                           placeholder="Enter Name"
                                                           required={true}
                                                           onChange={this.setAddCommentValueToState}
                                                    />
                                                </div>
                                                <div className="form-group col-lg-6 col-md-6 email">
                                                    <input type="email"
                                                           className="form-control"
                                                           id="email"
                                                           name="email"
                                                           placeholder="Enter email address"
                                                           required={true}
                                                           onChange={this.setAddCommentValueToState}
                                                    />
                                                </div>
                                            </div>
                                            <div className="form-group">
                                                <textarea className="form-control mb-10"
                                                          rows="5"
                                                          name="message"
                                                          placeholder="Message"
                                                          required={true}
                                                          onChange={this.setAddCommentValueToState}/>
                                            </div>
                                            <div className="col-md-12 form-group">
                                                <input type="checkbox"
                                                       id="dataProcessingAgreement"
                                                       name="dataProcessingAgreement"
                                                       className="form-control"
                                                       onChange={this.setAddCommentValueToState}
                                                       required={true}
                                                />
                                                <label htmlFor="dataProcessingAgreement">
                                                    I accept sales regulations and confirm acquaintance with Privacy Policy
                                                </label>
                                            </div>
                                            <div className="col-md-12 text-right">
                                                <p ref={this.addCommentMessageRef} />
                                            </div>
                                            <div className="col-md-12 text-right">
                                                <button type="submit"
                                                        value="submit"
                                                        className="primary-btn submit_btn border-0"
                                                >
                                                    Post Comment
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </>
                            }
                            </div>
                            <BlogSidebar />
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default Show
