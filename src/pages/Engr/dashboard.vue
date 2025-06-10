<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">
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
              @input="debounceSearch" ></v-text-field>
          </v-col>
        </v-row>

        <v-row class="mb-2">
          <v-col cols="2">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-2 text-center">
              <span class="font-weight-medium white--text">Date Received</span>
            </v-card>
          </v-col>
          <v-col cols="2">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-2 text-center">
              <span class="font-weight-medium white--text">Application Number</span>
            </v-card>
          </v-col>
          <v-col cols="2">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-2 text-center">
              <span class="font-weight-medium white--text">Professionals</span>
            </v-card>
          </v-col>
          <v-col cols="2">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-2 text-center">
              <span class="font-weight-medium white--text">User</span>
            </v-card>
          </v-col>
          <v-col cols="2">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-2 text-center">
              <span class="font-weight-medium white--text">Status</span>
            </v-card>
          </v-col>
          <v-col cols="2"></v-col> </v-row>

        <v-card class="mb-6" elevation="2" rounded="lg">
          <v-list density="comfortable" class="py-0">
            <template v-if="applications.length > 0">
              <template v-for="(application, index) in filteredApplications" :key="application.id">
                <v-list-item
                  class="pa-2 border-b"
                  @click="toggleRemarks(application.id)"
                  :class="{ 'selected-row': selectedApplicationForRemarks === application.id }"
                >
                  <v-row align="center">
                    <v-col cols="2" class="text-center">{{ application.dateReceived }}</v-col>
                    <v-col cols="2" class="text-center">{{ application.applicationNumber }}</v-col>
                    <v-col cols="2" class="text-center">{{ application.professionals }}</v-col>
                    <v-col cols="2" class="text-center">{{ application.user }}</v-col>
                    <v-col cols="2" class="py-0">
                      <v-select
                        v-model="application.status"
                        :items="['Compliant', 'In Progress']"
                        variant="outlined"
                        density="compact"
                        hide-details
                        class="status-select"
                        rounded="lg"
                        :bg-color="application.status === 'Compliant' ? '#D4EDDA' : '#F8D7DA'"
                        @click.stop=""
                        @update:modelValue="updateApplicationStatus(application.id, application.status)"
                      ></v-select>
                    </v-col>
                    <v-col cols="2" class="text-center">
                      <v-btn
                        small
                        color="#002060"
                        class="white--text"
                        rounded="lg"
                        @click.stop="viewApplication(application.id)"
                      >
                        View Plans
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-list-item>
                <v-divider v-if="index < filteredApplications.length - 1"></v-divider>
              </template>
            </template>
            <template v-else>
              <v-list-item>
                <v-list-item-content class="text-center py-4">
                  <v-list-item-title>No applications found.</v-list-item-title>
                </v-list-item-content>
              </v-list-item>
            </template>
          </v-list>
        </v-card>

        <v-row class="mb-4" v-if="selectedApplicationForRemarks !== null">
          <v-col cols="12">
            <v-card flat rounded="lg" color="#8b95d3" class="pa-3 text-center mb-4">
              <span class="text-h6 font-weight-bold white--text">Remarks for {{ getSelectedApplicationUser }}</span>
            </v-card>
            <v-card elevation="2" rounded="lg" class="pa-4" bg-color="white">
              <div class="d-flex align-center mb-2">
                <v-icon left>mdi-comment-text-multiple-outline</v-icon>
                <span class="ml-2 text-body-1">Add remarks to applicants construction plans</span>
              </div>
              <v-textarea
                v-model="remarksText"
                variant="outlined"
                rows="3"
                hide-details
                label="Type your remarks here..."
                rounded="lg"
                bg-color="#f5f5f5"
              ></v-textarea>
              <div class="d-flex justify-end mt-4">
                <v-btn
                  color="#002060"
                  class="white--text"
                  rounded="lg"
                  @click="saveRemarks(selectedApplicationForRemarks)"
                >
                  Save Remarks
                </v-btn>
              </div>
            </v-card>
          </v-col>
        </v-row>

        <v-row align="center" justify="space-between">
          <v-col cols="auto" class="py-0">
            <v-btn
              color="#002060"
              size="large"
              class="white--text"
              rounded="lg"
              elevation="2"
              @click="handleLogout"
            >
              Logout
            </v-btn>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import axios from 'axios'; // Import Axios

