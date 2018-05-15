<template>
    <div>
        <section class="groups" v-if="data && data.groups">
            <groupPronostics v-for="(group, index) in data.groups" :group="group" :key="index"></groupPronostics>
        </section>
        <section class="knockouts" v-if="data && data.knockouts">
            <knockoutPronostics v-for="(knockout, index) in data.knockouts" :knockout="knockout" :key="index"></knockoutPronostics>
        </section>
    </div>
</template>

<script>
    import GroupPronostics from '../Components/GroupPronostics.vue';
    import KnockoutPronostics from '../Components/KnockoutPronostics.vue'; //  TODO

    export default {
        computed: {
            data() {
                return this.$store.state.Data.data;
            },
        },
        components: {
            GroupPronostics,
            KnockoutPronostics,   
        },         
    };
</script>
