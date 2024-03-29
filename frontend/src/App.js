import React from 'react';
import Index from "./Pages/Index";
import { Route, BrowserRouter as Router, Switch, Redirect } from 'react-router-dom';
import Contact from "./Pages/Contact";
import {default as ShopCategory} from './Pages/Shop/Category';
import NotFound from './Pages/NotFound';
import ProductsList from './Pages/Shop/ProductsList';
import ProductDetail from './Pages/Shop/ProductDetail';
import Checkout from './Pages/Shop/Checkout';
import Cart from './Pages/Shop/Cart';
import Confirmation from './Pages/Shop/Confirmation';
import List from './Pages/Blog/List';
import Show from './Pages/Blog/Show';
import Login from './Pages/User/Login';
import Logout from './Pages/User/Logout';
import Register from './Pages/User/Register';
import ForgotPassword from './Pages/User/ForgotPassword';
import ScrollToTop from './Components/ScrollToTop';
import ShoppingCart from './Components/Shop/ShoppingCart';
import ChangePassword from './Pages/User/ChangePassword';
import ClientPanelRoute from './Routers/ClientPanelRoute';
import Orders from './Pages/User/Orders';
import {default as BlogCategory} from './Pages/Blog/Category';
import { default as BlogTag } from './Pages/Blog/Tag';
import Notify from './Pages/Payment/PayPal/Notify';

const App = () => (
    <Router>
        <ScrollToTop />
        <Switch>
            <HomepageRoute exact path='/' component={Index} />

            <Route path='/contact' component={Contact} />

            <Route path="/shop/product/:slug" component={ProductDetail}/>

            <ShopCheckoutRoute path="/shop/checkout" component={Checkout}/>

            <Route path="/shop/cart" component={Cart}/>
            <Route path="/shop/confirmation" component={Confirmation}/>
            <Route path="/shop/page/:page" component={ProductsList}/>
            <Route path="/shop/category/:slug" component={ShopCategory}/>
            <Route exact path="/shop" component={ProductsList}/>

            <Route path="/payment/pay-pal/notify" component={Notify}/>

            <Route path="/blog/page/:page" component={List}/>
            <Route path="/blog/category/:slug" component={BlogCategory}/>
            <Route path="/blog/tag/:slug" component={BlogTag}/>
            <Route path="/blog/:slug" component={Show}/>
            <Route path="/blog" component={List}/>

            <Route path="/login" component={Login}/>
            <Route path="/logout" component={Logout}/>
            <Route path="/register" component={Register}/>
            <Route path="/forgot-password" component={ForgotPassword}/>
            <ClientPanelAppRoute path="/user/panel" component={ChangePassword}/>
            <ClientPanelAppRoute path="/user/orders" component={Orders}/>

            <Route component={NotFound}/>
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

const ShopCheckoutRoute = ({component: Component, ...rest}) => {
    if(ShoppingCart.getCountProducts() === 0) {
        return <Redirect to='/' />
    }

    return <Route {...rest} render={(props) => (
        <Component {...props} />
    )} />
};

const ClientPanelAppRoute = ({component: Component, ...rest}) => {
    return <Route {...rest} render={(props) => (
        <ClientPanelRoute {...props} component={Component} />
    )} />
};

export default App;
