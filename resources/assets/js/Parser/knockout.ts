import MatchModel from '../Model/match';
import KnockoutModel from '../Model/knockout';
import ResultParser from '../Parser/result';
import DataParser from '../Parser/data';
import StadiumParser from '../Parser/stadium';
import ChannelParser from '../Parser/channel';
import TeamParser from '../Parser/team';
import GroupModel from '../Model/group';
import TeamModel from '../Model/team';
import AppModel from '../Model/app';

import Data from '../store/modules/data'; 
import axios from 'axios';

class KnockoutParser {
    public static knockoutmatches: any = [];
    public static parse(phases: any, groups: GroupModel[]): KnockoutModel[] {
        const output: KnockoutModel[] = [];
        Object.keys(phases).forEach((key) => {
            output.push(new KnockoutModel(
                key,
                phases[key].name,
                KnockoutParser.createKnockoutMatches(phases[key].matches, groups, phases[key].name)))
            ;
        });
        return output;
    }

    public static updateKnockouts(data: AppModel): AppModel {
        data.getKnockouts().forEach((k: KnockoutModel) => {
            k.getMatches().forEach((m: MatchModel) => {
                m.setHomeTeam(this.getKnockoutTeam(m.getType(), m.getRawHometeam(), data.getGroups()));
                m.setAwayTeam(this.getKnockoutTeam(m.getType(), m.getRawAwayteam(), data.getGroups()));
            });
        });
        return data;
    }  

    private static createKnockoutMatches(matches: any, groups: GroupModel[], key: string): MatchModel[] {
        const output: MatchModel[] = [];
        matches.forEach((match: any) => {
            const stadium = StadiumParser.getStadium(match.stadium);
            if (stadium) {
                const obj = new MatchModel(
                    match.name,
                    KnockoutParser.getKnockoutTeam(match.type, match.home_team, groups),
                    KnockoutParser.getKnockoutTeam(match.type, match.away_team, groups),
                    ResultParser.getResult(match, 'home'),
                    ResultParser.getResult(match, 'away'),
                    DataParser.getDate(match.date),
                    stadium,
                    ChannelParser.getChannels(match.channels),
                    match.type,
                    match.home_team,
                    match.away_team,
                    key)
                ;
                if (ResultParser.getPenaltyResult(match, 'home')) {
                    obj.setHomePenalty(ResultParser.getPenaltyResult(match, 'home'));
                }

                if (ResultParser.getPenaltyResult(match, 'away')) {
                    obj.setAwayPenalty(ResultParser.getPenaltyResult(match, 'away'));
                }
                KnockoutParser.knockoutmatches.push({ name: match.name, obj });
                output.push(obj);
                stadium.addMatch(obj);
            }
        });
        return output;
    }

    private static getKnockoutTeam(type: string, matchteam: string|number|TeamModel, groups: GroupModel[]): string | TeamModel {
        let foundmatch;
        let foundteam;
        switch (type) {
            default:
                if (typeof matchteam === 'number') {
                    throw new Error('matchteam variable can not be a number with type "' + type + '"');
                }
                return matchteam;
            case 'qualified':
                if (typeof matchteam === 'number') {
                    foundteam = TeamParser.getTeam(matchteam);
                    if (foundteam) {
                        return foundteam;
                    }
                }
                if (typeof matchteam === 'string') {
                    const splitted = matchteam.split('_');
                    const foundGroup = groups.find((group) => {
                        return group.getName() === splitted[1];
                    });
                    if (!foundGroup) {
                        throw new Error('Group not found in ' + matchteam);
                    }

                    if (splitted[0] === 'winner') {
                        return foundGroup.getFinished()
                            ? foundGroup.getStandings()[0].getTeam()
                            : 'Winner of group ' + foundGroup.getName().toUpperCase()
                            ;
                    }

                    return foundGroup.getFinished()
                        ? foundGroup.getStandings()[1].getTeam()
                        : 'Runner up group ' + foundGroup.getName().toUpperCase()
                    ;
                }

                throw new Error('matchteam variable should be a string ' + matchteam + ' given');
            case 'winner':
                if (typeof matchteam === 'number') {
                    foundteam = TeamParser.getTeam(matchteam);
                    if (foundteam) {
                        return foundteam;
                    }
                }
                foundmatch = KnockoutParser.findKnockoutMatch(matchteam);
                if (foundmatch && foundmatch.isFinish()) {
                    return foundmatch.getWinner();
                }
                return 'Winner of match ' + matchteam;
            case 'loser':
                if (typeof matchteam === 'number') {
                    foundteam = TeamParser.getTeam(matchteam);
                    if (foundteam) {
                        return foundteam;
                    }
                }
                foundmatch = KnockoutParser.findKnockoutMatch(matchteam);
                if (foundmatch && foundmatch.isFinish()) {
                    return foundmatch.getLoser();
                }
                return 'Loser of match ' + matchteam;
        }
    }

