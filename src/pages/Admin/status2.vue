<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">
        <h2 class="text-h5 font-weight-bold mb-4">Building Permit Monitoring</h2>

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
              @input="search" ></v-text-field>
          </v-col>
        </v-row>

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

        <h2 class="text-h5 font-weight-bold mb-4">Occupancy Monitoring</h2>

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

        <v-dialog v-model="notificationDialog" max-width="500px">
          <v-card rounded="lg" elevation="10">
            <v-card-title class="d-flex justify-center align-center py-4" style="background-color: #8b95d3;">
              <v-icon large color="white" class="mr-2">mdi-bell-outline</v-icon>
              <span class="text-h6 font-weight-bold text-white">Notification</span>
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
import axios from 'axios'; // Import Axios

export default {
  name: 'ApplicationMonitoring',
  data() {
    return {
      notificationDialog: false,
      currentNotificationRemarks: '',
      newApplicationType: 'New Application',
      searchQuery: '',

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
      buildingPermits: [], // Initialize as empty array, data will be fetched

      occupancyMonitoringHeaders: [
        { title: 'Applicant Name', key: 'applicantName', align: 'center', sortable: false },
        { title: 'Date Applied', key: 'dateApplied', align: 'center', sortable: false },
        { title: 'Occupancy', key: 'occupancy', align: 'center', sortable: false },
        { title: 'Next Action', key: 'nextAction', align: 'center', sortable: false },
        { title: 'Remarks', key: 'remarks', align: 'center', sortable: false },
        { title: 'Days Remaining (B-Day Limit)', key: 'daysRemaining', align: 'center', sortable: false },
      ],
      occupancyMonitoring: [], // Initialize as empty array, data will be fetched
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
      if (status === 'Completed') return 'text-green-darken-2';
      if (status === 'In Progress') return 'text-red-darken-2';
      return '';
    },
    showNotificationPopup(remarks) {
      this.currentNotificationRemarks = remarks;
      this.notificationDialog = true;
    },
    async fetchMonitoringData() {
      try {
        const response = await axios.get('http://localhost/compliance-monitoring-vue-umali/src/pages/Admin/admin_status2_php/status2.php');
        if (response.data.success) {
          this.buildingPermits = response.data.buildingPermits;
          this.occupancyMonitoring = response.data.occupancyMonitoring;
        } else {
          console.error('Failed to fetch monitoring data:', response.data.message);
          alert('Failed to load monitoring data: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error fetching monitoring data:', error);
        alert('Network error or server issue while fetching monitoring data.');
      }
    },
    // Optional: You might want to call fetchMonitoringData again on search if you plan to do server-side search
    search() {
        // If you were doing server-side search, you'd call fetchMonitoringData here with the search query
        // For now, it just re-filters the local data, which happens automatically with computed properties.
        console.log("Searching for:", this.searchQuery);
    }
  },
  mounted() {
    this.fetchMonitoringData(); // Call the fetch method when the component is mounted
  },
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