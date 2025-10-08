<?php
$pageTitle = "HR 4 Benefits";
$userName  = "User"; 

// Example dynamic values (replace with DB queries later)
$totalBenefitsCost = "₱15,500";
$enrolledEmployees = 3;
$pendingApplications = 1;
$enrollmentRate = 75;

// Example employees enrollment status
$employeeEnrollments = [
  [
    "name" => "Maria Santos",
    "role" => "Front Desk Manager",
    "dependents" => 2,
    "hmo" => "PhilCare",
    "sss" => true,
    "philhealth" => true,
    "pagibig" => true,
    "status" => "ENROLLED",
    "cost" => "₱4,500"
  ],
  [
    "name" => "John Dela Cruz",
    "role" => "Head Chef",
    "dependents" => 3,
    "hmo" => "Maxicare",
    "sss" => true,
    "philhealth" => true,
    "pagibig" => false,
    "status" => "ENROLLED",
    "cost" => "₱5,200"
  ],
  [
    "name" => "Sarah Wilson",
    "role" => "Housekeeping Supervisor",
    "dependents" => 1,
    "hmo" => "Intellicare",
    "sss" => true,
    "philhealth" => false,
    "pagibig" => false,
    "status" => "PENDING",
    "cost" => "₱2,800"
  ]
];

// Example providers
$providers = [
  [
    "name" => "PhilCare",
    "coverage" => "Comprehensive",
    "premium" => "₱2,500",
    "benefit" => "₱150,000",
    "network" => "500+ hospitals nationwide"
  ],
  [
    "name" => "Maxicare",
    "coverage" => "Premium",
    "premium" => "₱3,200",
    "benefit" => "₱300,000",
    "network" => "800+ hospitals nationwide"
  ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="icon" type="png" href="logo2.png" />
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      lucide.createIcons();
    });
  </script>
