<div class="main-container" id="main-container">
    <div class="header-section text-center my-4">
        <p class="lead">Manage your report</p>
    </div>

    <div class="main-content mb-5">

        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Reports</h3>
                    </div>

                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-5">
                                <select class="form-select" id="reportTypeSelect">
                                    <option value="sales">Sales Report</option>
                                    <option value="inventory">Inventory Report</option>
                                    <option value="customer">Customer Report</option>
                                    <option value="supplier">Supplier Report</option>
                                    <option value="order">Order Report</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-primary w-100" id="downloadReportBtn">
                                    <i class="fas fa-download"></i> Download Report
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Details</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>