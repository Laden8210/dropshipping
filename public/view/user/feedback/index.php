<?php
// User Feedback View Page - View Only (No Submission)
// Feedback is submitted via mobile API
?>

<div class="main-container">
    <div class="header-section text-center">
        <h2 class="mb-3">Product Feedback</h2>
        <p class="lead">View product ratings and reviews</p>
    </div>

    <div class="main-content">
        <!-- Feedback Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-primary" id="totalFeedback">0</h4>
                        <p class="text-muted">Total Feedback</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-warning" id="averageRating">0.0</h4>
                        <p class="text-muted">Average Rating</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <h4 class="text-info" id="totalProducts">0</h4>
                        <p class="text-muted">Products</p>
                    </div>
                </div>
            </div>
       </div>

        <!-- Product Filter -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filter Feedback</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <label for="productFilter" class="form-label">Product</label>
                        <select class="form-select" id="productFilter" onchange="filterFeedback()">
                            <option value="">All Products</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="ratingFilter" class="form-label">Rating</label>
                        <select class="form-select" id="ratingFilter" onchange="filterFeedback()">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
        
                </div>
            </div>
        </div>

        <!-- Feedback Table -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Product Feedback</h5>
                <button class="btn btn-sm btn-outline-primary" onclick="loadFeedbackData()">
                    <i class="fas fa-refresh"></i> Refresh
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product</th>
                                <th>Rating</th>
                                <th>Review</th>
                          
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="feedbackTableBody">
                            <tr>
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading feedback data...</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
   </div>

   <script>
let allFeedbackData = [];
let filteredFeedbackData = [];

document.addEventListener('DOMContentLoaded', function() {
    loadFeedbackData();
});

function loadFeedbackData() {
    new GetRequest({
        getUrl: 'controller/user/feedback/get-all-feedback.php',
        params: {},
        showLoading: false,
        showSuccess: false,
        callback: (err, data) => {
       
            if (err) {
                console.error('Error loading feedback data:', err);
                document.getElementById('feedbackTableBody').innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center text-danger">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                            <p>Failed to load feedback data</p>
                        </td>
                    </tr>
                `;
            } else {
                allFeedbackData = data;
                filteredFeedbackData = data;
                updateSummaryCards(data);
                populateProductFilter(data);
                displayFeedbackTable(data);
            }
        }
    }).send();
}

function updateSummaryCards(data) {
    console.log(data);
    const totalFeedback = data.length;
    console.log( "totalFeedback", totalFeedback);
    
    // Calculate average rating
    const ratings = data.filter(item => item.rating).map(item => parseInt(item.rating));
    console.log( "ratings", ratings);
    const averageRating = ratings.length > 0 ? (ratings.reduce((sum, rating) => sum + rating, 0) / ratings.length).toFixed(1) : '0.0';
    console.log( "averageRating", averageRating);
    
    // Count unique stores and products


    const uniqueProducts = [...new Set(data.map(item => item.product_id))].length;
    
    document.getElementById('totalFeedback').textContent = totalFeedback;
    document.getElementById('averageRating').textContent = averageRating;

    document.getElementById('totalProducts').textContent = uniqueProducts;
}

function populateProductFilter(data) {
    const productFilter = document.getElementById('productFilter');
    console.log( "data", data);
    const uniqueProducts = [...new Set(data.map(item => item.product_name))];
    console.log( "uniqueProducts", uniqueProducts);
    productFilter.innerHTML = '<option value="">All Products</option>';
    uniqueProducts.forEach(productName => {
        const option = document.createElement('option');
        option.value = productName;
        option.textContent = productName;
        productFilter.appendChild(option);
    });
    
    // Populate store filter

    const uniqueStores = [...new Set(data.map(item => item.store_id))];
    

}

function filterFeedback() {
    const productFilter = document.getElementById('productFilter').value;
    const ratingFilter = document.getElementById('ratingFilter').value;

    
    filteredFeedbackData = allFeedbackData.filter(item => {
        const matchesProduct = !productFilter || item.product_name === productFilter;
        const matchesRating = !ratingFilter || item.rating == ratingFilter;

        
        return matchesProduct && matchesRating;
    });
    
    displayFeedbackTable(filteredFeedbackData);
}

function displayFeedbackTable(data) {
    const tbody = document.getElementById('feedbackTableBody');
    
    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">
                    <i class="fas fa-comment-slash fa-3x mb-3"></i>
                    <p>No feedback found matching your criteria</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    data.forEach(item => {
        const ratingStars = generateStars(item.rating);
        
        html += `
            <tr>
                <td>
                    <strong>${item.product_name}</strong>
                    <br>
                    <small class="text-muted">${item.category_name || 'Uncategorized'}</small>
                </td>
                <td>
                    <div class="rating-display">
                        ${ratingStars}
                        <br>
                        <small class="text-muted">${item.rating}/5</small>
                    </div>
                </td>
                <td>
                    <div class="review-text">
                        ${item.review}
                    </div>
                </td>
      
                <td>
                    <small>${new Date(item.created_at).toLocaleDateString()}</small>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<span class="star-filled">★</span>';
        } else {
            stars += '<span class="star-empty">☆</span>';
        }
    }
    return stars;
}

</script>

<style>
.card {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.table th {
    background-color: #343a40;
    color: white;
    border: none;
    font-weight: 600;
}

.table td {
    vertical-align: middle;
}

.btn {
    border-radius: 6px;
}

.form-control, .form-select {
    border-radius: 6px;
}

.star-filled {
    color: #ffc107;
    font-size: 1.2rem;
}

.star-empty {
    color: #ddd;
    font-size: 1.2rem;
}

.rating-display {
    text-align: center;
}

.review-text {
    max-width: 300px;
    word-wrap: break-word;
}

.badge {
    font-size: 0.75em;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>