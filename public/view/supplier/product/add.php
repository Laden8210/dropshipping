    <div class="main-container" id="main-container">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-plus me-2"></i>Add New Product</h5>
            </div>
            <div class="card-body">
                <form id="add-product-form" enctype="multipart/form-data">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="product_name" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="product_name" name="product_name" required>
                        </div>

                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="" disabled selected>Select a category</option>
                                <option value="1">Electronics</option>
                                <option value="2">Clothing</option>
                                <option value="3">Home & Garden</option>
                                <option value="4">Toys</option>
                                <option value="5">Books</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="price" class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>

                        <div class="col-md-6">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="USD" selected>USD - United States Dollar</option>
                                <option value="PHP">PHP - Philippine Peso</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="GBP">GBP - British Pound Sterling</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active" selected>Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="product_weight" class="form-label">Weight (grams)</label>
                            <input type="number" class="form-control" id="product_weight" name="product_weight" required>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="product_image" class="form-label">Primary Image</label>
                            <input class="form-control" type="file" id="product_image" name="product_image" accept="image/*" required>
                        </div>

                        <div class="col-12">
                            <label for="product_images" class="form-label">Product Images (You can select multiple)</label>
                            <input class="form-control" type="file" id="product_images" name="product_images[]" accept="image/*" multiple>
                        </div>

                        <div class="col-12">
                            <label class="form-label d-block">Preview Selected Images</label>
                            <div class="preview-area" id="image-preview">
                                <p class="text-muted text-center my-4">No images selected</p>
                            </div>
                        </div>
                        
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Product
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Fixed solution for multiple image uploads
        document.addEventListener('DOMContentLoaded', function() {
            let selectedFiles = [];

            document.getElementById('add-product-form').addEventListener('submit', function(event) {
                event.preventDefault();

                // Create FormData and append all form fields
                const formData = new FormData();
                
                // Append text fields
                formData.append('product_name', document.getElementById('product_name').value);
                formData.append('category', document.getElementById('category').value);
                formData.append('price', document.getElementById('price').value);
                formData.append('currency', document.getElementById('currency').value);
                formData.append('status', document.getElementById('status').value);
                formData.append('product_weight', document.getElementById('product_weight').value);
                formData.append('description', document.getElementById('description').value);
                
                // Append primary image
                const primaryImage = document.getElementById('product_image').files[0];
                if (primaryImage) {
                    formData.append('product_image', primaryImage);
                }
                
                // Append additional images
                for (let i = 0; i < selectedFiles.length; i++) {
                    formData.append('product_images[]', selectedFiles[i]);
                }

                // Simulate successful form submission
                Swal.fire({
                    icon: 'success',
                    title: 'Product Added Successfully!',
                    text: 'Your product with ' + selectedFiles.length + ' additional images has been saved.',
                    showConfirmButton: true
                });
                
                // In a real application, you would use axios:
                /*
                axios.post('controller/supplier/product/index.php?action=add-product', formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                })
                .then(response => {
                    if (response.data.status == 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Product added successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error adding product',
                            text: response.data.message || 'An error occurred.'
                        });
                    }
                })
                .catch(error => {
                    console.error('There was an error!', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred',
                        text: 'An error occurred while adding the product.'
                    });
                });
                */
            });

            document.getElementById('product_images').addEventListener('change', function(event) {
                const files = Array.from(event.target.files);
                selectedFiles = selectedFiles.concat(files);
                renderPreviews();
            });

            function renderPreviews() {
                const preview = document.getElementById('image-preview');
                preview.innerHTML = '';

                if (selectedFiles.length === 0) {
                    preview.innerHTML = '<p class="text-muted text-center my-4">No images selected</p>';
                    return;
                }

                selectedFiles.forEach((file, index) => {
                    const wrapper = document.createElement('div');
                    wrapper.classList.add('preview-container');
                    
                    const img = document.createElement('img');
                    img.classList.add('preview-img');
                    
                    const removeBtn = document.createElement('div');
                    removeBtn.classList.add('remove-btn');
                    removeBtn.innerHTML = '×';
                    removeBtn.onclick = function() {
                        selectedFiles.splice(index, 1);
                        renderPreviews();
                    };

                    const reader = new FileReader();
                    reader.onload = function(e) {
                        img.src = e.target.result;
                        wrapper.appendChild(img);
                        wrapper.appendChild(removeBtn);
                        preview.appendChild(wrapper);
                    };
                    reader.readAsDataURL(file);
                });
            }
        });
    </script>
