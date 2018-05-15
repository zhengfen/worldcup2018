<!-- Matches -->
@forelse($groups as $group)
    <article class="col mb-3">
       <div class="card card--group">
           <div class="card-header">Groupe {{ $group->name  }}</div>
           <table class="table-bordered">
               <thead>
               <tr>
                   <th scope="col">&nbsp;</th>
                   <th scope="col" class="text-center"><abbr title="Played">Pld</abbr></th>
                   <th scope="col" class="text-center"><abbr title="Wins - Draws - Lost">W-D-L</abbr></th>
                   <th scope="col" class="text-center"><abbr title="Goals">G</abbr></th>
                   <th scope="col" class="text-center"><abbr title="Goal difference">GD</abbr></th>
                   <th scope="col" class="text-center"><abbr title="Points">Pts</abbr></th>
               </tr>
               </thead>
               <tbody>
                   @forelse($group->standings() as $key=>$standings)
                   <tr class="{{ $key === 0 ? 'table-success' : '' }} {{ $key === 1 ? 'table-info' : '' }}">                       
                       <td>
                           <span class="team--name">
                               <i class="{{'flag-icon flag-icon-'.$standings['team_iso']}}"></i>
                               {{ $standings['team_name']}}
                           </span>
                       </td>
                       <td class="text-center">{{ $standings['played'] }}</td>
                       <td class="text-center text-nowrap">{{ $standings['wins'] }} - {{ $standings['draws'] }} - {{ $standings['losts'] }}</td>
                       <td class="text-center text-nowrap">{{ $standings['goalsFor'] }} - {{ $standings['goalsAgainst'] }}</td>
                       <td class="text-center">{{ $standings['goalsFor']-$standings['goalsAgainst'] }}</td>
                       <td class="text-center">{{ $standings['wins']*3+$standings['draws'] }}</td>                       
                   </tr>                   
                   @empty
                   @endif
               </tbody>
           </table>
           <div class="card-footer p-0">
               <table class="table-groups">
                   <tbody>
                       @forelse($group->matches as $match)
                           @include('matches._match')
                       @empty
                       @endforelse
                   </tbody>
               </table>

           </div>
       </div>
    </article>
@empty
@endforelse