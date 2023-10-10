<template>
    <MenuItem>
        <a
            href="#"
            class="text-gray-700 block px-4 py-2 text-sm relative hover:bg-indigo-200"
        >
            Upload Files
            <input
                @change="onChange"
                type="file"
                class="absolute left-0 bottom-0 top-0 right-0 cursor-pointer opacity-0"
                multiple
            />
        </a>
    </MenuItem>
</template>

<script setup>
import { MenuItem } from "@headlessui/vue";
import { emitter, FILE_UPLOAD_STARTED } from "@/event-bus.js";

//method

function onChange(ev) {
    emitter.emit(FILE_UPLOAD_STARTED, ev.target.files);
}
</script>
