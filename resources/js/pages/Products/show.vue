<script setup>
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { useAddToCart } from '@/composables/useAddToCart';
import { Head, Link } from '@inertiajs/vue3';
import {
    ChevronRight,
    Minus,
    Package,
    Plus,
    RefreshCw,
    Share2,
    ShoppingCart,
    Star,
    Truck,
} from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps({
    product: Object,
});

const quantity = ref(1);

// Static UI setup: which product tab is active (Description / Specifications / Shipping & Returns)
const activeTab = ref('description');

const displayPrice = props.product?.discount_price ?? props.product?.price;
const originalPrice = props.product?.discount_price
    ? props.product?.price
    : null;
const discountPercent = props.product?.discount_price
    ? Math.round(
          ((Number(originalPrice) - Number(displayPrice)) /
              Number(originalPrice)) *
              100,
      )
    : 0;

// Placeholder thumbnails for gallery (same as main when no separate images). TODO: Replace with actual product images array when available.
const galleryThumbnails = [
    props.product?.thumbnail,
    'https://res.cloudinary.com/rsc/image/upload/b_rgb:FFFFFF,c_pad,dpr_2.625,f_auto,h_214,q_auto,w_380/c_pad,h_214,w_380/Y2444576-01?pgw=1',
    'https://m.media-amazon.com/images/I/81hDMqd2KFL.jpg',
    'https://i5.walmartimages.com/asr/62a9abd1-926c-4ff9-8f93-a3c2f4d4314d_1.b263a12f4422d31e6c24560b928b4418.jpeg',
].filter(Boolean);

// Placeholder rating breakdown (5–1 stars with percentage). TODO: Populate from dynamic rating data (e.g. API).
const ratingBreakdown = [
    { stars: 5, percent: 60, count: 12 },
    { stars: 4, percent: 25, count: 5 },
    { stars: 3, percent: 10, count: 2 },
    { stars: 2, percent: 3, count: 1 },
    { stars: 1, percent: 2, count: 0 },
];

// Placeholder reviews for list structure. TODO: Replace with actual review data and v-for over reviews.
const placeholderReviews = [
    {
        id: 1,
        userName: 'Jane D.',
        rating: 5,
        date: 'Jan 15, 2025',
        text: 'Great quality and fast shipping. Very happy with this purchase.',
    },
    {
        id: 2,
        userName: 'Alex M.',
        rating: 4,
        date: 'Jan 10, 2025',
        text: 'Good product overall. Would recommend to others.',
    },
];

const { addToCart } = useAddToCart();

const copyToClipboard = async () => {
    const shareData = {
        title: props.product?.name,
        text: 'Check out this product!',
        url: window.location.href,
    };
    try {
        if (navigator.share) {
            await navigator.share(shareData);
        } else {
            await navigator.clipboard.writeText(shareData.url);
            toast.success('Link copied to clipboard');
        }
    } catch (error) {
        console.error('Failed to share or copy:', error);
    }
};
</script>

