<script setup>
    const heroContent = {
        headline: 'Discover Amazing Products',
        subHeadline: 'at Unbeatable Prices',
        description: 'Shop the latest trends and find everything you need in one place. Quality products, fast delivery, and exceptional service.',
        backgroundImage: 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1920&h=1080&fit=crop',
    };
</script>
<template>
    <section
            class="relative min-h-[600px] sm:min-h-[700px] lg:min-h-[800px] flex items-center overflow-hidden"
            aria-label="Hero section"
        >
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 z-0">
                <img
                    :src="heroContent.backgroundImage"
                    alt="Hero background"
                    class="h-full w-full object-cover"
                />
                <!-- Dark Overlay for Text Contrast -->
                <div
                    class="absolute inset-0 bg-linear-to-r from-black/70 via-black/50 to-black/30 dark:from-black/80 dark:via-black/60 dark:to-black/40"
                />
            </div>

            <!-- Content Container -->
            <div class="container relative z-10 mx-auto px-4 sm:px-6 lg:px-8 py-20">
                <div class="max-w-3xl">
                    <!-- Headline -->
                    <h1
                        class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-bold mb-6 text-white leading-tight drop-shadow-lg"
                    >
                        {{ heroContent.headline }}
                        <span class="text-primary">{{ heroContent.subHeadline }}</span>
                    </h1>

                    <!-- Sub-headline / Description -->
                    <p
                        class="text-lg sm:text-xl lg:text-2xl text-white/90 mb-10 leading-relaxed drop-shadow-md max-w-2xl"
                    >
                        {{ heroContent.description }}
                    </p>

                    <!-- CTA Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4">
                        <Button
                            size="lg"
                            class="text-base sm:text-lg px-8 py-6 group bg-primary hover:bg-primary/90 text-primary-foreground shadow-xl"
                            aria-label="Start shopping now"
                        >
                            <ShoppingBag class="mr-2 h-5 w-5" />
                            Shop Now
                            <ArrowRight
                                class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Button>
                        <Button
                            size="lg"
                            variant="outline"
                            class="text-base sm:text-lg px-8 py-6 group bg-white/10 backdrop-blur-sm border-white/20 text-white hover:bg-white/20"
                            aria-label="View product gallery"
                        >
                            <ImageIcon class="mr-2 h-5 w-5" />
                            View Gallery
                            <ArrowRight
                                class="ml-2 h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </Button>
                    </div>
                </div>
            </div>
    </section>
</template>