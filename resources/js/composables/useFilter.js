import { router, usePage } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { ref } from 'vue';

export const useFilter = () => {
    let filter = usePage().props.filter;

    const activeCategory = ref(filter?.category || 'all');
    let search = ref(filter?.search || '');

    const normalFilter = (key, value) => {
        let params = { ...usePage().props.filter };

        if (key == 'category') {
            value
                ? (activeCategory.value = value)
                : (activeCategory.value = 'all');
            params[key] = value;
            params['search'] || delete params['search'];
        } else {
            value ? (params[key] = value) : delete params[key];
            params['category'] || delete params['category'];
        }

        router.get(route('products'), params, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };

    const debounceFilter = debounce((key, value) => {
        normalFilter(key, value);
    }, 300);

    const filterBy = (key, value) => {
        if (key == 'category') {
            normalFilter(key, value);
        } else {
            debounceFilter(key, value);
        }
    };

    return {
        activeCategory,
        search,
        filterBy,
    };
};