<template>
    <Head :title="product?.name ?? 'Product'" />
    <div
        class="min-h-screen bg-background text-foreground dark:bg-slate-950 dark:text-slate-100"
    >
        <div class="container mx-auto px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
            <!-- Breadcrumb -->
            <nav
                class="mb-4 flex items-center gap-1.5 text-sm text-muted-foreground sm:mb-6 dark:text-slate-400"
                aria-label="Breadcrumb"
            >
                <Link
                    :href="route('home')"
                    class="inline-flex items-center gap-1.5 rounded px-1 py-0.5 transition-colors hover:text-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:hover:text-slate-100"
                >
                    Home
                </Link>
                <ChevronRight
                    class="size-4 shrink-0 text-muted-foreground/60 dark:text-slate-500"
                    aria-hidden="true"
                />
                <Link
                    :href="route('products')"
                    class="inline-flex items-center gap-1.5 rounded px-1 py-0.5 transition-colors hover:text-foreground focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:hover:text-slate-100"
                >
                    Products
                </Link>
                <ChevronRight
                    class="size-4 shrink-0 text-muted-foreground/60 dark:text-slate-500"
                    aria-hidden="true"
                />
                <span
                    class="max-w-32 truncate font-medium text-foreground sm:max-w-48 dark:text-slate-100"
                    aria-current="page"
                >
                    {{ product?.name }}
                </span>
            </nav>

            <!-- Main content: Image gallery + Details -->
            <div class="grid gap-6 lg:grid-cols-2 lg:gap-10 xl:gap-16">
                <!-- 1. Interactive Image Gallery -->
                <div class="space-y-3 sm:space-y-4">
                    <!-- Main product image. TODO: Add your logic here — bind main image src to selected thumbnail state; add transition effect (e.g. fade) when image changes. -->
                    <div
                        class="relative aspect-square overflow-hidden rounded-2xl border border-border bg-muted shadow-sm dark:border-slate-800 dark:bg-slate-900/50"
                        role="img"
                        :aria-label="product?.name ?? 'Product image'"
                    >
                        <img
                            v-if="product?.thumbnail"
                            :src="product.thumbnail"
                            :alt="product.name"
                            class="h-full w-full object-cover"
                        />
                        <div
                            v-else
                            class="flex h-full w-full items-center justify-center text-muted-foreground"
                        >
                            No image
                        </div>
                        <div
                            v-if="discountPercent > 0"
                            class="absolute top-3 left-3 sm:top-4 sm:left-4"
                        >
                            <Badge
                                variant="destructive"
                                class="bg-primary text-primary-foreground shadow"
                            >
                                {{ discountPercent }}% OFF
                            </Badge>
                        </div>
                        <div
                            v-if="product?.isNew"
                            class="absolute top-3 right-3 sm:top-4 sm:right-4"
                        >
                            <Badge
                                class="bg-emerald-500 text-white shadow dark:bg-emerald-600"
                            >
                                New
                            </Badge>
                        </div>
                    </div>

                    <div
                        class="scrollbar-thin flex gap-2 overflow-x-auto pb-1"
                        role="list"
                        aria-label="Product image thumbnails"
                    >
                        <button
                            v-for="(thumb, index) in galleryThumbnails"
                            :key="index"
                            type="button"
                            class="h-16 w-16 shrink-0 overflow-hidden rounded-lg border-2 border-border bg-muted transition-colors hover:border-primary focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:border-slate-800 dark:bg-slate-900/50"
                            :aria-label="`View image ${index + 1}`"
                            :aria-pressed="index === 0"
                            @click="product.thumbnail = thumb"
                        >
                            <img
                                :src="thumb"
                                :alt="`Thumbnail ${index + 1}`"
                                class="h-full w-full object-cover"
                            />
                        </button>
                    </div>
                </div>

                <!-- Product details column -->
                <div class="flex flex-col">
                    <h1
                        class="font-serif text-2xl font-bold tracking-tight text-foreground sm:text-3xl lg:text-4xl dark:text-slate-50"
                    >
                        {{ product?.name }}
                    </h1>

                    <!-- Rating -->
                    <div
                        v-if="product?.rating != null"
                        class="mt-2 flex items-center gap-2 text-sm text-muted-foreground sm:mt-3 dark:text-slate-400"
                    >
                        <div
                            class="flex items-center gap-0.5"
                            aria-hidden="true"
                        >
                            <Star
                                v-for="i in 5"
                                :key="i"
                                class="size-4"
                                :class="
                                    i <= Math.floor(product.rating)
                                        ? 'fill-amber-400 text-amber-400'
                                        : 'text-muted-foreground/40'
                                "
                            />
                        </div>
                        <span>
                            {{ product.rating?.toFixed(1) ?? product.rating }}
                            ({{ product.reviews ?? 0 }} reviews)
                        </span>
                    </div>

                    <!-- Price -->
                    <div
                        class="mt-4 flex flex-wrap items-baseline gap-2 sm:mt-6 sm:gap-3"
                    >
                        <span
                            class="text-2xl font-bold text-primary sm:text-3xl dark:text-primary"
                        >
                            ${{ displayPrice }}
                        </span>
                        <span
                            v-if="product?.discount_price"
                            class="text-base text-muted-foreground line-through sm:text-lg dark:text-slate-400"
                        >
                            ${{ product?.price }}
                        </span>
                    </div>

                    <!-- Stock -->
                    <p
                        v-if="product?.stock != null"
                        class="mt-1.5 text-sm sm:mt-2"
                        :class="
                            product.stock > 0
                                ? 'text-muted-foreground dark:text-slate-400'
                                : 'text-destructive'
                        "
                    >
                        {{
                            product.stock > 0
                                ? `In stock (${product.stock})`
                                : 'Out of stock'
                        }}
                    </p>

                    <!-- Quantity + Add to cart. TODO: Add your logic here — bind quantity ref, enforce min/max vs stock, wire decrease/increase buttons and add-to-cart submit. -->
                    <div
                        class="mt-6 flex flex-wrap items-center gap-3 sm:mt-8 sm:gap-4"
                    >
                        <div
                            class="flex items-center rounded-lg border border-border bg-card dark:border-slate-800 dark:bg-slate-900/50"
                            role="group"
                            aria-label="Quantity"
                        >
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-10 w-10 rounded-r-none"
                                aria-label="Decrease quantity"
                                :disabled="quantity <= 1"
                                @click="quantity = Math.max(1, quantity - 1)"
                            >
                                <Minus class="size-4" />
                            </Button>
                            <span
                                class="flex h-10 w-12 items-center justify-center border-x border-border text-sm font-medium tabular-nums dark:border-slate-800"
                            >
                                {{ quantity }}
                            </span>
                            <Button
                                variant="ghost"
                                size="icon"
                                class="h-10 w-10 rounded-l-none"
                                aria-label="Increase quantity"
                                :disabled="quantity >= (product?.stock ?? 0)"
                                @click="
                                    quantity = Math.min(
                                        product.stock,
                                        quantity + 1,
                                    )
                                "
                            >
                                <Plus class="size-4" />
                            </Button>
                        </div>
                        <Button
                            class="min-h-10 flex-1 gap-2 px-4 sm:flex-none sm:px-6"
                            aria-label="Add to cart"
                            :disabled="
                                product?.stock != null && product.stock < 1
                            "
                            @click="
                                addToCart(
                                    product.id,
                                    quantity,
                                    product.thumbnail,
                                )
                            "
                        >
                            <ShoppingCart class="size-4 shrink-0" />
                            Add to Cart
                        </Button>
                    </div>

                    <!-- 4. Trust Badges & Social Share -->
                    <div
                        class="mt-6 flex flex-wrap items-center gap-4 border-t border-border pt-6 sm:mt-8 sm:gap-6 sm:pt-8 dark:border-slate-800"
                    >
                        <div
                            class="flex flex-wrap items-center gap-4 sm:gap-6"
                            aria-label="Trust and shipping information"
                        >
                            <div
                                class="flex items-center gap-2 text-sm text-muted-foreground dark:text-slate-400"
                            >
                                <Truck
                                    class="size-5 shrink-0 text-primary"
                                    aria-hidden="true"
                                />
                                <span>Free Shipping</span>
                            </div>
                            <div
                                class="flex items-center gap-2 text-sm text-muted-foreground dark:text-slate-400"
                            >
                                <Package
                                    class="size-5 shrink-0 text-primary"
                                    aria-hidden="true"
                                />
                                <span>Secure Payment</span>
                            </div>
                            <div
                                class="flex items-center gap-2 text-sm text-muted-foreground dark:text-slate-400"
                            >
                                <RefreshCw
                                    class="size-5 shrink-0 text-primary"
                                    aria-hidden="true"
                                />
                                <span>7-day Return</span>
                            </div>
                        </div>
                        <!-- TODO: Add your logic here — on click, copy current page URL to clipboard and optionally show a toast. -->
                        <Button
                            variant="outline"
                            size="sm"
                            class="shrink-0 gap-2"
                            aria-label="Share product"
                            @click="copyToClipboard"
                        >
                            <Share2 class="size-4" />
                            Share
                        </Button>
                    </div>

                    <!-- Meta: SKU, Brand -->
                    <div
                        class="mt-4 flex flex-wrap gap-4 text-sm text-muted-foreground sm:mt-6 dark:text-slate-400"
                    >
                        <span>
                            <span
                                class="font-medium text-foreground dark:text-slate-200"
                                >SKU</span
                            >
                            — {{ product?.id ?? '—' }}
                        </span>
                        <span v-if="product?.brand_id != null">
                            <span
                                class="font-medium text-foreground dark:text-slate-200"
                                >Brand</span
                            >
                            — {{ product.brand?.name ?? '—' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Product Tabs Section (headless-style, ARIA) -->
            <section
                class="mt-10 rounded-xl border border-border bg-card shadow-sm sm:mt-16 dark:border-slate-800 dark:bg-slate-900/50"
                aria-label="Product information tabs"
            >
                <div
                    role="tablist"
                    class="flex overflow-x-auto border-b border-border dark:border-slate-800"
                    aria-label="Product details"
                >
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeTab === 'description'"
                        :tabindex="activeTab === 'description' ? 0 : -1"
                        aria-controls="panel-description"
                        id="tab-description"
                        class="shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors sm:px-6"
                        :class="
                            activeTab === 'description'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:text-foreground dark:text-slate-400 dark:hover:text-slate-100'
                        "
                        @click="activeTab = 'description'"
                    >
                        Description
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeTab === 'specifications'"
                        :tabindex="activeTab === 'specifications' ? 0 : -1"
                        aria-controls="panel-specifications"
                        id="tab-specifications"
                        class="shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors sm:px-6"
                        :class="
                            activeTab === 'specifications'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:text-foreground dark:text-slate-400 dark:hover:text-slate-100'
                        "
                        @click="activeTab = 'specifications'"
                    >
                        Specifications
                    </button>
                    <button
                        type="button"
                        role="tab"
                        :aria-selected="activeTab === 'shipping'"
                        :tabindex="activeTab === 'shipping' ? 0 : -1"
                        aria-controls="panel-shipping"
                        id="tab-shipping"
                        class="shrink-0 border-b-2 px-4 py-3 text-sm font-medium transition-colors sm:px-6"
                        :class="
                            activeTab === 'shipping'
                                ? 'border-primary text-primary'
                                : 'border-transparent text-muted-foreground hover:text-foreground dark:text-slate-400 dark:hover:text-slate-100'
                        "
                        @click="activeTab = 'shipping'"
                    >
                        Shipping & Returns
                    </button>
                </div>
                <div class="p-4 sm:p-6">
                    <div
                        id="panel-description"
                        role="tabpanel"
                        aria-labelledby="tab-description"
                        class="text-foreground/90 dark:text-slate-300"
                        :hidden="activeTab !== 'description'"
                    >
                        <p v-if="product?.description">
                            {{ product.description }}
                        </p>
                        <p
                            v-else
                            class="text-muted-foreground dark:text-slate-400"
                        >
                            No description available.
                        </p>
                    </div>
                    <div
                        id="panel-specifications"
                        role="tabpanel"
                        aria-labelledby="tab-specifications"
                        :hidden="activeTab !== 'specifications'"
                    >
                        <!-- TODO: Add your logic here — render dynamic specification data (e.g. v-for over specs array: key/value rows). -->
                        <table
                            class="w-full text-left text-sm"
                            aria-label="Product specifications"
                        >
                            <tbody
                                class="divide-y divide-border dark:divide-slate-800"
                            >
                                <tr>
                                    <th
                                        scope="row"
                                        class="py-3 pr-4 font-medium text-muted-foreground dark:text-slate-400"
                                    >
                                        Weight
                                    </th>
                                    <td
                                        class="py-3 text-foreground dark:text-slate-200"
                                    >
                                        0.5 kg
                                    </td>
                                </tr>
                                <tr>
                                    <th
                                        scope="row"
                                        class="py-3 pr-4 font-medium text-muted-foreground dark:text-slate-400"
                                    >
                                        Dimensions
                                    </th>
                                    <td
                                        class="py-3 text-foreground dark:text-slate-200"
                                    >
                                        20 × 15 × 5 cm
                                    </td>
                                </tr>
                                <tr>
                                    <th
                                        scope="row"
                                        class="py-3 pr-4 font-medium text-muted-foreground dark:text-slate-400"
                                    >
                                        Material
                                    </th>
                                    <td
                                        class="py-3 text-foreground dark:text-slate-200"
                                    >
                                        —
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        id="panel-shipping"
                        role="tabpanel"
                        aria-labelledby="tab-shipping"
                        class="text-foreground/90 dark:text-slate-300"
                        :hidden="activeTab !== 'shipping'"
                    >
                        <p class="mb-3">
                            <strong class="text-foreground dark:text-slate-100"
                                >Shipping:</strong
                            >
                            We offer free standard shipping on orders over $50.
                            Delivery typically takes 3–5 business days. Express
                            options available at checkout.
                        </p>
                        <p>
                            <strong class="text-foreground dark:text-slate-100"
                                >Returns:</strong
                            >
                            You may return most items within 7 days of delivery
                            for a full refund. Items must be unused and in
                            original packaging. Contact support to initiate a
                            return.
                        </p>
                    </div>
                </div>
            </section>

            <!-- 3. Customer Reviews Section -->
            <section
                class="mt-10 rounded-xl border border-border bg-card p-4 shadow-sm sm:mt-16 sm:p-6 dark:border-slate-800 dark:bg-slate-900/50"
                aria-label="Customer reviews"
            >
                <h2
                    class="font-serif text-xl font-semibold text-foreground sm:text-2xl dark:text-slate-100"
                >
                    Customer Reviews
                </h2>

                <!-- Rating breakdown. TODO: Add your logic here — populate percent/count from dynamic rating data (e.g. API). -->
                <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        class="space-y-3"
                        aria-label="Rating distribution"
                    >
                        <div
                            v-for="row in ratingBreakdown"
                            :key="row.stars"
                            class="flex items-center gap-3"
                        >
                            <div
                                class="flex w-16 shrink-0 items-center gap-1 text-sm text-muted-foreground dark:text-slate-400"
                            >
                                <span>{{ row.stars }}</span>
                                <Star
                                    class="size-4 fill-amber-400 text-amber-400"
                                    aria-hidden="true"
                                />
                            </div>
                            <div class="min-w-0 flex-1">
                                <div
                                    class="h-2 w-full overflow-hidden rounded-full bg-muted dark:bg-slate-800"
                                >
                                    <div
                                        class="h-full rounded-full bg-amber-400 transition-[width]"
                                        :style="{ width: `${row.percent}%` }"
                                    />
                                </div>
                            </div>
                            <span
                                class="w-8 shrink-0 text-right text-sm text-muted-foreground dark:text-slate-400"
                            >
                                {{ row.percent }}%
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Review list. TODO: Add your logic here — iterate over actual review data (e.g. v-for="review in reviews") and display user name, star rating, date, review text. -->
                <ul
                    class="mt-8 space-y-6 border-t border-border pt-8 dark:border-slate-800"
                    role="list"
                >
                    <li
                        v-for="review in placeholderReviews"
                        :key="review.id"
                        class="flex flex-col gap-2"
                    >
                        <div class="flex flex-wrap items-center gap-2">
                            <span
                                class="font-medium text-foreground dark:text-slate-100"
                            >
                                {{ review.userName }}
                            </span>
                            <div
                                class="flex items-center gap-0.5"
                                :aria-label="`Rating: ${review.rating} out of 5`"
                            >
                                <Star
                                    v-for="i in 5"
                                    :key="i"
                                    class="size-4"
                                    :class="
                                        i <= review.rating
                                            ? 'fill-amber-400 text-amber-400'
                                            : 'text-muted-foreground/40'
                                    "
                                />
                            </div>
                            <span
                                class="text-sm text-muted-foreground dark:text-slate-400"
                            >
                                {{ review.date }}
                            </span>
                        </div>
                        <p
                            class="text-sm text-foreground/90 dark:text-slate-300"
                        >
                            {{ review.text }}
                        </p>
                    </li>
                </ul>
            </section>

            <!-- 5. Related Products Grid -->
            <section
                class="mt-10 sm:mt-16"
                aria-label="You may also like"
            >
                <h2
                    class="font-serif text-xl font-semibold text-foreground sm:text-2xl dark:text-slate-100"
                >
                    You May Also Like
                </h2>
                <div
                    class="mt-6 grid grid-cols-2 gap-4 sm:gap-6 lg:grid-cols-4"
                >
                    <Link
                        v-for="item in product.related_products"
                        :key="item.id"
                        :href="route('product.show', item.slug)"
                        class="group flex flex-col overflow-hidden rounded-xl border border-border bg-card transition-shadow hover:shadow-md focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 focus-visible:outline-none dark:border-slate-800 dark:bg-slate-900/50"
                    >
                        <div
                            class="aspect-square overflow-hidden bg-muted dark:bg-slate-800"
                        >
                            <img
                                v-if="item.thumbnail"
                                :src="item.thumbnail"
                                :alt="item.name"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="flex h-full w-full items-center justify-center text-muted-foreground"
                            >
                                No image
                            </div>
                        </div>
                        <div class="flex flex-1 flex-col p-3 sm:p-4">
                            <h3
                                class="line-clamp-2 text-sm font-medium text-foreground sm:text-base dark:text-slate-100"
                            >
                                {{ item.name }}
                            </h3>
                            <p
                                class="mt-auto pt-2 text-sm font-semibold text-primary dark:text-primary"
                            >
                                ${{ item.price }}
                            </p>
                        </div>
                    </Link>
                </div>
            </section>
        </div>
    </div>
</template>
