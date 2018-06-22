@forelse($knockouts as $knockout)
    <div class=" {{ 'mb-3 card card--knockouts card--'.$knockout->slug}}">
        <div class="card-header"> {{ $knockout->name  }}</div>
        <table class="table table-vsm table-knockouts">
            <tbody>
                @forelse($knockout->matches as $match)
                    @include('matches._match')
                @empty
                @endforelse
            </tbody>
        </table>
    </div>
@empty
@endforelse