<template>
    <div class="tags has-addons">
        <a class="tag is-link">
            {{ label }}
        </a>
        <a class="tag is-delete"
            @click="$emit('remove')"/>
    </div>
</template>

<script>
export default {
    name: 'Tag',

    props: {
        label: {
            type: String,
            default: null,
        },
    },
};
</script>

<style lang="scss" scoped>
    .tag {
        padding: 0.5em;
        height: 1.4em;
        font-size: 1em;
        margin: 0.05em 0.1em 0.05em;

        &:not(body).is-delete {
            width: 1.4em;
            margin-right: 0.2em;
        }

        &:last {
            margin-right:auto;
        }
    }
</style>
