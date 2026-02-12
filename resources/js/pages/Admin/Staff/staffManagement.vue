<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
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
import { getInitials } from '@/composables/useInitials';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import {
    MoreHorizontal,
    Pencil,
    ShieldCheck,
    Trash2,
    UserPlus,
} from 'lucide-vue-next';
import { ref } from 'vue';

defineOptions({
    layout: AdminLayout,
});

const props = defineProps({
    staff: {
        type: Array,
        default: () => [],
    },
});

// =============================================================================
// MOCK DATA (UI ONLY) — Replace with backend props when API is ready
// =============================================================================

const defaultStaff = [
    {
        id: 1,
        name: 'Admin',
        email: 'admin@gmail.com',
        role: 'super_admin',
        status: true,
        lastLogin: '2025-02-13 09:30',
    },
    {
        id: 2,
        name: 'John Manager',
        email: 'john@example.com',
        role: 'manager',
        status: true,
        lastLogin: '2025-02-12 14:22',
    },
    {
        id: 3,
        name: 'Sarah Support',
        email: 'sarah@example.com',
        role: 'support',
        status: false,
        lastLogin: '2025-02-10 11:05',
    },
];

const staffList = ref(
    props.staff?.length
        ? props.staff.map((s) => ({
              ...s,
              status: s.status ?? true,
              lastLogin: s.last_login ?? s.lastLogin ?? null,
          }))
        : defaultStaff,
);

const roles = [
    { value: 'super_admin', label: 'Super Admin' },
    { value: 'manager', label: 'Manager' },
    { value: 'support', label: 'Support' },
];

const permissions = [
    { id: 'manage_products', label: 'Manage Products' },
    { id: 'view_orders', label: 'View Orders' },
    { id: 'manage_orders', label: 'Manage Orders' },
    { id: 'access_settings', label: 'Access Settings' },
    { id: 'manage_staff', label: 'Manage Staff' },
    { id: 'view_analytics', label: 'View Analytics' },
];

const panelOpen = ref(false);
const isEditing = ref(false);
const editingStaff = ref(null);

const formName = ref('');
const formEmail = ref('');
const formPassword = ref('');
const formRole = ref('support');
const formPermissions = ref([]);

const selectInputClass =
    'h-10 w-full rounded-lg border border-gray-300 bg-white pl-4 pr-10 text-sm text-gray-700 focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200';

function getRoleBadgeClass(role) {
    switch (role) {
        case 'super_admin':
            return 'bg-violet-500/15 text-violet-400 border-violet-500/30';
        case 'manager':
            return 'bg-blue-500/15 text-blue-400 border-blue-500/30';
        case 'support':
            return 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30';
        default:
            return 'bg-gray-500/15 text-gray-400 border-gray-500/30';
    }
}

function formatRole(role) {
    return roles.find((r) => r.value === role)?.label ?? role;
}

function formatLastLogin(dateStr) {
    if (!dateStr) return '—';
    try {
        const d = new Date(dateStr);
        if (Number.isNaN(d.getTime())) return dateStr;
        return d.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return dateStr;
    }
}

function openAddPanel() {
    isEditing.value = false;
    editingStaff.value = null;
    formName.value = '';
    formEmail.value = '';
    formPassword.value = '';
    formRole.value = 'support';
    formPermissions.value = [];
    panelOpen.value = true;
}

function openEditPanel(staff) {
    isEditing.value = true;
    editingStaff.value = staff;
    formName.value = staff.name;
    formEmail.value = staff.email;
    formPassword.value = '';
    formRole.value = staff.role || 'support';
    formPermissions.value = staff.permissions ?? ['view_orders'];
    panelOpen.value = true;
}

function closePanel() {
    panelOpen.value = false;
    editingStaff.value = null;
}

function toggleStatus(staff) {
    staff.status = !staff.status;
}

function handleSubmit() {
    // UI only — no backend
    closePanel();
}

function isPermissionChecked(permId) {
    return formPermissions.value.includes(permId);
}

function onPermissionChange(permId, checked) {
    if (checked) {
        formPermissions.value = [...formPermissions.value, permId];
    } else {
        formPermissions.value = formPermissions.value.filter(
            (p) => p !== permId,
        );
    }
}
</script>

