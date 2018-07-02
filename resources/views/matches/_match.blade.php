<tr class="{{ ($match->finished) ? $match->gameClass.'--finished':'' }}" id="{{'match_'.$match->id}}">
    <td class="{{ 'text-center moment-date '.$match->gameClass.'--date' }}" title="{{ $match->date->diffInDays()>2? ('dans '.$match->date->diffInDays().' jours') : $match->date->diffForHumans() }}">{{  $match->date }}</td>
    <td class="{{ 'text-right '.$match->homeClass.' '.$match->gameClass.'--hometeam'}}">
        @if($match->homeTeam)
            <span class="team--name">
                <i class="{{'flag-icon flag-icon-'.$match->homeTeam->iso}}"></i>
                {{ $match->homeTeam->name}}
            </span>
        @else
            <span> {{ $match->team_h_description}} </span>
        @endif
        <label clss="{{ $match->gameClass.'--label'}}">
            <!--match score_h-->
            <input type="text" data-type="home"  id="{{ 'match-'.$match->id.'-result-home'}}" class="{{ $match->gameClass.'--result'}}" value="{{ $match->score_h}}" onchange="update_match_score_home({{$match->id}})" {{$match->allow_update()? '':'disabled'}}>
        </label>
    </td>
    <td class="{{ 'text-center '.$match->gameClass.'--spacer'}}" title="{{$match->stadium->name}}">
        <small>{{ 'Match '.$match->id}}</small>
        @if( $disabled)
            @php $statistics=$match->statistics() @endphp
            @if ($statistics)
                <br><small>{{ $statistics['percent_h']?$statistics['percent_h'].'%': ' '}} {{ $statistics['percent_a']?$statistics['percent_a'].'%':' '  }}</small>
            @endif 
        @endif
    </td>
    <td class="{{ 'text-left '.$match->awayClass.' '.$match->gameClass.'--awayteam' }}">
        <label class="{{ $match->gameClass.'--label' }}">
            <!--match score_a-->
            <input type="text" data-type="away"  id="{{ 'match-'.$match->id.'-result-away'}}" class="{{ $match->gameClass.'--result'}}" value="{{ $match->score_a}}" onchange="update_match_score_away({{$match->id}})" {{$match->allow_update()? '':'disabled'}}>
        </label>
        @if($match->awayTeam)
            <span class="team--name">
                <i class="{{'flag-icon flag-icon-'.$match->awayTeam->iso}}"></i>
                {{ $match->awayTeam->name}}
            </span>
        @else
            <span>{{ $match->team_a_description }}</span>
        @endif
    </td>
</tr>
