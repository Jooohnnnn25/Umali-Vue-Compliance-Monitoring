<template>
  <v-app>
    <v-main class="d-flex align-center justify-center" style="min-height: 100vh; background-color: #f5f5f5;">
      <v-container fluid class="pa-4">
        <v-row justify="center" align="center">
          <!-- Left Section: Logo and System Name -->
          <v-col cols="12" md="6" class="d-flex flex-column align-center justify-center text-center">
            <div class="mb-4 animated-text-block">
              <!-- Placeholder for city.png. In a real Vuetify app, you would place city.png in your 'public' or 'assets' folder and reference it like '/city.png' or require it. -->
              <!-- For this example, we use a placeholder image URL. -->
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

          <!-- Right Section: Login Form -->
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
export default {
  name: 'LoginLayout',
  data() {
    return {
      username: 'john@email.com', // Pre-filled username
      password: '********',     // Pre-filled password
      selectedRole: null, // New data property to hold the selected role
      roles: ['applicant', 'administrative aide', 'engineer/architect', 'cpdo'], // Array of roles for the dropdown
    };
  },
  methods: {
    handleLogin() {
      // Basic login logic: log username, password, and selected role to console
      console.log('Attempting login with:');
      console.log('Username:', this.username);
      console.log('Password:', this.password);
      console.log('Selected Role:', this.selectedRole);

      // Implement redirection based on selected role
      if (this.selectedRole === 'administrative aide') {
        // Assuming Vue Router is configured and available as this.$router
        this.$router.push('/pages/Admin/dashboard');
      } else if (this.selectedRole === 'engineer/architect') {
        this.$router.push('/pages/Engr/dashboard');
      } else if (this.selectedRole === 'cpdo') {
        this.$router.push('/pages/CPDO/CPDOdashboard');
      } else {
        // Default action for 'applicant' or any other unhandled role
        console.log('No specific redirection for this role, or role not selected.');
       
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
