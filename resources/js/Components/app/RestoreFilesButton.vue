<template>
    <button
        @click="onRestoreClick"
        class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium hover:font-semibold text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700 transition-transform transform hover:scale-105"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 28 28"
            fill="currentColor"
            class="w-6 h-6"
        >
            <path
                fill-rule="evenodd"
                d="M4.755 10.059a7.5 7.5 0 0112.548-3.364l1.903 1.903h-3.183a.75.75 0 100 1.5h4.992a.75.75 0 00.75-.75V4.356a.75.75 0 00-1.5 0v3.18l-1.9-1.9A9 9 0 003.306 9.67a.75.75 0 101.45.388zm15.408 3.352a.75.75 0 00-.919.53 7.5 7.5 0 01-12.548 3.364l-1.902-1.903h3.183a.75.75 0 000-1.5H2.984a.75.75 0 00-.75.75v4.992a.75.75 0 001.5 0v-3.18l1.9 1.9a9 9 0 0015.059-4.035.75.75 0 00-.53-.918z"
                clip-rule="evenodd"
            />
        </svg>

        Restore
    </button>
    <ConfirmationDialog
        :show="showConfirmationDialog"
        message="Are you sure want to restore selected files?"
        @cancel="onRestoreCancel"
        @confirm="onRestoreConfirm"
    />
</template>

<script setup>
import { ref } from "vue";
import ConfirmationDialog from "../ConfirmationDialog.vue";
import { showErrorDialog, showSuccessNotification } from "@/event-bus";
import { useForm, usePage } from "@inertiajs/vue3";

//refs
const showConfirmationDialog = ref(false);
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

function onRestoreClick() {
    if (!props.allSelected && !props.selectedIds.length) {
        showErrorDialog("Please select at least one a list of file to  delete");
        return;
    }
    showConfirmationDialog.value = true;
}

function onRestoreCancel() {
    showConfirmationDialog.value = false;
}

function onRestoreConfirm() {
    if (props.allSelected) {
        form.all = true;
    } else {
        form.ids = props.selectedIds;
    }
    form.post(route("file.restore"), {
        onSuccess: () => {
            showConfirmationDialog.value = false;
            emit("restore");
            //Todo show success confirmation
            showSuccessNotification("Selected File have been restored");
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
