document.addEventListener('DOMContentLoaded', function () {
    const bonModal = document.getElementById('bonModal');
    const bonForm = document.getElementById('bonForm');
    const bonId = document.getElementById('bonId');
    const bonDate = document.getElementById('bonDate');
    const bonMonth = document.getElementById('bonMonth');
    const bonNumber = document.getElementById('bonNumber');
    const bonVehicle = document.getElementById('bonVehicle');
    const bonDriver = document.getElementById('bonDriver');
    const bonAmount = document.getElementById('bonAmount');
    const modalTitle = document.getElementById('bonModalLabel');

    const bootstrapModal = new bootstrap.Modal(bonModal);

    window.editBon = async function (id) {
        const response = await fetch(`actions/get_bon.php?id=${id}`);
        const data = await response.json();
        if (!data || data.error) return;

        bonId.value = data.id;
        bonDate.value = data.date;
        bonMonth.value = data.month;
        bonNumber.value = data.bon_number;
        bonVehicle.value = data.vehicle_registration;
        bonDriver.value = data.driver_name;
        bonAmount.value = data.amount_spent;
        modalTitle.textContent = 'Modifier un Bon de Carburant';
        bootstrapModal.show();
    };

    window.deleteBon = function (id) {
        if (!confirm('Voulez-vous vraiment supprimer ce bon ?')) {
            return;
        }
        const form = document.createElement('form');
        form.method = 'post';
        form.action = 'actions/delete_bon.php';
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    };

    document.getElementById('addBonBtn').addEventListener('click', function () {
        modalTitle.textContent = 'Ajouter un Bon de Carburant';
        bonForm.reset();
        bonId.value = '';
    });
});

function exportExcel() {
    const table = document.querySelector('table');
    const wb = XLSX.utils.table_to_book(table, {sheet: 'Bons'});
    XLSX.writeFile(wb, 'rapport_carburant.xlsx');
}
