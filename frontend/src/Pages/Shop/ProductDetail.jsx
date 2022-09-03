import React, { Component } from 'react';
import BaseTemplate from '../../Components/BaseTemplate';
import { Link } from 'react-router-dom';
import OwlCarousel from 'react-owl-carousel';
import axios from 'axios';
import Loader from '../../Components/Loader';
import ShoppingCart from '../../Components/Shop/ShoppingCart';
import { toast } from 'react-toastify';
import CONFIG from '../../config';
import ValidateEmail from '../../Components/ValidateEmail';
import ProductDetailComment from '../../Components/Shop/ProductDetailComment';
import SetPageTitle from '../../Components/SetPageTitle';

class ProductDetail extends Component {
    constructor(props) {
        super(props);
        this.state = {
            product: null,
            notFoundProduct: false,
            loader: true,
            productReviewForm: {
                name: '',
                email: '',
                message: '',
                dataProcessingAgreement: false
            },
            commentForm: {
                name: '',
                email: '',
                message: '',
                dataProcessingAgreement: false
            }
        }

        this.increaseItemCount = this.increaseItemCount.bind(this);
        this.decreaseItemCount = this.decreaseItemCount.bind(this);
        this.addProductToCart = this.addProductToCart.bind(this);
        this.setReviewValueToState = this.setReviewValueToState.bind(this);
        this.submitReviewComment = this.submitReviewComment.bind(this);
        this.setAddCommentValueToState = this.setAddCommentValueToState.bind(this);
        this.submitComment = this.submitComment.bind(this);

        this.addReviewMessageRef = React.createRef();
        this.addCommentMessageRef = React.createRef();
    }

