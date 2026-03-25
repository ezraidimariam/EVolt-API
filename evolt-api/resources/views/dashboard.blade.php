@extends('layouts.app')

@section('title', 'Tableau de bord - EVolt')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-tachometer-alt mr-2 text-green-600"></i>
            Tableau de bord
        </h1>
        <p class="mt-2 text-gray-600">Bienvenue, {{ auth()->user()->name }}!</p>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 px-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <i class="fas fa-charging-station text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Stations disponibles</h3>
                    <p class="text-2xl font-bold text-green-600" id="availableStations">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Mes réservations</h3>
                    <p class="text-2xl font-bold text-blue-600" id="myReservations">-</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <i class="fas fa-bolt text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Sessions actives</h3>
                    <p class="text-2xl font-bold text-purple-600" id="activeSessions">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 px-4">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions rapides</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('stations') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-search text-green-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Rechercher une station</h3>
                        <p class="text-gray-600">Trouvez des stations de recharge près de chez vous</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('reservations.create') }}" class="bg-white rounded-lg shadow p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center">
                    <i class="fas fa-plus-circle text-blue-600 text-2xl mr-4"></i>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Nouvelle réservation</h3>
                        <p class="text-gray-600">Réservez une station de recharge</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Reservations -->
    <div class="mt-8 px-4">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Mes réservations récentes</h2>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6">
                <div id="recentReservations" class="space-y-4">
                    <!-- Reservations will be loaded here -->
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                        <p>Chargement de vos réservations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Load dashboard data
async function loadDashboardData() {
    try {
        // Load available stations
        const stationsResponse = await axios.get('/charging-stations');
        document.getElementById('availableStations').textContent = stationsResponse.data.length;

        // Load user reservations
        const reservationsResponse = await axios.get('/mes-reservations');
        const reservations = reservationsResponse.data;
        document.getElementById('myReservations').textContent = reservations.length;
        
        // Count active sessions
        const activeCount = reservations.filter(r => r.status === 'en_cours').length;
        document.getElementById('activeSessions').textContent = activeCount;

        // Display recent reservations
        displayRecentReservations(reservations.slice(0, 5));
        
    } catch (error) {
        console.error('Error loading dashboard data:', error);
        document.getElementById('availableStations').textContent = '0';
        document.getElementById('myReservations').textContent = '0';
        document.getElementById('activeSessions').textContent = '0';
        document.getElementById('recentReservations').innerHTML = 
            '<div class="text-center py-8 text-red-500"><p>Erreur de chargement</p></div>';
    }
}

function displayRecentReservations(reservations) {
    const container = document.getElementById('recentReservations');
    
    if (reservations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-calendar-times text-2xl mb-2"></i>
                <p>Aucune réservation pour le moment</p>
            </div>
        `;
        return;
    }

    container.innerHTML = reservations.map(reservation => `
        <div class="border-l-4 border-${getStatusColor(reservation.status)}-500 pl-4 py-2">
            <div class="flex justify-between items-start">
                <div>
                    <h4 class="font-medium text-gray-900">${reservation.charging_station.name}</h4>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        ${new Date(reservation.start_time).toLocaleDateString('fr-FR')} 
                        ${new Date(reservation.start_time).toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'})}
                    </p>
                </div>
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-${getStatusColor(reservation.status)}-100 text-${getStatusColor(reservation.status)}-800">
                    ${getStatusLabel(reservation.status)}
                </span>
            </div>
            <div class="mt-2 space-x-2">
                ${getActionButtons(reservation)}
            </div>
        </div>
    `).join('');
}

function getStatusColor(status) {
    const colors = {
        'en_cours': 'blue',
        'payee': 'green', 
        'annulee': 'red'
    };
    return colors[status] || 'gray';
}

function getStatusLabel(status) {
    const labels = {
        'en_cours': 'En cours',
        'payee': 'Payée',
        'annulee': 'Annulée'
    };
    return labels[status] || status;
}

function getActionButtons(reservation) {
    let buttons = '';
    
    if (reservation.status === 'en_cours') {
        buttons += `
            <button onclick="payReservation(${reservation.id})" class="px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                <i class="fas fa-credit-card mr-1"></i> Payer
            </button>
            <button onclick="cancelReservation(${reservation.id})" class="px-3 py-1 bg-red-600 text-white text-sm rounded hover:bg-red-700">
                <i class="fas fa-times mr-1"></i> Annuler
            </button>
        `;
    }
    
    return buttons;
}

async function payReservation(id) {
    try {
        await axios.post(`/reservations/${id}/pay`);
        alert('Réservation payée avec succès!');
        loadDashboardData();
    } catch (error) {
        alert('Erreur: ' + (error.response?.data?.message || 'Impossible de payer cette réservation'));
    }
}

async function cancelReservation(id) {
    if (!confirm('Êtes-vous sûr de vouloir annuler cette réservation?')) return;
    
    try {
        await axios.post(`/reservations/${id}/cancel`);
        alert('Réservation annulée avec succès!');
        loadDashboardData();
    } catch (error) {
        alert('Erreur: ' + (error.response?.data?.message || 'Impossible d\'annuler cette réservation'));
    }
}

// Load data when page loads
document.addEventListener('DOMContentLoaded', loadDashboardData);
</script>
@endsection
