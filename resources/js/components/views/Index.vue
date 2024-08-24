<template>
    <div class="container-fluid">
        <div class="slider">
            <div class="glide" id="slider">
                <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                        <li class="glide__slide" style="background-image: url('/img/misc/slider/slider_casino.png'); background-position-x: right">
                            <div class="slideContent">
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
                        <li class="glide__slide" style="background-image: url('/img/misc/slider/slider_sport.png'); background-position-x: right">
                            <div class="slideContent">
                                <div class="slideContentWrapper">
                                    <div class="header">
                                        <icon class="image" icon="sport"></icon>
                                    </div>
                                    <div class="description">
                                        {{ $t('sport.index_welcome') }}
                                    </div>
                                    <router-link tag="div" to="/sport" class="button">
                                        {{ $t('sport.lets_bet') }}
                                    </router-link>
                                </div>
                            </div>
                        </li>
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
        <div class="modes">
            <div class="mode">
                <div class="image" style="background-image: url('/img/misc/casino.png')"></div>
                <div class="description">
                    <div class="title">{{ $t('general.head.casino') }}</div>
                    <div class="desc">{{ $t('general.casino_desc') }}</div>
                    <router-link tag="button" to="/casino" class="btn btn-primary">{{ $t('general.go_to_casino') }}</router-link>
                </div>
            </div>
            <div class="mode">
                <div class="image" style="background-image: url('/img/misc/sport.png')"></div>
                <div class="description">
                    <div class="title">{{ $t('general.head.sport') }}</div>
                    <div class="desc">{{ $t('sport.index_welcome') }}</div>
                    <router-link tag="button" to="/sport" class="btn btn-primary">{{ $t('general.go_to_sport') }}</router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Bus from '../../bus';
    import Glide from '@glidejs/glide';

    export default {
        methods: {
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

    .modes {
        @include themed() {
            display: flex;

            .mode {
                margin: 15px;
                width: 50%;
                background: t('sidebar');
                border-radius: 10px;
                min-height: 23vw;
                display: flex;

                .image {
                    height: 100%;
                    background-size: cover;
                    width: 18vw;
                }

                .description {
                    padding: 25px;
                    width: calc(100% - 18vw);

                    .title {
                        font-weight: 600;
                        font-size: 1.4em;
                    }

                    .desc {
                        margin-top: 15px;
                    }

                    .btn {
                        border-radius: 500px;
                        padding: 10px 30px;
                        margin-top: 25px;
                    }
                }
            }
        }
    }

    @media(max-width: 1600px) {
        .modes {
            @include themed() {
                flex-direction: column;

                .mode {
                    width: calc(100% - 30px);
                    min-height: 300px;

                    .image {
                        height: 45vmin;
                        background-size: cover;
                        width: 35vmin;
                        min-height: 300px;
                    }

                    .description {
                        width: calc(100% - 35vmin);
                    }
                }
            }
        }
    }

    @media(max-width: 700px) {
        .modes {
            @include themed() {
                .mode {
                    flex-direction: column;
                    position: relative;

                    .image {
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        opacity: 0.1;
                    }

                    .description {
                        z-index: 1;

                        width: 100%;
                    }
                }
            }
        }
    }
</style>
