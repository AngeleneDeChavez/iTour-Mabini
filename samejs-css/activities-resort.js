document.addEventListener('DOMContentLoaded', function() {
    // Get references to tables
    const activitiesTable = document.querySelector('.activities-table tbody');
    const addedTable = document.querySelector('.added-table tbody');

    // Function to handle Add button clicks
    activitiesTable.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('add-btn')) {
            const currentRow = e.target.closest('tr');
            const activityName = currentRow.cells[0].innerText;
            const price = currentRow.cells[1].innerText;

            // Get the current date and time for the timestamp
            const currentDateTime = new Date().toLocaleString(); // Returns a localized string with date and time

            // Check if the activity is already added
            const existingRows = addedTable.querySelectorAll('tr');
            let alreadyAdded = false;
            existingRows.forEach(function(row) {
                if (row.cells[0] && row.cells[0].innerText === activityName) {
                    alreadyAdded = true;
                }
            });

            if (alreadyAdded) {
                alert('This activity has already been added.');
                return;
            }

            // Create a new row for the added activities table
            const newRow = document.createElement('tr');

            const activityCell = document.createElement('td');
            activityCell.innerText = activityName;
            newRow.appendChild(activityCell);

            const dateCell = document.createElement('td');
            dateCell.innerText = currentDateTime; // Set the date and time when activity is added
            newRow.appendChild(dateCell);

            const priceCell = document.createElement('td');
            priceCell.innerText = price;
            newRow.appendChild(priceCell);

            const removeCell = document.createElement('td');
            const removeBtn = document.createElement('button');
            removeBtn.innerText = 'Remove';
            removeBtn.classList.add('remove-btn');
            removeCell.appendChild(removeBtn);
            newRow.appendChild(removeCell);

            // Remove "No added activities yet" row if it exists
            if (addedTable.rows.length === 1 && addedTable.rows[0].cells[0].colSpan == 4) {
                addedTable.innerHTML = '';
            }

            addedTable.appendChild(newRow);

            
            e.target.disabled = true;
            e.target.innerText = 'Added';
        }
    });

    // Function to handle Remove button clicks
    addedTable.addEventListener('click', function(e) {
        if (e.target && e.target.classList.contains('remove-btn')) {
            const currentRow = e.target.closest('tr');
            currentRow.remove();

            // If no rows left, show the "No added activities yet" message
            if (addedTable.rows.length === 0) {
                const noDataRow = document.createElement('tr');
                const noDataCell = document.createElement('td');
                noDataCell.colSpan = 4;
                noDataCell.innerText = 'No added activities yet';
                noDataRow.appendChild(noDataCell);
                addedTable.appendChild(noDataRow);
            }

            // Optionally re-enable the Add button in the activities table
            // Find the corresponding Add button and enable it
            const activityName = currentRow.cells[0].innerText;
            const activityRows = activitiesTable.querySelectorAll('tr');
            activityRows.forEach(function(row) {
                if (row.cells[0].innerText === activityName) {
                    const addButton = row.querySelector('.add-btn');
                    if (addButton) {
                        addButton.disabled = false;
                        addButton.innerText = 'Add';
                    }
                }
            });
        }
    });
});
