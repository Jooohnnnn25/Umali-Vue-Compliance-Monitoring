<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">
        <!-- Top Section: Transaction Info, New Application, Search -->
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

        <!-- Table Header -->
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
          <v-col cols="2"></v-col> <!-- For the "View Application" button -->
        </v-row>

        <!-- Application Data Rows -->
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
                      @click.stop=""
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
              <v-divider v-if="index < filteredApplications.length - 1"></v-divider>
            </template>
          </v-list>
        </v-card>

        <!-- Remarks Section -->
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
                hide-details
                label="Type your remarks here..."
                rounded="lg"
                bg-color="#f5f5f5"
              ></v-textarea>
            </v-card>
          </v-col>
        </v-row>

        <!-- View Compliance Progress Button and Logout Button -->
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
export default {
  name: 'AdminDashboard',
  data() {
    return {
      newApplicationType: 'New Application', // For the "New Application" dropdown
      searchQuery: '', // For the search text field
      remarksText: '', // For the remarks textarea
      selectedApplicationForRemarks: null, // New: Stores the ID of the selected application for remarks
      applications: [
        {
          id: 1,
          dateReceived: '04/02/2025',
          applicationNumber: '(BP-2025-001-C)',
          professionals: 'Civil Engineer',
          user: 'Laurence Francisco',
          status: 'Completed',
          remarks: 'Initial review complete. Awaiting structural plan revisions.' // Example remark
        },
        {
          id: 2,
          dateReceived: '04/02/2025',
          applicationNumber: '05124213234',
          professionals: 'Civil Engineer',
          user: 'Aaron James Cortez',
          status: 'Incomplete',
          remarks: ''
        },
        {
          id: 3,
          dateReceived: '04/02/2025',
          applicationNumber: '05124213234',
          professionals: 'Architect',
          user: 'Jomar Cerda',
          status: 'Completed',
          remarks: 'All documents submitted and approved.'
        },
      ],
    };
  },
  computed: {
    // Filter applications based on search query
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
    // Computed property to get the selected application's user for the remarks header
    getSelectedApplicationUser() {
      const selectedApp = this.applications.find(app => app.id === this.selectedApplicationForRemarks);
      return selectedApp ? selectedApp.user : 'Selected User';
    }
  },
  methods: {
    toggleRemarks(applicationId) {
      // If the same application is clicked again, hide remarks
      if (this.selectedApplicationForRemarks === applicationId) {
        this.selectedApplicationForRemarks = null;
        this.remarksText = ''; // Clear remarks when hidden
      } else {
        this.selectedApplicationForRemarks = applicationId;
        // Load remarks for the selected application
        const selectedApp = this.applications.find(app => app.id === applicationId);
        this.remarksText = selectedApp ? selectedApp.remarks : '';
      }
    },
    viewApplication(applicationId) {
      // Navigate to Pages/Admin/status.vue with the application ID
      // This assumes you have Vue Router configured in your project
      // and a route like: { path: '/admin/status/:id', component: StatusPage }
      console.log(`Navigating to /pages/CPDO/status.vue for application ID: ${applicationId}`);
      if (this.$router) {
        this.$router.push({ path: '/pages/CPDO/status', query: { id: applicationId } });
      } else {
        console.error('Vue Router is not initialized. Please ensure it is set up.');
      }
    },
    viewComplianceProgress() {
      // Navigate to Pages/Admin/status2.vue
      // This assumes you have Vue Router configured in your project
      // and a route like: { path: '/admin/status2', component: Status2Page }
      console.log('Navigating to /pages/Admin/status2.vue');
      if (this.$router) {
        this.$router.push('/pages/Admin/status2');
      } else {
        console.error('Vue Router is not initialized. Please ensure it is set up.');
      }
    },
    handleLogout() {
      console.log('Logging out...');
      // Assuming Vue Router is configured and available as this.$router
      if (this.$router) {
        this.$router.push('/'); // Navigate to the root path
      } else {
        console.error('Vue Router is not initialized. Cannot redirect for logout.');
      }
    }
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

  // // Import Vue Router and define routes
  // import { createRouter, createWebHistory } from 'vue-router';
  // import StatusPage from './Pages/Admin/status.vue';
  // import Status2Page from './Pages/Admin/status2.vue';
  // // Import your AdminDashboard component
  // import AdminDashboard from './components/AdminDashboard.vue'; // Adjust path as needed

  // const routes = [
  //   { path: '/', component: AdminDashboard }, // This is the main route for your dashboard
  //   { path: '/Pages/Admin/status.vue', component: StatusPage },
  //   { path: '/Pages/Admin/status2.vue', component: Status2Page },
  // ];

  // const router = createRouter({
  //   history: createWebHistory(),
  //   routes,
  // });

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
  //           info: '#8b95d3', // Custom color for the blue headers
  //           background: '#f5f5f5',
  //         },
  //       },
  //     },
  //   },
  // });

  // createApp(App)
  //   .use(vuetify)
  //   .use(router) // Use router
  //   .mount('#app');
};
</script>

<style scoped>
/* Scoped styles for this component */
.border-b {
  border-bottom: 1px solid rgba(0, 0, 0, 0.12); /* Vuetify default divider color */
}

/* Specific styling adjustments for the status select for better visual feedback */
.status-select .v-field__outline {
  border-width: 2px !important;
}

/* Optional: Adjust select input background based on status */
.status-select.v-select--completed .v-field__input {
  background-color: #D4EDDA; /* Light green for completed */
}
.status-select.v-select--incomplete .v-field__input {
  background-color: #F8D7DA; /* Light red for incomplete */
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
