<template>
    <div>
        <!-- نمایش کارت‌های مقاله -->
        <p>مقالات بارگذاری شدند</p>
        <!-- یا v-for روی props.posts -->
    </div>
</template>

<script>
export default {
    name: "FeedCard",
    props: {
        posts: Array,
    },
};
</script>
