<template>
    <Modal :show="props.modelValue" @show="onShow" max-width="md">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">Share Files </h2>
            <div class="mt-6">
                <InputLabel
                    for="shareEmail"
                    value="Email"
                    class="sr-only"
                />
                <TextInput
                    type="email"
                    ref="emailInput"
                    id="shareEmail"
                    v-model="form.email"
                    class="mt-1 block w-full"
                    :class="
                        form.errors.email
                            ? 'border-red-500 focus:border-red-500 focus:ring-red-500'
                            : ''
                    "
                    placeholder="Enter Email Address"
                    @keyup.enter="share"
                />
                <InputError :message="form.errors.email" class="mt-2" />
            </div>
            <div class="mt-6 flex justify-end">
                <SecondaryButton class="hover:bg-indigo-200" @click="closeModal"
                    >Cancel</SecondaryButton
                >
                <PrimaryButton
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    @click="share"
                    :disable="form.processing"
                    >Submit</PrimaryButton
                >
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
import { showSuccessNotification } from "@/event-bus";

//props
const props = defineProps({
    modelValue: Boolean,
    allSelected:Boolean,
    selectedIds:Array,
});

//uses
const form = useForm({
    email: null,
    all:false,
    ids: [],
    parent_id:null
});
const emit = defineEmits(["update:modelValue"]);

//ref
const emailInput = ref(null);
const page = usePage();

//method
function share() {
    form.parent_id = page.props.folders.id;
    if(props.allSelected){
        form.all = true
        form.ids = [];
    }else{
        form.ids = props.selectedIds
    }
    const email = form.email;
    form.post(route("file.share"), {
        preventScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
            showSuccessNotification(`Selected File /Folder has been share to ${email}`)
        },
        onError: () => emailInput.value.focus(),
    });
}

function onShow() {
    nextTick(() => emailInput.value.focus());
}

function closeModal() {
    emit("update:modelValue");
    form.clearErrors();
    form.reset();
}
</script>
