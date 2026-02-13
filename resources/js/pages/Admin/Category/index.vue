<script setup>
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Sheet,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from '@/components/ui/sheet';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    FolderTree,
    MoreHorizontal,
    Pencil,
    Plus,
    Search,
    Trash2,
} from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({
    layout: AdminLayout,
});

// =============================================================================
// MOCK DATA (UI ONLY) — Replace with backend props when API is ready
// =============================================================================

const categories = [
    {
        id: 1,
        name: 'Electronics',
        slug: 'electronics',
        productCount: 42,
        status: 'active',
        icon: 'FolderTree',
    },
    {
        id: 2,
        name: 'Clothing',
        slug: 'clothing',
        productCount: 128,
        status: 'active',
        icon: 'FolderTree',
    },
    {
        id: 3,
        name: 'Home & Garden',
        slug: 'home-garden',
        productCount: 56,
        status: 'active',
        icon: 'FolderTree',
    },
    {
        id: 4,
        name: 'Seasonal (Archived)',
        slug: 'seasonal-archived',
        productCount: 20,
        status: 'hidden',
        icon: 'FolderTree',
    },
    {
        id: 5,
        name: 'Books & Media',
        slug: 'books-media',
        productCount: 89,
        status: 'active',
        icon: 'FolderTree',
    },
];

const searchQuery = ref('');
const addPanelOpen = ref(false);
const formName = ref('');
const formSlug = ref('');

const editPanelOpen = ref(false);
const editingCategory = ref(null);
const editFormName = ref('');
const editFormSlug = ref('');

function getStatusBadgeClass(status) {
    return status === 'active'
        ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30'
        : 'bg-gray-500/15 text-gray-400 border-gray-500/30';
}

function openAddPanel() {
    formName.value = '';
    formSlug.value = '';
    addPanelOpen.value = true;
}

function closeAddPanel() {
    addPanelOpen.value = false;
}

function handleSubmitAdd() {
    // UI only — no backend
    closeAddPanel();
}

function openEditPanel(category) {
    editingCategory.value = category;
    editFormName.value = category.name;
    editFormSlug.value = category.slug;
    editPanelOpen.value = true;
}

function closeEditPanel() {
    editPanelOpen.value = false;
    editingCategory.value = null;
}

function handleSubmitEdit() {
    // UI only — no backend
    closeEditPanel();
}
</script>

