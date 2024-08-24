<template>
    <div :class="'sidebar ' + (sidebar ? 'visible' : 'hidden')">
        <div class="fixed">
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" :class="`games ${!isCasino() ? 'sportSidebar' : ''}`">
                <template v-if="isCasino()">
                    <router-link tag="div" to="/casino/game/category/favorite" class="game">
                        <icon icon="fal fa-star"></icon>
                        <div>{{ $t('general.sidebar.favorite') }}</div>
                    </router-link>

                    <router-link tag="div" to="/casino/game/category/recent" class="game">
                        <icon icon="fal fa-history"></icon>
                        <div>{{ $t('general.sidebar.recent') }}</div>
                    </router-link>

                    <router-link tag="div" to="/bonus" class="game highlight">
                        <icon icon="fas fa-stars"></icon>
                        <div>{{ $t('general.head.bonus') }}</div>
                    </router-link>

<!--                    <div class="game" v-if="!isGuest" @click="openInvestModal()">
                        <icon icon="bch"></icon>
                        <div>{{ $t('general.head.invest') }}</div>
                    </div>
-->
                    <router-link tag="div" to="/admin" v-if="!isGuest && user.user.access === 'admin'" class="game">
                        <i class="fas fa-cog"></i>
                        <div>{{ $t('general.sidebar.admin') }}</div>
                    </router-link>

                    <div class="divider"></div>

                    <router-link v-for="game in games" v-if="!game.isDisabled && game.type === 'local'" :key="game.id" tag="div" :to="`/casino/game/` + game.id" class="game">
                        <icon :icon="game.icon"></icon>
                        <div>{{ game.name }}</div>
                    </router-link>

                    <content-placeholders class="game" v-for="n in 17" :key="n" v-if="games.length === 0">
                        <content-placeholders-img/>
                    </content-placeholders>

                    <div class="divider"></div>

                    <router-link tag="div" to="/partner" class="game">
                        <icon icon="fas fa-user-secret"></icon>
                        <div>{{ $t('footer.affiliates') }}</div>
                    </router-link>

                    <router-link tag="div" to="/fairness" class="game">
                        <icon icon="fal fa-badge-check"></icon>
                        <div>{{ $t('fairness.title') }}</div>
                    </router-link>
                </template>
                <template v-else>
                    <content-placeholders class="game" v-for="n in 17" :key="n" v-if="sportGames.length === 0">
                        <content-placeholders-img/>
                    </content-placeholders>
                    <template v-if="sportGames && sportGames.length > 0">
                        <template v-if="!isGuest && user.user.access === 'admin'">
                            <router-link tag="div" to="/admin" class="game">
                                <i class="fas fa-cog"></i>
                                <div>{{ $t('general.sidebar.admin') }}</div>
                            </router-link>
                            <div class="divider"></div>
                        </template>

                        <router-link tag="div" v-for="game in sportGames" :key="game.id" :to="'/sport/category/' + game.id" class="game">
                            <icon :icon="game.icon"></icon>
                            <div>
                                {{ game.id }}
                            </div>
                        </router-link>
                    </template>
                </template>
            </overlay-scrollbars>
            <div :class="`game ${betSlip ? 'active' : ''}`" v-if="!isCasino()" @click="$store.dispatch('toggleBetSlip')">
                <icon icon="fas fa-ticket-alt"></icon>
                <div>{{ $t('sport.bet_slip') }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import InvestModal from "../modals/InvestModal";

    export default {
        computed: {
            ...mapGetters(['isGuest', 'user', 'theme', 'games', 'currencies', 'sidebar', 'sportGames', 'betSlip'])
        },
        methods: {
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            openInvestModal() {
                InvestModal.methods.open();
            }
        }
    }
</script>

<style lang="scss">
    @import "resources/sass/variables";

    .sidebar.visible {
        width: 180px;

        .recentTpGames {
            display: flex !important;
        }

        .fixed {
            width: 180px;

            .game.router-link-exact-active {
                &:before {
                    background: rgba(black, .2);
                }
            }

            .game {
                justify-content: unset;
                padding-left: 17px;
                padding-right: 17px;
                position: relative;

                i {
                    width: 25px;
                }

                svg {
                    margin-right: 11px;
                }

                div {
                    display: block;
                    opacity: 1;
                }
            }
        }
    }

    .sidebar.visible ~.pageWrapper {
        padding-left: 180px;
    }

    .sidebar {
        width: $sidebar-width;
        flex-shrink: 0;
        z-index: 38002;
        transition: width 0.3s ease;

        .fixed {
            position: fixed;
            top: 0;
            width: $sidebar-width;
            height: 100%;
            border-radius: 3px;
            padding: 15px 0;

            @include themed() {
                border-right: 2px solid t('border');
                background: rgba(t('sidebar'), .8);
                backdrop-filter: blur(20px);
                transition: background 0.15s ease, width .3s ease;

                .games {
                    height: 100%;

                    &.sportSidebar {
                        height: calc(100% - 35px);
                    }

                    border-radius: 3px;

                    .divider {
                        margin-top: 5px !important;
                    }

                    .recentTpGames {
                        display: none;
                        width: 100%;
                        flex-direction: column;
                        margin-top: 10px;
                        border-top: 2px solid t('border');
                        padding-top: 15px;

                        .loaderContainer {
                            width: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            transform: scale(0.6);
                        }

                        .btn {
                            text-transform: uppercase;
                            margin-top: 10px;
                        }

                        .recentTpGame {
                            display: flex;
                            flex-direction: column;

                            .info {
                                display: flex;
                                padding-left: 15px;
                                padding-right: 15px;

                                .image {
                                    width: 50px;
                                    height: 50px;
                                    background-position: center;
                                    background-size: cover;
                                    margin-right: 10px;
                                    border-radius: 3px;
                                    cursor: pointer;
                                    display: flex;

                                    svg, i {
                                        margin: auto;
                                        font-size: 1.8em;
                                        color: t('secondary');
                                        opacity: .8;
                                    }
                                }

                                .meta {
                                    width: calc(100% - 50px);
                                    font-size: 0.8em;
                                    font-weight: 600;

                                    .gameName {
                                        text-transform: uppercase;
                                    }
                                }
                            }
                        }
                    }

                    .btn {
                        width: calc(100% - 30px);
                        margin-left: 15px;
                        margin-right: 15px;
                        margin-bottom: 15px;
                        border-radius: 20px;

                        &.btn-primary {
                            border-bottom: 3px solid darken(t('secondary'), 5%);
                        }

                        &.btn-secondary {
                            border-bottom: 3px solid darken($gray-600, 5%);
                        }
                    }
                }
            }

            .game {
                display: flex;
                align-items: center;
                justify-content: center;
                opacity: 0.4;
                transition: opacity 0.3s ease;
                width: 100%;
                height: 40px;
                font-size: 14px;
                cursor: pointer;
                position: relative;

                @include themed() {
                    &.highlight {
                        color: t('secondary') !important;
                    }
                }

                &:before {
                    transition: background .3s ease;
                    content: '';
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                }

                div {
                    display: none;
                    opacity: 0;
                    transition: opacity 1s ease;
                }

                .vue-content-placeholders-img {
                    display: block !important;
                    opacity: 1 !important;
                }

                .vue-content-placeholders-img {
                    height: 15px;
                    width: 15px;
                    border-radius: 3px;
                }

                img {
                    width: 14px;
                    height: 14px;
                }

                i {
                    cursor: pointer;
                }

                &:hover {
                    opacity: 1;
                }

                .online {
                    position: absolute !important;
                    top: 4px !important;
                    left: 17px !important;
                    border-radius: 50%;
                    width: 15px;
                    @include themed() {
                        background: t('secondary');
                    }
                    color: white;
                    height: 15px;
                    font-size: 0.5em;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    text-align: center;
                }
            }

            .game.router-link-exact-active {
                opacity: 1;
            }
        }
    }

    @include media-breakpoint-down(md) {
        .sidebar {
            display: none;
        }
    }
</style>
