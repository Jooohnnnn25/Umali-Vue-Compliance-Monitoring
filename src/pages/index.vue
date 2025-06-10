<template>
  <v-app>
    <v-main class="d-flex align-center justify-center" style="min-height: 100vh; background-color: #f5f5f5;">
      <v-container fluid class="pa-4">
        <v-row justify="center" align="center">
          <v-col cols="12" md="6" class="d-flex flex-column align-center justify-center text-center">
            <div class="mb-4 animated-text-block">
              <v-img
                src="../assets/city.png"
                alt="City Logo"
                contain
                max-width="100"
                class="mb-4 mx-auto animated-logo"
                style="border-radius: 8px;"
              ></v-img>
              <h1 class="text-h4 font-weight-bold mb-2">BOPEMS</h1>
              <p class="text-h6 mb-1">BUILDING & OCCUPANCY PERMIT</p>
              <p class="text-h6">MANAGEMENT SYSTEM</p>
            </div>
          </v-col>

          <v-col cols="12" md="6" class="d-flex justify-center">
            <v-card class="pa-6" elevation="3" rounded="xl" style="background-color: #e8f0fe; max-width: 450px; width: 100%;">
              <v-card-title class="text-h5 font-weight-medium text-center primary--text mb-4">
                Login to your Account
              </v-card-title>

              <v-card-text>
                <v-select
                  v-model="selectedRole"
                  :items="roles"
                  label="Select Role"
                  variant="outlined"
                  density="comfortable"
                  hide-details
                  class="mb-4"
                  style="border-radius: 8px; background-color: #ffffff;"
                ></v-select>

                <v-text-field
                  v-model="username"
                  label="username"
                  variant="outlined"
                  density="comfortable"
                  placeholder="usernamesample1"
                  hide-details
                  class="mb-4"
                  style="border-radius: 8px; background-color: #ffffff;"
                ></v-text-field>

                <v-text-field
                  v-model="password"
                  label="password"
                  type="password"
                  variant="outlined"
                  density="comfortable"
                  placeholder="**********"
                  hide-details
                  class="mb-4"
                  style="border-radius: 8px; background-color: #ffffff;"
                ></v-text-field>

                <v-btn
                  color="#002060"
                  size="large"
                  class="white--text"
                  block
                  rounded="lg"
                  @click="handleLogin"
                  elevation="2"
                >
                  LOGIN
                </v-btn>
              </v-card-text>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import axios from 'axios'; // Import axios

export default {
  name: 'LoginLayout',
  data() {
    return {
      username: 'adminaide1', // Pre-filled username for testing
      password: 'samplepassword', // Pre-filled password for testing
      selectedRole: null, // New data property to hold the selected role
      roles: ['applicant', 'administrative aide', 'engineer/architect', 'cpdo'], // Array of roles for the dropdown
    };
  },
  methods: {
    async handleLogin() { // Make the method async
      console.log('Attempting login with:');
      console.log('Username:', this.username);
      console.log('Password:', this.password);
      console.log('Selected Role:', this.selectedRole);

      try {
        // *** This is the CORRECTED URL for your login.php file ***
        const response = await axios.post('http://localhost/compliance-monitoring-vue-umali/src/pages/login.php', {
          username: this.username,
          password: this.password,
          selectedRole: this.selectedRole,
        }, {
          headers: {
            'Content-Type': 'application/json'
          }
        });

        if (response.data.success) {
          console.log('Login successful:', response.data.message);
          // Implement redirection based on selected role
          if (this.selectedRole === 'administrative aide') {
            this.$router.push('/pages/Admin/dashboard');
          } else if (this.selectedRole === 'engineer/architect') {
            this.$router.push('/pages/Engr/dashboard');
          } else if (this.selectedRole === 'cpdo') {
            this.$router.push('/pages/CPDO/CPDOdashboard');
          } else {
            console.log('No specific redirection for this role, or role not selected.');
            // Example for applicant: this.$router.push('/pages/Applicant/dashboard');
          }
        } else {
          console.error('Login failed:', response.data.message);
          alert(response.data.message); // Show error to user
        }
      } catch (error) {
        console.error('Error during login:', error);
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx (e.g., 404, 500)
          console.error('Data:', error.response.data);
          console.error('Status:', error.response.status);
          console.error('Headers:', error.response.headers);
          alert('Server error: ' + (error.response.data.message || 'An unexpected server error occurred.'));
        } else if (error.request) {
          // The request was made but no response was received (e.g., network down, CORS issue)
          console.error('No response received:', error.request);
          alert('Network error: Could not reach the server. Please ensure the backend (XAMPP/WAMP) is running and accessible.');
        } else {
          // Something else happened in setting up the request that triggered an error
          console.error('Error', error.message);
          alert('An unexpected client-side error occurred: ' + error.message);
        }
      }
    },
  },
};
</script>

<style scoped>
/* Specific styling for the component can go here if needed,
    though Vuetify's utility classes handle much of it. */
.text-h4 {
  font-family: 'Inter', sans-serif !important; /* Ensure Inter font if specified */
}

.primary--text {
  color: #3F51B5 !important; /* Vuetify primary color for the title */
}

/* Custom style for the button text color if not handled by default themes */
.white--text {
  color: #ffffff !important;
}

/* Animation for the logo and text block */
@keyframes fadeInSlideInLeft {
  0% {
    transform: translateX(-50px);
    opacity: 0;
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}

.animated-logo {
  animation: fadeInSlideInLeft 1s ease-out forwards;
}

.animated-text-block {
  animation: fadeInSlideInLeft 1s ease-out 0.2s forwards; /* Slight delay for the text */
}
</style>