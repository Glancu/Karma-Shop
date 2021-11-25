import React, {Component} from 'react';
import axios from 'axios';
import { Link } from 'react-router-dom';

class BlogListCategoriesLatest extends Component {
    constructor(props) {
        super(props);

        this.state = {
            blogCategoriesLatest: []
        };
    }

    getBlogCategoriesLatest() {
        axios.get('/api/blog/categories/latest')
            .then(result => {
                if(result && result.data) {
                    this.setState({blogCategoriesLatest: result.data});
                }
            })
            .catch(err => {
                console.error(err)
            });
    }

    componentDidMount() {
        this.getBlogCategoriesLatest();
    }

    render() {
        const {blogCategoriesLatest} = this.state;

        const renderBlogCategoriesHTML = () => {
            let key = 0;

            return blogCategoriesLatest.map(item => {
                key++;
                const image = `/assets/img/blog/cat-post/cat-post-${key}.jpg`;
                return (
                    <div className="col-lg-4" key={item.uuid}>
                        <div className="categories_post">
                            <img src={image} alt="category"/>
                            <Link to={`/blog/category/${item.slug}`}>
                                <div className="categories_details">
                                    <div className="categories_text">
                                        <h5>{item.name}</h5>
                                    </div>
                                </div>
                            </Link>
                        </div>
                    </div>
                )
            })
        };

        return renderBlogCategoriesHTML();
    }
}

export default BlogListCategoriesLatest;
