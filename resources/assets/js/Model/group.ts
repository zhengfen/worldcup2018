import StandingModel from '@/Model/standing';
import MatchModel from '@/Model/match';

class GroupModel {
    private name: string;
    private standings: StandingModel[];
    private pronostic_standings: StandingModel[];   // pronostic standings
    private matches: MatchModel[];
    private finished: boolean;
    private pronostic_finished: boolean;  // pronostic finished

    public constructor(name: string, standings: StandingModel[], pronostic_standings: StandingModel[],matches: MatchModel[], finished: boolean,  pronostic_finished: boolean) {
        this.name = name;
        this.standings = standings;
        this.pronostic_standings = pronostic_standings; 
        this.matches = matches;
        this.finished = finished;
        this.pronostic_finished = pronostic_finished;
    }

    public getName(): string {
        return this.name;
    }

    public getDisplayName(): string {
        return this.getName().toUpperCase();
    }

    public getStandings(): StandingModel[] {
        return this.standings;
    }

    public setStandings(standings: StandingModel[]) {
        this.standings = standings;
    }

    // pronostic standing
    public getPronosticStandings(): StandingModel[] {
        return this.pronostic_standings;
    }

    public setPronosticStandings(standings: StandingModel[]){
        this.pronostic_standings = standings;
    }

    public getMatches(): MatchModel[] {
        return this.matches;
    }

    public getFinished(): boolean {
        return this.finished;
    }

    public setFinish(finish: boolean) {
        this.finished = finish;
    }

    // pronostic finished
    public getPronosticFinished(): boolean {
        return this.pronostic_finished;
    }

    public setPronosticFinish(finish: boolean) {
        this.pronostic_finished = finish;
    }
}

export default GroupModel;
