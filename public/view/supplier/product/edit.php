    <div class="main-container" id="main-container">
        <div class="card mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white"><i class="fas fa-edit me-2"></i>Edit Product</h5>
            </div>
            <div class="card-body">
                <form id="edit-product-form" enctype="multipart/form-data">
                    <input type="hidden" name="product_id" id="product_id" value="<?php echo $_GET['product_id'] ?? ''; ?>">
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
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="AED">AED - United Arab Emirates Dirham</option>
                                <option value="AFN">AFN - Afghan Afghani</option>
                                <option value="ALL">ALL - Albanian Lek</option>
                                <option value="AMD">AMD - Armenian Dram</option>
                                <option value="ANG">ANG - Netherlands Antillean Guilder</option>
                                <option value="AOA">AOA - Angolan Kwanza</option>
                                <option value="ARS">ARS - Argentine Peso</option>
                                <option value="AUD">AUD - Australian Dollar</option>
                                <option value="AWG">AWG - Aruban Florin</option>
                                <option value="AZN">AZN - Azerbaijani Manat</option>
                                <option value="BAM">BAM - Bosnia-Herzegovina Convertible Mark</option>
                                <option value="BBD">BBD - Barbadian Dollar</option>
                                <option value="BDT">BDT - Bangladeshi Taka</option>
                                <option value="BGN">BGN - Bulgarian Lev</option>
                                <option value="BHD">BHD - Bahraini Dinar</option>
                                <option value="BIF">BIF - Burundian Franc</option>
                                <option value="BMD">BMD - Bermudian Dollar</option>
                                <option value="BND">BND - Brunei Dollar</option>
                                <option value="BOB">BOB - Bolivian Boliviano</option>
                                <option value="BRL">BRL - Brazilian Real</option>
                                <option value="BSD">BSD - Bahamian Dollar</option>
                                <option value="BTN">BTN - Bhutanese Ngultrum</option>
                                <option value="BWP">BWP - Botswanan Pula</option>
                                <option value="BYN">BYN - Belarusian Ruble</option>
                                <option value="BZD">BZD - Belize Dollar</option>
                                <option value="CAD">CAD - Canadian Dollar</option>
                                <option value="CDF">CDF - Congolese Franc</option>
                                <option value="CHF">CHF - Swiss Franc</option>
                                <option value="CLP">CLP - Chilean Peso</option>
                                <option value="CNY">CNY - Chinese Yuan</option>
                                <option value="COP">COP - Colombian Peso</option>
                                <option value="CRC">CRC - Costa Rican Colón</option>
                                <option value="CUP">CUP - Cuban Peso</option>
                                <option value="CVE">CVE - Cape Verdean Escudo</option>
                                <option value="CZK">CZK - Czech Koruna</option>
                                <option value="DJF">DJF - Djiboutian Franc</option>
                                <option value="DKK">DKK - Danish Krone</option>
                                <option value="DOP">DOP - Dominican Peso</option>
                                <option value="DZD">DZD - Algerian Dinar</option>
                                <option value="EGP">EGP - Egyptian Pound</option>
                                <option value="ERN">ERN - Eritrean Nakfa</option>
                                <option value="ETB">ETB - Ethiopian Birr</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="FJD">FJD - Fijian Dollar</option>
                                <option value="FKP">FKP - Falkland Islands Pound</option>
                                <option value="FOK">FOK - Faroese Króna</option>
                                <option value="GBP">GBP - British Pound Sterling</option>
                                <option value="GEL">GEL - Georgian Lari</option>
                                <option value="GGP">GGP - Guernsey Pound</option>
                                <option value="GHS">GHS - Ghanaian Cedi</option>
                                <option value="GIP">GIP - Gibraltar Pound</option>
                                <option value="GMD">GMD - Gambian Dalasi</option>
                                <option value="GNF">GNF - Guinean Franc</option>
                                <option value="GTQ">GTQ - Guatemalan Quetzal</option>
                                <option value="GYD">GYD - Guyanaese Dollar</option>
                                <option value="HKD">HKD - Hong Kong Dollar</option>
                                <option value="HNL">HNL - Honduran Lempira</option>
                                <option value="HRK">HRK - Croatian Kuna</option>
                                <option value="HTG">HTG - Haitian Gourde</option>
                                <option value="HUF">HUF - Hungarian Forint</option>
                                <option value="IDR">IDR - Indonesian Rupiah</option>
                                <option value="ILS">ILS - Israeli New Shekel</option>
                                <option value="IMP">IMP - Isle of Man Pound</option>
                                <option value="INR">INR - Indian Rupee</option>
                                <option value="IQD">IQD - Iraqi Dinar</option>
                                <option value="IRR">IRR - Iranian Rial</option>
                                <option value="ISK">ISK - Icelandic Króna</option>
                                <option value="JEP">JEP - Jersey Pound</option>
                                <option value="JMD">JMD - Jamaican Dollar</option>
                                <option value="JOD">JOD - Jordanian Dinar</option>
                                <option value="JPY">JPY - Japanese Yen</option>
                                <option value="KES">KES - Kenyan Shilling</option>
                                <option value="KGS">KGS - Kyrgystani Som</option>
                                <option value="KHR">KHR - Cambodian Riel</option>
                                <option value="KID">KID - Kiribati Dollar</option>
                                <option value="KMF">KMF - Comorian Franc</option>
                                <option value="KRW">KRW - South Korean Won</option>
                                <option value="KWD">KWD - Kuwaiti Dinar</option>
                                <option value="KYD">KYD - Cayman Islands Dollar</option>
                                <option value="KZT">KZT - Kazakhstani Tenge</option>
                                <option value="LAK">LAK - Laotian Kip</option>
                                <option value="LBP">LBP - Lebanese Pound</option>
                                <option value="LKR">LKR - Sri Lankan Rupee</option>
                                <option value="LRD">LRD - Liberian Dollar</option>
                                <option value="LSL">LSL - Lesotho Loti</option>
                                <option value="LYD">LYD - Libyan Dinar</option>
                                <option value="MAD">MAD - Moroccan Dirham</option>
                                <option value="MDL">MDL - Moldovan Leu</option>
                                <option value="MGA">MGA - Malagasy Ariary</option>
                                <option value="MKD">MKD - Macedonian Denar</option>
                                <option value="MMK">MMK - Myanma Kyat</option>
                                <option value="MNT">MNT - Mongolian Tugrik</option>
                                <option value="MOP">MOP - Macanese Pataca</option>
                                <option value="MRU">MRU - Mauritanian Ouguiya</option>
                                <option value="MUR">MUR - Mauritian Rupee</option>
                                <option value="MVR">MVR - Maldivian Rufiyaa</option>
                                <option value="MWK">MWK - Malawian Kwacha</option>
                                <option value="MXN">MXN - Mexican Peso</option>
                                <option value="MYR">MYR - Malaysian Ringgit</option>
                                <option value="MZN">MZN - Mozambican Metical</option>
                                <option value="NAD">NAD - Namibian Dollar</option>
                                <option value="NGN">NGN - Nigerian Naira</option>
                                <option value="NIO">NIO - Nicaraguan Córdoba</option>
                                <option value="NOK">NOK - Norwegian Krone</option>
                                <option value="NPR">NPR - Nepalese Rupee</option>
                                <option value="NZD">NZD - New Zealand Dollar</option>
                                <option value="OMR">OMR - Omani Rial</option>
                                <option value="PAB">PAB - Panamanian Balboa</option>
                                <option value="PEN">PEN - Peruvian Nuevo Sol</option>
                                <option value="PGK">PGK - Papua New Guinean Kina</option>
                                <option value="PHP" selected>PHP - Philippine Peso</option>
                                <option value="PKR">PKR - Pakistani Rupee</option>
                                <option value="PLN">PLN - Polish Zloty</option>
                                <option value="PYG">PYG - Paraguayan Guarani</option>
                                <option value="QAR">QAR - Qatari Rial</option>
                                <option value="RON">RON - Romanian Leu</option>
                                <option value="RSD">RSD - Serbian Dinar</option>
                                <option value="RUB">RUB - Russian Ruble</option>
                                <option value="RWF">RWF - Rwandan Franc</option>
                                <option value="SAR">SAR - Saudi Riyal</option>
                                <option value="SBD">SBD - Solomon Islands Dollar</option>
                                <option value="SCR">SCR - Seychellois Rupee</option>
                                <option value="SDG">SDG - Sudanese Pound</option>
                                <option value="SEK">SEK - Swedish Krona</option>
                                <option value="SGD">SGD - Singapore Dollar</option>
                                <option value="SHP">SHP - Saint Helena Pound</option>
                                <option value="SLE">SLE - Sierra Leonean Leone</option>
                                <option value="SLL">SLL - Sierra Leonean Leone (old)</option>
                                <option value="SOS">SOS - Somali Shilling</option>
                                <option value="SRD">SRD - Surinamese Dollar</option>
                                <option value="SSP">SSP - South Sudanese Pound</option>
                                <option value="STN">STN - São Tomé and Príncipe Dobra</option>
                                <option value="SYP">SYP - Syrian Pound</option>
                                <option value="SZL">SZL - Swazi Lilangeni</option>
                                <option value="THB">THB - Thai Baht</option>
                                <option value="TJS">TJS - Tajikistani Somoni</option>
                                <option value="TMT">TMT - Turkmenistani Manat</option>
                                <option value="TND">TND - Tunisian Dinar</option>
                                <option value="TOP">TOP - Tongan Paʻanga</option>
                                <option value="TRY">TRY - Turkish Lira</option>
                                <option value="TTD">TTD - Trinidad and Tobago Dollar</option>
                                <option value="TVD">TVD - Tuvaluan Dollar</option>
                                <option value="TWD">TWD - New Taiwan Dollar</option>
                                <option value="TZS">TZS - Tanzanian Shilling</option>
                                <option value="UAH">UAH - Ukrainian Hryvnia</option>
                                <option value="UGX">UGX - Ugandan Shilling</option>
                                <option value="USD">USD - United States Dollar</option>
                                <option value="UYU">UYU - Uruguayan Peso</option>
                                <option value="UZS">UZS - Uzbekistani Som</option>
                                <option value="VES">VES - Venezuelan Bolívar Soberano</option>
                                <option value="VND">VND - Vietnamese Dong</option>
                                <option value="VUV">VUV - Vanuatu Vatu</option>
                                <option value="WST">WST - Samoan Tala</option>
                                <option value="XAF">XAF - Central African CFA Franc</option>
                                <option value="XCD">XCD - East Caribbean Dollar</option>
                                <option value="XOF">XOF - West African CFA Franc</option>
                                <option value="XPF">XPF - CFP Franc</option>
                                <option value="YER">YER - Yemeni Rial</option>
                                <option value="ZAR">ZAR - South African Rand</option>
                                <option value="ZMW">ZMW - Zambian Kwacha</option>
                                <option value="ZWL">ZWL - Zimbabwean Dollar</option>
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

                        <!-- Unlisted Status -->
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_unlisted" name="is_unlisted" value="1">
                                <label class="form-check-label" for="is_unlisted">
                                    Unlisted Product (Hide from business owners)
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <!-- Product Variations Section -->
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Product Variations</h6>
                                    <small class="text-muted">Add variations with size, color, weight, price, and dimensions</small>
                                </div>
                                <div class="card-body">
                                    <div id="variations-container">
                                        <div class="variation-group mb-3">
                                            <div class="row g-2">
                                                <div class="col-md-2">
                                                    <label class="form-label">Size</label>
                                                    <input type="text" class="form-control variation-size" name="variation_size[]" placeholder="e.g., Small, Medium, Large">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Color</label>
                                                    <input type="text" class="form-control variation-color" name="variation_color[]" placeholder="e.g., Red, Blue, Black">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Weight (grams)</label>
                                                    <input type="number" class="form-control variation-weight" name="variation_weight[]" placeholder="Weight">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">Length (cm)</label>
                                                    <input type="number" step="0.01" class="form-control variation-length" name="variation_length[]" placeholder="Length">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">Width (cm)</label>
                                                    <input type="number" step="0.01" class="form-control variation-width" name="variation_width[]" placeholder="Width">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">Height (cm)</label>
                                                    <input type="number" step="0.01" class="form-control variation-height" name="variation_height[]" placeholder="Height">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Price</label>
                                                    <input type="number" step="0.01" class="form-control variation-price" name="variation_price[]" placeholder="Price">
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 remove-variation">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="add-variation">
                                        <i class="fas fa-plus me-1"></i>Add Variation
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="product_image" class="form-label">Primary Image</label>
                            <input class="form-control" type="file" id="product_image" name="product_image" accept="image/*">
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
                                <i class="fas fa-save me-2"></i>Update Product
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

            document.getElementById('edit-product-form').addEventListener('submit', function(event) {
                event.preventDefault();

                // Create FormData and append all form fields
                const formData = new FormData();
                formData.append('product_id', document.getElementById('product_id').value);

                // Append text fields
                formData.append('product_name', document.getElementById('product_name').value);
                formData.append('category', document.getElementById('category').value);

                formData.append('currency', document.getElementById('currency').value);
                formData.append('status', document.getElementById('status').value);
                formData.append('description', document.getElementById('description').value);

                // Append unlisted status
                formData.append('is_unlisted', document.getElementById('is_unlisted').checked ? '1' : '0');

                // Append variations
                const variations = collectVariations();
                if (variations.length > 0) {
                    formData.append('variations', JSON.stringify(variations));
                }

                // Append primary image
                const primaryImage = document.getElementById('product_image').files[0];
                if (primaryImage) {
                    formData.append('product_image', primaryImage);
                }

                // Append additional images
                for (let i = 0; i < selectedFiles.length; i++) {
                    formData.append('product_images[]', selectedFiles[i]);
                }

                axios.post('controller/supplier/product/index.php?action=update-product', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        if (response.data.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Product updated successfully!',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                window.location.href = 'product';
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error updating product',
                                text: response.data.message || 'An error occurred.'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('There was an error!', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'An error occurred',
                            text: 'An error occurred while updating the product.'
                        });
                    });
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
                    wrapper.style.display = 'inline-block';
                    wrapper.style.margin = '8px';
                    wrapper.style.position = 'relative';

                    const img = document.createElement('img');
                    img.classList.add('preview-img');
                    img.style.maxWidth = '120px';
                    img.style.maxHeight = '120px';
                    img.style.objectFit = 'cover';
                    img.style.border = '1px solid #ddd';
                    img.style.borderRadius = '6px';
                    img.style.display = 'block';

                    const removeBtn = document.createElement('div');
                    removeBtn.classList.add('remove-btn');
                    removeBtn.innerHTML = '×';
                    removeBtn.style.position = 'absolute';
                    removeBtn.style.top = '2px';
                    removeBtn.style.right = '6px';
                    removeBtn.style.cursor = 'pointer';
                    removeBtn.style.background = '#fff';
                    removeBtn.style.borderRadius = '50%';
                    removeBtn.style.width = '22px';
                    removeBtn.style.height = '22px';
                    removeBtn.style.display = 'flex';
                    removeBtn.style.alignItems = 'center';
                    removeBtn.style.justifyContent = 'center';
                    removeBtn.style.boxShadow = '0 1px 4px rgba(0,0,0,0.15)';
                    removeBtn.style.fontSize = '18px';
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

            // Variation management
            document.getElementById('add-variation').addEventListener('click', function() {
                addVariationRow();
            });

            function collectVariations() {
                const variations = [];
                const variationGroups = document.querySelectorAll('.variation-group');

                variationGroups.forEach(group => {
                    const variationId = group.querySelector('.variation-id').value;
                    console.log('variationId', variationId);
                    const size = group.querySelector('.variation-size').value;
                    const color = group.querySelector('.variation-color').value;
                    const weight = group.querySelector('.variation-weight').value;
                    const length = group.querySelector('.variation-length').value;
                    const width = group.querySelector('.variation-width').value;
                    const height = group.querySelector('.variation-height').value;
                    const price = group.querySelector('.variation-price').value;


                    variations.push({
                        variation_id: variationId || '',
                        size: size || '',
                        color: color || '',
                        weight: weight || null,
                        length: length || null,
                        width: width || null,
                        height: height || null,
                        price: price || null
                    });

                });

                return variations;
            }
        });

        // Define addVariationRow function outside DOMContentLoaded so it's accessible globally
        function addVariationRow() {
            const container = document.getElementById('variations-container');
            const newRow = document.createElement('div');
            newRow.className = 'variation-group mb-3';
            newRow.innerHTML = `
                <div class="row g-2">
                    <input type="hidden" class="form-control variation-id" name="variation_id[]" value="">
                    <div class="col-md-2">
                        <label class="form-label">Size</label>
                        <input type="text" class="form-control variation-size" name="variation_size[]" placeholder="e.g., Small, Medium, Large">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Color</label>
                        <input type="text" class="form-control variation-color" name="variation_color[]" placeholder="e.g., Red, Blue, Black">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Weight (grams)</label>
                        <input type="number" class="form-control variation-weight" name="variation_weight[]" placeholder="Weight">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Length (cm)</label>
                        <input type="number" step="0.01" class="form-control variation-length" name="variation_length[]" placeholder="Length">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Width (cm)</label>
                        <input type="number" step="0.01" class="form-control variation-width" name="variation_width[]" placeholder="Width">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">Height (cm)</label>
                        <input type="number" step="0.01" class="form-control variation-height" name="variation_height[]" placeholder="Height">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" class="form-control variation-price" name="variation_price[]" placeholder="Price">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                    
                    </div>
                </div>
            `;
            container.appendChild(newRow);

        }

        // Define collectVariations function outside DOMContentLoaded so it's accessible globally
        function collectVariations() {
            const variations = [];
            const variationGroups = document.querySelectorAll('.variation-group');

            variationGroups.forEach(group => {
                const variationId = group.querySelector('.variation-id').value || 0;
                const size = group.querySelector('.variation-size').value;
                const color = group.querySelector('.variation-color').value;
                const weight = group.querySelector('.variation-weight').value;
                const length = group.querySelector('.variation-length').value;
                const width = group.querySelector('.variation-width').value;
                const height = group.querySelector('.variation-height').value;
                const price = group.querySelector('.variation-price').value;

                // Only add variation if at least size or color is provided
                if (size || color) {
                    variations.push({
                        variation_id: variationId || '',
                        size: size || '',
                        color: color || '',
                        weight: weight || null,
                        length: length || null,
                        width: width || null,
                        height: height || null,
                        price: price || null
                    });
                }
            });

            return variations;
        }

        // Define loadProductForEdit function outside DOMContentLoaded so it's accessible globally
        function loadProductForEdit(productId) {
            axios.get('controller/supplier/product/index.php?action=single-product&pid=' + productId)
                .then(response => {
                    const product = response.data.data;
                    if (!product) {
                        console.log('No product data received');
                        return;
                    }

                    console.log('Product data loaded:', product);
                    console.log('Variations:', product.variations);

                    document.getElementById('product_name').value = product.product_name;
                    document.getElementById('category').value = product.product_category;

                    document.getElementById('currency').value = product.currency || 'PHP';
                    document.getElementById('status').value = product.status;
                    document.getElementById('description').value = product.description || '';
                    document.getElementById('is_unlisted').checked = product.is_unlisted == 1;

                    // Load variations
                    if (product.variations && product.variations.length > 0) {

                        // get the currency in first variant 
                        const currency = product.variations[0].currency;
                        document.getElementById('currency').value = currency;
                        console.log('Loading variations:', product.variations.length);
                        const variationsContainer = document.getElementById('variations-container');
                        variationsContainer.innerHTML = '';

                        product.variations.forEach((variation, index) => {
                            console.log('Adding variation:', index, variation);
                            addVariationRow();
                            const lastGroup = variationsContainer.lastElementChild;
                            if (lastGroup) {
                                lastGroup.querySelector('.variation-id').value = variation.variation_id || '';
                                lastGroup.querySelector('.variation-size').value = variation.size || '';
                                lastGroup.querySelector('.variation-color').value = variation.color || '';
                                lastGroup.querySelector('.variation-weight').value = variation.weight || '';
                                lastGroup.querySelector('.variation-length').value = variation.length || '';
                                lastGroup.querySelector('.variation-width').value = variation.width || '';
                                lastGroup.querySelector('.variation-height').value = variation.height || '';
                                lastGroup.querySelector('.variation-price').value = variation.price || '';
                            }
                        });
                    } else {
                        console.log('No variations found');
                    }
                })
                .catch(err => {
                    console.error('Error fetching product:', err);
                });
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

                    const productId = document.getElementById('product_id').value;
                    if (productId) {
                        loadProductForEdit(productId);
                    }
                }
            },
            promptMessage: 'Do you want to fetch the latest data?'
        });
        getRequest.send();
    </script>