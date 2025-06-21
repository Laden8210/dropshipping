<div class="main-container" id="main-container">
    <div class="header-section text-center mb-4">
        <p class="lead">Manage your product categories. Add, update, or remove categories as needed.</p>
    </div>

    <!-- Add Category Button -->
    <div class="mb-3 text-end">
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="fas fa-plus me-1"></i>Add Category
        </button>
    </div>

    <!-- Category Table -->
    <div class="card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Category List</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover inventory-table">
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="category-table-body">
                        <!-- Categories will be dynamically inserted here -->
                        <tr>
                            <td colspan="3" class="text-center">Loading categories...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="add-category" action="controller/supplier/category/index.php?action=add-category" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryLabel">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="newCategoryName" class="form-label">Category Name</label>
                    <input type="text" class="form-control" id="newCategoryName" name="category_name" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="submit-btn">Add</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="add-category" action="controller/supplier/category/index.php?action=edit-category" method="POST">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label for="editCategoryName" class="form-label">New Category Name</label>
                    <input type="text" class="form-control" id="editCategoryName" name="category_name" required>
                    <input type="hidden" id="originalCategoryName">
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-warning">Update</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>


<script>
    const createExamRequest = new CreateRequest({
        formSelector: '#add-category',
        submitButtonSelector: '#submit-btn',
        callback: (err, res) => err ? console.error("Form submission error:", err) : console.log("Form submitted successfully:", res),
        redirectUrl: 'category',
    });


    const getRequest = new GetAllRequest({
        getUrl: 'controller/supplier/category/index.php?action=get-categories',
        params: {},
        callback: (error, data) => {
            if (error) {
                console.log('Error:', error);
                const tableBody = document.getElementById('category-table-body');
                tableBody.innerHTML = '<tr><td colspan="3" class="text-center">'+ error+'</td></tr>';
            } else {
                console.log('Categories fetched successfully:', data);
                const tableBody = document.getElementById('category-table-body');
                tableBody.innerHTML = '';
                if (data.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="3" class="text-center">No categories found.</td></tr>';
                    return;
                }

                data.forEach(category => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${category.category_name}</td>
                        <td>${new Date(category.created_at).toLocaleDateString()}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="openEditModal('${category.category_name}')">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteCategory('${category.category_id}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });
            }
        },
        promptMessage: 'Do you want to fetch the latest data?'
    });
    getRequest.send();

    window.deleteCategory = categoryId => {
        new DeleteRequest({
            deleteUrl: 'controller/supplier/category/index.php?action=delete-category',
            data: {
                category_id: categoryId
            },
            promptMessage: 'Are you sure you want to delete this category?',
            callback: (err, data) => {
                if (err) return console.error("Deletion error:", err);
                console.log("Item deleted successfully:", res);
            }
        }).send();
    };
</script>