<template>
    <Head title="Staff Management - Admin" />
    <div class="space-y-6 font-[Inter,sans-serif]">
        <!-- Top Header -->
        <div
            class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <div>
                <h1
                    class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white"
                >
                    Staff Management
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage staff members, roles, and permissions
                </p>
            </div>
            <Button
                class="h-10 shrink-0 gap-2"
                @click="openAddPanel"
            >
                <UserPlus class="h-4 w-4" />
                Add New Staff
            </Button>
        </div>

        <!-- Staff Table -->
        <div
            class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-800 dark:bg-gray-900/80"
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
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Profile
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Role
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </th>
                            <th
                                class="px-4 py-3.5 font-medium text-gray-700 dark:text-gray-300"
                            >
                                Last Login
                            </th>
                            <th
                                class="w-28 px-4 py-3.5 text-right font-medium text-gray-700 dark:text-gray-300"
                                aria-label="Actions"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="staff in staffList"
                            :key="staff.id"
                            class="border-b border-gray-100 transition-colors duration-150 last:border-b-0 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-800/50"
                        >
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <Avatar class="h-10 w-10 shrink-0">
                                        <AvatarImage
                                            :src="staff.avatar"
                                            :alt="staff.name"
                                        />
                                        <AvatarFallback
                                            class="bg-primary/10 text-sm font-medium text-primary"
                                        >
                                            {{ getInitials(staff.name) }}
                                        </AvatarFallback>
                                    </Avatar>
                                    <div>
                                        <p
                                            class="font-medium text-gray-900 dark:text-white"
                                        >
                                            {{ staff.name }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ staff.email }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <span
                                    :class="[
                                        'inline-flex items-center gap-1.5 rounded-full border px-2.5 py-0.5 text-xs font-medium',
                                        getRoleBadgeClass(staff.role),
                                    ]"
                                >
                                    <ShieldCheck class="h-3 w-3" />
                                    {{ formatRole(staff.role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3.5">
                                <button
                                    type="button"
                                    role="switch"
                                    :aria-checked="staff.status"
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-gray-900"
                                    :class="
                                        staff.status
                                            ? 'bg-primary'
                                            : 'bg-gray-200 dark:bg-gray-700'
                                    "
                                    @click="toggleStatus(staff)"
                                >
                                    <span
                                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition-transform"
                                        :class="
                                            staff.status
                                                ? 'translate-x-5'
                                                : 'translate-x-0.5'
                                        "
                                    />
                                </button>
                            </td>
                            <td
                                class="px-4 py-3.5 text-gray-600 dark:text-gray-400"
                            >
                                {{ formatLastLogin(staff.lastLogin) }}
                            </td>
                            <td class="px-4 py-3.5">
                                <div
                                    class="flex items-center justify-end gap-1"
                                >
                                    <Button
                                        variant="ghost"
                                        size="sm"
                                        class="h-8 gap-1.5 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white"
                                        @click="openEditPanel(staff)"
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
                                                @click="openEditPanel(staff)"
                                            >
                                                <Pencil class="mr-2 h-4 w-4" />
                                                Edit Staff
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
                v-if="staffList.length === 0"
                class="px-4 py-16 text-center"
            >
                <UserPlus
                    class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"
                    aria-hidden="true"
                />
                <p
                    class="mt-4 text-sm font-medium text-gray-900 dark:text-white"
                >
                    No staff members yet
                </p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Add your first staff member to get started
                </p>
                <Button
                    class="mt-4 gap-2"
                    @click="openAddPanel"
                >
                    <UserPlus class="h-4 w-4" />
                    Add New Staff
                </Button>
            </div>
        </div>

        <!-- Add/Edit Staff — Slide-over Panel -->
        <Sheet v-model:open="panelOpen">
            <SheetContent
                side="right"
                class="flex flex-col sm:max-w-lg"
            >
                <SheetHeader>
                    <SheetTitle>
                        {{ isEditing ? 'Edit Staff' : 'Add New Staff' }}
                    </SheetTitle>
                    <SheetDescription class="sr-only">
                        Form to {{ isEditing ? 'update' : 'create' }} a staff
                        member with general info, role, and permissions
                    </SheetDescription>
                </SheetHeader>
                <form
                    class="flex flex-1 flex-col gap-6 overflow-y-auto p-2"
                    @submit.prevent="handleSubmit"
                >
                    <!-- General Info -->
                    <div class="space-y-4">
                        <h3
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            General Info
                        </h3>
                        <div class="space-y-2">
                            <Label for="staff-name">Name</Label>
                            <Input
                                id="staff-name"
                                v-model="formName"
                                type="text"
                                placeholder="e.g. John Doe"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="staff-email">Email</Label>
                            <Input
                                id="staff-email"
                                v-model="formEmail"
                                type="email"
                                placeholder="e.g. john@example.com"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            />
                        </div>
                        <div class="space-y-2">
                            <Label for="staff-password">
                                Password
                                <span
                                    v-if="isEditing"
                                    class="text-xs font-normal text-gray-500"
                                >
                                    (leave blank to keep current)
                                </span>
                            </Label>
                            <Input
                                id="staff-password"
                                v-model="formPassword"
                                type="password"
                                placeholder="••••••••"
                                autocomplete="new-password"
                                class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                            />
                        </div>
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-2">
                        <Label for="staff-role">Role</Label>
                        <select
                            id="staff-role"
                            v-model="formRole"
                            :class="selectInputClass"
                            aria-label="Staff role"
                        >
                            <option
                                v-for="r in roles"
                                :key="r.value"
                                :value="r.value"
                            >
                                {{ r.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Permissions Grid -->
                    <div class="space-y-3">
                        <h3
                            class="text-sm font-semibold text-gray-900 dark:text-white"
                        >
                            Permissions
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Assign specific access for this staff member
                        </p>
                        <div
                            class="grid grid-cols-1 gap-2 rounded-lg border border-gray-200 bg-gray-50/50 p-3 dark:border-gray-800 dark:bg-gray-800/50"
                        >
                            <label
                                v-for="perm in permissions"
                                :key="perm.id"
                                class="flex cursor-pointer items-center gap-2 rounded-md px-2 py-1.5 transition-colors hover:bg-gray-100 dark:hover:bg-gray-700/50"
                            >
                                <Checkbox
                                    :checked="isPermissionChecked(perm.id)"
                                    @update:checked="
                                        (v) => onPermissionChange(perm.id, v)
                                    "
                                />
                                <span
                                    class="text-sm text-gray-700 dark:text-gray-300"
                                >
                                    {{ perm.label }}
                                </span>
                            </label>
                        </div>
                    </div>

                    <SheetFooter
                        class="mt-auto gap-2 space-y-2 border-t pt-4 sm:gap-0"
                    >
                        <Button
                            type="button"
                            variant="outline"
                            @click="closePanel"
                        >
                            Cancel
                        </Button>
                        <Button type="submit">
                            {{ isEditing ? 'Save Changes' : 'Add Staff' }}
                        </Button>
                    </SheetFooter>
                </form>
            </SheetContent>
        </Sheet>
    </div>
</template>
