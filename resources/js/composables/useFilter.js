import { router, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { ref } from 'vue';

export const useFilter = () => {
    let filter = usePage().props.filter;

    const activeCategory = ref(filter?.category || 'all');
    let search = ref(filter?.search || '');
    let sortBy = ref(filter?.sortBy || 'default');
    const priceMax = ref(filter?.price || 0);
    const brand = ref(filter?.brand || null);

    const normalFilter = (url, key, value) => {
        let params = { ...usePage().props.filter, [key]: value || null };
        if (key == 'category') activeCategory.value = value || 'all';

        Object.keys(params).forEach((k) => {
            if (!params[k] || params[k] === null) delete params[k];
        });
        router.get(url, params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const debounceFilter = debounce((url, key, value) => {
        normalFilter(url, key, value);
    }, 300);

    const filterBy = (url, key, value) => {
        if (key == 'search' || key == 'price') {
            debounceFilter(url, key, value);
        } else {
            normalFilter(url, key, value);
        }
    };

    return {
        activeCategory,
        search,
        sortBy,
        priceMax,
        brand,
        filterBy,
    };
};
