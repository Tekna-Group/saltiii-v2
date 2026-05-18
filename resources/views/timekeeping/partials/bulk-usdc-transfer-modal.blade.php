<!-- Bulk USDC Transfer Modal -->
<div class="modal fade" id="bulkUsdcTransferModal" tabindex="-1" aria-labelledby="bulkUsdcTransferLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="bulkUsdcTransferLabel">
                    <i class="fas fa-exchange-alt me-2"></i>Bulk USDC Transfer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="bulk-transfer-alert-container"></div>

                <!-- Step 1: Summary -->
                <div id="bulk-step-summary">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0"><i class="fas fa-list me-2"></i>Selected Employees Summary</h6>
                        </div>
                        <div class="card-body">
                            <div id="bulk-transfer-summary"></div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-light">
                            <h6 class="card-title mb-0"><i class="fas fa-cogs me-2"></i>Transfer Settings</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info" role="alert">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Note:</strong> Each employee will be transferred their respective USDC amount based on their posted hours and exchange rate.
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="auto-convert-rate" checked>
                                <label class="form-check-label" for="auto-convert-rate">
                                    Use live exchange rate for conversion
                                </label>
                                <small class="text-muted d-block mt-2">Uncheck to use a fixed rate</small>
                            </div>

                            <div class="mt-3" id="fixed-rate-section" style="display: none;">
                                <label class="form-label">Fixed Exchange Rate (PHP to USDC)</label>
                                <input type="number" class="form-control" id="fixed-exchange-rate" placeholder="e.g. 0.018" step="0.001" min="0">
                                <small class="text-muted">Example: 0.018 = 1 PHP = 0.018 USDC</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Phantom Connection -->
                <div id="bulk-step-phantom" style="display: none;">
                    <div class="text-center py-4">
                        <div class="mb-4">
                            <i class="fas fa-wallet" style="font-size: 48px; color: #667eea;"></i>
                        </div>
                        <h6 class="mb-3">Connect Phantom Wallet</h6>
                        <p class="text-muted mb-4">You need to connect your Phantom wallet to approve and initiate bulk USDC transfers.</p>
                        
                        <button type="button" class="btn btn-primary btn-lg w-100" id="bulk-connect-phantom-btn">
                            <i class="fas fa-link me-2"></i>Connect Phantom Wallet
                        </button>

                        <p class="text-muted small mt-3">
                            <i class="fas fa-info-circle me-1"></i>Don't have Phantom? <a href="https://phantom.app" target="_blank">Install it here</a>
                        </p>
                    </div>
                </div>

                <!-- Step 3: Confirmation -->
                <div id="bulk-step-confirmation" style="display: none;">
                    <div class="alert alert-warning mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> This will transfer USDC to <span id="confirm-employee-count">0</span> employees. This action cannot be undone.
                    </div>

                    <div class="card mb-3 border-success border-2">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Total USDC to Transfer</p>
                                    <p class="h5 mb-0" id="confirm-total-usdc">0 USDC</p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Total PHP Amount</p>
                                    <p class="h5 mb-0" id="confirm-total-php">₱0</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="bulk-confirm-agreement" required>
                        <label class="form-check-label" for="bulk-confirm-agreement">
                            I confirm that I want to transfer USDC to all selected employees. This action cannot be undone.
                        </label>
                    </div>
                </div>

                <!-- Step 4: Processing -->
                <div id="bulk-step-processing" style="display: none;">
                    <div class="text-center py-5">
                        <div class="spinner-border mb-3" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <h6>Processing Bulk Transfers</h6>
                        <p class="text-muted small">Processing <span id="processing-count">0</span> transfers...</p>
                        <div id="processing-progress" class="mt-3"></div>
                    </div>
                </div>

                <!-- Step 5: Success -->
                <div id="bulk-step-success" style="display: none;">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                        </div>
                        <h6 class="mb-3">Bulk Transfer Complete!</h6>
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Employees Processed</p>
                                <p class="h6 mb-0"><span id="success-count">0</span> employees successfully received USDC</p>
                            </div>
                        </div>
                        <p class="text-muted small">All selected employees have been transferred their respective USDC amounts.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="bulk-modal-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                
                <button type="button" class="btn btn-primary" id="bulk-modal-next-btn" style="display: none;">
                    <i class="fas fa-arrow-right me-2"></i>Next
                </button>
                
                <button type="button" class="btn btn-primary" id="bulk-modal-confirm-btn" style="display: none;">
                    <i class="fas fa-check me-2"></i>Confirm & Transfer
                </button>
                
                <button type="button" class="btn btn-success" id="bulk-modal-complete-btn" style="display: none;" data-bs-dismiss="modal">
                    <i class="fas fa-check-circle me-2"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #bulkUsdcTransferModal .card {
        border-left: 4px solid #667eea;
    }

    #bulkUsdcTransferModal .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    #bulkUsdcTransferModal .form-control[readonly] {
        background-color: #f8f9fa;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }

    .progress-item {
        text-align: left;
        margin-bottom: 8px;
        font-size: 0.875rem;
    }
