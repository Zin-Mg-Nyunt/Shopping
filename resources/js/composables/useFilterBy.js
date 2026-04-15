import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash-es';

const useFilterBy = () => {
    const normalFilter = (filterKey, filterValue, routeName) => {
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

        router.get(route(routeName), newParams, {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        });
    };
    const debouncedFilter = debounce((filterKey, filterValue, routeName) => {
        normalFilter(filterKey, filterValue, routeName);
    }, 500);

    const filterBy = (filterKey, filterValue, routeName = 'products.list') => {
        if (filterKey === 'search' || filterKey === 'price') {
            debouncedFilter(filterKey, filterValue, routeName);
        } else {
            normalFilter(filterKey, filterValue, routeName);
        }
    };
    return { filterBy };
};
export default useFilterBy;
