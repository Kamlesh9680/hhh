import Vue from 'vue';
import Vuex from 'vuex';

import i18n, { selectedLocale } from './i18n';
import createPersistedState from 'vuex-persistedstate';
import Bus from './bus';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        user: null,
        demo: false,
        locale: selectedLocale,
        theme: 'dark',
        unit: 'btc',
        currency: null,
        liveFeedEntries: 10,
        sound: true,
        quick: false,
        chat: true,
        betSlip: false,
        channel: 'casino_' + selectedLocale,
        liveChannel: 'all',
        sidebar: false,
        fiatView: false,

        country: null,
        ignoreUnavailableCountryMessage: false,

        gameInstance: [],

        games: [],
        currencies: [],
        notifications: [],

        sportGames: [],

        recentGameHistory: []
    },
    mutations: {
        setFiatView(state, view) {
            state.fiatView = view;
        },
        setGameInstance(state, gameInstance) {
            state.gameInstance = { ...state.gameInstance, gameInstance };
        },
        setUserData(state, userData) {
            state.user = userData;
            axios.defaults.headers.common['Authorization'] = `Bearer ${userData.token}`;
        },
        updateLocale(state, newLocale) {
            state.locale = newLocale;
        },
        logout(state) {
            state.user = null;
            axios.defaults.headers.common['Authorization'] = null;
        },
        switchTheme(state, newTheme) {
            newTheme = 'dark';

            state.theme = newTheme ?? (state.theme === 'dark' ? 'default' : 'dark');
            document.querySelector('html').className = 'theme--'+state.theme;
        },
        setCurrencies(state, currencies) {
            state.currencies = currencies;
        },
        setDemo(state, status) {
            state.demo = status;
        },
        setUnit(state, unit) {
            state.unit = unit;
        },
        setCurrency(state, unit) {
            state.currency = unit;
        },
        setLiveFeedEntryCount(state, count) {
            state.liveFeedEntries = count;
        },
        setSoundState(state, soundState) {
            state.sound = soundState;
        },
        setQuickState(state, quickState) {
            if(state.gameInstance.playTimeout) return;
            state.quick = quickState;
        },
        toggleChat(state, toggle = null) {
            state.chat = toggle !== null ? toggle : !state.chat;
            Bus.$emit('layoutSizeChange');
        },
        toggleBetSlip(state, toggle = null) {
            state.betSlip = toggle !== null ? toggle : !state.betSlip;
        },
        toggleSidebar(state, toggle = null) {
            state.sidebar = toggle !== null ? toggle : !state.sidebar;
            Bus.$emit('layoutSizeChange');
        },
        updateData(state) {
            axios.post('/api/data/games').then(({ data }) => state.games = data);
            axios.post('/api/data/currencies').then(({ data }) => {
                state.currencies = data;
                if(!state.currency || data[state.currency] === undefined) state.currency = data[Object.keys(data)[0]].id;
            });
            axios.post('/api/data/notifications').then(({ data }) => state.notifications = data);
            axios.post('/api/sport/categories').then(({ data }) => state.sportGames = data);

            if(!state.country) axios.post('/api/data/country').then(({ data }) => state.country = data.country);
        },
        setChatChannel(state, channel) {
            state.channel = channel;
        },
        setLiveChannel(state, channel) {
            state.liveChannel = channel;
        },
        pushRecentGame(state, id) {
            if(state.recentGameHistory.includes(id)) state.recentGameHistory = state.recentGameHistory.filter((e) => e !== id);
            state.recentGameHistory.push(id);
        },
        ignoreUnavailableCountryMessage(state) {
            state.ignoreUnavailableCountryMessage = true;
        }
    },
    actions: {
        login({ commit }, credentials) {
            return axios.get('/sanctum/csrf-cookie').then(() => {
                axios.post('/auth/login', credentials, {
                    withCredentials: true
                }).then(({ data }) => {
                    commit('setUserData', data);
                    commit('updateData');
                    Bus.$emit('login:success');
                }).catch(() => Bus.$emit('login:fail'));
            });
        },
        setUserData({ commit }, data) {
            commit('setUserData', data);
        },
        update({ commit }) {
            if(this.state.user) axios.post('/auth/update').then(({ data }) => {
                commit('setUserData', {
                    user: data,
                    token: this.state.user.token
                });
            });
        },
        logout({ commit }) {
            commit('logout');
        },
        setLiveFeedEntryCount({ commit }, count) {
            commit('setLiveFeedEntryCount', count);
        },
        changeLocale({ commit }, newLocale) {
            i18n.locale = newLocale;
            commit('updateLocale', newLocale);
        },
        switchTheme({ commit }, theme = null) {
            commit('switchTheme', theme);
        },
        setDemo({ commit }, status) {
            commit('setDemo', status);
        },
        setUnit({ commit }, unit) {
            commit('setUnit', unit);
        },
        setCurrency({ commit }, currency) {
            commit('setCurrency', currency);
        },
        updateData({ commit }) {
            commit('updateData');
        },
        setCurrencies({ commit }, currencies) {
            commit('setCurrencies', currencies);
        },
        setGameInstance({ commit }, gameInstance) {
            commit('setGameInstance', gameInstance);
        },
        setSoundState({ commit }, state) {
            commit('setSoundState', state);
        },
        setQuickState({ commit }, quickState) {
            commit('setQuickState', quickState);
        },
        toggleChat({ commit }, toggle = null) {
            commit('toggleChat', toggle);
        },
        toggleBetSlip({ commit }, toggle = null) {
            commit('toggleBetSlip', toggle);
        },
        toggleSidebar({ commit }, toggle = null) {
            commit('toggleSidebar', toggle);
        },
        setLiveChannel({ commit }, channel) {
            commit('setLiveChannel', channel);
        },
        setChatChannel({ commit }, channel) {
            commit('setChatChannel', channel);
        },
        pushRecentGame({ commit }, id) {
            commit('pushRecentGame', id);
        },
        setFiatView({ commit }, value) {
            commit('setFiatView', value);
        },
        ignoreUnavailableCountryMessage({ commit }) {
            commit('ignoreUnavailableCountryMessage');
        }
    },
    plugins: [ createPersistedState() ],
    getters: {
        isGuest: state => !state.user,
        user: state => state.user,
        locale: state => state.locale,
        theme: state => state.theme,
        demo: state => !state.user || state.demo,
        unit: state => state.unit,
        currency: state => state.currency,
        liveFeedEntries: state => state.liveFeedEntries,
        sound: state => state.sound,
        quick: state => state.quick,
        chat: state => state.chat,
        betSlip: state => state.betSlip,
        sidebar: state => state.sidebar,
        channel: state => state.channel,
        liveChannel: state => !state.user ? (state.liveChannel === 'mine' ? 'all' : state.liveChannel) : state.liveChannel,
        fiatView: state => state.fiatView,
        country: state => state.country,
        ignoreUnavailableCountryMessage: state => state.ignoreUnavailableCountryMessage,

        games: state => state.games,
        sportGames: state => state.sportGames,
        currencies: state => state.currencies,
        notifications: state => state.notifications,
        gameInstance: state => state.gameInstance,

        recentGameHistory: state => state.recentGameHistory
    }
});
