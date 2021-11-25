import React, {Component} from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

class SinglePostList extends Component {
    render() {
        const {item} = this.props;

        return (
            <article className="row blog_item" key={item.uuid}>
                <div className="col-md-3">
                    <div className="blog_info text-right">
                        <div className="post_tag">
                            {
                                item.tags.map((tag, key) => (
                                    <Link to={`/blog/tag/${tag.slug}`} key={tag.uuid}>{tag.name}{key + 1 !== item.tags.length && ', '}</Link>
                                ))
                            }
                        </div>
                        <ul className="blog_meta list">
                            <li>
                                <a>
                                    {item.createdAt}<i className="lnr lnr-calendar-full"/>
                                </a>
                            </li>
                            <li><a>{item.views} Views<i className="lnr lnr-eye"/></a></li>
                            <li><a>{item.commentsCount} Comments<i className="lnr lnr-bubble"/></a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div className="col-md-9">
                    <div className="blog_post">
                        <Link to={`/blog/${item.slug}`}><img src={item.image.url} alt=""/></Link>
                        <div className="blog_details">
                            <Link to={`/blog/${item.slug}`}>
                                <h2>{item.title}</h2>
                            </Link>
                            <div dangerouslySetInnerHTML={{ __html: item.shortContent }} />
                            <Link to={`/blog/${item.slug}`} className="white_bg_btn">
                                View More
                            </Link>
                        </div>
                    </div>
                </div>
            </article>
        )
    }
}

SinglePostList.propTypes = {
    item: PropTypes.object.isRequired
}

export default SinglePostList;
