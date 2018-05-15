import Vue from 'vue';
import Vuex from 'vuex';
import createLogger from 'vuex/dist/logger';
import Data from './modules/data';
import Time from './modules/time';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
    modules: {
        Data,
        Time,
    },
    //In strict mode, whenever Vuex state is mutated outside of mutation handlers, an error will be thrown. This ensures that all state mutations can be explicitly tracked by debugging tools.
    strict: debug,
    plugins: debug ? [createLogger({})] : [],
});
