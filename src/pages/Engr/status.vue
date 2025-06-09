<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">

        <!-- Document Plans Table -->
        <h2 class="text-h5 font-weight-bold mb-4">Document Plans Overview</h2>
        <v-data-table
          :headers="documentHeaders"
          :items="documentPlans"
          item-key="documentType"
          class="elevation-2 rounded-lg mb-6"
          hide-default-footer
        >
          <template v-slot:item.action="{ item }">
            <v-btn
              small
              color="#002060"
              class="white--text"
              rounded="lg"
              @click="showPlanImage(item.planImagePath)"
            >
              View
            </v-btn>
          </template>
        </v-data-table>

        <!-- Image Viewer Popup Dialog -->
        <v-dialog v-model="imageViewDialog" max-width="800px">
          <v-card rounded="lg" elevation="10">
            <v-toolbar color="#8b95d3" dark flat dense>
              <v-toolbar-title class="text-white">Plan Viewer</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon color="white" @click="closePlanImage">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>
            <v-card-text class="pa-4">
              <v-img
                :src="currentPlanImage"
                alt="Architectural Plan"
                contain
                class="rounded-lg"
                max-height="600px"
              ></v-img>
            </v-card-text>
          </v-card>
        </v-dialog>

      </v-container>
    </v-main>
  </v-app>
</template>

<script>
// Import the image from the assets folder
// Adjust the path './../assets/logo.png' if your component is not directly in 'src/components'
// For example, if DocumentPlansViewer is in 'src/pages/Admin/', then '../assets/logo.png' might be correct.
import defaultPlanImage from '../../assets/buildingplans.png';

export default {
  name: 'DocumentPlansViewer',
  data() {
    return {
      imageViewDialog: false, // Controls the visibility of the image viewer dialog
      currentPlanImage: '',   // Stores the path of the image to display in the dialog

      // Headers for the Document Plans Table
      documentHeaders: [
        { title: 'Document Type', key: 'documentType', align: 'start', sortable: false },
        { title: 'Date Submitted', key: 'dateSubmitted', align: 'center', sortable: false },
        { title: 'Action', key: 'action', align: 'center', sortable: false },
      ],
      // Data for the Document Plans Table
      documentPlans: [
        { documentType: 'Structural Plans', dateSubmitted: '04/05/2025', planImagePath: defaultPlanImage },
        { documentType: 'Mechanical Plans', dateSubmitted: '04/05/2025', planImagePath: defaultPlanImage },
        { documentType: 'Electrical Plans', dateSubmitted: '04/05/2025', planImagePath: defaultPlanImage },
        { documentType: 'Electronics Plans', dateSubmitted: '04/05/2025', planImagePath: defaultPlanImage },
        { documentType: 'Sanitary/Plumbing Plans', dateSubmitted: '04/05/2025', planImagePath: defaultPlanImage },
        { documentType: 'Architectural Plans', dateSubmitted: '04/05/2025', planImagePath: defaultPlanImage },
      ],
    };
  },
  methods: {
    showPlanImage(imagePath) {
      this.currentPlanImage = imagePath;
      this.imageViewDialog = true;
    },
    closePlanImage() {
      this.imageViewDialog = false;
      this.currentPlanImage = ''; // Clear the image path when dialog is closed
    },
  },
  // Ensure Vuetify is properly initialized in your main.js or similar entry file.
  // Example main.js setup for Vue 3 with Vuetify:
  // import { createApp } from 'vue';
  // import App from './App.vue';
  // import 'vuetify/styles'; // Import Vuetify styles
  // import { createVuetify } from 'vuetify';
  // import * as components from 'vuetify/components';
  // import * as directives from 'vuetify/directives';
  // import '@mdi/font/css/materialdesignicons.css'; // Import Material Design Icons if you use them

  // const vuetify = createVuetify({
  //   components,
  //   directives,
  //   icons: {
  //     defaultSet: 'mdi',
  //   },
  //   theme: {
  //     themes: {
  //       light: {
  //         colors: {
  //           primary: '#3F51B5',
  //           secondary: '#002060',
  //           info: '#8b95d3', // Custom color for blue headers/toolbar
  //           background: '#f5f5f5',
  //         },
  //       },
  //     },
  //   },
  // });

  // createApp(App)
  //   .use(vuetify)
  //   .mount('#app');
};
</script>

<style scoped>
/* Global background color for the app main content */
.v-main {
  background-color: #f5f5f5;
}

/* Styling for the v-data-table itself */
.elevation-2.rounded-lg {
  border-radius: 12px !important;
  overflow: hidden; /* Ensures the rounded corners apply correctly to inner elements */
}

/* Custom header styling for v-data-table */
.v-data-table :deep(.v-data-table__th) {
  background-color: #8b95d3 !important;
  color: white !important;
  font-weight: medium !important;
  text-align: center !important; /* Center text in headers */
}

/* Ensure the first header column (Document Type) is left-aligned */
.v-data-table :deep(.v-data-table__th:first-child) {
  text-align: left !important;
  padding-left: 16px; /* Add some padding for better alignment with content */
  border-radius: 8px 0 0 0; /* Apply top-left rounded corner to the first header */
}

/* Apply top-right rounded corner to the last header */
.v-data-table :deep(.v-data-table__th:last-child) {
  border-radius: 0 8px 0 0;
}

/* Styling for data cells */
.v-data-table :deep(.v-data-table__td) {
  text-align: center !important;
  padding: 8px 16px; /* Adjust padding as needed */
  border-bottom: 1px solid rgba(0, 0, 0, 0.12); /* Default Vuetify divider */
}

/* Ensure the first data column (Document Type) is left-aligned */
.v-data-table :deep(.v-data-table__td:first-child) {
  text-align: left !important;
}

/* Remove bottom border from the last row of the table */
.v-data-table :deep(.v-data-table__tr:last-child .v-data-table__td) {
  border-bottom: none !important;
}

/* Headers for sections */
h2 {
  color: #333;
}
</style>