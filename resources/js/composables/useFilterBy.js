import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const useFilterBy = () => {
    const useFilter = (filterKey, filterValue) => {
        router.get(
            route('products.list'),
            { [filterKey]: filterValue },
            {
                preserveState: true,
                preserveScroll: true,
                replace: true,
            },
        );
    };
    const filterBy = (filterKey, filterValue) => {
        if (filterKey === 'search') {
            debounce(() => {
                useFilter(filterKey, filterValue);
            }, 500);
        } else {
            useFilter(filterKey, filterValue);
        }
    };
    return { filterBy };
};
export default useFilterBy;
