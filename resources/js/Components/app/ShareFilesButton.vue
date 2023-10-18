<template>
    <button
        @click="onShareClick"
        class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium hover:font-semibold text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700 transition-transform transform hover:scale-105"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 28 28"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z"
            />
        </svg>

        Share
    </button>
    <ShareFilesModal v-model="showEmailsModal" :all-selected="allSelected" :selected-ids="selectedIds" />
</template>

<script setup>
import { ref } from "vue";
import { showErrorDialog, showSuccessNotification } from "@/event-bus";
import { useForm, usePage } from "@inertiajs/vue3";
import ShareFilesModal from "./ShareFilesModal.vue";

//refs
const showEmailsModal = ref(false);
//props
const props = defineProps({
    allSelected: {
        type: Boolean,
        default: false,
        required: false,
    },
    selectedIds: {
        type: Array,
        required: false,
    },
});
const emit = defineEmits(["restore"]);
//method

function onShareClick() {
    if (!props.allSelected && !props.selectedIds.length) {
        showErrorDialog("Please select at least one a list of file to  share");
        return;
    }
    showEmailsModal.value = true;
}

function onRestoreCancel() {
    showEmailsModal.value = false;
}

function onRestoreConfirm() {
    if (props.allSelected) {
        form.all = true;
    } else {
        form.ids = props.selectedIds;
    }
    form.post(route("file.restore"), {
        onSuccess: () => {
            showEmailsModal.value = false;
            emit("restore");
            //Todo show success confirmation
            showSuccessNotification("Selected File have been share");
        },
    });
}

//uses

const page = usePage();
const form = useForm({
    all: null,
    ids: [],
    parent_id: null,
});
</script>
