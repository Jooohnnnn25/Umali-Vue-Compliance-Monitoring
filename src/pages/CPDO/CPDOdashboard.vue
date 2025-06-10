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
            ></v-text-field>
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
          <v-col cols="2"></v-col>
        </v-row>

        <v-card class="mb-6" elevation="2" rounded="lg">
          <v-list density="comfortable" class="py-0">
            <template v-for="(application, index) in filteredApplications" :key="application.id">
              <v-list-item class="pa-2 border-b" @click="toggleRemarks(application.id)" :class="{ 'selected-row': selectedApplicationForRemarks === application.id }">
                <v-row align="center">
                  <v-col cols="2" class="text-center">{{ application.dateReceived }}</v-col>
                  <v-col cols="2" class="text-center">{{ application.applicationNumber }}</v-col>
                  <v-col cols="2" class="text-center">{{ application.professionals }}</v-col>
                  <v-col cols="2" class="text-center">{{ application.user }}</v-col>
                  <v-col cols="2" class="py-0">
                    <v-select
                      v-model="application.status"
                      :items="['Completed', 'Incomplete']"
                      variant="outlined"
                      density="compact"
                      hide-details
                      class="status-select"
                      rounded="lg"
                      :bg-color="application.status === 'Completed' ? '#D4EDDA' : '#F8D7DA'"
                      @update:model-value="updateApplicationStatus(application.id, $event)"
                      @click.stop=""
                    ></v-select>
                  </v-col>
                  <v-col cols="2" class="text-center">
                    <v-btn
                      small
                      color="#002060"
                      class="white--text"
                      rounded="lg"
                      @click.stop="viewApplicationStatus()"
                    >
                      View Application
                    </v-btn>
                  </v-col>
                </v-row>
              </v-list-item>
              <v-divider v-if="index < filteredApplications.length - 1"></v-divider>
            </template>
            <v-list-item v-if="filteredApplications.length === 0" class="text-center pa-4">
                No applications found.
            </v-list-item>
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
                <span class="ml-2 text-body-1">Add remarks to applicants submitted CPDO Requirements</span>
              </div>
              <v-textarea
                v-model="remarksText"
                variant="outlined"
                rows="3"
                auto-grow
                hide-details
                label="Type your remarks here..."
                rounded="lg"
                bg-color="#f5f5f5"
                @blur="updateApplicationRemarks()"
              ></v-textarea>

              <v-divider class="my-4"></v-divider>

              <h4 class="text-subtitle-1 mb-2">Previous Remarks:</h4>
              <v-list dense>
                <v-list-item v-for="(remark, i) in currentApplicationRemarks" :key="remark.id || i" class="py-1">
                  <v-list-item-title class="text-caption text-wrap">{{ remark.remark_text }}</v-list-item-title>
                  <v-list-item-subtitle class="text-right text-caption grey--text">
                    - {{ remark.remark_by }} on {{ formatDate(remark.created_at) }}
                  </v-list-item-subtitle>
                </v-list-item>
                <v-list-item v-if="currentApplicationRemarks.length === 0">
                    <v-list-item-title class="text-caption text-center">No previous remarks.</v-list-item-title>
                </v-list-item>
              </v-list>
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
import axios from 'axios';

