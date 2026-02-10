<template>
  <v-container fluid>
      <v-row>    
        <v-col cols="12" sm="6" md="3" v-for="(value, key) in counter" :key="key">
          <dashboard-card :title="value.title" :count="value.count" />
        </v-col>
      </v-row>
  </v-container> 
</template>

<script>
import api from '@/plugins/axios';
import DashboardCard from './DashboardCard.vue';
import LineChart from './LineChart.vue';
import generalModel from '@/models/general.model';

export default {
  name: 'AuctionDashboard',
  components: {
    DashboardCard,
    LineChart,
  },
  data() {
    return {
      counter: {
        customers: {
          title: 'Customers',
          count: 100,
        },
        products: {
          title: 'Products',
          count: 100,
        },
        dc: {
          title: 'Challan',
          count: 100,
        },
        invoice: {
          title: 'Invoice',
          count: 100,
        },
      },
      chartData: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [
          {
            label: 'Sales',
            data: [100, 200, 300, 400, 500, 600],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
          },
        ],
      },
    };
  },
  mounted(){
    this.loadCounter();
  },
  methods:{

    async loadCounter(){

      try {

          const res = await generalModel.get('/api/dashboard/counters');

          console.log(res);
          
          this.counter.customers.count = res.user;
          this.counter.products.count = res.product;
          this.counter.dc.count = res.dc;
          this.counter.invoice.count = res.invoice;

      } catch (error) { 
        this.counter.customers.count = 0;
        this.counter.products.count = 0;
        this.counter.dc.count = 0;
        this.counter.invoice.count = 0;
      }
       
    }
  }
  

};
</script>



<style>
.kpi-tile {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.kpi-tile:hover {
  transform: translateY(-6px);
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25) !important;
}

.kpi-tile .v-card-text {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  min-height: 180px;
}
</style>