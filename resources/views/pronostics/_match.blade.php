<tr class="{{ ($match->finished) ? $match->pronosticClass.'--finished':'' }}" id="{{'match_'.$match->id}}">
    <td class="{{ 'text-center moment-date '.$match->gameClass.'--date' }}" title="{{ $match->date }}">{{ $match->date->diffInDays()>1? ('dans '.$match->date->diffInDays().' jours') : $match->date->diffForHumans()  }}</td>
    <td class="{{ 'text-right '}}">
        <!--  home team for pronostics-->
        <span class="team--name">  
        @if( $match->id<49 )                          
            {{ $match->homeTeam->name}}
            <i class="{{'flag-icon flag-icon-'.$match->homeTeam->iso}}"></i>            
        @else
            @php $pronostic = $pronostics->where('match_id',$match->id)->first() @endphp
            @if ( $pronostic && $pronostic->team_h )
                {{ $pronostic->homeTeam->name}}
                <i class="{{'flag-icon flag-icon-'.$pronostic->homeTeam->iso}}"></i>
            @else       
                {{ $match->team_h_description}}
            @endif
        @endif        
        </span>
        <label class="{{ $match->gameClass.'--label'}}">        
            <!--Prognostics score_h-->            
            <input type="text" data-type="home" id="{{'match-'.$match->id.'-result-home'}}" class="{{ $match->gameClass.'--result'}}" value="{{ Auth::user()->score_h($match->id)}}" onchange="update_score_home_p({{$match->id}})" {{$match->allow_pronostics()? '':'disabled'}}></input>  

        </label>
    </td>
    <td class="{{ 'text-center '.$match->gameClass.'--spacer'}}" title="{{$match->stadium->name}}">
        <small>{{ 'Match '.$match->id}}</small>
        @if( $disabled )
        <br><small>{{ $match->statistics()['percent_h']}}% {{ $match->statistics()['percent_a']}}%</small>
        @endif
    </td>
    <td class="">
        <label class="{{ $match->gameClass.'--label' }}">
            <!--Prognostics score_a-->
            <input type="text" data-type="away"  id="{{ 'match-'.$match->id.'-result-away'}}" class="{{ $match->gameClass.'--result'}}" value="{{ Auth::user()->score_a($match->id)}}" onchange="update_score_away_p({{$match->id}})" {{$match->allow_pronostics()? '':'disabled'}}></input>
        </label>
        <!--  away team for pronostics-->
        <span class="team--name">
        @if( $match->id<49 )
            <i class="{{'flag-icon flag-icon-'.$match->awayTeam->iso}}"></i>
            {{ $match->awayTeam->name}}            
        @else
            @if ( $pronostic && $pronostic->team_a )
                {{ $pronostic->awayTeam->name}}
                <i class="{{'flag-icon flag-icon-'.$pronostic->awayTeam->iso}}"></i>
            @else
                {{ $match->team_a_description }}
            @endif
        @endif
        </span>
    </td>
</tr>
