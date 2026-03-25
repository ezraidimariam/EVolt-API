@extends('layouts.app')

@section('title', 'Nouvelle Réservation - EVolt')

@section('content')
<div class="max-w-3xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-calendar-plus mr-2 text-green-600"></i>
            Nouvelle Réservation
        </h1>
        <p class="mt-2 text-gray-600">Réservez une station de recharge pour votre véhicule électrique</p>
    </div>

    <!-- Station Info -->
    <div id="stationInfo" class="bg-white rounded-lg shadow p-6 mb-6 px-4">
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
            <p>Chargement des informations de la station...</p>
        </div>
    </div>

    <!-- Reservation Form -->
    <div class="bg-white rounded-lg shadow p-6 px-4">
        <form id="reservationForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de début</label>
                    <input type="date" id="startDate" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure de début</label>
                    <input type="time" id="startTime" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de fin</label>
                    <input type="date" id="endDate" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Heure de fin</label>
                    <input type="time" id="endTime" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                </div>
            </div>
            
            <!-- Duration Display -->
            <div class="mt-6 p-4 bg-blue-50 rounded-md">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-blue-900">Durée estimée:</span>
                    <span id="durationDisplay" class="text-lg font-bold text-blue-600">-</span>
                </div>
            </div>
            
            <!-- Price Estimation -->
            <div class="mt-4 p-4 bg-green-50 rounded-md">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-green-900">Coût estimé:</span>
                    <span id="priceDisplay" class="text-lg font-bold text-green-600">-</span>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="mt-6">
                <button type="submit" id="submitBtn"
                        class="w-full bg-green-600 text-white px-6 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                    <i class="fas fa-check-circle mr-2"></i>
                    Confirmer la réservation
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentStation = null;
const PRICE_PER_KW_PER_HOUR = 0.15; // €0.15 per kW per hour

// Load station info from URL parameter
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const stationId = urlParams.get('station');
    
    if (stationId) {
        loadStationInfo(stationId);
    } else {
        // Redirect to stations page if no station selected
        window.location.href = '/stations';
    }
    
    // Setup form listeners
    setupFormListeners();
});

async function loadStationInfo(stationId) {
    try {
        const response = await axios.get('/charging-stations');
        const stations = response.data;
        currentStation = stations.find(s => s.id == stationId);
        
        if (!currentStation) {
            throw new Error('Station not found');
        }
        
        displayStationInfo(currentStation);
        
    } catch (error) {
        console.error('Error loading station:', error);
        document.getElementById('stationInfo').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p>Station non trouvée</p>
                <a href="/stations" class="mt-2 inline-block text-green-600 hover:text-green-700">
                    Retour aux stations
                </a>
            </div>
        `;
    }
}

function displayStationInfo(station) {
    document.getElementById('stationInfo').innerHTML = `
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">${station.name}</h2>
                <p class="text-gray-600 mt-1">
                    <i class="fas fa-map-marker-alt mr-1"></i>
                    ${station.address}
                </p>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="flex items-center">
                        <i class="fas fa-plug text-green-600 mr-2"></i>
                        <span class="text-sm font-medium">Connecteur:</span>
                        <span class="text-sm ml-1">${station.connector_type}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-bolt text-yellow-600 mr-2"></i>
                        <span class="text-sm font-medium">Puissance:</span>
                        <span class="text-sm ml-1">${station.power_kw} kW</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <span class="px-3 py-1 text-sm font-medium rounded-full ${station.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${station.is_available ? 'Disponible' : 'Indisponible'}
                </span>
            </div>
        </div>
    `;
    
    // Disable form if station is not available
    if (!station.is_available) {
        document.getElementById('reservationForm').style.opacity = '0.5';
        document.getElementById('reservationForm').style.pointerEvents = 'none';
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').textContent = 'Station indisponible';
    }
}

function setupFormListeners() {
    const dateInputs = ['startDate', 'startTime', 'endDate', 'endTime'];
    dateInputs.forEach(id => {
        document.getElementById(id).addEventListener('change', updateDurationAndPrice);
    });
    
    document.getElementById('reservationForm').addEventListener('submit', handleSubmit);
}

function updateDurationAndPrice() {
    const startDate = document.getElementById('startDate').value;
    const startTime = document.getElementById('startTime').value;
    const endDate = document.getElementById('endDate').value;
    const endTime = document.getElementById('endTime').value;
    
    if (!startDate || !startTime || !endDate || !endTime || !currentStation) {
        document.getElementById('durationDisplay').textContent = '-';
        document.getElementById('priceDisplay').textContent = '-';
        return;
    }
    
    const start = new Date(`${startDate}T${startTime}`);
    const end = new Date(`${endDate}T${endTime}`);
    
    if (end <= start) {
        document.getElementById('durationDisplay').textContent = 'Date invalide';
        document.getElementById('priceDisplay').textContent = '-';
        return;
    }
    
    const durationMs = end - start;
    const durationHours = durationMs / (1000 * 60 * 60);
    
    const hours = Math.floor(durationHours);
    const minutes = Math.round((durationHours - hours) * 60);
    
    let durationText = '';
    if (hours > 0) durationText += `${hours}h`;
    if (minutes > 0) durationText += `${minutes}min`;
    
    document.getElementById('durationDisplay').textContent = durationText;
    
    // Calculate estimated price
    const estimatedPrice = currentStation.power_kw * durationHours * PRICE_PER_KW_PER_HOUR;
    document.getElementById('priceDisplay').textContent = `€${estimatedPrice.toFixed(2)}`;
}

async function handleSubmit(e) {
    e.preventDefault();
    
    if (!currentStation || !currentStation.is_available) {
        alert('Station non disponible');
        return;
    }
    
    const startDate = document.getElementById('startDate').value;
    const startTime = document.getElementById('startTime').value;
    const endDate = document.getElementById('endDate').value;
    const endTime = document.getElementById('endTime').value;
    
    const startDateTime = new Date(`${startDate}T${startTime}`);
    const endDateTime = new Date(`${endDate}T${endTime}`);
    
    if (endDateTime <= startDateTime) {
        alert('La date de fin doit être après la date de début');
        return;
    }
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Loading state
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Création de la réservation...';
    submitBtn.disabled = true;
    
    try {
        const response = await axios.post('/reservations', {
            charging_station_id: currentStation.id,
            start_time: startDateTime.toISOString(),
            end_time: endDateTime.toISOString()
        });
        
        alert('Réservation créée avec succès!');
        window.location.href = '/dashboard';
        
    } catch (error) {
        console.error('Error creating reservation:', error);
        alert('Erreur: ' + (error.response?.data?.message || 'Impossible de créer cette réservation'));
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
}
</script>
@endsection
