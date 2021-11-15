import React from 'react';

class ShoppingCart {
    static localStorageShopKeyName = 'shop';

    static getLocalStorageShop() {
        let localStorageShop = window.localStorage.getItem(this.localStorageShopKeyName);
        if(!JSON.parse(localStorageShop)) {
            window.localStorage.setItem(this.localStorageShopKeyName, JSON.stringify({products: [], form: {}}));
            localStorageShop = window.localStorage.getItem(this.localStorageShopKeyName);
        }

        return localStorageShop;
    }

    static addProductToCart(product, quantity = 1) {
        if(product) {
            const localStorageShop = this.getLocalStorageShop();

            const parsedLocalStorageShop = JSON.parse(localStorageShop);
            if(parsedLocalStorageShop.hasOwnProperty('products')) {
                const localStorageProducts = parsedLocalStorageShop.products;

                const productInLocalStorage = Object.values(localStorageProducts).find(item => item && (item.uuid === product.uuid));
                if(!productInLocalStorage) {
                    const productObjToLocalStorage = {
                        uuid: product.uuid,
                        name: product.name,
                        slug: product.slug,
                        priceGross: product.priceGross,
                        priceNet: product.priceNet,
                        quantity: parseInt(quantity),
                        image: product.images[0]
                    };

                    localStorageProducts.push(productObjToLocalStorage);

                    window.localStorage.setItem('shop', JSON.stringify({products: localStorageProducts}));

                    return {newProductInCart: true, quantity};
                } else {
                    const productQuantity = this.increaseQuantityOfProduct(product);

                    return {newProductInCart: false, quantity: productQuantity};
                }
            }
        }
    }

    static increaseQuantityOfProduct(product) {
        let productQuantity = 1;
        const localStorageShop = this.getLocalStorageShop();

        const parsedLocalStorageShop = JSON.parse(localStorageShop);
        if(parsedLocalStorageShop.hasOwnProperty('products')) {
            const localStorageProducts = parsedLocalStorageShop.products;

            const updatedProducts = localStorageProducts.map((item) =>  {
                if(item) {
                    if(item.uuid === product.uuid) {
                        if(item.quantity + 1 <= 10) {
                            item.quantity++;
                        }
                        productQuantity = item.quantity;
                        return item;
                    } else {
                        return item;
                    }
                }
            });

            window.localStorage.setItem('shop', JSON.stringify({products: updatedProducts}));
        }

        return productQuantity;
    }

    static decreaseQuantityOfProduct(product) {
        const localStorageShop = this.getLocalStorageShop();

        const parsedLocalStorageShop = JSON.parse(localStorageShop);
        if(parsedLocalStorageShop.hasOwnProperty('products')) {
            const localStorageProducts = parsedLocalStorageShop.products;

            const updatedProducts = localStorageProducts.map(item => {
                if(item.uuid === product.uuid) {
                    if(item.quantity > 1) {
                        return {...item, quantity: item.quantity - 1}
                    } else {
                        this.removeProductFromCart(product);
                        return null;
                    }
                } else {
                    return item;
                }
            }).filter(item => {return item});

            window.localStorage.setItem('shop', JSON.stringify({products: updatedProducts}));
        }
    }

    static removeProductFromCart(product) {
        const localStorageShop = this.getLocalStorageShop();

        const parsedLocalStorageShop = JSON.parse(localStorageShop);
        if(parsedLocalStorageShop.hasOwnProperty('products')) {
            const localStorageProducts = parsedLocalStorageShop.products;

            const productInLocalStorage = Object.values(localStorageProducts).find(item => item.uuid === product.uuid);
            if(productInLocalStorage) {
                const index = localStorageProducts.indexOf(productInLocalStorage);
                localStorageProducts.splice(index, 1);

                window.localStorage.setItem('shop', JSON.stringify({products: localStorageProducts}));
            }
        }
    }

    static updateQuantityOfProduct(product, quantity) {
        const localStorageShop = this.getLocalStorageShop();

        const parsedLocalStorageShop = JSON.parse(localStorageShop);
        if(parsedLocalStorageShop.hasOwnProperty('products')) {
            const localStorageProducts = parsedLocalStorageShop.products;

            const updatedProducts = localStorageProducts.map(item => {
                if(item.uuid === product.uuid) {
                    return {...item, quantity}
                } else {
                    return item;
                }
            }).filter(item => {return item});

            window.localStorage.setItem('shop', JSON.stringify({products: updatedProducts}));
        }
    }

    static getProducts() {
        const localStorageShop = this.getLocalStorageShop();

        const parsedLocalStorageShop = JSON.parse(localStorageShop);
        if(!parsedLocalStorageShop.hasOwnProperty('products')) {
            return [];
        }

        return parsedLocalStorageShop.products;
    }

    static getCountProducts() {
        return this.getProducts().length;
    }

    static getTotalPrice() {
        const products = this.getProducts();
        let priceSum = 0;

        products.map(product => {
            priceSum += parseFloat(product.priceGross) * product.quantity;
        });

        priceSum = priceSum.toFixed(2);

        return priceSum;
    }

    static findProductByProductUuid(productUuid) {
        const products = this.getProducts();
        return products.find(product => product.uuid === productUuid);
    }
}

export default ShoppingCart;
