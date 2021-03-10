import React, { Component } from 'react';
import axios from 'axios';

class ShopCategoriesSidebar extends Component {
    constructor(props) {
        super(props);
        this.state = {
            items: []
        }
    }

    componentDidMount() {
        axios.get('/api/categories/list', null)
            .then(result => {
                if(result.status === 200) {
                    this.setState({items: result.data});
                }
            })
            .catch((err) => {
                console.log(err);
            });
    }

    render() {
        const {items} = this.state;

        return (
            <div className="sidebar-categories">
                <div className="head">Browse Categories</div>
                <ul className="main-categories">
                    {items && items.length > 0 ?
                        items.map((category) => (
                            <li className="main-nav-list" key={category.uuid}>
                                <a href="#">{category.title}<span className="number">({category.countProducts})</span></a>
                            </li>
                        )) :
                        null
                    }
                </ul>
            </div>
        )
    }
}

export default ShopCategoriesSidebar;