export default {
  name: 'EngrDashboard', // Renamed to match context
  data() {
    return {
      newApplicationType: 'New Application',
      searchQuery: '',
      remarksText: '',
      selectedApplicationForRemarks: null,
      applications: [], // This will now be populated from the backend
      // Debounce timer for search
      searchTimer: null,
      // Base URL for your PHP backend
      phpBackendUrl: 'http://localhost/compliance-monitoring-vue-umali/src/pages/Engr/engr_dashboard.php',
    };
  },
  computed: {
    // Filter applications based on current search query (if local filtering is desired, otherwise the backend handles it)
    // For now, we'll let the backend handle filtering by re-fetching. This computed property just mirrors the fetched data.
    filteredApplications() {
        return this.applications;
    },
    getSelectedApplicationUser() {
      const selectedApp = this.applications.find(app => app.id === this.selectedApplicationForRemarks);
      return selectedApp ? selectedApp.user : 'Selected User';
    }
  },
  watch: {
    // Watch for changes in searchQuery and debounce the API call
    searchQuery() {
      if (this.searchTimer) {
        clearTimeout(this.searchTimer);
      }
      this.searchTimer = setTimeout(() => {
        this.fetchApplications();
      }, 300); // Wait 300ms after user stops typing
    }
  },
  methods: {
    debounceSearch() {
      // The watcher already handles the debounce, this method is just a placeholder if needed for specific input events
    },
    async fetchApplications() {
      try {
        const response = await axios.get(this.phpBackendUrl, {
          params: {
            action: 'getApplications',
            searchQuery: this.searchQuery // Pass search query to backend
          }
        });
        if (response.data.success) {
          this.applications = response.data.applications;
          console.log("Applications fetched successfully:", this.applications);
        } else {
          console.error('Failed to fetch applications:', response.data.message);
          // Optionally show a user-friendly error
          alert('Failed to load applications: ' + (response.data.message || 'Unknown error.'));
        }
      } catch (error) {
        console.error('Error fetching applications:', error);
        // Handle network or server errors
        alert('Network error or server issue while fetching applications.');
      }
    },
    async updateApplicationStatus(applicationId, newStatus) {
      try {
        const response = await axios.put(this.phpBackendUrl, { // Using PUT for updates
          action: 'updateApplicationStatus',
          id: applicationId,
          status: newStatus
        });
        if (response.data.success) {
          console.log(`Status for application ${applicationId} updated to ${newStatus}`);
          // Optionally, re-fetch applications to ensure local data is consistent
          // this.fetchApplications();
        } else {
          console.error('Failed to update status:', response.data.message);
          alert('Failed to update status: ' + (response.data.message || 'Unknown error.'));
          // Revert status if update fails
          const app = this.applications.find(a => a.id === applicationId);
          if (app) {
              app.status = app.status === 'Compliant' ? 'In Progress' : 'Compliant'; // Revert to previous status
          }
        }
      } catch (error) {
        console.error('Error updating status:', error);
        alert('Network error or server issue while updating status.');
      }
    },
    async saveRemarks(applicationId) {
        if (applicationId === null) {
            alert('Please select an application to add remarks.');
            return;
        }
        try {
            const response = await axios.put(this.phpBackendUrl, { // Using PUT for updates
                action: 'updateApplicationRemarks',
                id: applicationId,
                remarks: this.remarksText
            });
            if (response.data.success) {
                console.log(`Remarks for application ${applicationId} saved.`);
                // Update the remarks in the local applications array directly
                const app = this.applications.find(a => a.id === applicationId);
                if (app) {
                    app.remarks = this.remarksText;
                }
                alert('Remarks saved successfully!');
            } else {
                console.error('Failed to save remarks:', response.data.message);
                alert('Failed to save remarks: ' + (response.data.message || 'Unknown error.'));
            }
        } catch (error) {
            console.error('Error saving remarks:', error);
            alert('Network error or server issue while saving remarks.');
        }
    },
    toggleRemarks(applicationId) {
      if (this.selectedApplicationForRemarks === applicationId) {
        this.selectedApplicationForRemarks = null;
        this.remarksText = ''; // Clear remarks when hidden
      } else {
        this.selectedApplicationForRemarks = applicationId;
        const selectedApp = this.applications.find(app => app.id === applicationId);
        this.remarksText = selectedApp ? selectedApp.remarks : '';
      }
    },
    viewApplication(applicationId) {
      console.log(`Navigating to /pages/Engr/status for application ID: ${applicationId}`);
      if (this.$router) {
        // Make sure your router has a route configured for this path, e.g.:
        // { path: '/pages/Engr/status', name: 'EngrStatus', component: StatusPage }
        this.$router.push({ path: '/pages/Engr/status', query: { id: applicationId } });
      } else {
        console.error('Vue Router is not initialized. Please ensure it is set up.');
      }
    },
    handleLogout() {
      console.log('Logging out...');
      if (this.$router) {
        this.$router.push('/'); // Navigate to the root path
      } else {
        console.error('Vue Router is not initialized. Cannot redirect for logout.');
      }
    }
  },
  mounted() {
    this.fetchApplications(); // Fetch applications when the component is mounted
  },
};
</script>

<style scoped>
/* Scoped styles for this component */
.border-b {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12); /* Vuetify default divider color */
}

.status-select .v-field__outline {
  border-width: 2px !important;
}

.selected-row {
  background-color: #e3f2fd; /* Light blue background for selected row */
  border-left: 5px solid #2196F3; /* Blue border to indicate selection */
}

body {
  font-family: 'Inter', sans-serif;
}
</style>

