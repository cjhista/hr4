document.addEventListener("DOMContentLoaded", () => {
    const payrollForm = document.getElementById("payrollForm");
    const payrollTableBody = document.querySelector("#payrollTable tbody");

    // Load payroll entries from the database when the page loads
    loadPayrollFromDB();

    payrollForm.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(payrollForm);

        const grossPay = parseFloat(formData.get("gross_pay"));
        const deductions = parseFloat(formData.get("deductions"));

        if (isNaN(grossPay) || isNaN(deductions)) {
            alert("Please enter valid numbers for Gross Pay and Deductions.");
            return;
        }

        fetch("save_payroll.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                alert(data.message);
                loadPayrollFromDB();
                payrollForm.reset();
                payrollForm.elements["payroll_id"].value = "";
                document.getElementById("submitButton").textContent = "Add Payroll";
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error saving payroll:", error);
            alert("Failed to save payroll.");
        });
    });

    function loadPayrollFromDB() {
        fetch("get_payroll.php")
            .then(response => response.json())
            .then(data => {
                payrollTableBody.innerHTML = "";
                data.forEach(payroll => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td>${payroll.payroll_id}</td>
                        <td>${payroll.employee_id}</td>
                        <td>${payroll.employee_name}</td>
                        <td>${payroll.pay_period}</td>
                        <td>₱${parseFloat(payroll.gross_pay).toFixed(2)}</td>
                        <td>₱${parseFloat(payroll.deductions).toFixed(2)}</td>
                        <td>₱${parseFloat(payroll.net_pay).toFixed(2)}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" onclick="editPayroll(${payroll.payroll_id})">Edit</button>
                            <button class="btn btn-danger btn-sm" onclick="deletePayroll(${payroll.payroll_id})">Delete</button>
                        </td>
                    `;
                    payrollTableBody.appendChild(row);
                });
            })
            .catch(error => {
                console.error("Error loading payroll:", error);
            });
    }

    window.deletePayroll = function (payrollId) {
        if (confirm("Are you sure you want to delete this payroll entry?")) {
            fetch("delete_payroll.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "payroll_id=" + encodeURIComponent(payrollId)
            })
            .then(response => response.text())
            .then(msg => {
                alert(msg);
                loadPayrollFromDB();
            })
            .catch(error => {
                console.error("Error deleting payroll:", error);
                alert("Failed to delete payroll.");
            });
        }
    };

    window.editPayroll = function (payrollId) {
        fetch("get_payroll.php?id=" + payrollId)
            .then(response => response.json())
            .then(payroll => {
                payrollForm.elements["payroll_id"].value = payroll.payroll_id;
                payrollForm.elements["employee_id"].value = payroll.employee_id;
                payrollForm.elements["employee_name"].value = payroll.employee_name;
                payrollForm.elements["pay_period"].value = payroll.pay_period;
                payrollForm.elements["gross_pay"].value = payroll.gross_pay;
                payrollForm.elements["deductions"].value = payroll.deductions;
                document.getElementById("submitButton").textContent = "Update Payroll";
            })
            .catch(error => {
                console.error("Error loading payroll for edit:", error);
                alert("Failed to load payroll details.");
            });
    };
});
