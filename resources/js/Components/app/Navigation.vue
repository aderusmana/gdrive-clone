<template>
    <nav class="min-w-[200px]">
        <div class="h-[80px] px-3 flex items-center gap-3">
            <Link :href="route('myFiles')">
                <ApplicationLogo
                    class="block h-9 w-auto fill-current text-gray-800"
                />
            </Link>
            Buchin Drive
        </div>
        <div class="px-3">
            <CreateNewDropdown />
            <div class="py-4">
                <NavLink
                    class="hover:text-indigo-600 hover:bg-indigo-100"
                    :href="route('myFiles')"
                    :active="$page.props.route_name == 'myFiles'"
                    >My Files</NavLink
                >
                <NavLink
                    class="hover:text-indigo-600 hover:bg-indigo-100"
                    :href="route('shareWithMe')"
                    :active="$page.props.route_name == 'shareWithMe'"
                    >Share with me</NavLink
                >
                <NavLink
                    class="hover:text-indigo-600 hover:bg-indigo-100"
                    :href="route('shareByMe')"
                    :active="$page.props.route_name == 'shareByMe'"
                    >Shared by me</NavLink
                >
                <NavLink
                    class="hover:text-indigo-600 hover:bg-indigo-100"
                    :href="route('trash')"
                    :active="$page.props.route_name == 'trash'"
                    >Trash</NavLink
                >
            </div>
        </div>
    </nav>
</template>

<script>
import { Link } from "@inertiajs/vue3";
import ApplicationLogo from "../ApplicationLogo.vue";
import CreateNewDropdown from "./CreateNewDropdown.vue";
import NavLink from "../NavLink.vue";

export default {
    components: { ApplicationLogo, Link, CreateNewDropdown, NavLink },
};
</script>