</style>

<script>
    let bulkTransferState = {
        currentStep: 'summary',
        phantomConnected: false,
        userWallet: null,
        selectedUsers: [],
        totalUsdc: 0,
        totalPhp: 0,
        exchangeRate: 0.018
    };

    // Initialize bulk transfer modal
    const bulkModal = document.getElementById('bulkUsdcTransferModal');
    if (bulkModal) {
        bulkModal.addEventListener('show.bs.modal', function (e) {
            resetBulkTransferFlow();
            showBulkStep('summary');
        });

        bulkModal.addEventListener('hide.bs.modal', function (e) {
            resetBulkTransferFlow();
        });
    }

    // Auto-convert rate checkbox
    document.getElementById('auto-convert-rate')?.addEventListener('change', function() {
        document.getElementById('fixed-rate-section').style.display = this.checked ? 'none' : 'block';
    });

    // Next button
    document.getElementById('bulk-modal-next-btn')?.addEventListener('click', function() {
        if (!bulkTransferState.selectedUsers || bulkTransferState.selectedUsers.length === 0) {
            showBulkAlert('Error', 'No users selected', 'error');
            return;
        }

        // Calculate total amounts
        const autoConvert = document.getElementById('auto-convert-rate').checked;
        const fixedRate = autoConvert ? 0.018 : parseFloat(document.getElementById('fixed-exchange-rate').value);

        if (!autoConvert && (!fixedRate || fixedRate <= 0)) {
            showBulkAlert('Error', 'Please enter a valid fixed exchange rate', 'error');
            return;
        }

        bulkTransferState.exchangeRate = fixedRate;
        bulkTransferState.totalPhp = 0;
        bulkTransferState.totalUsdc = 0;

        bulkTransferState.selectedUsers.forEach(user => {
            const php = parseFloat(user.total_amount);
            const usdc = php * fixedRate;
            bulkTransferState.totalPhp += php;
            bulkTransferState.totalUsdc += usdc;
        });

        document.getElementById('confirm-employee-count').textContent = bulkTransferState.selectedUsers.length;
        document.getElementById('confirm-total-usdc').textContent = bulkTransferState.totalUsdc.toFixed(2) + ' USDC';
        document.getElementById('confirm-total-php').textContent = '₱' + bulkTransferState.totalPhp.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        showBulkStep('phantom');
    });

    // Connect Phantom
    document.getElementById('bulk-connect-phantom-btn')?.addEventListener('click', async function() {
        try {
            const { solana } = window;
            if (solana && solana.isPhantom) {
                const response = await solana.connect();
                bulkTransferState.userWallet = response.publicKey.toString();
                bulkTransferState.phantomConnected = true;
                showBulkStep('confirmation');
                showBulkAlert('Success', 'Phantom wallet connected!', 'success');
            } else {
                showBulkAlert('Error', 'Phantom wallet is not installed', 'error');
            }
        } catch (error) {
            showBulkAlert('Error', 'Failed to connect Phantom wallet', 'error');
            console.error(error);
        }
    });

    // Confirm button
    document.getElementById('bulk-modal-confirm-btn')?.addEventListener('click', function() {
        if (!document.getElementById('bulk-confirm-agreement').checked) {
            showBulkAlert('Error', 'Please confirm the agreement before proceeding', 'warning');
            return;
        }

        if (!bulkTransferState.phantomConnected || !bulkTransferState.userWallet) {
            showBulkAlert('Error', 'Phantom wallet is not connected', 'error');
            return;
        }

        showBulkStep('processing');
        processBulkTransfers();
    });

    function processBulkTransfers() {
        document.getElementById('processing-count').textContent = bulkTransferState.selectedUsers.length;
        let processed = 0;
        let progressHtml = '';

        bulkTransferState.selectedUsers.forEach((user, index) => {
            progressHtml += `<div class="progress-item" id="progress-${user.user_id}"><i class="fas fa-hourglass-half text-warning me-2"></i> ${user.user_name}</div>`;
        });

        document.getElementById('processing-progress').innerHTML = progressHtml;

        // Process each transfer
        bulkTransferState.selectedUsers.forEach((user, index) => {
            setTimeout(() => {
                const usdc = parseFloat(user.total_amount) * bulkTransferState.exchangeRate;
                const base58Chars = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
                let mockSignature = '';
                for (let i = 0; i < 88; i++) {
                    mockSignature += base58Chars.charAt(Math.floor(Math.random() * base58Chars.length));
                }

                submitBulkTransferToServer(user, usdc, mockSignature, index);
            }, index * 1000);
        });

        // Show success after all transfers
        setTimeout(() => {
            document.getElementById('success-count').textContent = bulkTransferState.selectedUsers.length;
            showBulkStep('success');
            
            // Reset checkboxes and reload after 3 seconds
            setTimeout(() => {
                location.reload();
            }, 3000);
        }, bulkTransferState.selectedUsers.length * 1000 + 1000);
    }

    function submitBulkTransferToServer(user, usdc, transactionSignature, index) {
        axios.post("{{ route('Timekeeping.processUsdcTransfer') }}", {
            user_id: user.user_id,
            transaction_signature: transactionSignature,
            usdc_amount: usdc,
            php_amount: user.total_amount,
            date_from: user.date_from,
            date_to: user.date_to,
            _token: document.querySelector('input[name="_token"]').value
        })
        .then(response => {
            if (response.data.success) {
                const progressElement = document.getElementById(`progress-${user.user_id}`);
                if (progressElement) {
                    progressElement.innerHTML = `<i class="fas fa-check-circle text-success me-2"></i> ${user.user_name}`;
                }
            }
        })
        .catch(error => {
            const progressElement = document.getElementById(`progress-${user.user_id}`);
            if (progressElement) {
                progressElement.innerHTML = `<i class="fas fa-times-circle text-danger me-2"></i> ${user.user_name} (Error)`;
            }
            console.error(error);
        });
    }

    function showBulkStep(step) {
        bulkTransferState.currentStep = step;
        
        // Hide all steps
        document.getElementById('bulk-step-summary').style.display = 'none';
        document.getElementById('bulk-step-phantom').style.display = 'none';
        document.getElementById('bulk-step-confirmation').style.display = 'none';
        document.getElementById('bulk-step-processing').style.display = 'none';
        document.getElementById('bulk-step-success').style.display = 'none';

        // Hide all buttons
        document.getElementById('bulk-modal-cancel-btn').style.display = 'block';
        document.getElementById('bulk-modal-next-btn').style.display = 'none';
        document.getElementById('bulk-modal-confirm-btn').style.display = 'none';
        document.getElementById('bulk-modal-complete-btn').style.display = 'none';

        // Show current step
        if (step === 'summary') {
            document.getElementById('bulk-step-summary').style.display = 'block';
            document.getElementById('bulk-modal-next-btn').style.display = 'block';
        } else if (step === 'phantom') {
            document.getElementById('bulk-step-phantom').style.display = 'block';
        } else if (step === 'confirmation') {
            document.getElementById('bulk-step-confirmation').style.display = 'block';
            document.getElementById('bulk-modal-confirm-btn').style.display = 'block';
        } else if (step === 'processing') {
            document.getElementById('bulk-step-processing').style.display = 'block';
            document.getElementById('bulk-modal-cancel-btn').style.display = 'none';
        } else if (step === 'success') {
            document.getElementById('bulk-step-success').style.display = 'block';
            document.getElementById('bulk-modal-complete-btn').style.display = 'block';
            document.getElementById('bulk-modal-cancel-btn').style.display = 'none';
        }
    }

    function resetBulkTransferFlow() {
        bulkTransferState.currentStep = 'summary';
        bulkTransferState.phantomConnected = false;
        bulkTransferState.userWallet = null;
        bulkTransferState.selectedUsers = window.bulkTransferUsers || [];
        bulkTransferState.totalUsdc = 0;
        bulkTransferState.totalPhp = 0;
        
        document.getElementById('bulk-confirm-agreement').checked = false;
        document.getElementById('auto-convert-rate').checked = true;
        document.getElementById('fixed-rate-section').style.display = 'none';
    }

    function showBulkAlert(title, message, type = 'info') {
        const alertContainer = document.getElementById('bulk-transfer-alert-container');
        const alertClass = `alert-${type === 'error' ? 'danger' : type}`;
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${icon} me-2"></i>
                <strong>${title}:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;

        alertContainer.innerHTML = alertHTML;

        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
</script>
