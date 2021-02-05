import React from 'react';
import Index from "./Pages/Index";
import { Route, BrowserRouter as Router, Switch, Redirect } from 'react-router-dom';
import Contact from "./Pages/Contact";
import Category from './Pages/Shop/Category';
import NotFound from './Pages/NotFound';
import ProductDetail from './Pages/Shop/ProductDetail';
import Checkout from './Pages/Shop/Checkout';
import Cart from './Pages/Shop/Cart';
import Confirmation from './Pages/Shop/Confirmation';
import Tracking from './Pages/Shop/Tracking';
import List from './Pages/Blog/List';
import Show from './Pages/Blog/Show';
import Login from './Pages/User/Login';
import Logout from './Pages/User/Logout';
import Register from './Pages/User/Register';
import Elements from './Pages/Elements';
import ScrollToTop from './Components/ScrollToTop';

const App = () => (
    <Router>
        <ScrollToTop />
        <Switch>
            <HomepageRoute exact path='/' component={Index} />

            <Route path='/contact' component={Contact} />

            <Route path='/shop/category' component={Category} />
            <Route path='/shop/product' component={ProductDetail} />
            <Route path='/shop/checkout' component={Checkout} />
            <Route path='/shop/cart' component={Cart} />
            <Route path='/shop/confirmation' component={Confirmation} />
            <Route path='/shop/tracking' component={Tracking} />

            <Route path='/blog/:id' component={Show} />
            <Route path='/blog' component={List} />

            <Route path='/login' component={Login} />
            <Route path='/logout' component={Logout} />
            <Route path='/register' component={Register} />

            <Route path='/elements' component={Elements} />

            <Route component={NotFound} />
        </Switch>
    </Router>
);

const HomepageRoute = ({component: Component, ...rest}) => {
    if(rest.path !== rest.location.pathname) {
        return <Redirect to='/' />
    }

    return <Route {...rest} render={(props) => (
        <Component {...props} />
    )} />
};

export default App;
