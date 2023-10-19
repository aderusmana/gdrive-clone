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
            <div class="space-x-2 flex items-center">
                <lable>
                    Only Favourites
                    <Checkbox
                        @change="showOnlyFavorites"
                        v-model:checked="onlyFavourites"
                /></lable>
                <ShareFilesButton
                    :all-selected="allSelected"
                    :selected-ids="selectedIds"
                />
                <DownloadFilesButton :all="allSelected" :ids="selectedIds" />
                <DeleteFilesButton
                    :delete-all="allSelected"
                    :delete-ids="selectedIds"
                    @delete="onDelete"
                />
            </div>
        </nav>
        <div class="flex-1 overflow-auto">
            <table class="min-w-full">
                <thead class="bg-gray-100 border-b">
                    <tr>
                        <th
                            scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-[30px] max-w-[30px] pr-0"
                        >
                            <Checkbox
                                class="cursor-pointer"
                                @change="onSelectAllChange"
                                v-model:checked="allSelected"
                            />
                        </th>
                        <th></th>
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
                            v-if="search"
                            scope="col"
                            class="px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        >
                            Path
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
                        class="hover:bg-indigo-100 divide-y divide-gray-200v"
                        :class="
                            selected[file.id] || allSelected
                                ? 'bg-indigo-50'
                                : 'bg-white'
                        "
                    >
                        <td
                            class="px-6 py-4 whitespace-nowrap w-[30px] max-w-[30px] pr-0"
                        >
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700 flex items-center"
                                    >
                                        <Checkbox
                                            class="cursor-pointer"
                                            @change="
                                                ($event) =>
                                                    onSelectCheckboxChange(file)
                                            "
                                            v-model="selected[file.id]"
                                            :checked="
                                                selected[file.id] || allSelected
                                            "
                                        />
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td
                            class="px-6 py-4 whitespace-nowrap w-[40px] max-w-[40px] pr-0"
                        >
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-yellow-400 flex items-center"
                                    >
                                        <div
                                            class="cursor-pointer"
                                            @click.prevent="
                                                addRemoveFavorite(file)
                                            "
                                        >
                                            <svg
                                                v-if="!file.is_favorites"
                                                xmlns="http://www.w3.org/2000/svg"
                                                fill="none"
                                                viewBox="0 0 24 24"
                                                stroke-width="1.5"
                                                stroke="currentColor"
                                                class="w-6 h-6"
                                            >
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"
                                                />
                                            </svg>
                                            <svg
                                                v-else
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 24 24"
                                                fill="currentColor"
                                                class="w-6 h-6"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </div>
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
                        <td v-if="search" class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="ml-4">
                                    <div
                                        class="text-sm font-medium text-gray-700"
                                    >
                                        {{ file.path }}
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
import { onMounted, ref, onUpdated, computed } from "vue";
import { httpGet, httpPost } from "@/Helper/http-helper.js";
import Checkbox from "@/Components/Checkbox.vue";
import DeleteFilesButton from "@/Components/app/DeleteFilesButton.vue";
import DownloadFilesButton from "@/Components/app/DownloadFilesButton.vue";
import { ON_SEARCH, emitter, showErrorDialog, showSuccessNotification } from "@/event-bus";
import ShareFilesButton from "@/Components/app/ShareFilesButton.vue";

// refs

const loadMoreIntersect = ref(null);
const onlyFavourites = ref(false);
const allSelected = ref(false);
const selected = ref({});
let search = ref("")

const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next,
});

let params = null;


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
    console.log(allFiles.value.next);

    if (allFiles.value.next === null) {
        return;
    }

    httpGet(allFiles.value.next).then((res) => {
        allFiles.value.data = [...allFiles.value.data, ...res.data];
        allFiles.value.next = res.links.next;
    });
}

function onSelectAllChange() {
    allFiles.value.data.forEach((f) => {
        selected.value[f.id] = allSelected.value;
    });
}

function onSelectCheckboxChange(file) {
    if (!selected.value[file.id]) {
        allSelected.value = false;
    } else {
        let checked = true;

        for (let file of allFiles.value.data) {
            if (!selected.value[file.id]) {
                checked = false;
                break;
            }
        }
        allSelected.value = checked;
    }
}

function onDelete() {
    allSelected.value = false;
    selected.value = {};
}

function addRemoveFavorite(file) {
    httpPost(route("file.addFavorites"), { id: file.id })
        .then(() => {
            file.is_favorites = !file.is_favorites;
            showSuccessNotification(
                "Selected files have been added to favourites"
            );
        })
        .catch(async (er) => {
            showErrorDialog("Selected files failed added to favourites");
            console.log(er.error.message);
        });
}
function showOnlyFavorites() {
    // Set the "favourites" parameter in the URL
    params.set("favourites", onlyFavourites.value ? 1 : 0);

    // Use Vue Router to navigate to the updated URL
    router.get(window.location.pathname + "?" + params.toString());
}

// Hooks
onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next,
    };
});

onMounted(() => {
    // Parse the current URL parameters
    params = new URLSearchParams(window.location.search);
    onlyFavourites.value = params.get("favourites") === "1";
    search.value = params.get("search");
    emitter.on(ON_SEARCH, (value) => {
        search.value = value
    })


    const observer = new IntersectionObserver(
        (entries) =>
            entries.forEach((entry) => entry.isIntersecting && loadMore()),
        {
            rootMargin: "-250px 0px 0px 0px",
        }
    );

    observer.observe(loadMoreIntersect.value);
});

//computed
const selectedIds = computed(() =>
    Object.entries(selected.value)
        .filter((a) => a[1])
        .map((a) => a[0])
);
</script>
