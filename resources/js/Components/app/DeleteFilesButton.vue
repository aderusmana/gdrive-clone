<template>
    <button
        @click="onDeleteClick"
        class="inline-flex items-center gap-1 px-4 py-2 text-md font-medium hover:font-semibold text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700 transition-transform transform hover:scale-105"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            fill="currentColor"
            class="w-6 h-6"
        >
            <path
                fill-rule="evenodd"
                d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.488 49.488 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z"
                clip-rule="evenodd"
            />
        </svg>

        Delete
    </button>
    <ConfirmationDialog
        :show="showDeleteDialog"
        message="Are you sure want to delete selected files?"
        @cancel="onDeleteCancel"
        @confirm="onDeleteConfirm"
    />
</template>

<script setup>
import { ref } from "vue";
import ConfirmationDialog from "../ConfirmationDialog.vue";
import { showErrorDialog, showSuccessNotification } from "@/event-bus";
import { useForm, usePage } from "@inertiajs/vue3";

//refs
const showDeleteDialog = ref(false);
//props
const props = defineProps({
    deleteAll: {
        type: Boolean,
        default: false,
        required: false,
    },
    deleteIds: {
        type: Array,
        required: false,
    },
});
const emit = defineEmits(["delete"]);
//method

function onDeleteClick() {
    if(!props.deleteAll && !props.deleteIds.length){
        showErrorDialog('Please select at least one a list of file to  delete')
        return
    }
    showDeleteDialog.value = true;
}

function onDeleteCancel() {
    showDeleteDialog.value = false;
}

function onDeleteConfirm(){
    deleteFilesForm.parent_id = page.props.folders.data.id
    if(props.deleteAll) {
        deleteFilesForm.all = true
    }else{

        deleteFilesForm.ids = props.deleteIds
    }
    deleteFilesForm.delete(route('file.destroy'),{
        onSuccess: () => {
            showDeleteDialog.value = false;
            emit("delete");
            //Todo show success confirmation
            showSuccessNotification('Selected File have been deleted,the file now in trash');
        }
    })
    console.log("Delete" , props.deleteAll,props.deleteIds)
}

//uses

const page = usePage();
const deleteFilesForm = useForm({
    all:null,
    ids:[],
    parent_id:null
});
</script>
