<template>
    <MenuItem v-slot="{ active }">
        <a
            href="#"
            @click.prevent="showCreateFolderModal"
            class="text-gray-700 block px-4 py-2 text-sm hover:bg-indigo-200"
        >
            New Folder
            <input
                type="text"
                class="absolute left-0 bottom-0 top-0 right-0 cursor-pointer opacity-0"
            />
        </a>
    </MenuItem>
</template>

<script setup>
import { MenuItem } from "@headlessui/vue";

//method
function showCreateFolderModal() {
    this.$emit("show-create-folder-modal");
}
</script>
