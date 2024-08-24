<template>
    <div class="container-fluid">
        <div class="slider sportIndexSlider">
            <div class="glide" id="slider" v-if="sportGames">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <template v-if="category.isAll">
                            <li class="glide__slide" v-for="category in sportGames" :style="categoryBackground(category)" v-if="categoryBackground(category)['background-image'] !== `url('/img/misc/slider/slider_sport.png')`">
                                <div class="slideContent" v-if="line && line[category.id] && findHighlight(category.id)">
                                    <div class="slideContentWrapper sportSliderContent">
                                        <div class="header" @click="$router.push('/sport/game/' + category.id + '/' + findHighlight(category.id).id)">
                                            <img :src="findHighlight(category.id).competitors[0].logo">
                                            <img :src="findHighlight(category.id).competitors[1].logo">
                                        </div>
                                        <div class="description" @click="$router.push('/sport/game/' + category.id + '/' + findHighlight(category.id).id)">
                                            <div class="sport-highlight-header">{{ findHighlight(category.id).name }}</div>
                                        </div>
                                        <div class="markets">
                                            <div class="title">{{ $t(findHighlight(category.id).markets[0].runners[0].translation.market.key, findHighlight(category.id).markets[0].runners[0].translation.market.data) }}</div>
                                            <div class="runners">
                                                <bet-slip-button :category="category.id" :game="findHighlight(category.id)" :market="findHighlight(category.id).markets[0]" :runner="runner" :key="runner.name" :class="'market ' + (findHighlight(category.id).markets[0].open && findHighlight(category.id).open && runner.open ? '' : 'disabled')" v-for="runner in findHighlight(category.id).markets[0].runners">
                                                    <div class="runner">
                                                        <div class="name">{{ findHighlight(category.id).markets[0].open && findHighlight(category.id).open && runner.open ? $t(runner.translation.runner.key, runner.translation.runner.data) : '--' }}</div>
                                                        <div class="price">{{ findHighlight(category.id).markets[0].open && findHighlight(category.id).open && runner.open ? runner.price : '--' }}</div>
                                                    </div>
                                                </bet-slip-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="slideContent" v-else-if="line && line[category.id]">
                                    <div class="slideContentWrapper sportSliderContent">
                                        <div class="description">{{ $t('sport.no_category_events_slider', { category: category.id }) }}</div>
                                    </div>
                                </div>
                                <div class="slideContent" v-else>
                                    <div class="slideContentWrapper sportSliderContent">
                                        <div class="header">
                                            <icon class="image" icon="sport"></icon>
                                        </div>
                                        <div class="description">{{ $t('sport.index_welcome') }}</div>
                                    </div>
                                </div>
                            </li>
                        </template>
                        <template v-else>
                            <li class="glide__slide" :style="categoryBackground()">
                                <div class="slideContent" v-if="line && findHighlight()">
                                    <div class="slideContentWrapper sportSliderContent">
                                        <div class="header" @click="$router.push('/sport/game/' + category.id + '/' + findHighlight().id)">
                                            <img :src="findHighlight().competitors[0].logo">
                                            <img :src="findHighlight().competitors[1].logo">
                                        </div>
                                        <div class="description" @click="$router.push('/sport/game/' + category.id + '/' + findHighlight().id)">
                                            <div class="sport-highlight-header">{{ findHighlight().name }}</div>
                                        </div>
                                        <div class="markets">
                                            <div class="title">{{ $t(findHighlight().markets[0].runners[0].translation.market.key, findHighlight().markets[0].runners[0].translation.market.data) }}</div>
                                            <div class="runners">
                                                <bet-slip-button :category="category.id" :game="findHighlight()" :market="findHighlight().markets[0]" :runner="runner" :key="runner.name" :class="'market ' + (findHighlight().markets[0].open && findHighlight().open && runner.open ? '' : 'disabled')" v-for="runner in findHighlight().markets[0].runners">
                                                    <div class="runner">
                                                        <div class="name">{{ findHighlight().markets[0].open && findHighlight().open && runner.open ? $t(runner.translation.runner.key, runner.translation.runner.data) : '--' }}</div>
                                                        <div class="price">{{ findHighlight().markets[0].open && findHighlight().open && runner.open ? runner.price : '--' }}</div>
                                                    </div>
                                                </bet-slip-button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </template>
                    </ul>
                </div>
                <template v-if="category.isAll">
                    <div class="glide__arrows" data-glide-el="controls" v-if="line">
                        <button class="glide__arrow glide__arrow--left" data-glide-dir="<"></button>
                        <button class="glide__arrow glide__arrow--right" data-glide-dir=">"></button>
                    </div>
                    <div class="glide__bullets" data-glide-el="controls[nav]">
                        <button class="glide__bullet" :data-glide-dir="'=' + (n - 1)" v-for="(category, n) in sportGames" v-if="categoryBackground(category)['background-image'] !== `url('/img/misc/slider/slider_sport.png')`"></button>
                    </div>
                </template>
            </div>
        </div>

        <div class="sportIndexContainer">
            <loader v-if="!line || !sportGames"></loader>
            <template v-else>
                <template v-if="category.isAll">
                    <template v-for="category in sportGames" v-if="line[category.id] && line[category.id].games.length > 0">
                        <div class="category-header">
                            <icon :icon="category.icon"></icon>
                            {{ category.id }}
                        </div>

                        <div class="sportGames">
                            <sport-game v-for="game in line[category.id].games" :key="game.id" :game="game" :category="category"></sport-game>
                        </div>
                    </template>
                </template>
                <template v-else>
                    <div class="category-header">
                        <icon :icon="category.icon"></icon>
                        {{ category.id }}
                    </div>

                    <div class="sportGames">
                        <div class="emptyCategory" v-if="line.games.length === 0">{{ $t('sport.emptyCategoryTitle') }}</div>
                        <sport-game v-for="game in line.games" :key="game.id" :game="game" :category="category"></sport-game>
                    </div>

                    <!--
                    <div class="regions" v-if="regions">
                        <div :class="'region ' + (region.expand ? 'expand' : '')" v-for="region in regions">
                            <div class="header rootHeader" @click="region.expand = !region.expand">
                                <i :class="'fal fa-chevron-down ' + (region.expand ? 'fa-rotate-180' : '')"></i> {{ region.name }}
                            </div>
                            <div class="content">
                                <div class="regions">
                                    <div class="region" v-for="league in region.leagues" @click="$router.push('/sport/league/' + category.id + '/' + league.id)">
                                        <div class="header">
                                            <i class="fal fa-chevron-right"></i> {{ league.name }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    -->
                </template>
            </template>
        </div>
    </div>
</template>

<script>
    import Bus from '../../../bus';
    import Glide from '@glidejs/glide';
    import { mapGetters } from 'vuex';

    export default {
        data() {
            return {
                line: null,
                category: null,

                regions: null,

                updateInterval: null
            }
        },
        beforeDestroy() {
            clearInterval(this.updateInterval);
        },
        created() {
            if(this.$route.params.id) {
                this.category = this.sportGames.filter((e) => e.id === this.$route.params.id)[0];
                this.loadLine(this.$route.params.id);

                this.updateInterval = setInterval(() => this.loadLine(this.$route.params.id), 1500);
            } else {
                this.category = {
                    id: this.$i18n.t('sport.live_events'),
                    icon: 'sport',
                    isAll: true
                };

                this.loadLine('all');
            }
        },
        methods: {
            categoryBackground(category = this.category) {
                switch (category.icon) {
                    case 'fad fa-futbol':
                    case 'fal fa-futbol': return {
                        'background-image': `url('/img/misc/slider/sport/soccer.png')`
                    }
                    case 'fad fa-hockey-puck': return {
                        'background-image': `url('/img/misc/slider/sport/hockey.png')`,
                        'background-position-y': '80%'
                    }
                    case 'fad fa-tennis-ball': return {
                        'background-image': `url(/img/misc/slider/sport/tennis.jpg)`,
                        'background-position-y': '80%'
                    }
                    case 'fad fa-basketball-ball': return {
                        'background-image': `url(/img/misc/slider/sport/basketball.png)`,
                        'background-position-y': '15%'
                    }
                    case 'fad fa-table-tennis': return {
                        'background-image': `url(/img/misc/slider/sport/tabletennis.png)`,
                        'background-position-y': '65%'
                    }
                    case 'fad fa-shuttlecock': return {
                        'background-image': `url(/img/misc/slider/sport/badminton.png)`,
                        'background-position-y': '65%'
                    }
                    case 'fad fa-baseball-ball': return {
                        'background-image': `url(/img/misc/slider/sport/baseball.jpg)`,
                        'background-position-y': '65%'
                    }
                    case 'fad fa-boxing-glove': return {
                        'background-image': `url(/img/misc/slider/sport/boxing.png)`,
                        'background-position-y': '70%'
                    }
                    case 'fad fa-biking': return {
                        'background-image': `url(/img/misc/slider/sport/cyclist.jpg)`,
                        'background-position-y': '70%'
                    }
                    case 'fad fa-volleyball-ball': return {
                        'background-image': `url(/img/misc/slider/sport/volleyball.png)`,
                        'background-position-y': '70%'
                    }
                    case 'fad fa-golf-ball': return {
                        'background-image': `url(/img/misc/slider/sport/golf.jpg)`,
                        'background-position-y': '50%'
                    }
                    case 'darts': return {
                        'background-image': `url(/img/misc/slider/sport/darts.png)`,
                        'background-position-y': 'bottom'
                    }
                    default: return {
                        'background-image': `url('/img/misc/slider/slider_sport.png')`,
                        'background-position-x': 'right'
                    }
                }
            },
            findHighlight(category = null) {
                if(category) {
                    if (!this.line || !this.line[category] || this.line[category].games.length === 0) return null;

                    let highlight = null;

                    for (let i = 0; i < this.line[category].games.length; i++) {
                        let game = this.line[category].games[i];
                        if (game.competitors[0].logo && game.competitors[1].logo) {
                            highlight = game;
                            break;
                        }
                    }

                    if (!highlight)
                        for (let i = 0; i < this.line[category].games.length; i++) {
                            let game = this.line[category].games[i];
                            if (game.competitors[0].logo || game.competitors[1].logo) {
                                highlight = game;
                                break;
                            }
                        }

                    if (!highlight) return this.line[category].games[0];
                    return highlight;
                } else {
                    if (!this.line || this.line.games.length === 0) return null;

                    let highlight = null;

                    for (let i = 0; i < this.line.games.length; i++) {
                        let game = this.line.games[i];
                        if (game.competitors[0].logo && game.competitors[1].logo) {
                            highlight = game;
                            break;
                        }
                    }

                    if (!highlight)
                        for (let i = 0; i < this.line.games.length; i++) {
                            let game = this.line.games[i];
                            if (game.competitors[0].logo || game.competitors[1].logo) {
                                highlight = game;
                                break;
                            }
                        }

                    if (!highlight) return this.line.games[0];
                    return highlight;
                }
            },
            loadLine(category) {
                if(category === 'all') {
                    const line = this.line === null ? {} : this.line;

                    this.sportGames.forEach((category) => {
                        axios.post('/api/sport/live', { type: category.id }).then(({data}) => {
                            line[category.id] = data;

                            if(data.games.length > 0) this.line = line;
                        });
                    });

                    return;
                }

                axios.post('/api/sport/live', { type: category }).then(({ data }) => this.line = data);
//                if(!this.regions) axios.post('/api/sport/regions', { category: category }).then(({ data }) => this.regions = data);
            },
            initSlider() {
                try {
                    if(window.$indexGlide) window.$indexGlide.destroy();
                    const glide = new Glide('#slider', {
                        type: 'carousel',
                        perView: 1,
                        focusAt: 'center',
                        gap: 0,
                        autoplay: false,
                        keyboard: false
                    });
                    glide.mount();
                    window.$indexGlide = glide;
                } catch (ignored) {}
            }
        },
        computed: {
            ...mapGetters(['sportGames'])
        },
        mounted() {
            this.initSlider();

            Bus.$on('layoutSizeChange', () => setTimeout(this.initSlider, 301), true);

            const resize = () => {
                this.initSlider();
                setTimeout(this.initSlider, 301);
            };

            resize();

            $(window).on('resize', this.initSlider);
            $(window).on('load', resize);
            $(document).ready(resize);
        }
    }
</script>

<style lang="scss" scoped>
    @import "resources/sass/variables";

    .sportIndexSlider {
        @include themed() {
            .glide__slide {
                &:after {
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    background: rgba(black, .3);
                    width: 100%;
                    height: 100%;
                    backdrop-filter: blur(10px);
                }
            }

            .description {
                text-align: center;
            }

            .header {
                width: 320px;
                display: flex;
                align-items: center;
                justify-content: center;

                img:first-child {
                    margin-right: 10px;
                }
            }

            .markets {
                width: 320px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;

                .title {
                    font-weight: 600;
                    margin-bottom: 10px;
                }

                .runners {
                    display: flex;

                    .market {
                        min-width: 100px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        background: rgba(t('sidebar'), 0.5);
                        backdrop-filter: blur(20px);
                        padding: 5px 0;
                        transition: background .3s ease, opacity .3s ease, color .3s ease;
                        user-select: none;
                        opacity: 1;
                        cursor: pointer;

                        color: t('text');

                        &.active {
                            background: t('secondary') !important;
                            color: black !important;
                        }

                        &.disabled {
                            opacity: 0.5;
                            cursor: default;
                        }

                        &:not(.disabled):hover {
                            background: rgba(lighten(t('sidebar'), 2.5%), 0.5);
                        }

                        &:first-child {
                            border-top-left-radius: 3px;
                            border-bottom-left-radius: 3px;
                        }

                        &:last-child {
                            border-top-right-radius: 3px;
                            border-bottom-right-radius: 3px;
                        }

                        .runner {
                            .name {
                                font-weight: 600;
                            }
                        }
                    }
                }
            }
        }
    }

    .sportIndexContainer {
        display: flex;
        flex-direction: column;

        @include themed() {
            .regions {
                margin-top: 20px;
                margin-left: -15px;
                width: calc(100% + 30px);

                .regions {
                    margin-bottom: 20px;
                    width: unset;
                    margin-left: unset;
                }

                .region {
                    background: t('sidebar');

                    .header {
                        padding: 20px 30px;
                        font-size: 1.1em;
                        cursor: pointer;
                        background: transparent;
                        transition: background .3s ease;
                        display: flex;
                        align-items: center;

                        i {
                            margin-right: 10px;
                            font-size: 0.5em;
                            transition: all .3s ease;
                        }

                        &:hover {
                            background: lighten(t('sidebar'), 1.5%);
                        }
                    }

                    .content {
                        display: none;
                        pointer-events: none;
                        transition: opacity 0.1s ease;
                    }

                    &:nth-child(even) {
                        background: t('body');

                        .header {
                            &:hover {
                                background: lighten(t('body'), 1.5%);
                            }
                        }
                    }

                    &.expand {
                        .rootHeader {
                            color: black;
                            background: t('secondary') !important;
                        }

                        .content {
                            display: flex;
                            flex-direction: column;
                            pointer-events: unset;
                            opacity: 1;
                        }
                    }
                }
            }

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
    }

    @media(max-width: 991px) {
        .slideContent {
            .slideContentWrapper.sportSliderContent {
                cursor: pointer;

                .header {
                    width: calc(100% - 95px) !important;
                    align-items: unset !important;
                    justify-content: unset !important;

                    img {
                        width: 50px;
                        height: 50px;
                    }
                }

                .sport-highlight-header {
                    width: 100%;
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }

                .description {
                    text-align: left !important;
                }

                .markets {
                    width: calc(100% - 95px) !important;
                    align-items: unset !important;
                    justify-content: unset !important;

                    .title {
                        display: none;
                    }

                    .runners {
                        .market {
                            font-size: 0.7em;
                            height: 50px;
                            min-width: 70px;
                        }
                    }
                }
            }
        }
    }
</style>