<template>
    <Head title="Categories - Admin" />
    <div class="space-y-6 font-[Inter,sans-serif]">
        <!-- Page Header -->
        <div>
            <h1
                class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
            >
                Categories
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage product categories and their visibility
            </p>
        </div>

        <!-- Top Bar: Search + Add New Category -->
        <div
            class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
        >
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div class="relative max-w-md flex-1">
                    <Search
                        class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400"
                        aria-hidden="true"
                    />
                    <Input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search categories..."
                        class="h-10 w-full rounded-lg border-gray-300 bg-white pr-4 pl-10 text-sm focus:border-primary focus:ring-1 focus:ring-primary dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                        aria-label="Search categories"
                    />
                </div>
                <Button
                    class="h-10 gap-2"
                    @click="openAddPanel"
                >
                    <Plus class="h-4 w-4" />
                    Add New Category
                </Button>
            </div>
        </div>

        <!-- Categories Table -->
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/50"
        >
            <div class="overflow-x-auto">
                <table
                    class="w-full min-w-[800px] border-collapse text-left text-sm"
                    role="table"
                >
                    <thead>
                        <tr
                            class="border-b border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-800/50"
                        >
                            <th
                                class="w-14 px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Icon
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Category Name
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Slug
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Product Count
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </th>
                            <th
                                class="w-32 px-4 py-3.5 text-right font-medium text-gray-700 dark:text-gray-300"
                                aria-label="Actions"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="category in categories"
                            :key="category.id"
                            class="border-b border-gray-100 transition-colors duration-150 last:border-b-0 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
                                >
                                    <FolderTree
                                        class="h-5 w-5"
                                        aria-hidden="true"
                                    />
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <p
                                    class="font-medium text-gray-900 dark:text-white"
                                >
                                    {{ category.name }}
                                </p>
                            </td>
                            <td class="px-4 py-3.5">
                                <p
                                    class="font-mono text-xs text-gray-600 dark:text-gray-400"
                                >
                                    {{ category.slug }}
                                </p>
                            </td>
                            <td
                                class="px-4 py-3.5 text-gray-700 dark:text-gray-300"
                            >
                                {{ category.productCount }} products
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    :class="[
                                        'inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize',
                                        getStatusBadgeClass(category.status),
                                    ]"
                                >
                                    {{ category.status }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 gap-1.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                                        @click="openEditPanel(category)"
                                    >
                                        <Pencil class="h-3.5 w-3.5" />
                                        Edit
                                    </Button>
                                    <DropdownMenu>
                                        <DropdownMenuTrigger as-child>
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                class="h-8 w-8 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                                aria-label="More options"
                                            >
                                                <MoreHorizontal
                                                    class="h-4 w-4"
                                                    aria-hidden="true"
                                                />
                                            </Button>
                                        </DropdownMenuTrigger>
                                        <DropdownMenuContent
                                            align="end"
                                            class="w-44"
                                        >
                                            <DropdownMenuItem
                                                @click="openEditPanel(category)"
                                            >
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit Category
                                            </DropdownMenuItem>
                                            <DropdownMenuItem
                                                variant="destructive"
                                            >
                                                <Trash2 class="mr-2 h-4 w-4" />
                                                Delete
                                            </DropdownMenuItem>
                                        </DropdownMenuContent>
                                    </DropdownMenu>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty state -->
            <div
                v-if="categories.length === 0"
                class="px-4 py-16 text-center"
            >
                <FolderTree
                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                    aria-hidden="true"
                />
                <p
                    class="mt-4 text-sm font-medium text-gray-900 dark:text-white"
                >
                    No categories found
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Add your first category to get started
                </p>
                <Button
                    class="mt-4 gap-2"
                    @click="openAddPanel"
                >
                    <Plus class="h-4 w-4" />
                    Add New Category
                </Button>
            </div>
        </div>

        <!-- Add New Category — Slide-over Panel -->
        <Sheet v-model:open="addPanelOpen">
            <SheetContent
                side="right"
                class="sm:max-w-md"
            >
                <SheetHeader>
                    <SheetTitle>Add New Category</SheetTitle>
                    <SheetDescription class="sr-only">
                        Form to create a new category with name and slug
                    </SheetDescription>
                </SheetHeader>
                <form
                    class="flex flex-col gap-6 p-6"
                    @submit.prevent="handleSubmitAdd"
                >
                    <div class="space-y-2">
                        <Label for="category-name">Name</Label>
                        <Input
                            id="category-name"
                            v-model="formName"
                            type="text"
                            placeholder="e.g. Electronics"
                            class="w-full"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="category-slug">Slug</Label>
                        <Input
                            id="category-slug"
                            v-model="formSlug"
                            type="text"
                            placeholder="e.g. electronics"
                            class="w-full font-mono text-sm"
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            URL-friendly identifier; leave empty to
                            auto-generate from name
                        </p>
                    </div>
                    <SheetFooter class="space-y-2 sm:space-y-0">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeAddPanel"
                        >
                            Cancel
                        </Button>
                        <Button type="submit"> Add Category </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>

        <!-- Edit Category — Slide-over Panel -->
        <Sheet v-model:open="editPanelOpen">
            <SheetContent
                side="right"
                class="sm:max-w-md"
            >
                <SheetHeader>
                    <SheetTitle>Edit Category</SheetTitle>
                    <SheetDescription class="sr-only">
                        Form to update category name and slug
                    </SheetDescription>
                </SheetHeader>
                <form
                    class="flex flex-col gap-6 p-6"
                    @submit.prevent="handleSubmitEdit"
                >
                    <div class="space-y-2">
                        <Label for="edit-category-name">Name</Label>
                        <Input
                            id="edit-category-name"
                            v-model="editFormName"
                            type="text"
                            placeholder="e.g. Electronics"
                            class="w-full"
                        />
                    </div>
                    <div class="space-y-2">
                        <Label for="edit-category-slug">Slug</Label>
                        <Input
                            id="edit-category-slug"
                            v-model="editFormSlug"
                            type="text"
                            placeholder="e.g. electronics"
                            class="w-full font-mono text-sm"
                        />
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            URL-friendly identifier
                        </p>
                    </div>
                    <SheetFooter class="space-y-2 sm:space-y-0">
                        <Button
                            type="button"
                            variant="outline"
                            @click="closeEditPanel"
                        >
                            Cancel
                        </Button>
                        <Button type="submit"> Save Changes </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    </div>
</template>
