import GroupModel from '../Model/group';
import MatchModel from '../Model/match';
import StandingModel from '../Model/standing';
import TeamParser from '../Parser/team';
import ResultParser from '../Parser/result';
import DataParser from '../Parser/data';
import StadiumParser from '../Parser/stadium';
import ChannelParser from '../Parser/channel';

import Data from '../store/modules/data'; 

class GroupParser {
    public static parse(groupdata: any): GroupModel[] {
        const models: GroupModel[] = [];
        // The Object.keys() method returns an array of a given object's property's names   here keys:"a", "b", "c", "d"...
        Object.keys(groupdata).forEach((key) => {
            const matches = GroupParser.createMatches(groupdata[key].matches, key);
            models.push(new GroupModel(key, GroupParser.createStandings(matches),GroupParser.createPronosticStandings(matches,key), matches, GroupParser.finished, GroupParser.pronostic_finished));
        });
        return models;
    }

    public static updateStandings(group: GroupModel) {
        group.setStandings(this.createStandings(group.getMatches()));
        let finish = true;
        group.getMatches().forEach((m: MatchModel) => {
            if (m.getHomeResult() === null || m.getAwayResult() === null) {
                finish = false;
            }
        });
        group.setFinish(finish);
    }

    private static finished: boolean = false;
    private static pronostic_finished = false;

    private static createMatches(data: any, key: string): MatchModel[] {
        GroupParser.finished = false;
        const matches: MatchModel[] = [];
        data.forEach((match: any) => {
            const hometeam = TeamParser.getTeam(match.home_team);
            const awayteam = TeamParser.getTeam(match.away_team);
            const stadium = StadiumParser.getStadium(match.stadium);
            if (hometeam && awayteam && stadium) {
                const object = new MatchModel(
                    match.name,
                    hometeam,
                    awayteam,
                    ResultParser.getResult(match, 'home'),
                    ResultParser.getResult(match, 'away'),
                    DataParser.getDate(match.date),
                    stadium,
                    ChannelParser.getChannels(match.channels),
                    'groups',
                    null,
                    null,
                    key);

                if (object.getHomeResult() !== null || object.getAwayResult() !== null) {
                    GroupParser.finished = true;
                }

                matches.push(object);
                stadium.addMatch(object);
            }
        });
        return matches;
    }

    private static sortStandings(matches: MatchModel[], standings: StandingModel[]): StandingModel[] {
        standings.sort((a: StandingModel, b: StandingModel) => {
            // compare points
            if (a.getPoints() !== b.getPoints()) {
                return a.getPoints() < b.getPoints() ? 1 : -1;
            }
            // compare goal difference
            if (a.getGoalsDifference() !== b.getGoalsDifference()) {
                return a.getGoalsDifference() < b.getGoalsDifference() ? 1 : -1;
            }
            // compare match between the two team
            let match = matches.find((m: MatchModel) => {
                const ateam = a.getTeam();
                const bteam = b.getTeam();
                const hometeam = m.getHomeTeam();
                const awayteam = m.getAwayTeam();
                if (typeof hometeam !== 'string' && typeof awayteam !== 'string' && typeof ateam !== 'string' && typeof bteam !== 'string') {
                    return hometeam.getId() === ateam.getId() && awayteam.getId() === bteam.getId();
                }
            });
            if (match) {
                if (match.getHomeResult() > match.getAwayResult()) {
                    return -1;
                }

                if (match.getAwayResult() > match.getHomeResult()) {
                    return 1;
                }
            }
            match = matches.find((m: MatchModel) => {
                const ateam = a.getTeam();
                const bteam = b.getTeam();
                const hometeam = m.getHomeTeam();
                const awayteam = m.getAwayTeam();
                if (typeof hometeam !== 'string' && typeof awayteam !== 'string' && typeof ateam !== 'string' && typeof bteam !== 'string') {
                    return hometeam.getId() === bteam.getId() && awayteam.getId() === ateam.getId();
                }
            });
            if (match) {
                if (match.getHomeResult() > match.getAwayResult()) {
                    return 1;
                }

                if (match.getAwayResult() > match.getHomeResult()) {
                    return -1;
                }
            }
            // fair play
            const aTeam = a.getTeam();
            const bTeam = b.getTeam();

            if (typeof aTeam !== 'string' && typeof bTeam !== 'string') {
                return aTeam.getWeight() < bTeam.getWeight() ? 1 : -1;
            }
        });
        return standings;
    }

    private static createStandings(matches: MatchModel[]): StandingModel[] {
        let standings: StandingModel[] = [];
        matches.forEach((m) => {
            standings = GroupParser.parseStandingMatch(standings, m, true);
            standings = GroupParser.parseStandingMatch(standings, m, false);
        });
        return this.sortStandings(matches, standings);
    }

    private static parseStandingMatch(standings: StandingModel[], match: MatchModel, isHometeam: boolean): StandingModel[] {
        const team = isHometeam ? match.getHomeTeam() : match.getAwayTeam();
        let index = standings.findIndex((value) => value.getTeam() === team);
        if (index === -1) {
            standings.push(new StandingModel(team));
            index = standings.findIndex((value) => value.getTeam() === team);
        }
        const standing = standings[index];

        if (match.getHomeResult() !== null && match.getAwayResult() !== null) {
            standing.addPlayed();
            if (isHometeam) {
                standing.addGoalsFor(match.getHomeResult());
                standing.addGoalsAgainst(match.getAwayResult());
                if (match.getHomeResult() === match.getAwayResult()) {
                    standing.addDraw();
                } else if (match.getHomeResult() > match.getAwayResult()) {
                    standing.addWin();
                } else {
                    standing.addLost();
                }
            } else {
                standing.addGoalsFor(match.getAwayResult());
                standing.addGoalsAgainst(match.getHomeResult());
                if (match.getHomeResult() === match.getAwayResult()) {
                    standing.addDraw();
                } else if (match.getHomeResult() < match.getAwayResult()) {
                    standing.addWin();
                } else {
                    standing.addLost();
                }
            }
            standings[index] = standing;
        }

        return standings;
    }