</head>
<body class="h-screen overflow-hidden">
  <div class="flex h-full">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 flex flex-col overflow-y-auto">

      <!-- Sticky Header -->
      <div class="flex items-center justify-between border-b py-4 bg-white sticky top-0 z-50 px-6">
        <div class="flex items-center gap-4">
          <button id="sidebarToggle" class="text-gray-600 hover:text-gray-800 focus:outline-none">
            <i id="toggleIcon" data-lucide="menu" class="w-6 h-6"></i>
          </button>
          <h1 class="text-lg font-bold text-gray-800">HR 4 MANAGEMENT SYSTEM</h1>
        </div>
        <h1 class="text-lg font-semibold text-gray-600">Hotel & Restaurant NAME</h1>
      </div>

      <main class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between border-b pb-4">
          <div>
            <h1 class="text-2xl font-bold text-gray-800">Benefits Administration</h1>
            <p class="text-gray-500 text-sm">Manage employee benefits enrollment and healthcare providers</p>
          </div>
          <div class="flex gap-2">
            <button id="btnReport" class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-sm">Generate Report</button>
            <button id="btnAddProvider" class="px-4 py-2 rounded-lg bg-blue-900 text-white hover:bg-blue-800 text-sm">+ Add Provider</button>
          </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Total Benefits Cost</p>
            <h2 class="text-2xl font-bold"><?php echo $totalBenefitsCost; ?></h2>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Enrolled</p>
            <h2 class="text-2xl font-bold text-green-600"><?php echo $enrolledEmployees; ?></h2>
            <p class="text-xs text-gray-400">Employees</p>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Pending</p>
            <h2 class="text-2xl font-bold text-yellow-600"><?php echo $pendingApplications; ?></h2>
            <p class="text-xs text-gray-400">Applications</p>
          </div>
          <div class="bg-white p-6 rounded-2xl shadow">
            <p class="text-sm text-gray-500">Enrollment Rate</p>
            <h2 class="text-2xl font-bold"><?php echo $enrollmentRate; ?>%</h2>
            <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
              <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $enrollmentRate; ?>%"></div>
            </div>
          </div>
        </div>

        <!-- Tabs -->
        <div class="w-full flex justify-center">
          <div class="flex bg-gray-100 rounded-lg overflow-hidden text-sm font-medium text-gray-600">
            <button id="tabEnrollments" class="px-6 py-2 border-b-2 border-blue-600 text-blue-600">Employee Enrollments</button>
            <button id="tabProviders" class="px-6 py-2">Healthcare Providers</button>
          </div>
        </div>

        <!-- Employee Enrollments -->
        <div id="enrollmentsSection" class="space-y-4">
          <?php foreach ($employeeEnrollments as $emp): ?>
            <div class="bg-white shadow rounded-xl p-6 flex justify-between items-center">
              <div>
                <h2 class="font-bold text-gray-800"><?php echo $emp['name']; ?></h2>
                <p class="text-sm text-gray-500"><?php echo $emp['role']; ?> · <?php echo $emp['dependents']; ?> dependent(s)</p>
                <p class="text-xs text-gray-500">HMO: <?php echo $emp['hmo']; ?></p>
                <div class="flex flex-wrap gap-4 text-xs mt-2">
                  <p><?php echo $emp['sss'] ? "✓ SSS" : "✗ SSS"; ?></p>
                  <p><?php echo $emp['philhealth'] ? "✓ PhilHealth" : "✗ PhilHealth"; ?></p>
                  <p><?php echo $emp['pagibig'] ? "✓ Pag-IBIG" : "✗ Pag-IBIG"; ?></p>
                </div>
              </div>
              <div class="text-right">
                <span class="px-3 py-1 rounded-md text-xs font-semibold
                  <?php echo $emp['status'] == 'ENROLLED' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?>">
                  <?php echo $emp['status']; ?>
                </span>
                <p class="font-bold mt-2"><?php echo $emp['cost']; ?></p>
                <button onclick="openManageModal('<?php echo htmlspecialchars($emp['name'], ENT_QUOTES); ?>')" class="mt-2 text-sm text-blue-600 hover:underline">Manage</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <!-- Providers -->
        <div id="providersSection" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <?php foreach ($providers as $idx => $prov): ?>
            <div id="providerCard<?php echo $idx; ?>" class="bg-white shadow rounded-xl p-6">
              <h2 class="font-bold text-gray-800"><?php echo htmlspecialchars($prov['name']); ?></h2>
              <p class="text-sm text-gray-500 prov-coverage"><?php echo htmlspecialchars($prov['coverage']); ?> Coverage</p>
              <p class="mt-2 text-sm prov-premium">Monthly Premium: <span class="font-bold"><?php echo htmlspecialchars($prov['premium']); ?></span></p>
              <p class="text-sm prov-benefit">Max Benefit: <span class="font-bold"><?php echo htmlspecialchars($prov['benefit']); ?></span></p>
              <p class="text-sm prov-network">Network: <?php echo htmlspecialchars($prov['network']); ?></p>
              <div class="flex gap-2 mt-4">
                <button onclick="openViewProvider(<?php echo $idx; ?>)" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">View Details</button>
                <button onclick="openEditProvider(<?php echo $idx; ?>)" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded">Edit</button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

      </main>
    </div>
  </div>

  <!-- Modals -->
  <!-- Generate Report Modal -->
  <div id="modalReport" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Generate Report</h2>
      <p class="text-sm text-gray-600 mb-4">Download employee benefits report as PDF or Excel.</p>
      <div class="flex justify-end gap-2">
        <button onclick="closeModal('modalReport')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
        <button class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Download PDF</button>
      </div>
    </div>
  </div>

  <!-- Add Provider Modal -->
  <div id="modalAddProvider" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Add Healthcare Provider</h2>
      <form id="addProviderForm" class="space-y-3">
        <input type="text" id="addProviderName" placeholder="Provider Name" class="w-full border rounded px-3 py-2 text-sm" required />
        <input type="text" id="addProviderCoverage" placeholder="Coverage" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" id="addProviderPremium" placeholder="Premium" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" id="addProviderBenefit" placeholder="Max Benefit" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" id="addProviderNetwork" placeholder="Network" class="w-full border rounded px-3 py-2 text-sm" />
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('modalAddProvider')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- View Provider Modal -->
  <div id="modalViewProvider" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Provider Details</h2>
      <div id="providerDetails" class="space-y-2 text-sm text-gray-700"></div>
      <div class="flex justify-end gap-2 mt-4">
        <button onclick="closeModal('modalViewProvider')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Close</button>
      </div>
    </div>
  </div>

  <!-- Edit Provider Modal -->
  <div id="modalEditProvider" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Edit Provider</h2>
      <form id="editProviderForm" class="space-y-3" data-index="">
        <input type="text" id="editProviderName" placeholder="Provider Name" class="w-full border rounded px-3 py-2 text-sm" required />
        <input type="text" id="editProviderCoverage" placeholder="Coverage" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" id="editProviderPremium" placeholder="Premium" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" id="editProviderBenefit" placeholder="Max Benefit" class="w-full border rounded px-3 py-2 text-sm" />
        <input type="text" id="editProviderNetwork" placeholder="Network" class="w-full border rounded px-3 py-2 text-sm" />
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="closeModal('modalEditProvider')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Manage Employee Modal -->
  <div id="modalManage" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
    <div class="bg-white rounded-xl shadow-lg w-96 p-6">
      <h2 class="text-lg font-bold mb-4">Manage Employee</h2>
      <p id="manageEmployeeName" class="text-gray-700 mb-4"></p>
      <div class="flex justify-end gap-2">
        <button onclick="closeModal('modalManage')" class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Close</button>
        <button class="px-4 py-2 rounded bg-blue-900 text-white hover:bg-blue-800">Edit</button>
      </div>
    </div>
  </div>

  <script>
    // Client-side providers data (from PHP)
    const providersData = <?php echo json_encode($providers, JSON_UNESCAPED_UNICODE); ?>;

    document.addEventListener("DOMContentLoaded", function () {
      const sidebarToggle = document.getElementById("sidebarToggle");
      const sidebar = document.getElementById("sidebar");
      const sidebarTexts = document.querySelectorAll(".sidebar-text");
      const logoExpanded = document.querySelector(".sidebar-logo-expanded");
      const logoCollapsed = document.querySelector(".sidebar-logo-collapsed");

      sidebarToggle && sidebarToggle.addEventListener("click", function () {
        if (!sidebar) return;
        sidebar.classList.toggle("w-64");
        sidebar.classList.toggle("w-20");

        if (sidebar.classList.contains("w-20")) {
          sidebarTexts.forEach(el => el.classList.add("hidden"));
          logoExpanded && logoExpanded.classList.add("hidden");
          logoCollapsed && logoCollapsed.classList.remove("hidden");
        } else {
          sidebarTexts.forEach(el => el.classList.remove("hidden"));
          logoExpanded && logoExpanded.classList.remove("hidden");
          logoCollapsed && logoCollapsed.classList.add("hidden");
        }
        lucide.createIcons();
      });

      // Tabs
      const tabEnrollments = document.getElementById("tabEnrollments");
      const tabProviders = document.getElementById("tabProviders");
      const enrollmentsSection = document.getElementById("enrollmentsSection");
      const providersSection = document.getElementById("providersSection");

      tabEnrollments && tabEnrollments.addEventListener("click", () => {
        enrollmentsSection.classList.remove("hidden");
        providersSection.classList.add("hidden");
        tabEnrollments.classList.add("border-b-2", "border-blue-600", "text-blue-600");
        tabProviders.classList.remove("border-b-2", "border-blue-600", "text-blue-600");
      });

      tabProviders && tabProviders.addEventListener("click", () => {
        providersSection.classList.remove("hidden");
        enrollmentsSection.classList.add("hidden");
        tabProviders.classList.add("border-b-2", "border-blue-600", "text-blue-600");
        tabEnrollments.classList.remove("border-b-2", "border-blue-600", "text-blue-600");
      });

      // Modals open/close helpers
      window.openModal = function(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove("hidden");
      };
      window.closeModal = function(id) {
        const el = document.getElementById(id);
        if (el) el.classList.add("hidden");
      };

      const btnReport = document.getElementById("btnReport");
      const btnAddProvider = document.getElementById("btnAddProvider");
      btnReport && btnReport.addEventListener("click", () => openModal("modalReport"));
      btnAddProvider && btnAddProvider.addEventListener("click", () => openModal("modalAddProvider"));

      window.openManageModal = function(name) {
        document.getElementById("manageEmployeeName").textContent = "You are managing: " + name;
        openModal("modalManage");
      };

      // View Provider
      window.openViewProvider = function(index) {
        const provider = providersData[index];
        const detailsDiv = document.getElementById("providerDetails");
        if (!provider || !detailsDiv) return;
        detailsDiv.innerHTML = `
          <p><strong>Name:</strong> ${escapeHtml(provider.name)}</p>
          <p><strong>Coverage:</strong> ${escapeHtml(provider.coverage)}</p>
          <p><strong>Monthly Premium:</strong> ${escapeHtml(provider.premium)}</p>
          <p><strong>Max Benefit:</strong> ${escapeHtml(provider.benefit)}</p>
          <p><strong>Network:</strong> ${escapeHtml(provider.network)}</p>
        `;
        openModal("modalViewProvider");
      };

      // Edit Provider
      const editForm = document.getElementById("editProviderForm");
      window.openEditProvider = function(index) {
        const provider = providersData[index];
        if (!provider || !editForm) return;
        document.getElementById("editProviderName").value = provider.name || "";
        document.getElementById("editProviderCoverage").value = provider.coverage || "";
        document.getElementById("editProviderPremium").value = provider.premium || "";
        document.getElementById("editProviderBenefit").value = provider.benefit || "";
        document.getElementById("editProviderNetwork").value = provider.network || "";
        editForm.dataset.index = index;
        openModal("modalEditProvider");
      };

      // Save edited provider (client-side)
      editForm && editForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const idx = parseInt(editForm.dataset.index);
        if (Number.isNaN(idx)) return;
        const updated = {
          name: document.getElementById("editProviderName").value.trim(),
          coverage: document.getElementById("editProviderCoverage").value.trim(),
          premium: document.getElementById("editProviderPremium").value.trim(),
          benefit: document.getElementById("editProviderBenefit").value.trim(),
          network: document.getElementById("editProviderNetwork").value.trim()
        };
        providersData[idx] = updated;
        updateProviderCard(idx);
        closeModal("modalEditProvider");
      });

      // Add provider (client-side)
      const addForm = document.getElementById("addProviderForm");
      addForm && addForm.addEventListener("submit", function(e) {
        e.preventDefault();
        const newProv = {
          name: document.getElementById("addProviderName").value.trim(),
          coverage: document.getElementById("addProviderCoverage").value.trim(),
          premium: document.getElementById("addProviderPremium").value.trim(),
          benefit: document.getElementById("addProviderBenefit").value.trim(),
          network: document.getElementById("addProviderNetwork").value.trim()
        };
        if (!newProv.name) {
          alert("Provider name is required.");
          return;
        }
        providersData.push(newProv);
        const newIndex = providersData.length - 1;
        appendProviderCard(newIndex, newProv);
        // Reset and close
        addForm.reset();
        closeModal("modalAddProvider");
      });

      // Helper: update provider card UI
      function updateProviderCard(index) {
        const prov = providersData[index];
        const card = document.getElementById("providerCard" + index);
        if (!card) return;
        card.querySelector('h2').textContent = prov.name;
        const coverageEl = card.querySelector('.prov-coverage');
        const premiumEl = card.querySelector('.prov-premium');
        const benefitEl = card.querySelector('.prov-benefit');
        const networkEl = card.querySelector('.prov-network');
        coverageEl && (coverageEl.textContent = prov.coverage + " Coverage");
        premiumEl && (premiumEl.innerHTML = 'Monthly Premium: <span class="font-bold">' + prov.premium + '</span>');
        benefitEl && (benefitEl.innerHTML = 'Max Benefit: <span class="font-bold">' + prov.benefit + '</span>');
        networkEl && (networkEl.textContent = 'Network: ' + prov.network);
      }

      // Helper: append a new provider card to DOM
      function appendProviderCard(index, prov) {
        const container = document.getElementById("providersSection");
        if (!container) return;
        const card = document.createElement("div");
        card.id = "providerCard" + index;
        card.className = "bg-white shadow rounded-xl p-6";
        card.innerHTML = `
          <h2 class="font-bold text-gray-800">${escapeHtml(prov.name)}</h2>
          <p class="text-sm text-gray-500 prov-coverage">${escapeHtml(prov.coverage)} Coverage</p>
          <p class="mt-2 text-sm prov-premium">Monthly Premium: <span class="font-bold">${escapeHtml(prov.premium)}</span></p>
          <p class="text-sm prov-benefit">Max Benefit: <span class="font-bold">${escapeHtml(prov.benefit)}</span></p>
          <p class="text-sm prov-network">Network: ${escapeHtml(prov.network)}</p>
          <div class="flex gap-2 mt-4">
            <button onclick="openViewProvider(${index})" class="px-3 py-1 text-sm bg-gray-200 hover:bg-gray-300 rounded">View Details</button>
            <button onclick="openEditProvider(${index})" class="px-3 py-1 text-sm bg-blue-100 text-blue-700 hover:bg-blue-200 rounded">Edit</button>
          </div>
        `;
        container.appendChild(card);
      }

      // Small helper to safely escape text for innerHTML contexts
      function escapeHtml(str) {
        if (typeof str !== 'string') return str;
        return str.replace(/[&<>"']/g, function(m) {
          return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
          }[m];
        });
      }

    }); // DOMContentLoaded end



// Chatbot logic
const chatMessages = document.getElementById('chatMessages');
const chatInput = document.getElementById('chatInput');
const sendChatBtn = document.getElementById('sendChatBtn');

sendChatBtn && sendChatBtn.addEventListener('click', async () => {
  const userMsg = chatInput.value.trim();
  if (!userMsg) return;
  appendChat('You', userMsg);
  chatInput.value = '';

  // Send message to PHP backend (AJAX)
  const res = await fetch('ai_chatbot.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({message: userMsg})
  });
  const data = await res.json();
  if (data.reply) appendChat('AI', data.reply);
});

function appendChat(sender, text) {
  const msgEl = document.createElement('div');
  msgEl.className = sender === 'AI' ? 'text-gray-800 mb-2' : 'text-blue-700 mb-2';
  msgEl.innerHTML = `<strong>${sender}:</strong> ${text}`;
  chatMessages.appendChild(msgEl);
  chatMessages.scrollTop = chatMessages.scrollHeight;
}




  </script>






</body>
</html>
