<template>
    <main>
        <div class="bg-indigo h-2"></div>

        <div class="min-h-screen flex flex-col">

            <main class="flex-1 ">
                <div class="bg-white">
                    <nav class="w-full max-w-screen mx-auto px-32 py-8">
                        <div class="text-center md:flex md:justify-between md:items-center">
                            <inertia-link href="#">
                                NAME
                            </inertia-link>

                            <inertia-link class="hidden md:flex hover:text-indigo" :href="route('home.index')" v-if="$page.auth.user">
                                Home
                            </inertia-link>
                            <div class="hidden md:flex" v-else>
                                <inertia-link :href="route('login')" class="bg-white text-indigo font-normal py-3 px-6 border border-grey-light rounded shadow hover:shadow-md mr-3">
                                    Log in
                                </inertia-link>
                                <inertia-link :href="route('register')" class="bg-indigo text-white font-normal py-3 px-6 border rounded shadow hover:shadow-md">
                                    Get Started
                                </inertia-link>
                            </div>
                        </div>
                    </nav>
                </div>

                <div class="w-full max-w-2xl mx-auto pt-16">
                    <div class="py-8 px-4 md:px-2 text-center">
                        <h1 class="text-black text-3xl font-bold mb-6 leading-none md:text-6xl">Lorem Ipsum - Simple.</h1>
                        <p class="text-grey-darker mx-auto text-lg max-w-md md:text-2xl">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque nibh magna, efficitur vel sapien a, aliquet lacinia dolor.
                        </p>
                    </div>

                    <img src="https://pingping.io/images/marketing/mockup.png" style="margin-bottom: -5px;" />
                </div>
            </main>
        </div>
    </main>
</template>