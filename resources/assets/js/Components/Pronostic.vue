<template>
    <tr :class="finishedclass" :id="'match_' + game.getId()">
        <td :class="gameclass + '--date'" class="text-center moment-date" :title="titledate" v-text="viewDate"></td>
        <td :class="homeclass + ' ' + gameclass + '--hometeam'" class="text-right">
            <teamname :team="hometeam"></teamname>
            <label :class="gameclass + '--label'">
                <input type="text" @change="setResult" :id="'match-' + game.getId() + '-result-home'" data-type="home" :class="gameclass + '--result'" :value="scores.score_h" :disabled="disabled">
            </label>
        </td>
        <td :class="gameclass + '--spacer'" class="text-center" :title="game.getStadium().getName()">
            <small v-text="'Match ' + game.getId()"></small>
        </td>
        <td :class="awayclass + ' ' + gameclass + '--awayteam'">
            <label :class="gameclass + '--label'">
                <input type="text" @change="setResult" :id="'match-' + game.getId() + '-result-away'" data-type="away" :class="gameclass + '--result'" :value="scores.score_a" :disabled="disabled">
            </label>
            <teamname :team="awayteam"></teamname>
        </td>
    </tr>
</template>

<script>
    import { mapMutations } from 'vuex';
    import MatchModel from '../Model/match';
    import Teamname from './Teamname.vue';
    import functions from '../static';
    import TeamParser from '../Parser/team';

    import moment from 'moment';

    export default {        
        props: {
            id: {
                type: String,    // id="group.getName()"  a,b,..
                required: true,
            },
            game: {
                type: MatchModel,
                required: true,
            },
            gametype: {
                type: String,
                required: true,
                default: 'groups',
            },
        },
        methods: {
            ...mapMutations([
                'SET_GROUP_PRONOSTIC',
                'SET_KNOCKOUT_PRONOSTIC',
            ]),
            setResult(e) {
                let otherobject;
                let awayscore;
                let homescore;
                if (e.target.dataset.type === 'home') {
                    homescore = e.target.value.trim();
                    otherobject = document.getElementById('match-' + this.game.getId() + '-result-away');
                    awayscore = otherobject.value.trim();
                } else {
                    awayscore = e.target.value.trim();
                    otherobject = document.getElementById('match-' + this.game.getId() + '-result-home');
                    homescore = otherobject.value.trim();
                }

                if ((awayscore && homescore) || awayscore === '' && homescore === '') {
                    if (awayscore === '' && homescore === '') {
                        awayscore = null;
                        homescore = null;
                    }

                    if (this.gametype === 'groups') {
                        this.SET_GROUP_PRONOSTIC({
                            matchid: this.game.getId(),
                            groupid: this.id,           // id="group.getName()"  a,b,..
                            homescore,
                            awayscore,
                        });
                        
                    } else {
                        this.SET_KNOCKOUT_PRONOSTIC({
                            matchid: this.game.getId(),
                            knockoutid: this.id,
                            homescore,
                            awayscore,
                        });
                    }
                    // update input scores in database table pronostics
                    axios.post('/pronostics/update_scores', {
                            score_h: homescore,
                            score_a: awayscore,
                            match_id: this.game.getId(),
                        }) ; 
                }
            },
        },
        computed: {
            disabled(){
                return  (  moment().add(24, 'hours').isBefore(this.game.getDate()) ) ? false : true;
            },
            pronostic(){
               let id = this.game.getId();
               let pronostic = this.$store.state.Data.pronostics.find(function (obj) { return obj.match_id === id; });
               if (pronostic){ 
                    return pronostic;
                }
                return null; 
            },
            scores(){       
               if (this.pronostic){ 
                    return { 
                        score_h: this.pronostic.score_h, 
                        score_a: this.pronostic.score_a 
                    }; 
                }
               else return { score_h: null, score_a:null };
            },   
            hometeam(){
                if (this.pronostic && this.pronostic.team_h){ 
                    let team_id = this.pronostic.team_h;
                    return TeamParser.getTeam(team_id);
                }
                return this.game.getHomeTeam();
            },
            awayteam(){
                if (this.pronostic && this.pronostic.team_a){ 
                    let team_id = this.pronostic.team_a;
                    return TeamParser.getTeam(team_id);
                }
                return this.game.getAwayTeam();
            },

            gameclass() {
                if (this.gametype !== 'groups') {
                    return 'table-knockouts';
                }
                return 'table-' + this.gametype;
            },
            viewDate() {
                return this.game.getDate().from(this.$store.state.Time.now);
            },
            titledate() {
                return functions.titleDate(this.game.getDate());
            },
            finished() {
                return this.game.getFinished();
            },
            finishedclass() {
                return this.game.isFinish() ? this.gameclass + '--finished' : '';
            },
            homeclass() {
                if (this.game.isFinish()) {
                    if (this.game.getHomeResult() === this.game.getAwayResult()) {
                        return this.gameclass + '--draw';
                    }
                    if (this.game.getHomeResult() > this.game.getAwayResult()) {
                        return this.gameclass + '--winner';
                    }
                    if (this.game.getHomeResult() < this.game.getAwayResult()) {
                        return this.gameclass + '--loser';
                    }
                }
                return '';
            },
            awayclass() {
                if (this.game.isFinish()) {
                    if (this.game.getHomeResult() === this.game.getAwayResult()) {
                        return this.gameclass + '--draw';
                    }
                    if (this.game.getHomeResult() < this.game.getAwayResult()) {
                        return this.gameclass + '--winner';
                    }
                    if (this.game.getHomeResult() > this.game.getAwayResult()) {
                        return this.gameclass + '--loser';
                    }
                }
                return '';
            },
        },
        components: {
            Teamname,
        },
    };
</script>
