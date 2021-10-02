export default {
    shop: {
        prefixPage: 'page',
        sortItems: {
            1: {
                title: 'Newset',
                value: 'newset',
                order: 'DESC'
            },
            2: {
                title: 'Price: Low to High',
                value: 'price',
                order: 'ASC'
            },
            3: {
                title: 'Price: High to Low',
                value: 'price',
                order: 'DESC'
            }
        },
        sortPerPage: {
            1: 1,
            2: 2,
            3: 4
        },
        filters: {
            brand: null,
            color: null,
            priceFrom: 0,
            priceTo: 2000
        },
        currencySymbol: process.env.CURRENCY_SYMBOL
    }
}
