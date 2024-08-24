<template>
    <div class="container-fluid">
        <div class="slider">
            <div class="glide" id="slider">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide" style="background-image: url('/img/misc/slider/slider_1.svg')">
                            <div class="slideContent right">
                                <div class="slideContentWrapper">
                                    <div class="header">
                                        <icon class="image" icon="casino"></icon>
                                    </div>
                                    <div class="description">
                                        {{ $t('sport.casino_welcome') }}
                                    </div>
                                    <router-link tag="div" to="/casino" class="button">
                                        {{ $t('sport.lets_bet') }}
                                    </router-link>
                                </div>
                            </div>
                        </li>
                        <li class="glide__slide" style="background-image: url('/img/misc/slider/slider_2.png');"></li>
                    </ul>
                </div>
                <div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<"></button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">"></button>
                </div>
                <div class="glide__bullets" data-glide-el="controls[nav]">
                    <button class="glide__bullet" data-glide-dir="=0"></button>
                    <button class="glide__bullet" data-glide-dir="=1"></button>
                </div>
            </div>
        </div>

        <div class="indexCategories">
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                <div class="category active" @click="$router.push('/casino/game/category/originals')">
                    <icon icon="originals"></icon>
                    <div>{{ $t('general.sidebar.all_games') }}</div>
                </div>

                <template v-if="!isGuest">
                    <div class="category" @click="$router.push('/casino/game/category/favorite')">
                        <icon icon="fal fa-star"></icon>
                        <div>{{ $t('general.sidebar.favorite') }}</div>
                    </div>
                    <div class="category" @click="$router.push('/casino/game/category/recent')">
                        <icon icon="fal fa-history"></icon>
                        <div>{{ $t('general.sidebar.recent') }}</div>
                    </div>
                </template>

                <div class="category" @click="$router.push('/casino/game/category/instant')">
                    <icon icon="instant"></icon>
                    <div>{{ $t('general.sidebar.instant') }}</div>
                </div>
                <div class="category" @click="$router.push('/casino/game/category/table')">
                    <icon icon="table"></icon>
                    <div>{{ $t('general.sidebar.table') }}</div>
                </div>
            </overlay-scrollbars>
        </div>
        <div class="index_cat">
            <icon icon="multiplayer"></icon>
            <div>{{ $t('general.providers') }}</div>
        </div>
        <div class="index_cat providers">
            <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }">
                <div class="provider" @click="$router.push('/casino/game/category/originals')">
                    <div style="background-image: url('/img/misc/logo.png')"></div>
                </div>
            </overlay-scrollbars>
        </div>

        <games :categories="categoryGames"></games>
    </div>
</template>

<script>
    import Bus from '../../bus';
    import Glide from '@glidejs/glide';
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import PasswordResetModal from "../modals/PasswordResetModal";
    import TermsModal from "../modals/TermsModal";

    export default {
        computed: {
            ...mapGetters(['games', 'isGuest'])
        },
        data() {
            return {
                categoryGames: {}
            };
        },
        watch: {
            games() {
                this.categoryGames = {};
                this.load();
            }
        },
        methods: {
            openFaucetModal() {
                this.$router.push('/bonus');
            },
            openAuthModal(type) {
                AuthModal.methods.open(type);
            },
            load() {
                let duplicates = [];
                _.forEach(this.games, (game) => {
                    _.forEach(game.category, (category) => {
                        if(category === 'evo-play') return;

                        if (duplicates.includes(game.id)) return;
                        duplicates.push(game.id);

                        if (!this.categoryGames[category]) this.categoryGames[category] = [game];
                        else this.categoryGames[category].push(game);
                    });
                });
            },
            initSlider() {
                try {
                    if(window.$indexGlide) window.$indexGlide.destroy();
                    const glide = new Glide('#slider', {
                        type: 'carousel',
                        perView: 1,
                        focusAt: 'center',
                        gap: 0,
                        autoplay: 4000,
                        keyboard: false
                    });
                    glide.mount();
                    window.$indexGlide = glide;
                } catch (ignored) {}
            }
        },
        created() {
            this.load();

            if(this.$route.params.user && this.$route.params.token)
                PasswordResetModal.methods.open(this.$route.params.user, this.$route.params.token);
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

<style lang="scss">
    @import "resources/sass/variables";

    .indexCategories {
        height: 75px;
        margin-bottom: 40px;

        .os-host {
            flex: 1;
            width: 0;

            .os-content {
                display: flex;
            }
        }

        @include themed() {
            display: flex;
            padding-left: 40px;
            padding-right: 40px;
            background: t('sidebar');

            .category {
                cursor: pointer;
                transition: color .3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 25px 20px;
                white-space: nowrap;

                svg, i {
                    margin-right: 10px;
                }

                &:hover {
                    color: t('secondary');
                }

                &.active {
                    color: t('secondary');
                }
            }
        }
    }

    .providers {
        display: flex;
        margin-bottom: 50px !important;

        .os-host {
            flex: 1;
            width: 0;

            .os-content {
                display: flex;
            }
        }

        .provider {
            @include themed() {
                background: t('sidebar');
                border-radius: 6px;
                padding: 10px 40px;
                margin-right: 25px;
                cursor: pointer;
                transition: background .3s ease;

                &:hover {
                    background: lighten(t('sidebar'), 2.5%);
                }

                div {
                    width: 100px;
                    height: 70px;
                    background-size: contain;
                    background-repeat: no-repeat;
                    background-position: center;
                }
            }
        }
    }

    @media(max-width: 450px) {
        .indexCategories {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }
    }
</style>