    // Pronostic  
    // Update Pronostic knockout matches team
        public static updatePronosticKnockouts(data: AppModel): AppModel {
            data.getKnockouts().forEach((k: KnockoutModel) => {
                k.getMatches().forEach((m: MatchModel) => {
                    // update pronostics
                    let match_id = m.getId();
                    let pronostic = Data.state.pronostics.find(function( p ) {
                        return p.match_id == match_id;
                    }); 
                    let team;
                    if(pronostic){
                        team = this.getPronosticKnockoutTeam(m.getType(), m.getRawHometeam(), data.getGroups());
                        if(pronostic.team_h !== team){ pronostic.team_h=team; pronostic.score_h=null; }
                        team = this.getPronosticKnockoutTeam(m.getType(), m.getRawAwayteam(), data.getGroups());
                        if(pronostic.team_a !== team){ pronostic.team_a=team; pronostic.score_a=null; }
                    }
                });
            });
            return data;
        }  
    //    return team_id or null          (m.getType(), m.getRawHometeam(), data.getGroups())
    private static getPronosticKnockoutTeam(type: string, matchteam: string | TeamModel, groups: GroupModel[]): number {
        let pronostic;
        switch (type) {
            default:
                return null;
            case 'qualified':
                if (typeof matchteam === 'string') {   
                    // "name": 49, "type": "qualified", "home_team": "winner_a", "away_team": "runner_b",
                    const splitted = matchteam.split('_');
                    const foundGroup = groups.find((group) => {
                        return group.getName() === splitted[1];
                    });
                    if (!foundGroup) {
                        throw new Error('Group not found in ' + matchteam);
                    }
                    // pronostic group winner team 
                    if ( foundGroup.getPronosticFinished() ){
                        if (splitted[0] === 'winner'){
                            let team = foundGroup.getPronosticStandings()[0].getTeam();
                            if(typeof team !== 'string'){                                
                                return team.getId();
                            }
                        }
                        else{
                            let team = foundGroup.getPronosticStandings()[1].getTeam();
                            if(typeof team !== 'string'){
                                return team.getId();
                            }
                        }
                    }
                    return null;
                }
                throw new Error('matchteam variable should be a string ' + matchteam + ' given');

            case 'winner':
                // "name": 57, "type": "winner", "home_team": 49, "away_team": 50,
                pronostic = Data.state.pronostics.find(function( p ) {
                    return p.match_id == matchteam;
                }); 
                if (pronostic && pronostic.score_h !== null && pronostic.score_a !== null) {
                    if (pronostic.score_h > pronostic.score_a){
                        return pronostic.team_h;
                    }
                    else {
                        return pronostic.team_a;
                    }
                }
                return null;

            case 'loser':
                // "name": 63, "type": "loser", "home_team": 61, "away_team": 62,
                pronostic = Data.state.pronostics.find(function( p ) {
                    return p.match_id == matchteam;
                }); 
                if (pronostic && pronostic.score_h !== null && pronostic.score_a !== null) {
                    if (pronostic.score_h > pronostic.score_a){
                        return pronostic.team_a;
                    }
                    else {
                        return pronostic.team_h;
                    }
                }
                return null;
        }
    }

    private static findKnockoutMatch(matchteam: string | number | TeamModel): MatchModel | undefined {
        const found = KnockoutParser.knockoutmatches.find((match: any) => {
            return match.name === matchteam;
        });
        if (found) {
            return found.obj;
        }
        return undefined;
    }

}

export default KnockoutParser;
