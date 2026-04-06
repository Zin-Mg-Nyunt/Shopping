import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const useFilterBy = () => {
    const normalFilter = (filterKey, filterValue) => {
        const currentParams = route().params;
        const newParams = { ...currentParams, [filterKey]: filterValue };
        delete newParams.page;

        Object.keys(newParams).forEach((key) => {
            if (
                newParams[key] == null ||
                newParams[key] === '' ||
                newParams[key] === 0
            ) {
                delete newParams[key];
            }
        });

        router.get(route('products.list'), newParams, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };
    const debouncedFilter = debounce((filterKey, filterValue) => {
        normalFilter(filterKey, filterValue);
    }, 500);

    const filterBy = (filterKey, filterValue) => {
        if (filterKey === 'search' || filterKey === 'price') {
            debouncedFilter(filterKey, filterValue);
        } else {
            normalFilter(filterKey, filterValue);
        }
    };
    return { filterBy };
};
export default useFilterBy;
