<template>
    <div class="h-screen bg-gray-50 flex w-full gap-4">
        <Navigation />
        <main
            @drop.prevent="handleDrop"
            @dragover.prevent="onDragOver"
            @dragleave.prevent="onDragLeave"
            class="flex flex-col flex-1 px-4 overflow-hidden"
            :class="dragOver ? 'dropzone' : ''"
        >
            <template
                v-if="dragOver"
                class="text-gray-500 text-center py-8 text-sm"
            >
                Drop files here to upload
            </template>
            <template v-else>
                <div class="flex items-center justify-between w-full">
                    <SearchForm />
                    <UserSettingDropDown class="z-10" />
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    <slot />
                </div>
            </template>
        </main>
    </div>
    <ErrorDialog />
    <FormProgress :form="fileUploadForm" />
    <Notification />
</template>

<script setup>
import ErrorDialog from "@/Components/app/ErrorDialog.vue";
import FormProgress from "@/Components/app/FormProgress.vue";
import Navigation from "@/Components/app/Navigation.vue";
import Notification from "@/Components/app/Notification.vue";
import SearchForm from "@/Components/app/SearchForm.vue";
import UserSettingDropDown from "@/Components/app/UserSettingDropDown.vue";
import { emitter, FILE_UPLOAD_STARTED, showErrorDialog, showSuccessNotification } from "@/event-bus.js";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref, onMounted } from "vue";

//uses
const page = usePage();
const fileUploadForm = useForm({
    files: [],
    relative_paths: [],
    parent_id: null,
});

//refs
const dragOver = ref(false);

//method
function uploadFiles(files) {
    fileUploadForm.parent_id = page.props.folders.id;
    fileUploadForm.files = files;
    fileUploadForm.relative_paths = [...files].map((f) => f.webkitRelativePath);

    fileUploadForm.post(route("file.store"), {
        onSuccess: () => {

            showSuccessNotification(`${files.length} files have been uploaded`);
        },
        onError: (errors) => {
            let message = "";

            if (Object.keys(errors).length > 0) {
                message = "Error: " + errors[Object.keys(errors)[0]];
            } else {
                message = "Error during file upload: ";
            }
            showErrorDialog(message);
        },

        onFinish: () => {
            fileUploadForm.clearErrors();
            fileUploadForm.reset();
        },
    });
}
function handleDrop(ev) {
    dragOver.value = false;
    const files = ev.dataTransfer.files;

    if (!files.length) {
        return;
    }
    uploadFiles(files);
}
function onDragOver() {
    dragOver.value = true;
}
function onDragLeave() {
    dragOver.value = false;
}

//hooks
onMounted(() => {
    emitter.on(FILE_UPLOAD_STARTED, uploadFiles);
});
</script>

<style scoped>
.dropzone {
    border: 2px dashed #706363;
    border-radius: 5px;
    padding: 10px;
    margin: 10px 0;
    text-align: center;
    color: #706363;
    display: flex;
    justify-content: center;
}
</style>
