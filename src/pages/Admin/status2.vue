<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">
        <!-- Building Permit Monitoring Section -->
        <h2 class="text-h5 font-weight-bold mb-4">Building Permit Monitoring</h2>

        <!-- Building Permit Table Header -->
        <v-row align="center" class="mb-6">
          <v-col cols="12" sm="4" md="3">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-3 text-center">
              <span class="text-h6 font-weight-bold white--text">Transaction Information</span>
            </v-card>
          </v-col>
          <v-col cols="12" sm="4" md="3">
            <v-select
              v-model="newApplicationType"
              :items="['New Application', 'Existing Application']"
              label="New Application"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              hide-details
              class="rounded-lg"
              bg-color="white"
            ></v-select>
          </v-col>
          <v-col cols="12" sm="4" md="6">
            <v-text-field
              v-model="searchQuery"
              append-inner-icon="mdi-magnify"
              label="Search"
              variant="outlined"
              density="comfortable"
              rounded="lg"
              hide-details
              class="rounded-lg"
              bg-color="white"
            ></v-text-field>
          </v-col>
        </v-row>

        <!-- Building Permit Data Table -->
        <v-data-table
          :headers="buildingPermitHeaders"
          :items="filteredBuildingPermits"
          item-key="id"
          class="elevation-2 rounded-lg mb-10"
          hide-default-footer
        >
          <template v-slot:item.buildingPermit="{ item }">
            <span :class="getStatusColorClass(item.buildingPermit)">{{ item.buildingPermit }}</span>
          </template>
          <template v-slot:item.buildingPlans="{ item }">
            <span :class="getStatusColorClass(item.buildingPlans)">{{ item.buildingPlans }}</span>
          </template>
          <template v-slot:item.clearances="{ item }">
            <span :class="getStatusColorClass(item.clearances)">{{ item.clearances }}</span>
          </template>
          <template v-slot:item.nextAction="{ item }">
            <v-btn
              v-if="item.remarks"
              small
              color="#8b95d3"
              class="white--text"
              rounded="lg"
              @click="showNotificationPopup(item.remarks)"
            >
              Notify
            </v-btn>
            <span v-else>N/A</span>
          </template>
          <template v-slot:item.remarks="{ item }">
            {{ item.remarks || '-' }}
          </template>
          <template v-slot:item.daysRemaining="{ item }">
            {{ item.daysRemaining || '-' }}
          </template>
        </v-data-table>

        <!-- Occupancy Monitoring Section -->
        <h2 class="text-h5 font-weight-bold mb-4">Occupancy Monitoring</h2>

        <!-- Occupancy Monitoring Data Table -->
        <v-data-table
          :headers="occupancyMonitoringHeaders"
          :items="filteredOccupancyMonitoring"
          item-key="id"
          class="elevation-2 rounded-lg mb-6"
          hide-default-footer
        >
          <template v-slot:item.occupancy="{ item }">
            <span :class="getStatusColorClass(item.occupancy)">{{ item.occupancy }}</span>
          </template>
          <template v-slot:item.nextAction="{ item }">
            <v-btn
              v-if="item.remarks"
              small
              color="#8b95d3"
              class="white--text"
              rounded="lg"
              @click="showNotificationPopup(item.remarks)"
            >
              Notify
            </v-btn>
            <span v-else>N/A</span>
          </template>
          <template v-slot:item.remarks="{ item }">
            {{ item.remarks || '-' }}
          </template>
          <template v-slot:item.daysRemaining="{ item }">
            {{ item.daysRemaining || '-' }}
          </template>
        </v-data-table>

        <!-- Notification Popup Dialog -->
        <v-dialog v-model="notificationDialog" max-width="500px">
          <v-card rounded="lg" elevation="10">
            <v-card-title class="d-flex justify-center align-center py-4" style="background-color: #8b95d3;">
  <v-icon large color="white" class="mr-2">mdi-bell-outline</v-icon>
  <span class="text-h6 font-weight-bold text-white">Notification</span> <!-- Changed white--text to text-white -->
</v-card-title>
            <v-card-text class="pt-6 pb-4 text-center">
              <v-icon x-large color="success" class="mb-4">mdi-check-circle-outline</v-icon>
              <p class="text-body-1 mb-4">
                Notified about the missing requirements:
                <br>
                <strong>{{ currentNotificationRemarks }}</strong>
              </p>
              <v-btn color="#8b95d3" class="white--text" @click="notificationDialog = false" rounded="lg">
                Got It!
              </v-btn>
            </v-card-text>
          </v-card>
        </v-dialog>

      </v-container>
    </v-main>
  </v-app>
</template>