export default {
  name: 'CPDODashboard',
  data() {
    return {
      newApplicationType: 'New Application',
      searchQuery: '',
      remarksText: '',
      selectedApplicationForRemarks: null,
      applications: [],
      currentApplicationRemarks: [],
    };
  },
  computed: {
    filteredApplications() {
      if (!this.searchQuery) {
        return this.applications;
      }
      const query = this.searchQuery.toLowerCase();
      return this.applications.filter(app =>
        Object.values(app).some(value =>
          String(value).toLowerCase().includes(query)
        )
      );
    },
    getSelectedApplicationUser() {
      const selectedApp = this.applications.find(app => app.id === this.selectedApplicationForRemarks);
      return selectedApp ? selectedApp.user : 'Selected User';
    }
  },
  methods: {
    formatDate(timestamp) {
        if (!timestamp) return '';
        // Format to a more readable date-time, e.g., "Jun 10, 2025, 02:30 PM"
        const date = new Date(timestamp);
        return date.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
    },
    async fetchApplications() {
      try {
        // !!! IMPORTANT: Ensure this URL exactly matches where your PHP script is served !!!
        const response = await axios.get('http://localhost/compliance-monitoring-vue-umali/src/pages/CPDO/cpdo_dashboard.php', {
          params: {
            action: 'getApplications'
          }
        });

        // Debugging: Log the full response data
        console.log('Full API Response for applications:', response.data);

        if (response.data.success) {
          this.applications = response.data.applications;
          console.log('Applications fetched:', this.applications);
        } else {
          // If success is false, but message might be missing (shouldn't happen with updated PHP)
          console.error('Failed to fetch applications:', response.data.message || 'Unknown error from server.');
          alert('Failed to load applications: ' + (response.data.message || 'Please check console for details.'));
        }
      } catch (error) {
        console.error('Error fetching applications:', error);
        alert('Network error or server issue while fetching applications. Check console for details.');
      }
    },
    async fetchRemarksForApplication(applicationId) {
        try {
            // !!! IMPORTANT: Ensure this URL exactly matches where your PHP script is served !!!
            const response = await axios.get('http://localhost/compliance-monitoring-vue-umali/src/pages/CPDO/cpdo_dashboard.php', {
                params: {
                    action: 'getApplicationRemarks',
                    id: applicationId
                }
            });
            if (response.data.success) {
                return response.data.remarks;
            } else {
                console.error('Failed to fetch remarks:', response.data.message);
                return [];
            }
        } catch (error) {
            console.error('Error fetching remarks:', error);
            alert('Error fetching remarks for application ' + applicationId);
            return [];
        }
    },
    async updateApplicationStatus(applicationId, newStatus) {
      const application = this.applications.find(app => app.id === applicationId);
      if (!application) return;

      try {
        // !!! IMPORTANT: Ensure this URL exactly matches where your PHP script is served !!!
        const response = await axios.post('http://localhost/compliance-monitoring-vue-umali/src/pages/CPDO/cpdo_dashboard.php', {
          action: 'updateApplication',
          id: applicationId,
          status: newStatus,
        });
        if (response.data.success) {
          console.log('Application status updated successfully.');
          // Optionally, refetch applications or update the local array
          // this.fetchApplications();
        } else {
          console.error('Failed to update application status:', response.data.message);
          alert('Failed to update status: ' + response.data.message);
          // Revert status in UI if update failed
          application.status = application.status === 'Completed' ? 'Incomplete' : 'Completed'; // Simple toggle back
        }
      } catch (error) {
        console.error('Error updating application status:', error);
        alert('Network error or server issue while updating application status.');
        // Revert status in UI on network error
        application.status = application.status === 'Completed' ? 'Incomplete' : 'Completed';
      }
    },
    async updateApplicationRemarks() {
      if (this.selectedApplicationForRemarks === null) return;
      if (!this.remarksText.trim()) {
          console.log("Remarks text is empty or only whitespace, not saving.");
          return;
      }

      // Check if the remark is identical to the very last one to prevent duplicates on blur
      const latestExistingRemark = this.currentApplicationRemarks[this.currentApplicationRemarks.length - 1]?.remark_text;
      if (this.remarksText.trim() === latestExistingRemark) {
          console.log("Remarks unchanged from latest, not sending new remark.");
          return;
      }

      try {
        // !!! IMPORTANT: Ensure this URL exactly matches where your PHP script is served !!!
        const response = await axios.post('http://localhost/compliance-monitoring-vue-umali/src/pages/CPDO/cpdo_dashboard.php', {
          action: 'updateApplication',
          id: this.selectedApplicationForRemarks,
          remarks: this.remarksText.trim() // Send trimmed remarks
        });
        if (response.data.success) {
          console.log('Application remarks added successfully.');
          // Refetch remarks to show the newly added one
          this.currentApplicationRemarks = await this.fetchRemarksForApplication(this.selectedApplicationForRemarks);
          this.remarksText = ''; // Clear the input field
        } else {
          console.error('Failed to add application remarks:', response.data.message);
          alert('Failed to add remarks: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error adding application remarks:', error);
        alert('Network error or server issue while adding application remarks.');
      }
    },
    async toggleRemarks(applicationId) {
        // If switching to a new application or closing the current one, save current remarks first
        if (this.selectedApplicationForRemarks !== null && this.remarksText.trim() !== '' && this.selectedApplicationForRemarks !== applicationId) {
            await this.updateApplicationRemarks();
        }

        if (this.selectedApplicationForRemarks === applicationId) {
            // If clicking the currently selected application, close remarks
            this.selectedApplicationForRemarks = null;
            this.remarksText = '';
            this.currentApplicationRemarks = [];
        } else {
            // Select new application and fetch its remarks
            this.selectedApplicationForRemarks = applicationId;
            this.remarksText = ''; // Clear text area for new remarks
            this.currentApplicationRemarks = await this.fetchRemarksForApplication(applicationId);
        }
    },
    viewApplicationStatus() {
      if (this.$router) {
        // Navigates directly to /pages/CPDO/status (no ID passed, as requested)
        this.$router.push('/pages/CPDO/status');
      } else {
        console.error('Vue Router is not initialized. Cannot redirect to status page.');
      }
    },
    handleLogout() {
      console.log('Logging out...');
      if (this.$router) {
        // Redirect to the login page or home page
        this.$router.push('/');
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

/* Specific styling adjustments for the status select for better visual feedback */
.status-select :deep(.v-field__outline) {
  border-width: 2px !important;
}

/* Added style for selected row to give visual feedback */
.selected-row {
  background-color: #e3f2fd; /* Light blue background for selected row */
  border-left: 5px solid #2196F3; /* Blue border to indicate selection */
}

/* Global font setting if needed for consistency across app */
body {
  font-family: 'Inter', sans-serif;
}
</style>