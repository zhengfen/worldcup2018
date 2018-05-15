<!-- Prognostics -->
@forelse($groups as $group)
    <article class="col mb-3">
       <div class="card card--group">
           <div class="card-header">Groupe {{ $group->name  }}</div>
           <table class="table-bordered" >
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
                   @foreach($standings[$group->id] as $key=>$record)
                   <tr class="{{ $key === 0 ? 'table-success' : '' }} {{ $key === 1 ? 'table-info' : '' }} ">
                       <td>
                           <span class="team--name">
                               <i class="{{'flag-icon flag-icon-'.$record['team_iso']}}"></i>
                               {{ $record['team_name'] }}
                           </span>
                       </td>
                       <td class="text-center">{{ $record['played'] }}</td>
                       <td class="text-center text-nowrap">{{ $record['wins'] }} - {{ $record['draws'] }} - {{ $record['losts'] }}</td>
                       <td class="text-center text-nowrap">{{ $record['goalsFor'] }} - {{ $record['goalsAgainst'] }}</td>
                       <td class="text-center">{{ $record['goalsFor']-$record['goalsAgainst'] }}</td>
                       <td class="text-center">{{ $record['wins']*3+$record['draws'] }}</td>                       
                   </tr>                   
                   @endforeach
               </tbody>
           </table>
           <div class="card-footer p-0">
               <table class="table-groups">
                   <tbody>
                       @forelse($group->matches as $match)
                           @include('pronostics._match')
                       @empty
                       @endforelse
                   </tbody>
               </table>
               <button type="button" v-if="withstanding" @click="isShow = !isShow" class="close"><span>&times;</span></button>
           </div>
       </div>
    </article>
@empty
@endforelse