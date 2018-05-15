<template>
    <input type="text" data-type="home" v-model="score" :id="id" @change="onChange">
</template>

<script>
    export default {
        props: ['value','match_id'],
        data() {
            return {
                score:this.value,
                id: 'match-' + this.match_id + '-result-home',
            };
        },
        methods: {
            onChange(e) {
                if (! e.target.value) return;
                axios.post(baseurl.value + '/prognostics/update_score_home', {
                    score_h: this.score,
                    match_id: this.match_id 
                })  
            }
        }
    }

// <score_h_input :value="{{ json_encode( Auth::user()->score_h($match->id) )}}" class="{{ $match->gameClass.'--result'}}" :match_id="{{ json_encode($match->id)}}"></score_h_input>
</script>
