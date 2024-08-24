<template>
    <div class="sportIndexContainer">
        <loader v-if="!line"></loader>
        <template v-else>
            <div class="category-header">
                <icon :icon="category.icon"></icon>
                {{ category.id }}
            </div>

            <div class="sportGames">
                <div class="emptyCategory" v-if="line.length === 0">{{ $t('sport.emptyCategoryTitle') }}</div>
                <sport-game v-for="game in line" :key="game.id" :game="game" :category="category"></sport-game>
            </div>
        </template>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                line: null,
                updateInterval: null,

                category: null
            }
        },
        created() {
            this.category = this.sportGames.filter((e) => e.id === this.$route.params.category)[0];
            this.loadLine();

            this.updateInterval = setInterval(() => this.loadLine(), 1500);
        },
        computed: {
            ...mapGetters(['sportGames'])
        },
        methods: {
            loadLine() {
                axios.post('/api/sport/line', { league_id: this.$route.params.id }).then(({ data }) => this.line = data);
            }
        },
        beforeDestroy() {
            clearInterval(this.updateInterval);
        }
    }
</script>

<style lang="scss" scoped>
    .sportIndexContainer {
        display: flex;
        flex-direction: column;
        padding: 10px 15px;

        .category-header {
            padding: 20px 15px 10px;
            margin-bottom: 25px;
            font-size: 1.5em;

            i, svg {
                margin-right: 10px;
            }
        }

        .loaderContainer {
            margin: auto;
            margin-top: 50px;
            margin-bottom: 50px;
        }

        .emptyCategory {
            text-align: center;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
    }
</style>
