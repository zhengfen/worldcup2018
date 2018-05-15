import AppModel from '../../Model/app';
import KnockoutModel from '../../Model/knockout';
import MatchModel from '../../Model/match';
import GroupModel from '../../Model/group';
import DataParser from '../../Parser/data';
import GroupParser from '../../Parser/group';
import KnockoutParser from '../../Parser/knockout';

import axios from 'axios';
const Promise = require('es6-promise').Promise;

const DATAURL = 'https://cdn.rawgit.com/lsv/fifa-worldcup-2018/master/data.json';

const state = {
    loading: true as boolean,
    data: null as AppModel,
    pronostics: [],
};

// getter's result is cached based on its dependencies, and will only re-evaluate when some of its dependencies have changed. computed properties for stores
const getters = {};

// A promise has 3 states: pending, resolved, rejected. set a timeout for resolving the promise. wait(2000) returns a promise, that will be resolved in 2000ms (2 sec.)
const wait = (ms) => new Promise((r) => setTimeout(r, ms));

// actions commit mutations. Actions can contain arbitrary asynchronous operations.
const actions = {
    loadData({commit}: {commit: any}) {
        commit('LOADING', true);
        axios.get('/pronostics_json')
           .then(response => {
                commit('LOAD_PRONOSTICS', response.data);            
            })
           .then(()=>{
                //  fetch() allows you to make network requests similar to XMLHttpRequest (XHR). request a URL, get a response and parse it as JSON. Fetch API uses Promises
                fetch(DATAURL)
                    .then((response) => {
                        return response.json();
                    })
                    .then( (json) => {
                        return DataParser.parse(json);
                    })
                    .then((data: AppModel) => {
                        wait(0).then(() => {
                            commit('LOAD_DATA', data);
                            commit('LOADING', false);
                        });
                    })
                ;
            })
        ;
 
    },
};

// Vuex mutations are very similar to events: each mutation has a string type and a handler. The handler function is where we perform actual state modifications, and it will receive the state as the first argument
const mutations = {
    ['LOADING'](state: any, payload: any) {
        state.loading = payload;
    },
    ['LOAD_DATA'](state: any, payload: any) {
        state.data = payload;
    },    
    ['SET_GROUP_MATCH_RESULT'](state: any, payload: any) {
        const group = state.data.getGroups().find((g: GroupModel) => {
            return g.getName() === payload.groupid;
        });
        if (group) {
            const match = group.getMatches().find((m: MatchModel) => {
                return m.getId() === payload.matchid;
            });
            if (match) {
                match.setAwayResult(payload.awayscore);
                match.setHomeResult(payload.homescore);
                GroupParser.updateStandings(group);
                KnockoutParser.updateKnockouts(state.data);  
            }
        }
    },
    // set a pronostic for a given group match
    ['SET_GROUP_PRONOSTIC'](state: any, payload: any) {
        const group = state.data.getGroups().find((g: GroupModel) => {
            return g.getName() === payload.groupid;   //  payload.groupid   id="group.getName()"  a,b,..
        });
        if (group) {
            let pronostic = state.pronostics.find((pronostic) => {
                    return pronostic.match_id === payload.matchid;
                });
            if (pronostic) {
                    pronostic.score_h = (payload.homescore);
                    pronostic.score_a = (payload.awayscore);
                    GroupParser.updatePronosticStandings(group);
                    KnockoutParser.updatePronosticKnockouts(state.data);  
            }
        }
    },

    ['SET_KNOCKOUT_MATCH_RESULT'](state: any, payload: any) {
        const knockout = state.data.getKnockouts().find((k: KnockoutModel) => {
            return k.getName() === payload.knockoutid;
        });
        if (knockout) {
            const match = knockout.getMatches().find((m: MatchModel) => {
                return m.getId() === payload.matchid;
            });
            if (match) {
                match.setAwayResult(payload.awayscore);
                match.setHomeResult(payload.homescore);
                KnockoutParser.updateKnockouts(state.data);
            }
        }
    },
    ['SET_KNOCKOUT_PRONOSTIC'](state: any, payload: any) {
        const knockout = state.data.getKnockouts().find((k: KnockoutModel) => {
            return k.getName() === payload.knockoutid;
        });
        if (knockout) {
            let pronostic = state.pronostics.find((pronostic) => {
                return pronostic.match_id === payload.matchid; 
            });
            if (pronostic) {
                pronostic.score_a = payload.awayscore;
                pronostic.score_h = payload.homescore;
                KnockoutParser.updatePronosticKnockouts(state.data);  // TODO
            }
        }
    },

    ['LOAD_PRONOSTICS'](state:any, payload: any) {
        state.pronostics = payload;
    },   
};

export default {
    state,
    getters,
    actions,
    mutations,
};
