<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Invoice Receipt of Property</title>
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          university: {
            red: '#DC143C',
            darkred: '#B01030',
            light: '#FFF5F7',
            gold: '#FFD700',
            navy: '#0A2463',
          },
          invoice: {
            light: '#F9FAFB',
            border: '#E5E7EB',
            header: '#F3F4F6',
            input: 'rgba(239, 246, 255, 0.6)', // Light blue background for inputs
            focus: '#DBEAFE', // Slightly darker blue for focus
            required: 'rgba(254, 242, 242, 0.6)', // Light red for required fields
          }
        },
        fontFamily: {
          sans: ['Poppins', 'Segoe UI', 'sans-serif'],
          serif: ['Playfair Display', 'Georgia', 'serif'],
        },
        boxShadow: {
          'invoice': '0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.02)',
          'input': '0 2px 3px rgba(0, 0, 0, 0.05)',
        }
      }
    }
  }
</script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap');
  
  body {
    background-image: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
    background-attachment: fixed;
  }
  
  /* Animated dotted border for inputs */
  @keyframes borderDance {
    0% { background-position: 0% 0%; }
    100% { background-position: 100% 0%; }
  }
  
  .input-field {
    background-color: rgba(239, 246, 255, 0.6);
    border: 1px solid #E5E7EB;
    border-radius: 4px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 3px rgba(0, 0, 0, 0.05);
  }
  
  .input-field:hover {
    background-color: rgba(219, 234, 254, 0.7);
    border-color: #93C5FD;
    transform: translateY(-1px);
  }
  
  .input-field:focus {
    background-color: #FFFFFF;
    border-color: #3B82F6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    outline: none;
    transform: translateY(-1px);
  }
  
  .required-field {
    background-color: rgba(254, 242, 242, 0.6);
    border: 1px solid #FCA5A5;
  }
  
  .required-field:hover {
    background-color: rgba(254, 226, 226, 0.7);
    border-color: #F87171;
  }
  
  .required-field:focus {
    background-color: #FFFFFF;
    border-color: #EF4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
  }
  
  .signature-line {
    background-image: linear-gradient(to right, #CBD5E1 50%, transparent 50%);
    background-size: 8px 1px;
    background-repeat: repeat-x;
    background-position: bottom;
    animation: borderDance 30s linear infinite;
  }
  
  .university-seal {
    position: relative;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: radial-gradient(circle, #FFF5F7 0%, #FFE8EC 100%);
    border: 2px solid #DC143C;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  }
  
  .university-seal::before {
    content: '';
    position: absolute;
    width: 70px;
    height: 70px;
    border-radius: 50%;
    border: 1px solid rgba(220, 20, 60, 0.3);
  }
  
  .university-seal::after {
    content: '';
    position: absolute;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 1px solid rgba(220, 20, 60, 0.2);
  }
  
  .decorative-corner {
    position: absolute;
    width: 100px;
    height: 100px;
    opacity: 0.1;
  }
  
  .top-left {
    top: 0;
    left: 0;
    border-top: 3px solid #DC143C;
    border-left: 3px solid #DC143C;
    border-top-left-radius: 8px;
  }
  
  .top-right {
    top: 0;
    right: 0;
    border-top: 3px solid #DC143C;
    border-right: 3px solid #DC143C;
    border-top-right-radius: 8px;
  }
  
  .bottom-left {
    bottom: 0;
    left: 0;
    border-bottom: 3px solid #DC143C;
    border-left: 3px solid #DC143C;
    border-bottom-left-radius: 8px;
  }
  
  .bottom-right {
    bottom: 0;
    right: 0;
    border-bottom: 3px solid #DC143C;
    border-right: 3px solid #DC143C;
    border-bottom-right-radius: 8px;
  }
  
  .watermark {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) rotate(-45deg);
    font-size: 150px;
    color: rgba(220, 20, 60, 0.03);
    font-weight: bold;
    pointer-events: none;
    white-space: nowrap;
    z-index: 0;
  }
  
  /* Add row button animation */
  @keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
  }
  
  .add-row-btn:hover {
    animation: pulse 1s infinite;
  }
  
  @media print {
    body {
      print-color-adjust: exact;
      -webkit-print-color-adjust: exact;
      background: none;
    }
    .no-print {
      display: none;
    }
    .container {
      box-shadow: none !important;
      max-width: 100% !important;
    }
    .input-field, .required-field {
      border: 1px dashed #CBD5E1 !important;
      background-color: transparent !important;
    }
    .input-field:focus, .required-field:focus {
      outline: none !important;
      box-shadow: none !important;
    }
    .watermark {
      display: none;
    }
  }
</style>
</head>
<body class="py-8 px-4 sm:px-6 md:px-8 min-h-screen">
<!-- Instructions Banner -->
<div class="max-w-5xl mx-auto mb-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-500 p-4 rounded-md shadow-md no-print">
  <div class="flex">
    <div class="flex-shrink-0">
      <svg class="h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
      </svg>
    </div>
    <div class="ml-3">
      <h3 class="text-sm font-medium text-blue-800">Filling Instructions</h3>
      <div class="mt-1 text-sm text-blue-700">
        <p>Click on highlighted areas to fill in the required information. <span class="bg-invoice-input px-2 py-0.5 rounded border border-blue-200">Blue fields</span> are optional and <span class="bg-invoice-required px-2 py-0.5 rounded border border-red-200">red fields</span> are required. Click "Add Row" to add more items.</p>
      </div>
    </div>
  </div>
</div>

<!-- Print Button -->
<div class="max-w-5xl mx-auto mb-4 flex justify-end no-print">
  <button onclick="window.print()" class="bg-gradient-to-r from-university-red to-university-darkred text-white px-5 py-2.5 rounded-md shadow-md transition-all duration-300 hover:shadow-lg transform hover:-translate-y-0.5 flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
    </svg>
    Print Invoice
  </button>
</div>

<!-- Main Container -->
<div class="container max-w-5xl mx-auto bg-white rounded-xl shadow-invoice overflow-hidden relative">
  <!-- Decorative Corners -->
  <div class="decorative-corner top-left"></div>
  <div class="decorative-corner top-right"></div>
  <div class="decorative-corner bottom-left"></div>
  <div class="decorative-corner bottom-right"></div>
  
  <!-- Watermark -->
  <div class="watermark">WMSU</div>
  
  <!-- Header Section -->
  <div class="bg-gradient-to-r from-university-light via-white to-university-light border-b border-invoice-border relative">
    <div class="py-8 px-8">
      <div class="flex flex-col items-center justify-center">
        <div class="flex items-center mb-4">
          <div class="university-seal mr-4">
            <span class="text-university-red font-serif font-bold text-xl">WMSU</span>
          </div>
          <div class="text-center">
            <h1 class="text-2xl md:text-3xl font-bold text-university-red tracking-tight font-serif">WESTERN MINDANAO STATE UNIVERSITY</h1>
            <div class="h-0.5 bg-gradient-to-r from-transparent via-university-red to-transparent mt-1 mb-2"></div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-700 mt-2">Property Management Office</h2>
            <h3 class="text-gray-600 font-medium">Zamboanga City</h3>
          </div>
        </div>
        <div class="mt-4 bg-gradient-to-r from-university-red/10 via-university-red/20 to-university-red/10 px-8 py-3 rounded-full border border-university-red/20 shadow-sm">
          <h2 class="text-xl font-bold text-gray-800 font-serif tracking-wide">INVOICE RECEIPT OF PROPERTY</h2>
        </div>
      </div>
    </div>
    <!-- Decorative Wave -->
    <div class="absolute bottom-0 left-0 right-0 h-2 bg-gradient-to-r from-university-red/0 via-university-red/30 to-university-red/0"></div>
  </div>

  <!-- Document Body -->
  <div class="p-6 md:p-8 relative z-10">
    <!-- Date Section -->
    <div class="flex justify-end mb-6">
      <div class="flex items-center">
        <span class="font-semibold text-gray-700 mr-2">Date:</span>
        <input type="text" placeholder="April 2, 2025" class="input-field required-field px-3 py-2 w-48 text-gray-700" />
        <div class="ml-1 text-university-red text-lg">*</div>
      </div>
    </div>

    <!-- Custodian Information -->
    <div class="mb-8 bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-100">
      <div class="flex flex-wrap items-center text-gray-700">
        <span class="font-semibold mr-2">From:</span>
        <input type="text" placeholder="Name of Previous Custodian" class="input-field required-field px-3 py-2 w-64 mr-2 text-gray-700" />
        <div class="mr-2 text-university-red text-lg">*</div>
        <span class="mx-2 font-medium">to</span>
        <input type="text" placeholder="Name of New Custodian" class="input-field required-field px-3 py-2 w-64 text-gray-700" />
        <div class="ml-1 text-university-red text-lg">*</div>
      </div>
    </div>

    <!-- Items Table -->
    <div class="overflow-x-auto mb-4">
      <table class="w-full border-collapse">
        <thead>
          <tr class="bg-gradient-to-r from-gray-100 to-gray-50">
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700 w-16">QTY</th>
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700">ARTICLE</th>
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700">DESCRIPTION</th>
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700">PROPERTY NUMBER</th>
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700">ACCOUNT CODE</th>
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700">UNIT VALUE</th>
            <th class="border border-invoice-border px-4 py-3 text-left text-sm font-semibold text-gray-700">TOTAL VALUE</th>
          </tr>
        </thead>
        <tbody id="invoice-items">
          <tr class="hover:bg-gray-50 transition-colors duration-150">
            <td class="border border-invoice-border px-4 py-3">
              <input type="number" id="qty-1" value="1" min="1" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" onchange="calculateRowTotal(1)" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Article name" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Detailed description here..." class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Property No." class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Account Code" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="number" id="unit-value-1" value="0.00" step="0.01" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm text-right" onchange="calculateRowTotal(1)" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="number" id="total-value-1" value="0.00" class="input-field required-field w-full px-2 py-1 text-gray-700 text-sm text-right bg-gray-50" readonly />
            </td>
          </tr>
          <tr class="hover:bg-gray-50 transition-colors duration-150">
            <td class="border border-invoice-border px-4 py-3">
              <input type="number" id="qty-2" value="1" min="1" class="input-field w-full px-2 py-1 text-gray-700 text-sm" onchange="calculateRowTotal(2)" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Article name" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Detailed description here..." class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Property No." class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" placeholder="Account Code" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="number" id="unit-value-2" value="0.00" step="0.01" class="input-field w-full px-2 py-1 text-gray-700 text-sm text-right" onchange="calculateRowTotal(2)" />
            </td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="number" id="total-value-2" value="0.00" class="input-field w-full px-2 py-1 text-gray-700 text-sm text-right bg-gray-50" readonly />
            </td>
          </tr>
        </tbody>
        <tfoot>
          <tr class="bg-gradient-to-r from-gray-100 to-gray-50">
            <td colspan="5" class="border border-invoice-border px-4 py-3"></td>
            <td class="border border-invoice-border px-4 py-3 font-semibold text-gray-700 text-sm text-right">TOTAL</td>
            <td class="border border-invoice-border px-4 py-3">
              <input type="text" id="grand-total" value="0.00" class="input-field required-field w-full px-2 py-1 text-gray-700 font-semibold text-sm text-right bg-gray-50" readonly />
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
    
    <!-- Add Row Button -->
    <div class="flex justify-center mb-8 no-print">
      <button id="add-row-btn" class="add-row-btn bg-gradient-to-r from-blue-500 to-blue-600 text-white px-4 py-2 rounded-md shadow-sm transition-all duration-300 hover:shadow-md flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Add Row
      </button>
    </div>

    <!-- Certification Section -->
    <div class="mb-8 bg-gray-50 p-4 rounded-lg shadow-sm border border-gray-100">
      <div class="text-gray-700">
        <span class="font-medium">I HEREBY CERTIFY that I have this</span>
        <input type="text" placeholder="2nd day of April 2025" class="mx-2 input-field required-field px-3 py-2 w-64 text-gray-700" />
        <div class="inline-block ml-1 text-university-red text-lg">*</div>
      </div>
    </div>

    <!-- Signature Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <!-- Left Signature Block -->
      <div class="border border-invoice-border rounded-lg overflow-hidden shadow-sm">
        <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-4 py-3 border-b border-invoice-border">
          <h3 class="font-semibold text-gray-700">Previous Custodian</h3>
        </div>
        <div class="p-5">
          <div class="mb-4">
            <span class="font-semibold text-gray-700">Received from:</span>
            <input type="text" placeholder="Previous Custodian Name" class="mt-1 w-full input-field required-field px-3 py-2 text-gray-700" />
            <div class="text-university-red text-lg mt-1">*</div>
          </div>
          
          <p class="text-sm text-gray-600 mb-8">The above listed articles/property</p>
          
          <div class="mt-12 pt-2 relative">
            <div class="absolute inset-x-0 bottom-0 h-0.5 signature-line"></div>
            <input type="text" placeholder="Signature of Recipient" class="w-full text-center font-semibold text-gray-700 focus:outline-none bg-transparent required-field py-1" />
            <div class="text-university-red text-lg text-center mt-1">*</div>
          </div>
          
          <p class="text-xs text-gray-500 text-center mt-1">Signature over printed name of Invoicing Accountable Officer</p>
        </div>
      </div>
      
      <!-- Right Signature Block -->
      <div class="border border-invoice-border rounded-lg overflow-hidden shadow-sm">
        <div class="bg-gradient-to-r from-gray-100 to-gray-50 px-4 py-3 border-b border-invoice-border">
          <h3 class="font-semibold text-gray-700">New Custodian</h3>
        </div>
        <div class="p-5">
          <div class="mb-4">
            <span class="font-semibold text-gray-700">Invoiced to:</span>
            <input type="text" placeholder="New Custodian Name" class="mt-1 w-full input-field required-field px-3 py-2 text-gray-  placeholder="New Custodian Name" class="mt-1 w-full input-field required-field px-3 py-2 text-gray-700" />
            <div class="text-university-red text-lg mt-1">*</div>
          </div>
          
          <p class="text-sm text-gray-600 mb-8">The above listed articles/property</p>
          
          <div class="mt-12 pt-2 relative">
            <div class="absolute inset-x-0 bottom-0 h-0.5 signature-line"></div>
            <input type="text" placeholder="Signature of Issuer" class="w-full text-center font-semibold text-gray-700 focus:outline-none bg-transparent required-field py-1" />
            <div class="text-university-red text-lg text-center mt-1">*</div>
          </div>
          
          <p class="text-xs text-gray-500 text-center mt-1">Signature over printed name of Invoicing Accountable Officer</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <div class="bg-gradient-to-r from-gray-100 to-gray-50 py-4 px-8 border-t border-invoice-border">
    <div class="flex justify-between items-center">
      <div class="flex items-center">
        <div class="w-3 h-3 rounded-full bg-university-red mr-2"></div>
        <p class="text-xs text-gray-500 font-medium">WMSU-PMO-IRP-001</p>
      </div>
      <div class="flex items-center">
        <div class="flex items-center mr-4">
          <div class="w-3 h-3 bg-invoice-input border border-blue-200 rounded-sm mr-1"></div>
          <span class="text-xs text-gray-500">Optional Field</span>
        </div>
        <div class="flex items-center">
          <div class="w-3 h-3 bg-invoice-required border border-red-200 rounded-sm mr-1"></div>
          <span class="text-xs text-gray-500">Required Field</span>
        </div>
      </div>
      <div class="flex items-center">
        <p class="text-xs text-gray-500">Page 1 of 1</p>
        <div class="w-3 h-3 rounded-full bg-university-red ml-2"></div>
      </div>
    </div>
  </div>
</div>

<script>
  let rowCount = 2;
  
  function calculateRowTotal(row) {
    const qty = parseFloat(document.getElementById(`qty-${row}`).value) || 0;
    const unitValue = parseFloat(document.getElementById(`unit-value-${row}`).value) || 0;
    const total = (qty * unitValue).toFixed(2);
    document.getElementById(`total-value-${row}`).value = total;
    calculateGrandTotal();
  }
  
  function calculateGrandTotal() {
    let grandTotal = 0;
    for (let i = 1; i <= rowCount; i++) {
      const rowTotal = parseFloat(document.getElementById(`total-value-${i}`)?.value) || 0;
      grandTotal += rowTotal;
    }
    document.getElementById("grand-total").value = grandTotal.toFixed(2);
  }
  
  document.getElementById('add-row-btn').addEventListener('click', function() {
    rowCount++;
    const newRow = document.createElement('tr');
    newRow.className = 'hover:bg-gray-50 transition-colors duration-150';
    
    newRow.innerHTML = `
      <td class="border border-invoice-border px-4 py-3">
        <input type="number" id="qty-${rowCount}" value="1" min="1" class="input-field w-full px-2 py-1 text-gray-700 text-sm" onchange="calculateRowTotal(${rowCount})" />
      </td>
      <td class="border border-invoice-border px-4 py-3">
        <input type="text" placeholder="Article name" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
      </td>
      <td class="border border-invoice-border px-4 py-3">
        <input type="text" placeholder="Detailed description here..." class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
      </td>
      <td class="border border-invoice-border px-4 py-3">
        <input type="text" placeholder="Property No." class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
      </td>
      <td class="border border-invoice-border px-4 py-3">
        <input type="text" placeholder="Account Code" class="input-field w-full px-2 py-1 text-gray-700 text-sm" />
      </td>
      <td class="border border-invoice-border px-4 py-3">
        <input type="number" id="unit-value-${rowCount}" value="0.00" step="0.01" class="input-field w-full px-2 py-1 text-gray-700 text-sm text-right" onchange="calculateRowTotal(${rowCount})" />
      </td>
      <td class="border border-invoice-border px-4 py-3">
        <input type="number" id="total-value-${rowCount}" value="0.00" class="input-field w-full px-2 py-1 text-gray-700 text-sm text-right bg-gray-50" readonly />
      </td>
    `;
    
    document.getElementById('invoice-items').appendChild(newRow);
    calculateRowTotal(rowCount);
  });
  
  // Optional: Add JavaScript to highlight unfilled required fields when printing
  document.querySelector('button[onclick="window.print()"]').addEventListener('click', function() {
    const requiredFields = document.querySelectorAll('.required-field');
    let allFilled = true;
    
    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.style.backgroundColor = '#FECACA';
        allFilled = false;
      }
    });
    
    if (!allFilled) {
      if (!confirm('Some required fields are not filled. Do you still want to print?')) {
        event.preventDefault();
        return false;
      }
    }
  });
  
  // Initialize calculations
  calculateRowTotal(1);
  calculateRowTotal(2);
</script>
</body>
</html>