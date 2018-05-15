@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Bienvenue(e) @auth {{Auth::user()->name}} @endauth
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h2>Règlement des pronostics de Foot</h2>
                    <strong>Principes:</strong>
                    <ul>
                        <li>le prix de participation est de CHF 10.-</li>
                        <li>l'intégralité des prix sera reversée selon le barème suivant :
                            <ul>
                                 <li>50 % pour le vainqueur</li>
                                 <li>30 % pour le deuxième</li>
                                 <li>15 % pour le troisième</li>
                                 <li>5 % pour l'avant dernier</li>
                                 <li>En cas d'égalité après la finale, les participants seront départagés (par ordre de priorité) :
                                     <ol>
                                         <li>Celui qui avait le plus de points après le premier tour</li>
                                         <li>Une femme remporte sur un homme</li>
                                         <li>Date de payement au sécretariat</li>
                                         <li>Repartition des gains entre vainqueurs</li>
                                     </ol>
                                 </li>
                            </ul>
                        </li>
                        <li>1 feuille de pronostic par personne</li>
                        <li>Il n'y a pas de date limite pour l'inscription.</li>
                        <li><strong>Les résultats peuvent être modifiés jusqu'à un jour avant le match</strong></li>
                    </ul>
                    <strong>Décompte des points</strong>
                    <ol>
                        <li><strong>Premier tour (poules)</strong>
                            <ul>
                                <li>2 points pour avoir choisi la bonne équipe gagnante (ou match nul)</li>
                                <li>1 point supplémentaire pour le score d'une équipe du match correct</li>
                            </ul>
                        </li>
                        <li><strong>Huitièmes de finale</strong>
                            <ul>
                                <li>4 points pour avoir choisi la bonne équipe qualifiée</li>
                            </ul>
                        </li>
                        <li><strong>Quart de finale</strong>
                            <ul>
                                <li>6 points pour avoir choisi la bonne équipe qualifiée</li>
                            </ul>
                        </li>
                        <li><strong>Semi-finale</strong>
                            <ul>
                                <li>8 points pour avoir choisi la bonne équipe qualifiée</li>
                            </ul>
                        </li>
                        <li><strong>Petite finale</strong>
                            <ul>
                                <li>10 points pour avoir choisi la bonne équipe gagnante</li>
                            </ul>
                        </li>
                        <li><strong>Finale</strong>
                            <ul>
                                <li>10 points pour avoir choisi la bonne équipe qualifiée</li>
                                <li>20 points pour avoir choisi le bon champion du monde</li>
                            </ul>
                        </li>
                    </ol>
                    <a href="https://github.com/lsv/fifa-worldcup-2018-jsfrontend">Based on : lsv/fifa-worldcup-2018-jsfrontend</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection