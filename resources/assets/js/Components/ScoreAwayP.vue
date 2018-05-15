<template>
    <input type="text" data-type="away" v-model="score" :id="id" @change="onChange">
</template>

<script>
    export default {
        props: ['value','match_id'],
        data() {
            return {
                score:this.value,
                id: 'match-' + this.match_id + '-result-away',
            };
        },
        methods: {
            onChange(e) {
                if (! e.target.value) return;
                axios.post(baseurl.value + '/prognostics/update_score_away', {
                    score_a: this.score,
                    match_id: this.match_id 
                })  
            }
        }
    }

// <score_away_p :value="{{ json_encode( Auth::user()->score_a($match->id) )}}" class="{{ $match->gameClass.'--result'}}" :match_id="{{ json_encode($match->id)}}"></score_away_p>
</script>