<script>
export default {
  name: 'ApplicationMonitoring',
  data() {
    return {
      notificationDialog: false, // Controls the visibility of the notification dialog
      currentNotificationRemarks: '', // Stores the remarks to be displayed in the dialog
      newApplicationType: 'New Application', // Added for the top section (existing)
      searchQuery: '', // Added for the search bar (existing)
      
      // Headers for Building Permit Table
      buildingPermitHeaders: [
        { title: 'Applicant Name', key: 'applicantName', align: 'center', sortable: false },
        { title: 'Date Applied', key: 'dateApplied', align: 'center', sortable: false },
        { title: 'Building Permit', key: 'buildingPermit', align: 'center', sortable: false },
        { title: 'Building Plans', key: 'buildingPlans', align: 'center', sortable: false },
        { title: 'Clearances', key: 'clearances', align: 'center', sortable: false },
        { title: 'Next Action', key: 'nextAction', align: 'center', sortable: false },
        { title: 'Remarks', key: 'remarks', align: 'center', sortable: false },
        { title: 'Days Remaining (B-Day Limit)', key: 'daysRemaining', align: 'center', sortable: false },
      ],
      // Data for Building Permit Table
      buildingPermits: [
        {
          id: 1,
          applicantName: 'Laurence Francisco (BP-2025-001-C)',
          dateApplied: '2025-06-01',
          buildingPermit: 'Completed',
          buildingPlans: 'Completed',
          clearances: 'In Progress',
          remarks: 'Pending Clearances',
          daysRemaining: '2 Days Remaining',
        },
        {
          id: 2,
          applicantName: 'Aaron James Cortez (BP-2025-001-C)',
          dateApplied: '2025-06-01',
          buildingPermit: 'Completed',
          buildingPlans: 'In Progress',
          clearances: 'In Progress',
          remarks: 'Pending Clearances & Plans',
          daysRemaining: '4 Days Remaining',
        },
        {
          id: 3,
          applicantName: 'Jomar Cerda (BP-2025-001-C)',
          dateApplied: '2025-06-01',
          buildingPermit: 'Completed',
          buildingPlans: 'Completed',
          clearances: 'Completed',
          remarks: '', // No remarks for this entry
          daysRemaining: '-',
        },
      ],

      // Headers for Occupancy Monitoring Table
      occupancyMonitoringHeaders: [
        { title: 'Applicant Name', key: 'applicantName', align: 'center', sortable: false },
        { title: 'Date Applied', key: 'dateApplied', align: 'center', sortable: false },
        { title: 'Occupancy', key: 'occupancy', align: 'center', sortable: false },
        { title: 'Next Action', key: 'nextAction', align: 'center', sortable: false },
        { title: 'Remarks', key: 'remarks', align: 'center', sortable: false },
        { title: 'Days Remaining (B-Day Limit)', key: 'daysRemaining', align: 'center', sortable: false },
      ],
      // Data for Occupancy Monitoring Table
      occupancyMonitoring: [
        {
          id: 1,
          applicantName: 'Laurence Francisco (BP-2025-001-C)',
          dateApplied: '2025-06-01',
          occupancy: 'In Progress',
          remarks: 'Pending Requirements',
          daysRemaining: '2 Days Remaining',
        },
      ],
    };
  },
  computed: {
    filteredBuildingPermits() {
      if (!this.searchQuery) {
        return this.buildingPermits;
      }
      const query = this.searchQuery.toLowerCase();
      return this.buildingPermits.filter(item =>
        Object.values(item).some(value =>
          String(value).toLowerCase().includes(query)
        )
      );
    },
    filteredOccupancyMonitoring() {
      if (!this.searchQuery) {
        return this.occupancyMonitoring;
      }
      const query = this.searchQuery.toLowerCase();
      return this.occupancyMonitoring.filter(item =>
        Object.values(item).some(value =>
          String(value).toLowerCase().includes(query)
        )
      );
    }
  },
  methods: {
    getStatusColorClass(status) {
      // Returns a class for coloring text based on status
      if (status === 'Completed') return 'text-green-darken-2';
      if (status === 'In Progress') return 'text-red-darken-2';
      return ''; // Default or no class
    },
    showNotificationPopup(remarks) {
      this.currentNotificationRemarks = remarks;
      this.notificationDialog = true;
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
  // import '@mdi/font/css/materialdesignicons.css'; // Import Material Design Icons

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
  //           success: '#28a745', // Green for completed status
  //           error: '#dc3545', // Red for in progress status / error
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
/* Scoped styles for this component */
.elevation-2.rounded-lg {
  border-radius: 12px !important; /* Ensure rounded corners on the table itself */
}

/* Text colors for status */
.text-green-darken-2 {
  color: #28a745 !important; /* Green for "Completed" */
  font-weight: bold;
}

.text-red-darken-2 {
  color: #dc3545 !important; /* Red for "In Progress" */
  font-weight: bold;
}

/* Adjust data table header background to match the original design */
.v-data-table :deep(.v-data-table__th) {
  background-color: #8b95d3 !important;
  color: white !important;
  font-weight: medium !important;
  text-align: center !important;
  border-radius: 8px 8px 0 0; /* Rounded top corners for header */
}

.v-data-table :deep(.v-data-table__tr:last-child .v-data-table__td) {
  border-bottom: none !important; /* Remove bottom border from last row */
}

/* Ensure consistent cell alignment */
.v-data-table :deep(.v-data-table__td) {
  text-align: center !important;
}

/* Card styling */
.v-card.rounded-lg {
  border-radius: 12px;
}
</style>
