<template>
  <div class="py-4 container-fluid">
    <div class="row mb-4">
      <div class="col-lg-12 position-relative z-index-2">
        <div class="row" v-if="resume">
          <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <mini-statistics-card
              :title="{ text: 'Total deliveries', value: resume.total_deliveries }"
              :detail="`<span class='text-success text-sm font-weight-bolder'>${resume.completed_deliveries}</span> completed`"
              :icon="{
                name: 'list',
                color: 'text-white',
                background: 'dark',
              }"
            />
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <mini-statistics-card
              :title="{ text: 'Last 7 days deliveries', value: resume.last_7_days_deliveries }"
              :detail="`${formatStatisticPercentage(resume.increase_deliveries_last_week)} than last month`"
              :icon="{
                name: 'leaderboard',
                color: 'text-white',
                background: 'primary',
              }"
            />
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <mini-statistics-card
              :title="{ text: 'New customers', value: resume.today_new_customers }"
              :detail="`${formatStatisticPercentage(resume.new_customer_increase_from_yesterday)} than yesterday`"
              :icon="{
                name: 'person',
                color: 'text-white',
                background: 'success',
              }"
            />
          </div>
          <div class="col-lg-3 col-md-6 col-sm-6 mt-lg-0 mt-4">
            <mini-statistics-card
              :title="{ text: 'Total cities', value: resume.total_cities }"
              :detail="`in <span class='text-success text-sm font-weight-bolder'>${resume.total_countries}</span> diferent countries`"
              :icon="{
                name: 'place',
                color: 'text-white',
                background: 'info',
              }"
            />
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-lg-4 col-md-6 mt-4">
            <chart-holder-card
              title="Orders last week"
              subtitle="Viewing incoming delivery orders from prefious week"
              v-if="chart_orders_last_week"
            >
              <reports-bar-chart
                id="chart_orders_last_week"
                :chart="{
                  labels: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
                  datasets: {
                    label: 'Orders',
                    data: chart_orders_last_week.values,
                  },
                }"
              />
            </chart-holder-card>
          </div>
          <div class="col-lg-4 col-md-6 mt-4">
            <chart-holder-card
              title="Customer increase"
              subtitle="Vieweing customers increase from the last 5 month"
              color="success"
              v-if="chart_new_clients"
            >
              <reports-line-chart
                id="chart_new_clients"
                :chart="{
                  labels: chart_new_clients.labels,
                  datasets: {
                    label: 'Total clients',
                    data: chart_new_clients.values,
                  },
                }"
              />
            </chart-holder-card>
          </div>
          <div class="col-lg-4 mt-4">
            <chart-holder-card
              title="Shipping average days"
              subtitle="Vieweing delivery average days from the last 5 months"
              color="dark"
              v-if="chart_average_deliveries_month"
            >
              <reports-line-chart
                id="chart_average_deliveries_month"
                :chart="{
                  labels: chart_average_deliveries_month.labels,
                  datasets: {
                    label: 'Average days',
                    data: chart_average_deliveries_month.values,
                  },
                }"
              />
            </chart-holder-card>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-lg-4 mt-4">
            <chart-holder-card
              title="Top 5 Delivery Cities"
              subtitle="Vieweing of the top 5 Ciies that have most deliveries ever."
              color="warning"
              v-if="chart_top_5_cities"
            >
              <reports-line-chart
                id="chart_top_5_cities"
                :chart="{
                  labels: chart_top_5_cities.labels,
                  datasets: {
                    label: 'Deliveries quantity',
                    data: chart_top_5_cities.values,
                  },
                }"
              />
            </chart-holder-card>
          </div>
          <div class="col-lg-4 col-md-6 mt-4">
            <chart-holder-card
              title="Revenue by Month"
              subtitle="Viewing revenue amount from the last 5 month"
              color="secondary"
              v-if="chart_revenue_by_month"
            >
              <reports-bar-chart
                id="chart_revenue_by_month"
                :chart="{
                  labels: chart_revenue_by_month.labels,
                  datasets: {
                    label: 'Amount',
                    data: chart_revenue_by_month.values,
                  },
                }"
              />
            </chart-holder-card>
          </div>
          <div class="col-lg-4 col-md-6 mt-4">
            <chart-holder-card
              title="Average Cost per Delivery"
              subtitle="Vieweing average delivery cost in the last 5 month"
              color="info"
              v-if="chart_cost_per_delivery"
            >
              <reports-line-chart
                id="chart_cost_per_delivery"
                :chart="{
                  labels: chart_cost_per_delivery.labels,
                  datasets: {
                    label: 'Total clients',
                    data: chart_cost_per_delivery.values,
                  },
                }"
              />
            </chart-holder-card>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from 'axios';
import ChartHolderCard from "./components/ChartHolderCard.vue";
import ReportsBarChart from "@/examples/Charts/ReportsBarChart.vue";
import ReportsLineChart from "@/examples/Charts/ReportsLineChart.vue";
import MiniStatisticsCard from "./components/MiniStatisticsCard.vue";

export default {
  name: "dashboard-default",
  components: {
    ChartHolderCard,
    ReportsBarChart,
    ReportsLineChart,
    MiniStatisticsCard
  },
  data() {
    return {
      resume: false,
      chart_orders_last_week: false,
      chart_new_clients: false,
      chart_average_deliveries_month: false,
      chart_revenue_by_month: false,
      chart_top_5_cities: false,
      chart_cost_per_delivery: false,
    };
  },
  mounted () {
    this.renderDeliveriesResume();
    this.renderChartOrdersLastWeek();
    this.renderChartIncreaseClients();
    this.renderChartAverageDeliveryDays();
    this.renderChartTop5Cities();
    this.renderChartRevenueByMonth();
    this.renderChartCostPerDelivery();
  },
  methods: {
    async renderChartCostPerDelivery() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/charts/cost-average`);
        this.chart_cost_per_delivery = response.data
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    async renderChartTop5Cities() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/charts/top-5-cities`);
        this.chart_top_5_cities = response.data
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    async renderChartRevenueByMonth() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/charts/revenue-by-month`);
        this.chart_revenue_by_month = response.data
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    async renderChartAverageDeliveryDays() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/charts/average-delivery-days-by-month`);
        this.chart_average_deliveries_month = response.data
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    async renderChartIncreaseClients() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/charts/customers-by-month`);
        this.chart_new_clients = response.data
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    async renderChartOrdersLastWeek() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/charts/orders-last-week`);
        this.chart_orders_last_week = {
          values: response.data
        }
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    async renderDeliveriesResume() {
      const BASE_URL = process.env.VUE_APP_API_BASE_URL
      try {
        const response = await axios.get(`${BASE_URL}dashboard/statistics`);
        this.resume = response.data;
      } catch (error) {
        console.error('Erro ao obter dados da API:', error);
      }
    },
    formatStatisticPercentage(percentage) {
      if (percentage > 0){
        return `<span class=' text-success text-sm font-weight-bolder'>
         +${percentage}% 
        </span>`
      }
      return `<span class=' text-danger text-sm font-weight-bolder'>
         ${percentage}% 
        </span>`
    }
  }
};
</script>