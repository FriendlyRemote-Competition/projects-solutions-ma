const tableHead = document.getElementById("headerRow");
const tableBody = document.getElementById("tableBody");
const addButton = document.getElementById("addRow");
const saveButton = document.getElementById("save");

let tableData = [];

let fields = [];

// Load table.json
async function loadData() {
    const response = await fetch("api.php");
    tableData = await response.json();
    if (tableData.length > 0) {
        fields = Object.keys(tableData[0]);
    }
    renderTable();
}

// Render table
function renderTable() {
    tableHead.innerHTML = "";
    tableBody.innerHTML = "";

    // Create column headers
    fields.forEach(field => {
        const th = document.createElement("th");
        const input = document.createElement("input");
        input.value = field;
        input.dataset.originalField = field;
        input.addEventListener("change", () => {
            renameField(
                input.dataset.originalField,
                input.value
            );
            input.dataset.originalField = input.value;
        });
        th.appendChild(input);
        tableHead.appendChild(th);
    });

    // Delete column
    const deleteHeader = document.createElement("th");
    deleteHeader.textContent = "-Delete-";
    tableHead.appendChild(deleteHeader);
    // Create row
    tableData.forEach((row, rowIndex) => {
        const tr = document.createElement("tr");

        fields.forEach(field => {
            const td = document.createElement("td");
            const input = document.createElement("input");
            input.value = row[field] ?? "";
            input.addEventListener("input", () => {
                tableData[rowIndex][field] = input.value;
            });
            td.appendChild(input);
            tr.appendChild(td);
        });

        // Delete button
        const deleteTd = document.createElement("td");
        const deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
        deleteButton.className = "delete-btn";
        deleteButton.addEventListener("click", () => {
            deleteRow(rowIndex);
        });
        deleteTd.appendChild(deleteButton);
        tr.appendChild(deleteTd);
        tableBody.appendChild(tr);
    });
}

// Add row
async function addRow() {
    const newRow = {};
    fields.forEach(field => {
        newRow[field] = "";
    });
    tableData.push(newRow);
    renderTable();
    // Immediately save addition
    await saveData();
}

// Delete row
async function deleteRow(index) {
    tableData.splice(index, 1);
    renderTable();
    // Immediately save deletion
    await saveData();
}

// Rename field
function renameField(oldName, newName) {
    if (!newName || oldName === newName) {
        return;
    }
    const fieldIndex = fields.indexOf(oldName);
    if (fieldIndex === -1) {
        return;
    }

    fields[fieldIndex] = newName;
    tableData.forEach(row => {
        row[newName] = row[oldName];
        delete row[oldName];
    });
    renderTable();
}

// Save
async function saveData() {
    await fetch("api.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(tableData)
    });
}

// Save button
saveButton.addEventListener("click", async () => {
    await saveData();
    alert("Data saved successfully.");
});

// Add button
addButton.addEventListener("click", addRow);

// Start
loadData();