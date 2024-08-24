<template>
    <div>
        <div :class="'mobile-menu-extended '+(expandWindow ? 'd-flex' : '')" v-if="expandWindow">
            <!--<div class="control theme-switcher" @click="$store.commit('switchTheme')">
                <i class="fas fa-moon-stars" v-if="theme === 'dark'"></i>
                <i class="fas fa-sun" v-else></i>
                <div>{{ $t('general.head.theme') }}</div>
            </div>-->
            <router-link tag="div" to="/help" :class="'control ' + ($route.path === '/help' ? 'active' : '')" @click.native="expandWindow = false">
                <i class="fas fa-question-circle"></i>
                <div>{{ $t('general.head.help') }}</div>
            </router-link>
            <router-link tag="div" to="/fairness" :class="'control ' + ($route.paht === '/fairness' ? 'active' : '')" @click.native="expandWindow = false">
                <i class="fas fa-balance-scale-right"></i>
                <div>{{ $t('fairness.title') }}</div>
            </router-link>
            <div class="control" @click="expandWindow = false; gamesWindow = false; sportGamesWindow = false; openFaucetModal();">
                <i class="fad fa-coins"></i>
                <div>{{ $t('general.head.bonus_short') }}</div>
            </div>
        </div>
        <div class="mobile-menu-games" v-if="gamesWindow" @click="gamesWindow = false">
            <div class="mobile-menu-games-container os-host-flexbox">
                <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" class="games w-100">
                    <router-link to="/" class="game" @click.native="gamesWindow = false">
                        <div class="icon">
                            <i class="fas fa-spade"></i>
                        </div>
                        <div class="name">
                            {{ $t('general.head.index') }}
                        </div>
                    </router-link>

                    <router-link to="/casino/game/category/favorite" class="game" v-if="!isGuest" @click.native="gamesWindow = false">
                        <div class="icon"><icon icon="fal fa-star"></icon></div>
                        <div class="name">{{ $t('general.sidebar.favorite') }}</div>
                    </router-link>

                    <router-link to="/casino/game/category/recent" class="game" v-if="!isGuest" @click.native="gamesWindow = false">
                        <div class="icon"><icon icon="fal fa-history"></icon></div>
                        <div class="name">{{ $t('general.sidebar.recent') }}</div>
                    </router-link>
<!--
                    <a class="game" v-if="!isGuest" @click="gamesWindow = false; openInvestModal()">
                        <div class="icon"><icon icon="bch"></icon></div>
                        <div class="name">{{ $t('general.head.invest') }}</div>
                    </a>
-->
                    <router-link to="/admin" v-if="!isGuest && user.user.access === 'admin'" class="game" @click.native="gamesWindow = false">
                        <div class="icon"><i class="fas fa-cog"></i></div>
                        <div class="name">{{ $t('general.sidebar.admin') }}</div>
                    </router-link>

                    <router-link v-for="game in games" v-if="!game.isDisabled" :key="game.id" :to="'/casino/game/' + game.id" class="game" @click.native="gamesWindow = false">
                        <div class="icon"><icon :icon="game.icon"></icon></div>
                        <div class="name">{{ game.name }}</div>
                    </router-link>
                </overlay-scrollbars>
            </div>
        </div>
        <div class="mobile-menu-games" v-if="sportGamesWindow" @click="sportGamesWindow = false">
            <div class="mobile-menu-games-container os-host-flexbox">
                <overlay-scrollbars :options="{ scrollbars: { autoHide: 'leave' }, className: 'os-theme-thin-light' }" class="games w-100">
                    <router-link v-for="game in sportGames" :key="game.id" :to="'/sport/category/' + game.id" class="game" @click.native="sportGamesWindow = false">
                        <div class="icon"><icon :icon="game.icon"></icon></div>
                        <div class="name">{{ game.id }}</div>
                    </router-link>
                </overlay-scrollbars>
            </div>
        </div>
        <div class="mobile-menu">
            <div class="control" @click="gamesWindow = !gamesWindow; sportGamesWindow = false; expandWindow = false; $store.dispatch('toggleChat', false)">
                <icon icon="casino"></icon>
                <div><i :class="'fal fa-angle-up '+(gamesWindow ? 'fa-rotate-180' : '')" style="margin-right: 1px"></i> {{ $t('general.head.casino') }}</div>
            </div>
            <div class="control" @click="sportGamesWindow = !sportGamesWindow; gamesWindow = false; expandWindow = false; $store.dispatch('toggleChat', false)">
                <icon icon="sport"></icon>
                <div><i :class="'fal fa-angle-up '+(sportGamesWindow ? 'fa-rotate-180' : '')" style="margin-right: 1px"></i> {{ $t('general.head.sport') }}</div>
            </div>
            <div v-if="!isCasino()" class="control" @click="sportGamesWindow = false; gamesWindow = false; expandWindow = false; $store.dispatch('toggleBetSlip')">
                <icon icon="fas fa-ticket-alt"></icon>
                <div>{{ $t('sport.bet_slip') }}</div>
            </div>
            <div class="control" @click="$store.dispatch('toggleChat'); expandWindow = false; sportGamesWindow = false; gamesWindow = false">
                <i class="fad fa-comments"></i>
                <div>{{ $t('general.head.chat') }}</div>
            </div>
            <div :class="'control '+(expandWindow ? 'd-flex' : '')" @click="expandWindow = !expandWindow; gamesWindow = false; $store.dispatch('toggleChat', false)">
                <i :class="'fal fa-angle-up '+(expandWindow ? 'fa-rotate-180' : '')"></i>
            </div>
        </div>
    </div>
</template>

<script>
    import { mapGetters } from 'vuex';
    import AuthModal from "../modals/AuthModal";
    import InvestModal from "../modals/InvestModal";

    export default {
        data() {
            return {
                gamesWindow: false,
                sportGamesWindow: false,
                expandWindow: false
            }
        },
        methods: {
            openLoginModal() {
                AuthModal.methods.open('auth');
            },
            openFaucetModal() {
                if(this.isGuest) return this.openLoginModal();
                this.$router.push('/bonus');
            },
            openInvestModal() {
                InvestModal.methods.open();
            }
        },
        computed: {
            ...mapGetters(['theme', 'games', 'isGuest', 'user', 'sportGames'])
        }
    }
</script>
