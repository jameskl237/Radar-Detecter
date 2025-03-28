<template>
    <div>
        <header class="py-4 max-w-7xl mx-auto">
            <nav class="flex justify-end space-x-3">
                <Link
                    :href="route('login')"
                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Log in
                </Link>

                <Link
                    :href="route('register')"
                    class="rounded-md py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                >
                    Register
                </Link>
            </nav>
        </header>

              <!-- Modal pour enregistrer la position -->
              <div class="modal fade" id="savePositionModal" tabindex="-1" aria-hidden="true" ref="modalElement">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enregistrer une nouvelle position</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Coordonnées</label>
                            <div class="input-group mb-3">
                                <span class="input-group-text">Latitude</span>
                                <input type="text" class="form-control" v-model="form.latitude" readonly>
                                <span class="input-group-text">Longitude</span>
                                <input type="text" class="form-control" v-model="form.longitude" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Nom du point *</label>
                            <input type="text" class="form-control" id="name" v-model="form.name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="type" class="form-label">Type *</label>
                            <select class="form-select" id="type" v-model="form.type" required>
                                <option value="">Sélectionnez un type</option>
                                <option value="Panneau">Panneau</option>
                                <option value="Radar">Radar</option>
                                <option value="Position">Position</option>
                            </select>
                        </div>
                        
                        <div v-if="form.type === 'Panneau'" class="mb-3">
                            <label for="limitation" class="form-label">Limitation</label>
                            <input class="form-control" id="limitation" v-model="form.limitation">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="button" class="btn btn-primary-2" @click="submitForm">Enregistrer</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid vh-100 d-flex justify-content-center align-items-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Carte de Géolocalisation</h5>
                        <div>
                            <button @click="locateUser" class="btn btn-primary me-2">
                                <i class="fas fa-location-arrow"></i> Me localiser
                            </button>
                            <button @click="openSaveModal" class="btn btn-success" :disabled="!currentPosition">
                                <i class="fas fa-save"></i> Enregistrer ma position
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="map" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import L from "leaflet";
import "leaflet/dist/leaflet.css";
import { Link, router } from '@inertiajs/vue3';
import { Modal } from 'bootstrap';

export default {
    components: {
        Link
    },
    props: {
        points: {
            type: Array,
        }
    },
    setup(props) {
        const map = ref(null);
        const userMarker = ref(null);
        const currentPosition = ref(null);
        const modalElement = ref(null);
        let modalInstance = null;

        const form = ref({
            latitude: '',
            longitude: '',
            name: '',
            type: '',
            limitation: ''
        });

        const locateUser = () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const { latitude, longitude } = position.coords;
                        currentPosition.value = { latitude, longitude };

                        form.value.latitude = latitude;
                        form.value.longitude = longitude;

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

                        map.value.setView([latitude, longitude], 13);
                        userMarker.value.bindPopup("Vous êtes ici").openPopup();
                    },
                    (error) => {
                        console.error('Erreur de géolocalisation :', error);
                        currentPosition.value = null;
                    }
                );
            } else {
                console.error('Géolocalisation non supportée par ce navigateur.');
                currentPosition.value = null;
            }
        };

        const openSaveModal = () => {
            if (!currentPosition.value) return;
            
            if (!modalInstance) {
                modalInstance = new Modal(modalElement.value);
            }
            modalInstance.show();
        };

        const submitForm = () => {
            if (!form.value.name || !form.value.type) {
                alert("Les champs 'Nom' et 'Type' sont obligatoires");
                return;
            }

            router.post(route('store'), form.value, {
                onSuccess: () => {
                    modalInstance.hide();
                    resetForm();
                    alert('Position enregistrée avec succès!');
                },
                onError: (errors) => {
                    console.error(errors);
                    alert("Une erreur est survenue lors de l'enregistrement");
                }
            });
        };

        const resetForm = () => {
            form.value = {
                latitude: '',
                longitude: '',
                name: '',
                type: '',
                limitation: ''
            };
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
                    .bindPopup(`
                        <b>${point.name}</b><br>
                        Type: ${point.type}<br>
                        ${point.limitation ? 'Limitation: ' + point.limitation : ''}
                    `);
            });
            if (modalElement.value) {
                modalInstance = new Modal(modalElement.value);
            }

        });

        return { 
            locateUser, 
            openSaveModal,
            submitForm,
            currentPosition,
            form,
            modalElement
        };
    },
};
</script>

<style scoped>
    @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

    header {
        width: 100%;
        border-bottom: 1px solid #e2e8f0;
    }

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

    .btn-success {
    background-color: #28a745;
    border-color: #28a745;
    gap: 0.5rem;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
}

.btn-success:hover {
    background-color: #218838;
    border-color: #1e7e34;
}

.btn-success:disabled {
    background-color: #6c757d;
    border-color: #6c757d;
    cursor: not-allowed;
}

.modal-content {
    border-radius: 10px;
    border: none;
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.modal-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #eee;
    padding: 1rem 1.5rem;
}

.modal-title {
    font-weight: 600;
    color: #333;
}

.modal-body {
    padding: 1.5rem;
}

.form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    color: #555;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 5px;
    padding: 0.5rem 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.input-group-text {
    background-color: #f8f9fa;
    font-weight: 500;
}

.btn-primary-2 {
    background-color: #0d6efd;
    border: none;
    padding: 0.5rem 1.25rem;
}

.btn-secondary {
    background-color: #6c757d;
    border: none;
}

</style>