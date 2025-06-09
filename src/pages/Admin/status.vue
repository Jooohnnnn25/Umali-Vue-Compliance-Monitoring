<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">

        <!-- General Documents Section -->
        <h2 class="text-h5 font-weight-bold mb-4">General Documents</h2>
        <v-data-table
          :headers="generalDocumentHeaders"
          :items="generalDocuments"
          item-key="documentType"
          class="elevation-2 rounded-lg mb-8"
          hide-default-footer
        >
          <template v-slot:item.status="{ item }">
            <span :class="getStatusColorClass(item.status)">{{ item.status }}</span>
          </template>
        </v-data-table>

        <!-- Proof of Ownership Section -->
        <h2 class="text-h5 font-weight-bold mb-4">Proof of Ownership</h2>
        <v-data-table
          :headers="proofOfOwnershipHeaders"
          :items="proofOfOwnershipDocuments"
          item-key="documentType"
          class="elevation-2 rounded-lg mb-8"
          hide-default-footer
        >
          <template v-slot:item.status="{ item }">
            <span :class="getStatusColorClass(item.status)">{{ item.status }}</span>
          </template>
        </v-data-table>

        <!-- Clearances Section -->
        <h2 class="text-h5 font-weight-bold mb-4">Clearances</h2>
        <v-data-table
          :headers="clearancesHeaders"
          :items="clearanceDocuments"
          item-key="documentType"
          class="elevation-2 rounded-lg mb-6"
          hide-default-footer
        >
          <template v-slot:item.status="{ item }">
            <span :class="getStatusColorClass(item.status)">{{ item.status }}</span>
          </template>
        </v-data-table>

      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'DocumentStatus',
  data() {
    return {
      // Headers for General Documents Table
      generalDocumentHeaders: [
        { title: 'Document Type', key: 'documentType', align: 'start', sortable: false },
        { title: 'Date Submitted', key: 'dateSubmitted', align: 'center', sortable: false },
        { title: 'Status', key: 'status', align: 'center', sortable: false },
      ],
      // Data for General Documents Table (Combined from both images)
      generalDocuments: [
        { documentType: 'Unified Application Form', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Ancillary Permit Forms', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Architectural Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Civil/Structural Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Electrical Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Sanitary Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Plumbing Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Mechanical Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Electronics Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Geodetic Documents', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Lot Plan', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Photocopies of Valid Licenses', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Notarized estimated value', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Technical Specifications', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Structural Design and Seismic Analysis', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Plate Load Test Result', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Soil Boring Test Result', dateSubmitted: '04/05/2025', status: 'Submitted' },
      ],

      // Headers for Proof of Ownership Table
      proofOfOwnershipHeaders: [
        { title: 'Document Type', key: 'documentType', align: 'start', sortable: false },
        { title: 'Date Submitted', key: 'dateSubmitted', align: 'center', sortable: false },
        { title: 'Status', key: 'status', align: 'center', sortable: false },
      ],
      // Data for Proof of Ownership Table
      proofOfOwnershipDocuments: [
        { documentType: 'Original Certificate of Title (OCT)', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Transfer Certificate Title (TCT)', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Deed of Absolute Sale', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Tax Declaration', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Tax Receipt', dateSubmitted: '04/05/2025', status: 'Submitted' },
      ],

      // Headers for Clearances Table
      clearancesHeaders: [
        { title: 'Clearance Type', key: 'documentType', align: 'start', sortable: false },
        { title: 'Date Submitted', key: 'dateSubmitted', align: 'center', sortable: false },
        { title: 'Status', key: 'status', align: 'center', sortable: false },
      ],
      // Data for Clearances Table
      clearanceDocuments: [
        { documentType: 'Construction Safety and Health Program', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Fire Safety Evaluation Clearance', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Locational Clearance', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'DPWH Clearance', dateSubmitted: '04/05/2025', status: 'Submitted' },
        { documentType: 'Barangay Clearance', dateSubmitted: '04/05/2025', status: 'Submitted' },
      ],
    };
  },
  methods: {
    getStatusColorClass(status) {
      // Returns a class for coloring text based on status
      if (status === 'Submitted') return 'text-green-darken-2';
      // Add other status colors if needed (e.g., 'Pending', 'Rejected')
      return ''; // Default or no class
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
  //           info: '#8b95d3', // Custom color for blue headers/buttons
  //           background: '#f5f5f5',
  //           'green-darken-2': '#28a745', // Define a specific green color for status
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

/* Ensure the first header column (Document Type / Clearance Type) is left-aligned */
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

/* Ensure the first data column (Document Type / Clearance Type) is left-aligned */
.v-data-table :deep(.v-data-table__td:first-child) {
  text-align: left !important;
}

/* Remove bottom border from the last row of the table */
.v-data-table :deep(.v-data-table__tr:last-child .v-data-table__td) {
  border-bottom: none !important;
}

/* Text colors for status */
.text-green-darken-2 {
  color: #28a745 !important; /* Green for "Submitted" */
  font-weight: bold;
}

/* Headers for sections */
h2 {
  color: #333;
}
</style>