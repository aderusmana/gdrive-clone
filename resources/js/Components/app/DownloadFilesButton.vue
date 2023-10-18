<template>
    <button
        @click="download"
        class="inline-flex items-center gap-1 px-4 py-2 text-sm font-medium hover:font-semibold text-gray-900 bg-white border border-gray-200 rounded-lg hover:bg-indigo-100 hover:text-indigo-700 focus:z-10 focus:ring-2 focus:ring-indigo-700 focus:text-indigo-700 transition-transform transform hover:scale-105"
    >
        <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 28 28"
            stroke-width="1.5"
            stroke="currentColor"
            class="w-4 h-4 mr-2"
        >
            <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"
            />
        </svg>
        Download
    </button>
</template>

<script setup>
import { httpGet } from "@/Helper/http-helper";
import { usePage } from "@inertiajs/vue3";

//uses
const page = usePage();

//refs

//props
const props = defineProps({
    all: {
        type: Boolean,
        default: false,
        required: false,
    },
    ids: {
        type: Array,
        required: false,
    },
    shareWithMe:false,
    shareByMe:false
});

//method
function download() {
    if (!props.all && props.ids.length === 0) {
        return;
    }
    const p = new URLSearchParams();
    if (page.props.folders?.id) {
        p.append("parent_id", page.props.folders?.id);
    }

    if (props.all) {
        p.append("all", props.all ? 1 : 0);
    } else {
        for (let id of props.ids) {
            p.append("ids[]", id);
        }
    }

    let url = route('file.download')
    if(props.shareWithMe){
        url = route('file.downloadShareWithMe')
    }else if(props.shareByMe){
        url = route('file.downloadShareByMe')
    }
    httpGet(url + "?" + p.toString()).then((res) => {
        console.log(res);
        if (!res.url) return;

        const a = document.createElement("a");
        a.download = res.fileName;
        a.href = res.url;
        a.click();
    });
}
</script>
