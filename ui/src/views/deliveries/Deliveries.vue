<template>
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card my-4">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
            <div
              class="bg-gradient-success shadow-success border-radius-lg pt-4 pb-3"
            >
              <h6 class="text-white text-capitalize ps-3">Deliveries details</h6>
            </div>
          </div>
          <div class="card-body px-0 pb-2">
            <form @submit.prevent="applyFilters">
              <div class="row mx-3">
                <div class="col-md-6 col-lg-3">
                  <MaterialInput
                    id="fromDeliveryDate"
                    label="From delivery date"
                    type="date"
                    variant="static"
                    class="my-3"
                    color=""
                  />
                </div>
                <div class="col-md-6 col-lg-3">
                  <MaterialInput
                    id="toDeliveryDate"
                    label="To delivery date"
                    type="date"
                    variant="static"
                    class="my-3"
                    color=""
                  />
                </div>
                <div class="col-md-6 col-lg-3">
                  <MaterialInput
                    id="fromDispatchDate"
                    label="From dispatch date"
                    type="datetime-local"
                    variant="static"
                    class="my-3"
                    color=""
                  />
                </div>
                <div class="col-md-6 col-lg-3">
                  <MaterialInput
                    id="toDispatchDate"
                    label="To dispatch date"
                    type="datetime-local"
                    variant="static"
                    class="my-3"
                    color=""
                  />
                </div>
              </div>
              <div class="row mx-3">
                <div class="col-md-3 pb-3">
                  <MaterialInput
                    id="orderId"
                    label="Order ID"
                    type="number"
                  />
                </div>
                <div class="col-md-3 pb-3">
                  <MaterialInput
                    id="customerId"
                    label="Customer ID"
                    type="number"
                  />
                </div>
                <div class="col-md-6 pb-3">
                  <MaterialInput
                    id="cityStateCountry"
                    label="City, State or Country"
                    type="text"
                  />
                </div>
                <div class="col-md-3 pb-3">
                  <div class="input-group input-group-static">
                    <select class="form-control" id="status">
                      <option>All status</option>
                      <option>Delivered</option>
                      <option>Pending</option>
                      <option>In transit</option>
                      <option>Cancelled</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-3 pb-3">
                  <MaterialInput
                    id="minCost"
                    label="Min. cost"
                    type="number"
                    min="0.00"
                    step="0.01"
                  />
                </div>
                <div class="col-md-3 pb-3">
                  <MaterialInput
                    id="maxCost"
                    label="Max. cost"
                    type="number"
                    min="0.00"
                    step="0.01"
                  />
                </div>
                <div class="col-md-3 pb-3">
                  <material-button color="info" variant="gradient" full-width>
                    Filter
                  </material-button>
                </div>
              </div>
            </form>
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Order ID / Customer ID
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Destination city
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Province / Country
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Cost
                    </th>
                    <th
                      class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2"
                    >
                      Delivery / Dispatch date
                    </th>
                    <th
                      class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                    >
                      Status
                    </th>
                    <th class="text-secondary opacity-7"></th>
                  </tr>
                </thead>
                <tbody>
                  <DeliveryRow
                    v-for="delivery in deliveries"
                    :key="delivery.id"
                    :delivery="delivery"
                    @show-notes="showNotes"
                  />
                </tbody>
              </table>
              <MaterialPagination :color="'success'" :size="'md'">
                <MaterialPaginationItem
                  v-for="link in paginationLinks"
                  :key="link.label"
                  :label="link.label"
                  :active="link.active"
                  :disabled="!link.url"
                  v-on:click="fetchPage(link.url)"
                />
              </MaterialPagination>
            </div>
          </div>
        </div>
      </div>
    </div>
    <DeliveryNotesModal :notes="currentNotes" />
  </div>
</template>

<script>
import axios from 'axios';
import DeliveryRow from './DeliveryRow.vue';
import DeliveryNotesModal from './DeliveryNotesModal.vue';
import MaterialPagination from '../../components/MaterialPagination.vue';
import MaterialPaginationItem from '../../components/MaterialPaginationItem.vue';
import MaterialInput from '../../components/MaterialInput.vue';
import MaterialButton from '../../components/MaterialButton.vue';

export default {
  name: "Deliveries",
  components: {
    DeliveryRow,
    DeliveryNotesModal,
    MaterialPagination,
    MaterialPaginationItem,
    MaterialInput,
    MaterialButton
  },
  data() {
    return {
      deliveries: [],
      paginationLinks: [],
      currentPage: 1,
      currentNotes: "",
    }
  },
  mounted () {
    this.getDeliveriesData();
  },
  methods: {

    async getDeliveriesData(page = 1, filters = {}) {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const params = { page, ...filters };
        const response = await axios.get(`${BASE_URL}deliveries`, { params });
        this.deliveries = response.data.data
        this.paginationLinks = response.data.links;
        this.currentPage = response.data.current_page;
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    getFilters() {
      return {
        from_delivery_date: document.getElementById('fromDeliveryDate').value,
        to_delivery_date: document.getElementById('toDeliveryDate').value,
        from_dispatch_date: document.getElementById('fromDispatchDate').value,
        to_dispatch_date: document.getElementById('toDispatchDate').value,
        order_id: document.getElementById('orderId').value,
        customer_id: document.getElementById('customerId').value,
        city_state_country: document.getElementById('cityStateCountry').value,
        status: document.getElementById('status').value,
        min_cost: document.getElementById('minCost').value,
        max_cost: document.getElementById('maxCost').value,
      };
    },
    applyFilters() {
      const filters = this.getFilters();
      this.getDeliveriesData(1, filters);
    },
    fetchPage(url) {
      const filters = this.getFilters();
      const page = new URL(url).searchParams.get('page');
      if (page) {
        this.getDeliveriesData(page, filters);
      }
    },
    showNotes(notes) {
      this.currentNotes = notes;
    }

  }
};
</script>
