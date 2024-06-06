<template>
    <div class="modal fade" id="notesModal" tabindex="-1" role="dialog" aria-labelledby="notesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-normal" id="notesModalLabel">Delivery Notes</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ notes }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>
  </template>
  
  <script>
  export default {
    name: "DeliveryNotesModal",
    props: {
      notes: {
        type: String,
        required: true,
      },
    },
  };
  </script>
  
  <style scoped>
  /* Adicione estilos personalizados aqui, se necessário */
  </style>
  