    componentDidMount() {
        const _this = this;
        const slug = this.props.match.params.slug;
        if(slug) {
            axios.get(`/api/shop/products/product/${slug}`)
                .then(result => {
                    if(result.status === 200) {
                        if(result.data) {
                            this.state.product = result.data;
                            this.state.notFoundProduct = false;

                            SetPageTitle(`Product: ${result.data.name}`);

                            setTimeout(() => {
                                _this.setState({loader: false});
                            }, 1000);
                        } else {
                            this.setState({notFoundProduct: true});
                        }
                    } else {
                        this.setState({notFoundProduct: true});
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.setState({notFoundProduct: true});
                });
        }

        setTimeout(() => {
            const {product} = this.state;
            const productQuantity = product.quantity;

            const quantityInputEl = document.getElementById('productQuantity');
            if(quantityInputEl && product) {
                quantityInputEl.addEventListener('change', function() {
                    if(parseInt(this.value) < 1) {
                        quantityInputEl.value = 1;
                    } else if(parseInt(this.value) > productQuantity) {
                        quantityInputEl.value = productQuantity;
                    }
                });
            }
        }, 2000);
    }

    increaseItemCount() {
        const {product} = this.state;
        const productQuantity = product.quantity;

        const quantityInputEl = document.getElementById('productQuantity');
        const quantityInputElValue = quantityInputEl.value;
        if(!isNaN(quantityInputElValue) && quantityInputElValue < productQuantity)  {
            quantityInputEl.value++;
        } else {
            quantityInputEl.value = productQuantity;
        }
    }

    decreaseItemCount() {
        const quantityInputEl = document.getElementById('productQuantity');
        const quantityInputElValue = quantityInputEl.value;
        if(!isNaN(quantityInputElValue) && quantityInputElValue > 1)  {
            quantityInputEl.value--;
        } else if(quantityInputElValue < 1) {
            quantityInputEl.value = 1;
        }
    }

    addProductToCart() {
        const {product} = this.state;

        const quantityInputEl = document.getElementById('productQuantity');
        if(product && quantityInputEl) {
            ShoppingCart.addProductToCart(product, parseInt(quantityInputEl.value));

            toast.info('Product was added to the cart!', {autoClose: 2000});

            quantityInputEl.value = 1;
        }
    }

    setReviewValueToState(e) {
        const {productReviewForm} = this.state;

        productReviewForm[e.target.getAttribute('name')] = e.target.value;

        this.state.productReviewForm = productReviewForm;
    }

    submitReviewComment(e) {
        e.preventDefault();
        const messageEl = this.addReviewMessageRef.current;

        const {productReviewForm} = this.state;
        if(!productReviewForm.name) {
            messageEl.textContent = 'Name cannot be null.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!productReviewForm.email) {
            messageEl.textContent = 'Email cannot be null.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!ValidateEmail(productReviewForm.email)) {
            messageEl.textContent = 'Email is not valid.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!productReviewForm.message) {
            messageEl.textContent = 'Message cannot be null.'
            messageEl.classList = 'form-error-message';
            return;
        }

        const ratingValue = document.querySelector('.rate input:checked') && document.querySelector('.rate input:checked').value;
        if(ratingValue === null) {
            messageEl.textContent = 'Select your rating before submit review.'
            messageEl.classList = 'form-error-message';
            return;
        }

        if(!productReviewForm.dataProcessingAgreement) {
            messageEl.textContent = 'You need to approve the regulations before submit.'
            messageEl.classList = 'form-error-message';
            return;
        }

        const errorMessageCustom = 'Something went wrong. Try again.';

        axios.post('/api/shop/product-review/create', {
            'name': productReviewForm.name,
            'email': productReviewForm.email,
            'subject': productReviewForm.subject,
            'message': productReviewForm.message,
            'rating': parseInt(ratingValue),
            'dataProcessingAgreement': productReviewForm.dataProcessingAgreement,
            'productUuid': this.state.product.uuid
        })
            .then(result => {
                if(result.status === 201) {
                    const data = result.data;
                    if(!data.error && data.uuid) {
                        this.state.productReviewForm = {
                            name: '',
                            email: '',
                            message: '',
                            dataProcessingAgreement: false
                        };

                        messageEl.textContent = 'Review was add, but it must be accepted by administrator.'
                        messageEl.classList = 'form-notice-message';

                        document.getElementById('add_product_review_form').reset();
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
            'dataProcessingAgreement': commentForm.dataProcessingAgreement,
            'productUuid': this.state.product.uuid
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
        const currencySymbol = CONFIG.shop.currencySymbol;
        const slug = this.props.match.params.slug;
        const {product, notFoundProduct, loader} = this.state;

        if(notFoundProduct || !product || loader) {
            return (
                <BaseTemplate>
                    <section className="banner-area organic-breadcrumb">
                        <div className="container">
                            <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                                <div className="col-first">
                                    <h1>Product: {slug}</h1>
                                    <nav className="d-flex align-items-center">
                                        <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                        <Link to={'/shop'}>Shop<span className="lnr lnr-arrow-right"/></Link>
                                        <p>{slug}</p>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </section>

                    <div className="product_image_area">
                        <div className="container">
                            <div className="row s_product_inner">
                                <h1 className='ml-auto mr-auto'>
                                    {notFoundProduct ?
                                        'Product was not found.' :
                                        <Loader isLoading={loader} />
                                    }
                                </h1>
                            </div>
                        </div>
                    </div>
                </BaseTemplate>
            );
        }

        const specifications = () => {
            return product.shopProductSpecifications.map((item) => {
                return (
                    <tr key={item.uuid}>
                        <td>
                            <h5>{item.name}</h5>
                        </td>
                        <td>
                            <h5>{item.value}</h5>
                        </td>
                    </tr>
                )
            })
        };

        const carouselImages = () => {
            return product.images.map((image, key) => {
                return (
                    <div className="single-prd-item" key={key}>
                        <img className="img-fluid" src={image.url} alt={image.name}/>
                    </div>
                );
            });
        }

        const productReviewsLength = product.reviews.length;
        let overallReviewsStars = {
            1: 0,
            2: 0,
            3: 0,
            4: 0,
            5: 0
        }

        const overallReviews = () => {
            if(productReviewsLength === 0) {
                return '0.0';
            }

            let reviews = 0;

            product.reviews.map((review) => {
                reviews += review.rating;
            });

            return (reviews / productReviewsLength).toFixed(1);
        }

        product.reviews.map((review) => {
            overallReviewsStars[review.rating] = overallReviewsStars[review.rating] + 1;
        });

        const renderReviewsComments = () => {
            return product.reviews.map((review) => {
                return (
                    <ProductDetailComment key={review.uuid}
                                          name={review.name}
                                          date={review.createdAt}
                                          message={review.message}
                                          rating={review.rating}
                    />
                )
            });
        }

        const renderComments = () => {
            return product.comments.map((comment) => {
                return (
                    <ProductDetailComment key={comment.uuid}
                                          name={comment.name}
                                          date={comment.createdAt}
                                          message={comment.text}
                    />
                )
            });
        }

        return (
            <BaseTemplate>
                <section className="banner-area organic-breadcrumb">
                    <div className="container">
                        <div className="breadcrumb-banner d-flex flex-wrap align-items-center justify-content-end">
                            <div className="col-first">
                                <h1>Product: {slug}</h1>
                                <nav className="d-flex align-items-center">
                                    <Link to={'/'}>Home<span className="lnr lnr-arrow-right"/></Link>
                                    <Link to={'/shop'}>Shop<span className="lnr lnr-arrow-right"/></Link>
                                    <p>{slug}</p>
                                </nav>
                            </div>
                        </div>
                    </div>
                </section>

                <div className="product_image_area">
                    <div className="container">
                        <div className="row s_product_inner">
                            <div className="col-lg-6">
                                <OwlCarousel className="s_Product_carousel"
                                             items={1}
                                             autoPlay={false}
                                             autoplayTimeout={5000}
                                             loop={true}
                                             nav={false}
                                             dots={true}
                                >
                                    {carouselImages()}
                                </OwlCarousel>
                            </div>
                            <div className="col-lg-5 offset-lg-1">
                                <div className="s_product_text">
                                    <h3>{product.name}</h3>
                                    <h2>{currencySymbol} {product.priceGross}</h2>
                                    <ul className="list">
                                        <li>
                                            <span>Brand: </span>
                                            <Link className="active" to={`/shop?brand=${product.shopBrand.slug}`}>
                                                {product.shopBrand.title}
                                            </Link>
                                        </li>
                                        <li>
                                            <span>Category: </span>
                                            <Link className="active" to={`/shop/category/${product.shopCategory.slug}`}>
                                                {product.shopCategory.title}
                                            </Link>
                                        </li>
                                        <li>
                                            <span>Quantity: {product.quantity}</span>
                                        </li>
                                    </ul>
                                    <div dangerouslySetInnerHTML={
                                        { __html: product.description.length > 70 ?
                                                product.description.substring(0, 70) + '...' :
                                                product.description
                                        }
                                    } />
                                    <div className="product_count">
                                        <label htmlFor="qty">Quantity:</label>
                                        <input type="text"
                                               name="qty"
                                               id="productQuantity"
                                               maxLength={product.quantity}
                                               defaultValue="1"
                                               title="Quantity:"
                                               className="input-text qty"/>
                                        <button className="increase items-count"
                                                onClick={this.increaseItemCount}
                                                type="button"><i className="lnr lnr-chevron-up"/></button>
                                        <button className="reduced items-count"
                                                onClick={this.decreaseItemCount}
                                                type="button"><i className="lnr lnr-chevron-down"/></button>
                                    </div>
                                    <div className="card_area d-flex align-items-center">
                                        <button className="primary-btn border-0" onClick={() => this.addProductToCart()}>
                                            Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <section className="product_description_area">
                    <div className="container">
                        <ul className="nav nav-tabs" id="myTab" role="tablist">
                            <li className="nav-item">
                                <a className="nav-link"
                                   id="home-tab"
                                   data-toggle="tab"
                                   href="#home"
                                   role="tab"
                                   aria-controls="home"
                                   aria-selected="true"
                                >
                                    Description
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link"
                                   id="profile-tab"
                                   data-toggle="tab"
                                   href="#profile"
                                   role="tab"
                                   aria-controls="profile"
                                   aria-selected="false"
                                >
                                    Specification
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link"
                                   id="contact-tab"
                                   data-toggle="tab"
                                   href="#contact"
                                   role="tab"
                                   aria-controls="contact"
                                   aria-selected="false"
                                >
                                    Comments
                                </a>
                            </li>
                            <li className="nav-item">
                                <a className="nav-link active"
                                   id="review-tab"
                                   data-toggle="tab"
                                   href="#review"
                                   role="tab"
                                   aria-controls="review"
                                   aria-selected="false"
                                >
                                    Reviews
                                </a>
                            </li>
                        </ul>
                        <div className="tab-content" id="myTabContent">
                            <div className="tab-pane fade" id="home" role="tabpanel" aria-labelledby="home-tab">
                                <div dangerouslySetInnerHTML={{ __html: product.description }} />
                            </div>
                            <div className="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                <div className="table-responsive">
                                    <table className="table">
                                        <tbody>
                                        {specifications()}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div className="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
                                <div className="row">
                                    <div className="col-lg-6">
                                        <div className="comment_list">
                                            {renderComments()}
                                        </div>
                                    </div>
                                    <div className="col-lg-6">
                                        <div className="review_box">
                                            <h4>Post a comment</h4>
                                            <form className="row contact_form"
                                                  id="add_comment_form"
                                                  onSubmit={this.submitComment}>
                                                <div className="col-md-12">
                                                    <div className="form-group">
                                                        <input type="text"
                                                               className="form-control"
                                                               id="name"
                                                               name="name"
                                                               placeholder="Your Full name"
                                                               required={true}
                                                               onChange={this.setAddCommentValueToState}/>
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="form-group">
                                                        <input type="email"
                                                               className="form-control"
                                                               id="email"
                                                               name="email"
                                                               placeholder="Email Address"
                                                               required={true}
                                                               onChange={this.setAddCommentValueToState}/>
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="form-group">
                                                        <textarea className="form-control"
                                                                  name="message"
                                                                  id="message"
                                                                  rows="1"
                                                                  placeholder="Message"
                                                                  required={true}
                                                                  onChange={this.setAddCommentValueToState}/>
                                                    </div>
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
                                                            className="btn primary-btn"
                                                    >
                                                        Submit Now
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div className="tab-pane fade show active" id="review" role="tabpanel"
                                 aria-labelledby="review-tab">
                                <div className="row">
                                    <div className="col-lg-6">
                                        <div className="row total_rate">
                                            <div className="col-6">
                                                <div className="box_total">
                                                    <h5>Overall</h5>
                                                    <h4>
                                                        {overallReviews()}
                                                    </h4>
                                                    <h6>({productReviewsLength} Reviews)</h6>
                                                </div>
                                            </div>
                                            <div className="col-6">
                                                <div className="rating_list">
                                                    <h3>Based on {productReviewsLength} Reviews</h3>
                                                    <ul className="list">
                                                        <li>
                                                            <a>
                                                                5 Star
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                ({String(overallReviewsStars[5]).padStart(2, '0')})
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a>
                                                                4 Star
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star-o"/>
                                                                ({String(overallReviewsStars[4]).padStart(2, '0')})
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a>
                                                                3 Star
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star-o"/>
                                                                <i className="fa fa-star-o"/>
                                                                ({String(overallReviewsStars[3]).padStart(2, '0')})
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a>
                                                                2 Star
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star-o"/>
                                                                <i className="fa fa-star-o"/>
                                                                <i className="fa fa-star-o"/>
                                                                ({String(overallReviewsStars[2]).padStart(2, '0')})
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a>
                                                                1 Star
                                                                <i className="fa fa-star"/>
                                                                <i className="fa fa-star-o"/>
                                                                <i className="fa fa-star-o"/>
                                                                <i className="fa fa-star-o"/>
                                                                <i className="fa fa-star-o"/>
                                                                ({String(overallReviewsStars[1]).padStart(2, '0')})
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div className="review_list">
                                            {renderReviewsComments()}
                                        </div>
                                    </div>
                                    <div className="col-lg-6">
                                        <div className="review_box">
                                            <h4>Add a Review</h4>
                                            <form className="row contact_form"
                                                  id="add_product_review_form"
                                                  onSubmit={this.submitReviewComment}>
                                                <div className="col-md-12">
                                                    <p>Your Rating:</p>
                                                    <div className="rate">
                                                        <input type="radio" id="star5" name="rate" value="5"/>
                                                        <label htmlFor="star5">5 stars</label>
                                                        <input type="radio" id="star4" name="rate" value="4"/>
                                                        <label htmlFor="star4">4 stars</label>
                                                        <input type="radio" id="star3" name="rate" value="3"/>
                                                        <label htmlFor="star3">3 stars</label>
                                                        <input type="radio" id="star2" name="rate" value="2"/>
                                                        <label htmlFor="star2">2 stars</label>
                                                        <input type="radio" id="star1" name="rate" value="1"/>
                                                        <label htmlFor="star1">1 star</label>
                                                    </div>
                                                    <p>Outstanding</p>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="form-group">
                                                        <input type="text"
                                                               className="form-control"
                                                               id="name"
                                                               name="name"
                                                               placeholder="Your Full name"
                                                               onChange={(e) => this.setReviewValueToState(e)}
                                                               required={true}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="form-group">
                                                        <input type="email"
                                                               className="form-control"
                                                               id="email"
                                                               name="email"
                                                               placeholder="Email Address"
                                                               onChange={(e) => this.setReviewValueToState(e)}
                                                               required={true}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="col-md-12">
                                                    <div className="form-group">
                                                        <textarea className="form-control"
                                                                  name="message"
                                                                  id="message"
                                                                  rows="1"
                                                                  placeholder="Review"
                                                                  onChange={(e) => this.setReviewValueToState(e)}
                                                                  required={true}
                                                        />
                                                    </div>
                                                </div>
                                                <div className="col-md-12 form-group">
                                                    <input type="checkbox"
                                                           id="dataProcessingAgreement"
                                                           name="dataProcessingAgreement"
                                                           className="form-control"
                                                           onChange={this.setReviewValueToState}
                                                           required={true}
                                                    />
                                                    <label htmlFor="dataProcessingAgreement">
                                                        I accept sales regulations and confirm acquaintance with Privacy Policy
                                                    </label>
                                                </div>
                                                <div className="col-md-12 text-right">
                                                    <p ref={this.addReviewMessageRef} />
                                                </div>
                                                <div className="col-md-12 text-right">
                                                    <button type="submit"
                                                            value="submit"
                                                            className="primary-btn"
                                                    >
                                                        Submit Now
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </BaseTemplate>
        )
    }
}

export default ProductDetail
