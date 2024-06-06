<template>
    <tr>
        <td>
            <div class="d-flex px-2 py-1">
                <div class="d-flex flex-column justify-content-center">
                    <h6 class="mb-0 text-sm"># {{ delivery.order_id }}</h6>
                    <p class="text-xs text-secondary mb-0">Customer: {{ delivery.customer_id }}</p>
                </div>
            </div>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">{{ delivery.city }}</p>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">{{ delivery.state }}</p>
            <p class="text-xs text-secondary mb-0">{{ delivery.country }}</p>
        </td>
        <td>
            <p class="text-sm font-weight-bold mb-0">{{ formatCurrency(delivery.cost) }}</p>
        </td>
        <td>
            <p class="text-xs font-weight-bold mb-0">
                <span v-if="delivery.status === 'In transit'">
                    Estimative: 
                </span>
                {{ delivery.estimated_delivery_date }}</p>
            <p class="text-xs text-secondary mb-0">{{ delivery.dispatch_date }}</p>
        </td>
        <td class="align-middle text-center text-sm">
            <span
                class="badge badge-sm bg-gradient-success"
                v-if="delivery.status === 'Delivered'"
            >
                Delivered
            </span>
            <span
                class="badge badge-sm bg-gradient-info"
                v-else-if="delivery.status === 'In transit'"
            >
                In transit
            </span>
            <span
                class="badge badge-sm bg-gradient-secondary"
                v-else-if="delivery.status === 'Cancelled'"
            >
                Cancelled
            </span>
            <span
                class="badge badge-sm bg-gradient-warning"
                v-else
            >
                Pending
            </span>
        </td>
        <td class="align-middle">
            <a href="javascript:;" class="text-secondary font-weight-bold text-xs" data-bs-toggle="modal" data-bs-target="#notesModal" @click="$emit('show-notes', delivery.notes)">
                See notes
            </a>
        </td>
    </tr>
</template>

<script>
export default {
  name: 'DeliveryRow',
  props: {
    delivery: {
      type: Object,
      required: true,
    },
  },
  methods: {
    formatCurrency(currency) {
        return currency.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
    }
  }
};
</script>