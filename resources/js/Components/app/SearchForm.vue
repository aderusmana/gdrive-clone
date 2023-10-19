<template>
    <div action="" class="w-[600px] h-[80px] flex items-center">
        <TextInput
            type="text"
            class="block w-full mr-2"
            v-model="search"
            autocomplete="on"
            @keyup.enter.prevent="onSearch"
            placeholder="Search for files and folders"
        />
    </div>
</template>

<script setup>
import { router, useForm } from "@inertiajs/vue3";
import TextInput from "../TextInput.vue";
import { onMounted, ref } from "vue";
import { ON_SEARCH, emitter } from "@/event-bus";

const form = useForm({
    search: "",
});

//uses
let params = "";

//refs
const search = ref("");

//methods

function onSearch() {
    params.set("search", search.value);
    router.get(window.location.pathname + '?' + params.toString());
    emitter.emit(ON_SEARCH, search.value)
}

//hook

onMounted(() => {
    params = new URLSearchParams(window.location.search);
    search.value = params.get('search')
});
</script>