    // pronostic standing
    public static createPronosticStandings(matches: MatchModel[],name: string): StandingModel[] {
        let standings = GroupParser.createInitialStandings(matches);
        // get the pronostics relate to the group
        let pronostics = Data.state.pronostics.filter(function( obj ) {            
            return obj.group_name == name.toUpperCase();
        }); 
        let finish = true;
        if( pronostics.length<6){ finish = false; }  // with initial pronostics already set in database for each users, pronostics.length will be 6 anyway
        else {
            pronostics.forEach((p) => {
                if ( p.score_h == null || p.score_h == null) {
                    finish = false;
                }
            });
        }        
        GroupParser.pronostic_finished = finish;
        pronostics.forEach((p) => {
            standings = GroupParser.parseStandingPronostic(standings, p, true);
            standings = GroupParser.parseStandingPronostic(standings, p, false);
        });
        return this.sortPronosticStandings(standings);
    }

    public static updatePronosticStandings(group: GroupModel) {
        group.setPronosticStandings(this.createPronosticStandings(group.getMatches(),group.getName() ));
        // set pronostic finish
        let finish = true;
        const pronostics = Data.state.pronostics.filter(function( obj ) {
                            return obj.group_name == group.getName().toUpperCase();
                         });
        if(pronostics.length<6){ finish = false; }
        else {
            pronostics.forEach((p) => {
                if ( p.score_h === null || p.score_h === null) {
                    finish = false;
                }
            });
        }
        group.setPronosticFinish(finish);
    }

    private static createInitialStandings(matches: MatchModel[]): StandingModel[] {
        let standings: StandingModel[] = [];
        matches.forEach((match) => {
            const team = match.getHomeTeam();
            let index = standings.findIndex((value) => value.getTeam() === team);
            if (index === -1) {
                standings.push(new StandingModel(team));
                index = standings.findIndex((value) => value.getTeam() === team);
            }
        });
        return standings;
    }

    private static parseStandingPronostic(standings: StandingModel[], pronostic, isHometeam: boolean, ): StandingModel[] {
        let team_id = isHometeam ? pronostic.match.team_h :  pronostic.match.team_a;  
        const team = TeamParser.getTeam(team_id);   // get Team object
        let index = standings.findIndex((value) => value.getTeam() === team);
        const standing = standings[index];
        if (pronostic.score_h !== null && pronostic.score_a !== null) {
            standing.addPlayed();
            if (isHometeam) {
                standing.addGoalsFor(pronostic.score_h);
                standing.addGoalsAgainst(pronostic.score_a);
                if (pronostic.score_h === pronostic.score_a) {
                    standing.addDraw();
                } else if (pronostic.score_h > pronostic.score_a) {
                    standing.addWin();
                } else {
                    standing.addLost();
                }
            } else {
                standing.addGoalsFor(pronostic.score_a);
                standing.addGoalsAgainst(pronostic.score_h);
                if (pronostic.score_h === pronostic.score_a) {
                    standing.addDraw();
                } else if (pronostic.score_h < pronostic.score_a) {
                    standing.addWin();
                } else {
                    standing.addLost();
                }
            }
            standings[index] = standing;
        }
        return standings;
    }

    private static sortPronosticStandings(standings: StandingModel[]): StandingModel[] {
        standings.sort((a: StandingModel, b: StandingModel) => {
            if (a.getPoints() !== b.getPoints()) {
                return a.getPoints() < b.getPoints() ? 1 : -1;
            }

            if (a.getGoalsDifference() !== b.getGoalsDifference()) {
                return a.getGoalsDifference() < b.getGoalsDifference() ? 1 : -1;
            }
            
            let pronostic =  Data.state.pronostics.find(  (p) =>{
                const ateam = a.getTeam();  
                const bteam = b.getTeam();
                if (typeof ateam !== 'string' && typeof bteam !== 'string') {
                    return p.match.team_h === ateam.getId() && p.match.team_a === bteam.getId();
                }
            });

            if (pronostic) {
                if (pronostic.score_h && pronostic.score_a){
                    if (pronostic.score_h > pronostic.score_a ) {
                        return -1;
                    }
                    if (pronostic.score_a > pronostic.score_h ) {
                        return 1;
                    }
                }           
            }
            pronostic =  Data.state.pronostics.find(  (p) =>{
                const ateam = a.getTeam();  
                const bteam = b.getTeam();
                if (typeof ateam !== 'string' && typeof bteam !== 'string') {
                    return p.match.team_h === bteam.getId() && p.match.team_a === ateam.getId();
                }
            });
            if (pronostic) {
                if (pronostic.score_h && pronostic.score_a){
                    if (pronostic.score_h > pronostic.score_a ) {
                        return 1;
                    }
                    if (pronostic.score_a > pronostic.score_h ) {
                        return -1;
                    }
                }           
            }
        });
        return standings;
    }
}

export default GroupParser;
