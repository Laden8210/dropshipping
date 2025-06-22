<div class="main-container" id="main-container">
    <div class="card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Add New Product</h5>
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

                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>

                    <div class="col-md-6">
                        <label for="currency" class="form-label">Currency</label>
                        <select class="form-select" id="currency" name="currency">
                            <option value="USD">USD - United States Dollar</option>
                            <option value="PHP">PHP - Philippine Peso</option>
                            <option value="EUR">EUR - Euro</option>
                            <option value="GBP">GBP - British Pound Sterling</option>
                            <option value="JPY">JPY - Japanese Yen</option>
                            <option value="AUD">AUD - Australian Dollar</option>
                            <option value="CAD">CAD - Canadian Dollar</option>
                            <option value="CHF">CHF - Swiss Franc</option>
                            <option value="CNY">CNY - Chinese Yuan</option>
                            <option value="INR">INR - Indian Rupee</option>
                            <option value="SGD">SGD - Singapore Dollar</option>
                            <option value="NZD">NZD - New Zealand Dollar</option>
                            <option value="HKD">HKD - Hong Kong Dollar</option>
                            <option value="KRW">KRW - South Korean Won</option>
                            <option value="SEK">SEK - Swedish Krona</option>
                            <option value="NOK">NOK - Norwegian Krone</option>
                            <option value="DKK">DKK - Danish Krone</option>
                            <option value="RUB">RUB - Russian Ruble</option>
                            <option value="ZAR">ZAR - South African Rand</option>
                            <option value="BRL">BRL - Brazilian Real</option>
                            <option value="MXN">MXN - Mexican Peso</option>
                            <option value="MYR">MYR - Malaysian Ringgit</option>
                            <option value="THB">THB - Thai Baht</option>
                            <option value="IDR">IDR - Indonesian Rupiah</option>
                            <option value="TRY">TRY - Turkish Lira</option>
                            <option value="SAR">SAR - Saudi Riyal</option>
                            <option value="AED">AED - UAE Dirham</option>
                            <option value="PLN">PLN - Polish Zloty</option>
                            <option value="TWD">TWD - Taiwan Dollar</option>
                            <option value="VND">VND - Vietnamese Dong</option>
                            <option value="EGP">EGP - Egyptian Pound</option>
                            <option value="ILS">ILS - Israeli New Shekel</option>
                            <option value="PKR">PKR - Pakistani Rupee</option>
                            <option value="BDT">BDT - Bangladeshi Taka</option>
                            <option value="NGN">NGN - Nigerian Naira</option>
                            <option value="ARS">ARS - Argentine Peso</option>
                            <option value="CLP">CLP - Chilean Peso</option>
                            <option value="COP">COP - Colombian Peso</option>
                            <option value="CZK">CZK - Czech Koruna</option>
                            <option value="HUF">HUF - Hungarian Forint</option>
                            <option value="RON">RON - Romanian Leu</option>
                            <option value="UAH">UAH - Ukrainian Hryvnia</option>
                            <option value="KES">KES - Kenyan Shilling</option>
                            <option value="GHS">GHS - Ghanaian Cedi</option>
                            <option value="MAD">MAD - Moroccan Dirham</option>
                            <option value="QAR">QAR - Qatari Riyal</option>
                            <option value="KWD">KWD - Kuwaiti Dinar</option>
                            <option value="BHD">BHD - Bahraini Dinar</option>
                            <option value="OMR">OMR - Omani Rial</option>
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
                        <div id="image-preview" class="d-flex flex-wrap gap-3"></div>
                    </div>
                    <div class="col-12 text-end">
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
    document.getElementById('add-product-form').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);
        const productImage = document.getElementById('product_image').files[0];
        if (productImage) {
            formData.append('product_image', productImage);
        }

        axios.post('controller/supplier/product/index.php?action=add-product', formData, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            })
            .then(response => {
                if (response.data.success) {
                    alert('Product added successfully!');
                    window.location.reload();
                } else {
                    alert('Error adding product: ' + response.data.message);
                }
            })
            .catch(error => {
                console.error('There was an error!', error);
                alert('An error occurred while adding the product.');
            });
    });

    let selectedFiles = [];

    document.getElementById('product_images').addEventListener('change', function(event) {
        const preview = document.getElementById('image-preview');
        const files = Array.from(event.target.files);

        selectedFiles = selectedFiles.concat(files);
        renderPreviews();

        event.target.value = '';
    });

    function renderPreviews() {
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const wrapper = document.createElement('div');
                wrapper.classList.add('position-relative');

                const img = document.createElement('img');
                img.src = e.target.result;
                img.classList.add('img-thumbnail', 'me-2');
                img.style.width = '120px';
                img.style.height = '120px';
                img.style.objectFit = 'cover';

                const removeBtn = document.createElement('button');
                removeBtn.innerHTML = '&times;';
                removeBtn.type = 'button';
                removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0';
                removeBtn.style.transform = 'translate(50%, -50%)';
                removeBtn.onclick = function() {
                    selectedFiles.splice(index, 1);
                    renderPreviews();
                };

                wrapper.appendChild(img);
                wrapper.appendChild(removeBtn);
                preview.appendChild(wrapper);
            };
            reader.readAsDataURL(file);
        });

        updateInputFiles();
    }

    function updateInputFiles() {
        const input = document.getElementById('product_images');
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
    }


    const getRequest = new GetAllRequest({
        getUrl: 'controller/supplier/category/index.php?action=get-categories',
        params: {},
        callback: (error, data) => {
            if (error) {
                console.log('Error:', error);
                const categorySelect = document.getElementById('category');
                categorySelect.innerHTML = '<option value="" disabled selected>' + error + '</option>';

            } else {

                console.log('Categories fetched successfully:', data);
                const categorySelect = document.getElementById('category');
                categorySelect.innerHTML = '<option value="" disabled selected>Select a category</option>';
                data.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category_id;
                    option.textContent = category.category_name;
                    categorySelect.appendChild(option);
                });



            }
        },
        promptMessage: 'Do you want to fetch the latest data?'
    });
    getRequest.send();
</script>