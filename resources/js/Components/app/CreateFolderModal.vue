<template>
    <Modal :show="modelValue" @show="onShow" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Create New Folder</h2>
            <div class="mt-6">
                <InputLabel for="folderName" value="Folder Name" class="sr-only" />
                <TextInput type="text" ref="folderNameInput" id="folderName" v-model="form.name" class="mt-1 block w-full"
                    :class="form.errors.name ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : ''"
                    placeholder="Folder Name" @keyup.enter="createFolder" />
                <InputError :message="form.errors.name" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton class="hover:bg-indigo-200" @click="closeModal">Cancel</SecondaryButton>
                <PrimaryButton class="ml-3" :class="{ 'opacity-25': form.processing }" @click="createFolder"
                    :disable="form.processing">Submit</PrimaryButton>

            </div>
        </div>
    </Modal>
</template>

<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import InputError from "../InputError.vue";
import InputLabel from "../InputLabel.vue";
import Modal from "../Modal.vue";
import TextInput from "../TextInput.vue";
import SecondaryButton from "../SecondaryButton.vue";
import PrimaryButton from "../PrimaryButton.vue";
import { nextTick, ref } from "vue";


//props
const { modelValue } = defineProps({
    modelValue: Boolean,
});

//uses
const form = useForm({
    name: ' ',
    parent_id: null
});
const emit = defineEmits(["update:modelValue"]);

//ref
const folderNameInput = ref(null);
const page = usePage();

//method
function createFolder() {
    form.parent_id = page.props.folders.data.id
    form.post(route('folder.create'), {
        preventScroll: true,
        onSuccess: () => {
            closeModal()
            form.reset();
        },
        onError: () => folderNameInput.value.focus()
    })
}

function onShow() {
    nextTick(() => folderNameInput.value.focus());
}

function closeModal() {
    emit("update:modelValue");
    form.clearErrors();
    form.reset();
}
</script>
