<template>
    <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-8">
            <div class="card">
                <!-- En-tête de la card avec le titre et le bouton -->
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Carte de Géolocalisation</h5>
                    <button @click="locateUser" class="btn btn-primary">
                        <i class="fas fa-location-arrow"></i> Me localiser
                    </button>
                </div>
                <div class="card-body">
                    <div id="map" style="height: 500px;"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import L from "leaflet";
import "leaflet/dist/leaflet.css";

export default {
    props: {
        points: {
            type: Array,
        }
    },
    setup(props) {
        const map = ref(null);
        const userMarker = ref(null);

        // Fonction pour localiser l'utilisateur
        const locateUser = () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const { latitude, longitude } = position.coords;

                        // Ajouter un marqueur pour la position de l'utilisateur
                        if (userMarker.value) {
                            userMarker.value.setLatLng([latitude, longitude]);
                        } else {
                            userMarker.value = L.marker([latitude, longitude], {
                                icon: L.icon({
                                    iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/images/marker-icon.png',
                                    iconSize: [25, 41],
                                    iconAnchor: [12, 41],
                                    popupAnchor: [1, -34],
                                }),
                            }).addTo(map.value);
                        }

                        // Centrer la carte sur la position de l'utilisateur
                        map.value.setView([latitude, longitude], 13);

                        // Ajouter un popup pour indiquer "Vous êtes ici"
                        userMarker.value.bindPopup("Vous êtes ici").openPopup();
                    },
                    (error) => {
                        console.error('Erreur de géolocalisation :', error);
                    }
                );
            } else {
                console.error('Géolocalisation non supportée par ce navigateur.');
            }
        };

        onMounted(() => {
            map.value = L.map('map').setView([51.505, -0.09], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '',
                maxZoom: 19,
            }).addTo(map.value);

            
            props.points.forEach(point => {
                L.marker([point.latitude, point.longitude])
                    .addTo(map.value)
                    .bindPopup(point.name);
            });

            
        });

        return { locateUser };
    },
};
</script>

<style scoped>

    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

    .container {
        margin-top: 2rem;
    }

    .card {
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #ddd;
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: bold;
        margin: 0;
    }

    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
        gap: 0.5rem;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 4px;
        cursor: pointer;
    }

    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }

    #map {
        height: 500px;
        width: 100%;
        border-radius: 0 0 8px 8px;
    }
</style>