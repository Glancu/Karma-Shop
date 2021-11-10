import React, { Component } from 'react';
import axios from 'axios';
import PropTypes from 'prop-types';

class ShopCategoriesSidebar extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: []
        }
    }

    componentDidMount() {
        axios.get('/api/shop/categories/list', null)
            .then(result => {
                if(result.status === 200) {
                    this.setState({items: result.data});
                }
            })
            .catch((err) => {
                console.error(err);
            });
    }

    render() {
        const {items} = this.state;

        return (
            <div className="sidebar-categories">
                <div className="head">Browse Categories</div>
                <ul className="main-categories">
                    {(items && items.length > 0) &&
                        items.map((category) => {
                            let classes = 'nav-link';
                            if(this.props.categorySlug === category.slug) {
                                classes += ' active';
                            }

                            return (
                                <li className="main-nav-list" key={category.uuid}>
                                    <a className={classes} href={'/shop/category/' + category.slug}>
                                        {category.title}<span className="number">({category.countProducts})</span>
                                    </a>
                                </li>
                            )
                        })
                    }
                </ul>
            </div>
        )
    }
}

ShopCategoriesSidebar.propTypes = {
    categorySlug: PropTypes.string
}

export default ShopCategoriesSidebar;
