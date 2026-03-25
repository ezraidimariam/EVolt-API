@extends('layouts.app')

@section('title', 'Admin Dashboard - EVolt')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-cog mr-2 text-purple-600"></i>
            Tableau de bord Admin
        </h1>
        <p class="mt-2 text-gray-600">Gestion des stations et réservations</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-calendar text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Réservations</h3>
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['total_reservations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Payées</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['payee_reservations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">En cours</h3>
                    <p class="text-2xl font-bold text-yellow-600">{{ $stats['en_cours_reservations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Annulées</h3>
                    <p class="text-2xl font-bold text-red-600">{{ $stats['annulee_reservations'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Station Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6 px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i class="fas fa-charging-station text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Total Stations</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['total_stations'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-power-off text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Stations Actives</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $stats['active_stations'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 px-4">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.stations') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-list text-purple-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Gérer les stations</h3>
                        <p class="text-gray-600">Voir et modifier toutes les stations</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.stations.create') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-plus-circle text-green-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Ajouter une station</h3>
                        <p class="text-gray-600">Créer une nouvelle station</p>
                    </div>
                </div>
            </a>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <i class="fas fa-chart-line text-blue-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Statistiques avancées</h3>
                        <p class="text-gray-600">Analyser les tendances</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="mt-8 px-4">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Réservations récentes</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateur</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Station</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($lastReservations as $reservation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $reservation->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->chargingStation->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $reservation->start_time->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($reservation->status == 'payee') bg-green-100 text-green-800
                                        @elseif($reservation->status == 'en_cours') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($reservation->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucune réservation trouvée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
