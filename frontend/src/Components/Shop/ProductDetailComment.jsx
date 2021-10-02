import React, {Component} from 'react';
import PropTypes from 'prop-types';

// Avatars
import avatar1 from '../../../public/assets/img/product/review-1.png';
import avatar2 from '../../../public/assets/img/product/review-2.png';
import avatar3 from '../../../public/assets/img/product/review-3.png';

class ProductDetailComment extends Component {
    getRandomInt(max) {
        return Math.floor(Math.random() * max);
    }

    getRandomAvatar() {
        switch(this.getRandomInt(4)) {
            case 1: return avatar1;
            case 2: return avatar2;
            case 3: return avatar3;
            default: return avatar1;
        }
    }

    render() {
        const {name, date, message, rating} = this.props;

        const renderStarsByRating = () => {
            if(!rating) {
                return null;
            }

            let stars = '';

            for(let i = 0; i < rating; i++) {
                stars += '<i class="fa fa-star"/>';
            }

            return stars;
        }

        return (
            <div className="review_item">
                <div className="media">
                    <div className="d-flex">
                        <img src={this.getRandomAvatar()} alt=""/>
                    </div>
                    <div className="media-body">
                        <h4>{name}</h4>
                        <h5>{date}</h5>
                        <div dangerouslySetInnerHTML={{ __html: renderStarsByRating() }} />
                    </div>
                </div>
                <p>{message}</p>
            </div>
        )
    }
}

ProductDetailComment.propTypes = {
    name: PropTypes.string.isRequired,
    date: PropTypes.string.isRequired,
    message: PropTypes.string.isRequired,
    rating: PropTypes.number
};

export default ProductDetailComment;
