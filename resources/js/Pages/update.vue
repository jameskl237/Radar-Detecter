<template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Messages Flash -->
        <div v-if="flashMessage" :style="flashMessageStyle">
          {{ flashMessage.message }}
        </div>
  
        <div v-if="$page.props.flash.success" class="bg-green-200 text-green-800 p-4 mb-4 rounded">
          {{ $page.props.flash.success }}
        </div>
  
        <div v-if="$page.props.flash.errors" class="bg-red-200 text-red-800 p-4 mb-4 rounded">
          <ul>
            <li v-for="(error, key) in $page.props.flash.errors" :key="key">
              {{ error }}
            </li>
          </ul>
        </div>
  
        <div class="flex justify-center">
          <div class="w-full max-w-3xl bg-white rounded-lg shadow-lg p-8">
            <form @submit.prevent="validateForm">
              <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                  <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="name">
                    Nom
                  </label>
                  <input v-model="form.name"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    id="name" type="text" placeholder="Entrer le nom du point" />
                  <p v-if="submitted && !form.name" class="text-red-500 text-xs italic">
                    Veuillez remplir ce champ.
                  </p>
                </div>
  
                <div class="w-full md:w-1/2 px-3">
                  <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="type">
                    Type
                  </label>
                  <select v-model="form.type"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white"
                    id="type">
                    <option value="Panneau">Panneau</option>
                    <option value="Radar">Radar</option>
                    <option value="Position">Position</option>
                  </select>
                  <p v-if="submitted && !form.type" class="text-red-500 text-xs italic">
                    Veuillez remplir ce champ.
                  </p>
                </div>
              </div>
  
              <div class="flex flex-wrap -mx-3 mb-6">
                <div class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                  <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="longitude">
                    Longitude
                  </label>
                  <input v-model="form.longitude"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                    id="longitude" type="text" placeholder="Entrer la longitude" />
                  <p v-if="submitted && !form.longitude" class="text-red-500 text-xs italic">
                    Veuillez remplir ce champ.
                  </p>
                </div>
  
                <div class="w-full md:w-1/2 px-3">
                  <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="latitude">
                    Latitude
                  </label>
                  <input v-model="form.latitude"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white"
                    id="latitude" type="text" placeholder="Entrer la latitude" />
                  <p v-if="submitted && !form.latitude" class="text-red-500 text-xs italic">
                    Veuillez remplir ce champ.
                  </p>
                </div>
              </div>
  
              <div class="flex flex-col items-center">
                <div v-if="form.type === 'Panneau'" class="w-full md:w-1/2 px-3 mb-6 md:mb-0">
                  <div class="flex justify-center">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2" for="limitation">
                      Limitation
                    </label>
                  </div>
                  <input v-model="form.limitation"
                    class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white"
                    id="limitation" type="text" placeholder="Entrez la limitation" />
                  <p v-if="submitted && form.type === 'Panneau' && !form.limitation"
                    class="text-red-500 text-xs italic">
                    Veuillez remplir ce champ.
                  </p>
                </div>
  
                <div class="mt-6">
                  <button
                    class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded"
                    type="submit">
                    Enregistrer
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  import { router } from '@inertiajs/vue3';
  
  export default {
    props: {
      point: Object, // Récupère les données du point à modifier
    },
    data() {
      return {
        form: {
          name: this.point.name,
          type: this.point.type,
          longitude: this.point.longitude,
          latitude: this.point.latitude,
          limitation: this.point.limitation ,
        },
        submitted: false,
        flashMessage: null
      };
    },
  
    computed: {
      flashMessageStyle() {
        if (!this.flashMessage) return {};
        return {
          padding: '1rem',
          marginBottom: '1rem',
          borderRadius: '4px',
          color: 'white',
          backgroundColor: this.flashMessage.type === 'success' ? '#48bb78' : '#f56565'
        };
      }
    },
  
    methods: {
      validateForm() {
        this.submitted = true;
  
        if (this.form.type === 'Panneau' && !this.form.limitation) {
          this.showFlashMessage('error', 'Le champ "limitation" est requis pour le type "Panneau".');
          return;
        }
  
        if (this.form.type !== 'Panneau') {
          this.form.limitation = null;
        }
  
        this.submitForm();
      },
  
      submitForm() {
        router.put(route('update', this.point.id), this.form);
      },
  
      showFlashMessage(type, message) {
        this.flashMessage = { type, message };
        setTimeout(() => {
          this.flashMessage = null;
        }, 3000);
      }
    }
  };
  </script>
  