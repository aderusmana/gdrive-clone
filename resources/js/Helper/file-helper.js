export function isImage(file) {
    return /^image\/\w+$/.test(file.mime);
}

export function isAudio(file) {
    return [
        "audio/mpeg",
        "audio/x-mpeg",
        "audio/mp3",
        "audio/x-mp3",
        "audio/ogg",
        "audio/webm",
        "audio/x-webm",
        "audio/wav",
        "audio/x-wav",
        "audio/x-m4a",
    ].includes(file.mime);
}

export function isVideo(file) {
    return [
        "video/mpeg",
        "video/x-mpeg",
        "video/mp4",
        "video/x-mp4",
        "video/ogg",
        "video/webm",
        "video/x-webm",
        "video/x-flv",
        "video/x-m4v",
        "video/quicktime",
    ].includes(file.mime);
}

export function isPDF(file) {
    return [
        "application/pdf",
        "application/x-pdf",
        "application/acrobat",
        "application/x-acrobat",
        "application/vnd.pdf",
        "text/pdf",
        "text/x-pdf",
    ].includes(file.mime);
}

export function isWord(file) {
    return [
        "application/msword",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/vnd.ms-word.document.macroEnabled.12",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
        "application/vnd.ms-word.template.macroEnabled.12",
    ].includes(file.mime);
}

export function isExcel(file) {
    return [
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-excel.sheet.macroEnabled.12",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
        "application/vnd.ms-excel.template.macroEnabled.12",
    ].includes(file.mime);
}

export function isPowerPoint(file) {
    return [
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
        "application/vnd.openxmlformats-officedocument.presentationml.template",
        "application/vnd.ms-powerpoint.template.macroEnabled.12",
    ].includes(file.mime);
}

export function isZip(file) {
    return /^application\/zip$/.test(file.mime);
}

export function isRar(file) {
    return /^application\/rar$/.test(file.mime);
}

export function isSql(file) {
    return [
        "text/x-sql",
        "application/x-sql",
        "application/sql",
        "application/vnd.sqlite3",
        "application/x-sqlite3",
        "application/vnd.sqlite",
        "application/x-sqlite",
        "text/x-script.sql",
        "text/x-script.php",
        "text/x-php",
        "text/x-php3",
        "text/x-php4",
    ].includes(file.mime);
}

export function isTxt(file) {
    return ["text/plain", "text/html", "text/css", "text/javascript"].includes(
        file.mime
    );
}
