export default {
    shop: {
        prefixPage: 'page',
        localStorageKey: 'shop_pagination',
        sortItems: {
            1: {
                title: 'Newset',
                value: '',
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
            1: 12,
            2: 21,
            3: 48
        }
    }
}
