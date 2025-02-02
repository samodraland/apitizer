<?php
    $allowed = array(
        "mime" => array(
            "application/zip",
            "application/x-rar-compressed",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "application/vnd.ms-powerpoint",
            "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
            "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "application/pdf",
            "application/rtf",
            "image/png",
            "image/jpeg",
            "image/jpg",
            "image/gif"
        ),
        "extensions" => array(
            "doc",
            "docx",
            "xls",
            "xlsx",
            "ppt",
            "pptx",
            "pps",
            "ppsx",
            "pdf",
            "jpg",
            "jpeg",
            "png",
            "gif"
        ),
        "headers" => array("HTTP")
    );

    return $allowed;
?>