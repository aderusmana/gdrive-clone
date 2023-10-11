<template>
    <Modal :show="show" max-width="md">
        <div class="p-6">
            <h2 class="text-2xl mb-2 text-red-600 font-semibold">Error</h2>
            <p>{{ message }}</p>
            <div class="mt-6 flex justify-end">
                <PrimaryButton @click="close">OK</PrimaryButton>
            </div>
        </div>
    </Modal>
</template>

<script setup>
import { ref, onMounted } from "vue";
import Modal from "../Modal.vue";
import PrimaryButton from "../PrimaryButton.vue";
import { SHOW_ERROR_DIALOG, emitter } from "@/event-bus";

//ref

const show = ref(false);
const message = ref("");

//props
const emit = defineEmits(["close"]);

//methods

function close() {
    show.value = false;
    message.value = "";
}

//hooks

onMounted(() => {
    emitter.on(SHOW_ERROR_DIALOG, ({ message: msg }) => {
        show.value = true;
        message.value = msg;
    });
});
</script>
