<template>
    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <nav class="flex items-center justify-between p-1 mb-3">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li
                    v-for="ans of ancestors.data"
                    :key="ans.id"
                    class="inline-flex items-center"
                >
                    <Link
                        v-if="!ans.parent_id"
                        :href="route('myFiles')"
                        class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-600"
                    >
                        <HomeIcon class="w-4 h-4 mr-2" />
                        My Files
                    </Link>
                    <div v-else class="flex items-center">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-6 h-6 text-gray-700 dark:text-gray-400"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8.25 4.5l7.5 7.5-7.5 7.5"
                            />
                        </svg>

                        <Link
                            :href="route('myFiles', { folder: ans.path })"
                            class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2 dark:text-gray-600"
                        >
                            {{ ans.name }}
                        </Link>
                    </div>
                </li>
            </ol>
        </nav>
        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[30px] max-w-[30px] pr-0">
                            <Checkbox @change="onSelectAllChange" v-model:checked="allSelected " />
                        </th>
                        <th
                            scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Name
                        </th>
                        <th
                            scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Owner
                        </th>
                        <th
                            scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Last Modified
                        </th>
                        <th
                            scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Size
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="file in allFiles.data"
                        :key="file.id"
                        @dblclick="openFolder(file)"
                        @click="$event => toogleFileSelect(file)"
                        class="hover:bg-indigo-100 divide-y divide-gray-200v" :class="(selected[file.id] ? 'bg-indigo-50' : 'bg-white')"
                        >
                        <td class="px-6 py-4 whitespace-nowrap w-[30px] max-w-[30px] pr-0">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700 flex items-center"
                                    >
                                        <Checkbox @change="$event => onSelectCheckboxChange(file)" v-model="selected[file.id]" :checked="selected[file.id] || allSelected" />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700 flex items-center"
                                    >
                                        <FileIcon :file="file" />
                                        {{ file.name }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        {{ file.owner }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        {{ file.updated_at }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        {{ file.size }}
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div
                v-if="!allFiles.data.length"
                class="py-8 text-center text-lg text-gray-500"
            >
                There is no data in this folder
            </div>
            <div ref="loadMoreIntersect"></div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import FileIcon from "@/Components/app/FileIcon.vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { HomeIcon } from "@heroicons/vue/24/solid";
import { Head, Link, router } from "@inertiajs/vue3";
import { onMounted, ref, onUpdated } from "vue";
import { httpGet } from "@/Helper/http-helper.js";
import Checkbox from "@/Components/Checkbox.vue";

// refs

const loadMoreIntersect = ref(null);
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next,
});

const allSelected = ref(false)
const selected = ref({})

//props

const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object,
});

//methods
function openFolder(file) {
    if (!file.is_folder) {
        return;
    }

    router.visit(route("myFiles", { folder: file.path }));
}

function loadMore() {
    console.log("load more");
    console.log(allFiles.value.next);

    if (allFiles.value.next === null) {
        return;
    }

    httpGet(allFiles.value.next).then((res) => {
        allFiles.value.data = [...allFiles.value.data, ...res.data];
        allFiles.value.next = res.links.next;
    });
}

function onSelectAllChange()
{
    allFiles.value.data.forEach(f => {
        selected.value[f.id] = allSelected.value;
    })
}

function toogleFileSelect(file){
    selected.value[file.id] =!selected.value[file.id]
    onSelectCheckboxChange();

}
function onSelectCheckboxChange(file){
    if(selected.value[file.id]) {
        allSelected.value = false;

    }else{
        let checked = true;

        for(let file of allFiles.value.data){
            if(selected.value[file.id]) {
                checked = false;
                break;
            }
        }
        allSelected.value = checked;
    }
}

// Hooks
onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next,
    };
});

onMounted(() => {
    // params = new URLSearchParams(window.location.search);
    // onlyFavourites.value = params.get("favourites") === "1";

    // search.value = params.get("search");
    // emitter.on(ON_SEARCH, (value) => {
    //     search.value = value;
    // });

    const observer = new IntersectionObserver(
        (entries) =>
            entries.forEach((entry) => entry.isIntersecting && loadMore()),
        {
            rootMargin: "-250px 0px 0px 0px",
        }
    );

    observer.observe(loadMoreIntersect.value);
});
</script>
