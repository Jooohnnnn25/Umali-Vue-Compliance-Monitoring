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
              @input="fetchApplications"
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
          <v-col cols="2"></v-col> </v-row>

        <v-card class="mb-6" elevation="2" rounded="lg">
          <v-list density="comfortable" class="py-0">
            <template v-if="applications.length > 0">
              <template v-for="(application, index) in applications" :key="application.id">
                <v-list-item class="pa-2 border-b" @click="toggleRemarks(application.id)" :class="{ 'selected-row': selectedApplicationForRemarks === application.id }">
                  <v-row align="center">
                    <v-col cols="2" class="text-center">{{ application.date_received }}</v-col>
                    <v-col cols="2" class="text-center">{{ application.application_number }}</v-col>
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
                        @click.stop=""
                        @update:model-value="updateApplicationStatus(application.id, application.status)"
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
                        View Application
                      </v-btn>
                    </v-col>
                  </v-row>
                </v-list-item>
                <v-divider v-if="index < applications.length - 1"></v-divider>
              </template>
            </template>
            <template v-else>
              <v-list-item>
                <v-list-item-content class="text-center py-4">
                  No applications found.
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
                <span class="ml-2 text-body-1">Add remarks to applicants construction plans & requirements</span>
              </div>
              <v-textarea
                v-model="remarksText"
                variant="outlined"
                rows="3"
                hide-details
                label="Type your remarks here..."
                rounded="lg"
                bg-color="#f5f5f5"
                @blur="saveRemarks"
              ></v-textarea>
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
              @click="viewComplianceProgress"
            >
              View Compliance Progress
            </v-btn>
          </v-col>
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
  name: 'AdminDashboard',
  data() {
    return {
      newApplicationType: 'New Application',
      searchQuery: '',
      remarksText: '',
      selectedApplicationForRemarks: null,
      applications: [],
    };
  },
  computed: {
    getSelectedApplicationUser() {
      const selectedApp = this.applications.find(app => app.id === this.selectedApplicationForRemarks);
      return selectedApp ? selectedApp.user : 'Selected User';
    }
  },
  methods: {
    async fetchApplications() {
      try {
        const response = await axios.get(`http://localhost/compliance-monitoring-vue-umali/src/pages/Admin/admin_dashboard_php/api.php`, {
          params: {
            action: 'getApplications', // Specify the action
            search: this.searchQuery
          }
        });
        if (response.data.success) {
          this.applications = response.data.applications;
        } else {
          console.error('Failed to fetch applications:', response.data.message);
          alert('Failed to load applications: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error fetching applications:', error);
        alert('Network error or server issue while fetching applications.');
      }
    },
    async updateApplicationStatus(applicationId, newStatus) {
      try {
        const response = await axios.post('http://localhost/compliance-monitoring-vue-umali/src/pages/Admin/admin_dashboard_php/api.php', {
          action: 'updateStatus', // Specify the action
          id: applicationId,
          status: newStatus
        });
        if (response.data.success) {
          console.log('Status updated successfully:', response.data.message);
        } else {
          console.error('Failed to update status:', response.data.message);
          alert('Failed to update status: ' + response.data.message);
          this.fetchApplications(); // Re-fetch data to revert if update failed
        }
      } catch (error) {
        console.error('Error updating status:', error);
        alert('Network error or server issue while updating status.');
      }
    },
    async saveRemarks() {
      if (this.selectedApplicationForRemarks !== null) {
        try {
          const response = await axios.post('http://localhost/compliance-monitoring-vue-umali/src/pages/Admin/admin_dashboard_php/api.php', {
            action: 'updateRemarks', // Specify the action
            id: this.selectedApplicationForRemarks,
            remarks: this.remarksText
          });
          if (response.data.success) {
            console.log('Remarks saved successfully:', response.data.message);
            // Update the local data with the saved remarks
            const appIndex = this.applications.findIndex(app => app.id === this.selectedApplicationForRemarks);
            if (appIndex !== -1) {
              this.applications[appIndex].remarks = this.remarksText;
            }
          } else {
            console.error('Failed to save remarks:', response.data.message);
            alert('Failed to save remarks: ' + response.data.message);
          }
        } catch (error) {
          console.error('Error saving remarks:', error);
          alert('Network error or server issue while saving remarks.');
        }
      }
    },
    toggleRemarks(applicationId) {
      if (this.selectedApplicationForRemarks === applicationId) {
        this.selectedApplicationForRemarks = null;
        this.remarksText = '';
      } else {
        this.selectedApplicationForRemarks = applicationId;
        const selectedApp = this.applications.find(app => app.id === applicationId);
        this.remarksText = selectedApp ? selectedApp.remarks || '' : '';
      }
    },
    viewApplication(applicationId) {
      console.log(`Navigating to /pages/Admin/status.vue for application ID: ${applicationId}`);
      if (this.$router) {
        this.$router.push({ path: '/pages/Admin/status', query: { id: applicationId } });
      } else {
        console.error('Vue Router is not initialized. Please ensure it is set up.');
      }
    },
    viewComplianceProgress() {
      console.log('Navigating to /pages/Admin/status2.vue');
      if (this.$router) {
        this.$router.push('/pages/Admin/status2');
      } else {
        console.error('Vue Router is not initialized. Please ensure it is set up.');
      }
    },
    handleLogout() {
      console.log('Logging out...');
      if (this.$router) {
        this.$router.push('/');
      } else {
        console.error('Vue Router is not initialized. Cannot redirect for logout.');
      }
    }
  },
  mounted() {
    this.fetchApplications();
  }
};
</script>

<style scoped>
/* Scoped styles remain the same */
.border-b {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.status-select .v-field__outline {
  border-width: 2px !important;
}

.status-select.v-select--completed .v-field__input {
  background-color: #D4EDDA;
}
.status-select.v-select--incomplete .v-field__input {
  background-color: #F8D7DA;
}

.selected-row {
  background-color: #e3f2fd;
  border-left: 5px solid #2196F3;
}

body {
  font-family: 'Inter', sans-serif;
}

.white--text {
  color: white !important;
}
</style>