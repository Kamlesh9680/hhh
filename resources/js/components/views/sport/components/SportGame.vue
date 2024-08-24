<template>
    <div class="sportGame">
        <router-link tag="div" :to="'/sport/game/' + category.id + '/' + game.id" class="info">
            <div class="header">
                <div class="live-badge" v-if="game.live">
                    <div class="pulsating-circle"></div>
                </div>
                <icon :icon="category.icon"></icon>
                {{ game.name }}
            </div>
            <div class="league" v-if="game.league">{{ game.league.name }}</div>
            <div class="competitors">
                <div class="competitor">{{ game.competitors[0].name }}</div>
                <div class="competitor">{{ game.competitors[1].name }}</div>
            </div>
        </router-link>
        <div class="markets">
            <div class="title">{{ $t(game.markets[0].runners[0].translation.market.key, game.markets[0].runners[0].translation.market.data) }}</div>
            <div class="runners">
                <bet-slip-button :category="category.id" :market="game.markets[0]" :runner="runner" :game="game" :key="runner.name" :class="'market ' + (game.markets[0].open && game.open && runner.open ? '' : 'disabled')" v-for="runner in game.markets[0].runners">
                    <div class="runner">
                        <div class="name">{{ runner.open && game.open && game.markets[0].open ? $t(runner.translation.runner.key, runner.translation.runner.data) : '--' }}</div>
                        <div class="price">{{ runner.open && game.open && game.markets[0].open ? runner.price : '--' }}</div>
                    </div>
                </bet-slip-button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['game', 'category']
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sportGames {
        .sportGame {
            @include themed() {
                background: t('sidebar');
                display: flex;
                padding: 20px 30px;
                margin-left: -15px;
                width: calc(100% + 30px);

                &:nth-child(even) {
                    background: t('body');

                    .market {
                        background: t('sidebar') !important;

                        &:hover {
                            background: lighten(t('sidebar'), 2.5%) !important;
                        }
                    }
                }

                .info {
                    width: 50%;
                    cursor: pointer;

                    .header {
                        font-weight: 600;
                        font-size: 1.15em;
                        display: flex;
                        align-items: center;

                        i, svg {
                            margin-right: 5px;
                        }

                        .live-badge {
                            margin-right: 15px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-top: -3px;

                            .pulsating-circle {
                                &:after, &:before {
                                    background-color: #cd3a2d;
                                }
                            }
                        }
                    }

                    .league {
                        font-size: 0.9em;
                        margin-top: 5px;
                        opacity: .6;
                    }

                    .competitors {
                        margin-top: 15px;

                        .competitor:last-child {
                            margin-top: 5px;
                        }
                    }
                }

                .markets {
                    width: 50%;
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
                            background: t('body');
                            padding: 5px 0;
                            transition: background .3s ease, opacity .3s ease, color .3s ease;
                            user-select: none;
                            opacity: 1;
                            cursor: pointer;
                            color: t('text');

                            &.active {
                                background: t('secondary') !important;
                                color: black;
                            }

                            &.disabled {
                                opacity: 0.5;
                                cursor: default;
                            }

                            &:not(.disabled):hover {
                                background: lighten(t('body'), 2.5%);
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
    }

    @media(max-width: 991px) {
        .sportGame {
            flex-direction: column;

            .info, .markets {
                width: 100% !important;
            }

            .info {
                margin-bottom: 10px;
            }
        }
    }
</style>
