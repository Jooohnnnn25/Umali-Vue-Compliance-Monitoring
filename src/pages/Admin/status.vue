<template>
  <v-app>
    <v-main style="background-color: #f5f5f5;">
      <v-container fluid class="pa-6">

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
import axios from 'axios';

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
      // Data will now be fetched from backend
      generalDocuments: [],

      // Headers for Proof of Ownership Table
      proofOfOwnershipHeaders: [
        { title: 'Document Type', key: 'documentType', align: 'start', sortable: false },
        { title: 'Date Submitted', key: 'dateSubmitted', align: 'center', sortable: false },
        { title: 'Status', key: 'status', align: 'center', sortable: false },
      ],
      // Data will now be fetched from backend
      proofOfOwnershipDocuments: [],

      // Headers for Clearances Table
      clearancesHeaders: [
        { title: 'Clearance Type', key: 'documentType', align: 'start', sortable: false },
        { title: 'Date Submitted', key: 'dateSubmitted', align: 'center', sortable: false },
        { title: 'Status', key: 'status', align: 'center', sortable: false },
      ],
      // Data will now be fetched from backend
      clearanceDocuments: [],
    };
  },
  methods: {
    getStatusColorClass(status) {
      if (status === 'Submitted') return 'text-green-darken-2';
      return '';
    },
    async fetchDocumentStatus() {
      try {
        // UPDATED URL HERE
        const response = await axios.get('http://localhost/compliance-monitoring-vue-umali/src/pages/Admin/admin_status_backend.php/document_status_api.php');
        if (response.data.success) {
          this.generalDocuments = response.data.generalDocuments;
          this.proofOfOwnershipDocuments = response.data.proofOfOwnershipDocuments;
          this.clearanceDocuments = response.data.clearanceDocuments;
        } else {
          console.error('Failed to fetch document status:', response.data.message);
          alert('Failed to load document status: ' + response.data.message);
        }
      } catch (error) {
        console.error('Error fetching document status:', error);
        alert('Network error or server issue while fetching document status.');
      }
    }
  },
  mounted() {
    this.fetchDocumentStatus(); // Call the fetch method when the component is mounted
  },
};
</script>

<style scoped>
/* Your existing styles remain unchanged */
.v-main {
  background-color: #f5f5f5;
}

.elevation-2.rounded-lg {
  border-radius: 12px !important;
  overflow: hidden;
}

.v-data-table :deep(.v-data-table__th) {
  background-color: #8b95d3 !important;
  color: white !important;
  font-weight: medium !important;
  text-align: center !important;
}

.v-data-table :deep(.v-data-table__th:first-child) {
  text-align: left !important;
  padding-left: 16px;
  border-radius: 8px 0 0 0;
}

.v-data-table :deep(.v-data-table__th:last-child) {
  border-radius: 0 8px 0 0;
}

.v-data-table :deep(.v-data-table__td) {
  text-align: center !important;
  padding: 8px 16px;
  border-bottom: 1px solid rgba(0, 0, 0, 0.12);
}

.v-data-table :deep(.v-data-table__td:first-child) {
  text-align: left !important;
}

.v-data-table :deep(.v-data-table__tr:last-child .v-data-table__td) {
  border-bottom: none !important;
}

.text-green-darken-2 {
  color: #28a745 !important;
  font-weight: bold;
}

h2 {
  color: #333;
}
</style>