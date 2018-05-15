<template>
    <section id="vue_app">
        <div v-if="loading" class="loading">Loading&#8230;</div>
        <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3" v-if="!loading">
            <router-link class="navbar-brand" :to="{name: 'phase'}">Worldcup 2018 - Russia</router-link>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav">
                    <li class="nav-item"><a  class="nav-link" href="/welcome">Bienvenue(e)</a></li>
                    <router-link tag="li" class="nav-item" :to="{name: 'pronostics'}" :exact-active-class="'active'">
                        <a class="nav-link">Pronostics</a>
                    </router-link>
                    <router-link tag="li" class="nav-item" :to="{name: 'phase'}" :exact-active-class="'active'">
                        <a class="nav-link">Matches</a>
                    </router-link>
                    <router-link tag="li" class="nav-item" :to="{name: 'stadiums'}" :exact-active-class="'active'">
                        <a class="nav-link">Stadiums</a>
                    </router-link>
                    <li class="nav-item"><a  class="nav-link" href="/ranking">Classement</a></li>
                    <li class="nav-item"><a class="nav-link" href="/admin">Admin</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto pull-right"> 
                    <li class="nav-link">    
                        
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container-fluid">
            <router-view v-if="!loading" />
        </div>
    </section>
</template>

<script>
    export default {
        date:function(){
            return {
                user: window.App.user,
            }
        },
        computed: {
            loading() {
                return this.$store.state.Data.loading;
            },
        },
        created() {
            this.$store.dispatch('loadData');
            this.$store.dispatch('setDate');
        },
    };
</script>
