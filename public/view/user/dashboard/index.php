   <div class="main-container" id="main-container">
       <div class="header-section text-center">

           <p class="lead">Manage your report</p>
       </div>


       <div class="main-content">


           <!-- Stats Cards -->
           <div class="stats-container">
               <div class="stat-card">
                   <div class="stat-header">
                       <div class="stat-title">Total Revenue</div>
                       <div class="stat-icon" style="background-color: rgba(67, 97, 238, 0.1); color: var(--primary);">
                           <i class="fas fa-dollar-sign"></i>
                       </div>
                   </div>
                   <div class="stat-value">$24,568</div>
                   <div class="stat-change change-up">
                       <i class="fas fa-arrow-up"></i>
                       12.5% from last month
                   </div>
               </div>

               <div class="stat-card">
                   <div class="stat-header">
                       <div class="stat-title">Orders</div>
                       <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1); color: var(--success);">
                           <i class="fas fa-shopping-bag"></i>
                       </div>
                   </div>
                   <div class="stat-value">1,248</div>
                   <div class="stat-change change-up">
                       <i class="fas fa-arrow-up"></i>
                       8.3% from last month
                   </div>
               </div>

               <div class="stat-card">
                   <div class="stat-header">
                       <div class="stat-title">Customers</div>
                       <div class="stat-icon" style="background-color: rgba(76, 201, 240, 0.1); color: var(--accent);">
                           <i class="fas fa-users"></i>
                       </div>
                   </div>
                   <div class="stat-value">3,842</div>
                   <div class="stat-change change-up">
                       <i class="fas fa-arrow-up"></i>
                       5.2% from last month
                   </div>
               </div>

               <div class="stat-card">
                   <div class="stat-header">
                       <div class="stat-title">Conversion Rate</div>
                       <div class="stat-icon" style="background-color: rgba(243, 156, 18, 0.1); color: var(--warning);">
                           <i class="fas fa-percentage"></i>
                       </div>
                   </div>
                   <div class="stat-value">4.8%</div>
                   <div class="stat-change change-down">
                       <i class="fas fa-arrow-down"></i>
                       1.2% from last month
                   </div>
               </div>
           </div>

           <!-- Charts Section -->
           <div class="charts-row">
               <div class="chart-container" id="sales-performance">
                   <div class="chart-header">
                       <h3>Sales Performance</h3>
                       <div class="chart-actions">
                           <button><i class="fas fa-download" onclick="saveDashboardAsPDF('sales-performance')"></i></button>
                           <button><i class="fas fa-expand"></i></button>
                       </div>
                   </div>
                   <div class="chart-wrapper">
                       <canvas id="salesChart"></canvas>
                   </div>
               </div>

               <div class="chart-container" id="revenue-by-category">
                   <div class="chart-header">
                       <h3>Revenue by Category</h3>
                       <div class="chart-actions">
                           <button><i class="fas fa-download" onclick="saveDashboardAsPDF('revenue-by-category')"></i></button>
                       </div>
                   </div>
                   <div class="chart-wrapper">
                       <canvas id="revenueChart"></canvas>
                   </div>
               </div>
           </div>

           <!-- Report Tables -->
           <div class="tables-row">
               <div class="table-container" id="top-products">
                   <div class="table-header">
                       <h3>Top Performing Products</h3>
                       <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('top-products')">
                           <i class="fas fa-download"></i> Export
                       </button>
                   </div>
                   <div class="table-wrapper">
                       <table>
                           <thead>
                               <tr>
                                   <th>Product</th>
                                   <th>Category</th>
                                   <th>Sales</th>
                                   <th>Revenue</th>
                                   <th>Stock</th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr>
                                   <td>Wireless Headphones Pro</td>
                                   <td>Electronics</td>
                                   <td>$248,000</td>
                                   <td>$12,450</td>
                                   <td>42</td>
                               </tr>
                               <tr>
                                   <td>Smartwatch Series 5</td>
                                   <td>Electronics</td>
                                   <td>$198,000</td>
                                   <td>$9,800</td>
                                   <td>35</td>
                               </tr>
                               <tr>
                                   <td>Organic Cotton T-Shirt</td>
                                   <td>Clothing</td>
                                   <td>$150,000</td>
                                   <td>$7,500</td>
                                   <td>120</td>
                               </tr>
                               <tr>
                                   <td>Stainless Steel Cookware Set</td>
                                   <td>Home & Kitchen</td>
                                   <td>$120,000</td>
                                   <td>$6,000</td>
                                   <td>60</td>
                               </tr>

                           </tbody>
                       </table>
                   </div>
               </div>

               <div class="table-container" id="recent-orders">
                   <div class="table-header">
                       <h3>Recent Orders</h3>
                       <button class="export-btn" style="padding: 8px 15px; font-size: 14px;" onclick="saveDashboardAsPDF('recent-orders')">
                           <i class="fas fa-download"></i> Export
                       </button>
                   </div>
                   <div class="table-wrapper">
                       <table>
                           <thead>
                               <tr>
                                   <th>Order ID</th>
                                   <th>Customer</th>
                                   <th>Date</th>
                                   <th>Amount</th>
                                   <th>Status</th>
                               </tr>
                           </thead>
                           <tbody>
                               <tr>
                                   <td>#ORD-2023-00142</td>
                                   <td>John Smith</td>
                                   <td>Oct 12, 2023</td>
                                   <td>$248.95</td>
                                   <td><span class="status status-completed">Completed</span></td>
                               </tr>
                               <tr>
                                   <td>#ORD-2023-00141</td>
                                   <td>Mary Johnson</td>
                                   <td>Oct 11, 2023</td>
                                   <td>$129.99</td>
                                   <td><span class="status status-completed">Completed</span></td>
                               </tr>
                               <tr>
                                   <td>#ORD-2023-00140</td>
                                   <td>Robert Williams</td>
                                   <td>Oct 10, 2023</td>
                                   <td>$89.95</td>
                                   <td><span class="status status-pending">Processing</span></td>
                               </tr>
                               <tr>
                                   <td>#ORD-2023-00139</td>
                                   <td>Emily Davis</td>
                                   <td>Oct 9, 2023</td>
                                   <td>$421.50</td>
                                   <td><span class="status status-completed">Completed</span></td>
                               </tr>
                               <tr>
                                   <td>#ORD-2023-00138</td>
                                   <td>Thomas Brown</td>
                                   <td>Oct 8, 2023</td>
                                   <td>$75.25</td>
                                   <td><span class="status status-pending">Processing</span></td>
                               </tr>
                           </tbody>
                       </table>
                   </div>
               </div>
           </div>
       </div>
   </div>
   </div>

   <script>
       // Initialize charts
       document.addEventListener('DOMContentLoaded', function() {
           // Sales Performance Chart
           const salesCtx = document.getElementById('salesChart').getContext('2d');
           const salesChart = new Chart(salesCtx, {
               type: 'line',
               data: {
                   labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                   datasets: [{
                       label: 'Revenue',
                       data: [12000, 19000, 15000, 18000, 22000, 21000, 24000, 23000, 25000, 24568],
                       borderColor: '#4361ee',
                       backgroundColor: 'rgba(67, 97, 238, 0.1)',
                       tension: 0.4,
                       fill: true
                   }, {
                       label: 'Orders',
                       data: [800, 950, 920, 1050, 1100, 1150, 1200, 1180, 1240, 1248],
                       borderColor: '#4cc9f0',
                       backgroundColor: 'rgba(76, 201, 240, 0.1)',
                       tension: 0.4,
                       fill: true
                   }]
               },
               options: {
                   responsive: true,
                   maintainAspectRatio: false,
                   plugins: {
                       legend: {
                           position: 'top',
                       }
                   },
                   scales: {
                       y: {
                           beginAtZero: true,
                           grid: {
                               drawBorder: false
                           }
                       },
                       x: {
                           grid: {
                               display: false
                           }
                       }
                   }
               }
           });

           // Revenue by Category Chart
           const revenueCtx = document.getElementById('revenueChart').getContext('2d');
           const revenueChart = new Chart(revenueCtx, {
               type: 'doughnut',
               data: {
                   labels: ['Electronics', 'Home & Kitchen', 'Clothing', 'Sports', 'Beauty'],
                   datasets: [{
                       data: [35, 20, 15, 18, 12],
                       backgroundColor: [
                           '#4361ee',
                           '#4cc9f0',
                           '#2ecc71',
                           '#f39c12',
                           '#7209b7'
                       ],
                       borderWidth: 0
                   }]
               },
               options: {
                   responsive: true,
                   maintainAspectRatio: false,
                   plugins: {
                       legend: {
                           position: 'right',
                       }
                   },
                   cutout: '70%'
               }
           });


           document.getElementById('date-range').addEventListener('change', function() {

               alert(`Loading data for: ${this.value}`);
           });
       });
   </script>



   <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
   <script>
       function saveDashboardAsPDF(target) {
           const card = document.getElementById(target);

           const button = card.querySelector('button');
           button.style.display = 'none';


           html2canvas(card).then(canvas => {
               const imgData = canvas.toDataURL('image/png');
               const pdf = new window.jspdf.jsPDF({
                   orientation: 'landscape',
                   unit: 'pt',
                   format: [canvas.width, canvas.height]
               });
               pdf.addImage(imgData, 'PNG', 0, 0, canvas.width, canvas.height);
               pdf.save(target + '.pdf');
           });


           button.style.display = 'block';
       }
   </script>