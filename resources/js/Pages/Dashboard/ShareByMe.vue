<template>
    <Head title="Share By Me" />
    <AuthenticatedLayout>
        <nav class="flex items-center justify-end p-1 mb-3">
            <div class="space-x-2 flex items-center">
                <DownloadFilesButton :all="allSelected" :ids="selectedIds" :share-by-me="true"/>

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
                                @change="onSelectAllChange"
                                v-model:checked="allSelected"
                            />
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
                            Path
                        </th>

                    </tr>
                </thead>
                <tbody>
                    <tr
                        v-for="file in allFiles.data"
                        :key="file.id"
                        @click="($event) => toogleFileSelect(file)"
                        class="hover:bg-indigo-100 divide-y divide-gray-200v"
                        :class="selected[file.id] ? 'bg-indigo-50' : 'bg-white'"
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
                                        {{ file.path }}
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
import { Head } from "@inertiajs/vue3";
import { onMounted, ref, onUpdated, computed } from "vue";
import { httpGet } from "@/Helper/http-helper.js";
import Checkbox from "@/Components/Checkbox.vue";
import DownloadFilesButton from "@/Components/app/DownloadFilesButton.vue";

// refs

const loadMoreIntersect = ref(null);
const allFiles = ref({
    data: props.files.data,
    next: props.files.links.next,
});

const allSelected = ref(false);
const selected = ref({});

//props

const props = defineProps({
    files: Object,
    folder: Object,
    ancestors: Object,
});

//methods

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

function onSelectAllChange() {
    allFiles.value.data.forEach((f) => {
        selected.value[f.id] = allSelected.value;
    });
}

function toogleFileSelect(file) {
    selected.value[file.id] = !selected.value[file.id];
    onSelectCheckboxChange(file);
}
function onSelectCheckboxChange(file) {
    if (selected.value[file.id]) {
        allSelected.value = false;
    } else {
        let checked = true;

        for (let file of allFiles.value.data) {
            if (selected.value[file.id]) {
                checked = false;
                break;
            }
        }
        allSelected.value = checked;
    }
}

function resetForm() {
    allSelected.value = false;
    selected.value = {};
}

// Hooks
onUpdated(() => {
    allFiles.value = {
        data: props.files.data,
        next: props.files.links.next,
    };
});

onMounted(() => {
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
