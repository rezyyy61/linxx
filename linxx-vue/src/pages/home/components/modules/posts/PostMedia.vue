<template>
    <div class="bg-white dark:bg-gray-800/70 rounded-2xl shadow-sm dark:shadow-md border border-gray-200 dark:border-gray-600 p-5 transition-all duration-300">
        <ImageGallery :images="post.media.filter(m => m.type === 'image')" />
        <VideoPlayer :videos="post.media.filter(m => m.type === 'video')" />
        <AudioPlayer :audios="post.media.filter(m => m.type === 'audio')" />
        <FileAttachment :files="post.media.filter(m => m.type === 'file')" />
    </div>
</template>


<script setup>
import ImageGallery from "@/pages/home/components/modules/posts/media/ImageGallery.vue";
import VideoPlayer from "@/pages/home/components/modules/posts/media/VideoPlayer.vue";
import AudioPlayer from "@/pages/home/components/modules/posts/media/AudioPlayer.vue";
import FileAttachment from "@/pages/home/components/modules/posts/media/FileAttachment.vue";

defineProps({
    post: Object
})
</script>
