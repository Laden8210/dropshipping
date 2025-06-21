   <div class="main-container" id="main-container">
       <div class="header-section">

           <p class="lead text-center">Review customer feedback, analyze sentiment, and respond to improve customer satisfaction</p>
       </div>

    
   </div>

   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script>
       // Tab switching functionality
       document.querySelectorAll('.tab').forEach(tab => {
           tab.addEventListener('click', function() {
               // Remove active class from all tabs
               document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
               // Add active class to clicked tab
               this.classList.add('active');

               // Hide all tab content
               document.querySelectorAll('.tab-content').forEach(content => {
                   content.classList.remove('active');
               });

               // Show corresponding tab content
               const tabId = this.getAttribute('data-tab');
               document.getElementById(`${tabId}Tab`).classList.add('active');
           });
       });

       // Feedback item selection
       document.querySelectorAll('.feedback-item').forEach(item => {
           item.addEventListener('click', function() {
               document.querySelectorAll('.feedback-item').forEach(i => i.classList.remove('active'));
               this.classList.add('active');
           });
       });

       // Initialize sentiment chart
       document.addEventListener('DOMContentLoaded', function() {
           const ctx = document.getElementById('sentimentChart').getContext('2d');
           const sentimentChart = new Chart(ctx, {
               type: 'line',
               data: {
                   labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
                   datasets: [{
                       label: 'Positive',
                       data: [65, 70, 75, 72, 78, 80, 82, 81, 83, 85],
                       borderColor: '#2ecc71',
                       backgroundColor: 'rgba(46, 204, 113, 0.1)',
                       tension: 0.4,
                       fill: true
                   }, {
                       label: 'Negative',
                       data: [15, 12, 10, 14, 11, 9, 8, 7, 8, 5],
                       borderColor: '#e74c3c',
                       backgroundColor: 'rgba(231, 76, 60, 0.1)',
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
                           max: 100,
                           grid: {
                               drawBorder: false
                           },
                           ticks: {
                               callback: function(value) {
                                   return value + '%';
                               }
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
       });
   </script>