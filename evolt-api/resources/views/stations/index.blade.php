@extends('layouts.app')

@section('title', 'Stations de Recharge - EVolt')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="px-4 py-6 sm:px-0">
        <h1 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-map-marked-alt mr-2 text-green-600"></i>
            Stations de Recharge
        </h1>
        <p class="mt-2 text-gray-600">Trouvez et réservez des stations de recharge électriques</p>
    </div>

    <!-- Search Filters -->
    <div class="bg-white rounded-lg shadow p-6 mb-6 px-4">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Rechercher une station</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type de connecteur</label>
                <select id="connectorType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
                    <option value="">Tous les types</option>
                    <option value="Type 1">Type 1</option>
                    <option value="Type 2">Type 2</option>
                    <option value="CHAdeMO">CHAdeMO</option>
                    <option value="CCS">CCS</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Puissance minimale (kW)</label>
                <input type="number" id="minPower" min="0" step="1" placeholder="0" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rayon (km)</label>
                <input type="number" id="radius" min="1" max="50" value="10" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
            </div>
            
            <div class="flex items-end">
                <button onclick="searchStations()" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <i class="fas fa-search mr-2"></i> Rechercher
                </button>
            </div>
        </div>
        
        <!-- Location Input -->
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                <input type="number" id="latitude" step="0.000001" placeholder="33.5731" value="33.5731"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                <input type="number" id="longitude" step="0.000001" placeholder="-7.5898" value="-7.5898"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-green-500 focus:border-green-500">
            </div>
        </div>
        
        <div class="mt-2">
            <button onclick="getCurrentLocation()" class="text-sm text-green-600 hover:text-green-700">
                <i class="fas fa-location-arrow mr-1"></i> Utiliser ma position actuelle
            </button>
        </div>
    </div>

    <!-- Stations List -->
    <div class="px-4">
        <div id="stationsContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Stations will be loaded here -->
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fas fa-charging-station text-4xl mb-4"></i>
                <p class="text-lg">Chargement des stations...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentStations = [];

// Load all stations on page load
document.addEventListener('DOMContentLoaded', function() {
    loadAllStations();
});

async function loadAllStations() {
    try {
        const response = await axios.get('/charging-stations');
        currentStations = response.data;
        displayStations(currentStations);
    } catch (error) {
        console.error('Error loading stations:', error);
        document.getElementById('stationsContainer').innerHTML = 
            '<div class="col-span-full text-center py-12 text-red-500"><p>Erreur de chargement des stations</p></div>';
    }
}

async function searchStations() {
    const latitude = parseFloat(document.getElementById('latitude').value);
    const longitude = parseFloat(document.getElementById('longitude').value);
    const radius = parseFloat(document.getElementById('radius').value);
    const connectorType = document.getElementById('connectorType').value;
    const minPower = parseFloat(document.getElementById('minPower').value) || 0;

    if (!latitude || !longitude) {
        alert('Veuillez entrer des coordonnées valides');
        return;
    }

    try {
        let url = `/charging-stations/search?latitude=${latitude}&longitude=${longitude}`;
        
        if (radius) url += `&radius=${radius}`;
        if (connectorType) url += `&connector_type=${encodeURIComponent(connectorType)}`;
        if (minPower > 0) url += `&min_power=${minPower}`;

        const response = await axios.get(url);
        currentStations = response.data;
        displayStations(currentStations);
        
    } catch (error) {
        console.error('Error searching stations:', error);
        alert('Erreur lors de la recherche');
    }
}

function displayStations(stations) {
    const container = document.getElementById('stationsContainer');
    
    if (stations.length === 0) {
        container.innerHTML = `
            <div class="col-span-full text-center py-12 text-gray-500">
                <i class="fas fa-search text-4xl mb-4"></i>
                <p class="text-lg">Aucune station trouvée</p>
                <p class="text-sm mt-2">Essayez d'élargir votre recherche ou de modifier les filtres</p>
            </div>
        `;
        return;
    }

    container.innerHTML = stations.map(station => `
        <div class="station-card bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">${station.name}</h3>
                        <p class="text-sm text-gray-600 mt-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            ${station.address}
                        </p>
                    </div>
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${station.is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${station.is_available ? 'Disponible' : 'Indisponible'}
                    </span>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Connecteur:</span>
                        <span class="font-medium">${station.connector_type}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Puissance:</span>
                        <span class="font-medium">${station.power_kw} kW</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Coordonnées:</span>
                        <span class="font-medium text-xs">${station.latitude.toFixed(4)}, ${station.longitude.toFixed(4)}</span>
                    </div>
                </div>
                
                ${station.is_available ? `
                    <button onclick="createReservation(${station.id})" class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <i class="fas fa-calendar-plus mr-2"></i> Réserver cette station
                    </button>
                ` : `
                    <button disabled class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed">
                        <i class="fas fa-times-circle mr-2"></i> Indisponible
                    </button>
                `}
            </div>
        </div>
    `).join('');
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            position => {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                searchStations();
            },
            error => {
                console.error('Error getting location:', error);
                alert('Impossible d\'obtenir votre position. Veuillez entrer les coordonnées manuellement.');
            }
        );
    } else {
        alert('La géolocalisation n\'est pas supportée par votre navigateur.');
    }
}

function createReservation(stationId) {
    // Redirect to reservation creation page with station pre-selected
    window.location.href = `/reservations/create?station=${stationId}`;
}
</script>
@endsection
