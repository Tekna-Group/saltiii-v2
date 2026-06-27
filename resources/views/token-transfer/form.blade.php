@extends('layouts.header')

@section('css')
<style>
    .token-transfer-container {
        max-width: 600px;
        margin: 40px auto;
    }

    .card {
        border: none;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border-radius: 8px;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .wallet-info {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        margin-bottom: 20px;
        border-left: 4px solid #667eea;
    }

    .wallet-info p {
        margin: 5px 0;
        font-size: 14px;
    }

    .balance-display {
        font-size: 24px;
        font-weight: bold;
        color: #667eea;
    }

    .loading-spinner {
        display: none;
    }

    .alert {
        border-radius: 6px;
    }

    .network-badge {
        display: inline-block;
        background: #e7f5ff;
        color: #1971c2;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
    }

    .form-section {
        margin-bottom: 25px;
    }

    .form-section-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .invalid-feedback {
        display: block;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .spinner {
        animation: spin 1s linear infinite;
        display: inline-block;
        margin-right: 8px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="token-transfer-container">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">
                    <i class="fas fa-exchange-alt me-2"></i>
                    USDC Token Transfer
                </h4>
            </div>

            <div class="card-body p-4">
                <!-- Alert Messages -->
                <div id="alert-container"></div>

                <!-- Wallet Connection Status -->
                <div class="form-section">
                    <div class="form-section-title">Wallet Connection</div>
                    
                    <div class="wallet-info" id="wallet-info">
                        <div class="spinner" id="wallet-spinner" style="display: none;">
                            <i class="fas fa-circle-notch"></i>
                        </div>
                        <p id="wallet-status">
                            <span id="status-text">Checking wallet connection...</span>
                        </p>
                        <p class="mb-0">
                            <small class="text-muted">Network: <span class="network-badge" id="network-display">Checking...</span></small>
                        </p>
                    </div>

                    <button type="button" class="btn btn-outline-primary w-100 mb-3" id="connect-wallet-btn">
                        <i class="fas fa-wallet me-2"></i>
                        Connect Phantom Wallet
                    </button>
                </div>

                <!-- Token Balance -->
                <div class="form-section" id="balance-section" style="display: none;">
                    <div class="form-section-title">Your Balance</div>
                    
                    <div class="wallet-info">
                        <p class="mb-0">
                            <span id="balance-value" class="balance-display">--</span>
                            <span class="ms-2">USDC</span>
                        </p>
                        <small class="text-muted" id="balance-update-time"></small>
                    </div>
                </div>

                <!-- Transfer Form -->
                <form id="transfer-form" style="display: none;">
                    @csrf

                    <div class="form-section">
                        <div class="form-section-title">Transfer Details</div>

                        <!-- Recipient Wallet Address -->
                        <div class="mb-3">
                            <label for="recipient_wallet" class="form-label">
                                <i class="fas fa-map-pin me-2"></i>Recipient Wallet Address
                            </label>
                            <input 
                                type="text" 
                                class="form-control" 
                                id="recipient_wallet" 
                                name="recipient_wallet" 
                                placeholder="Enter Solana wallet address (e.g., 3xQ...)" 
                                required>
                            <small class="form-text text-muted">
                                Must be a valid Solana wallet address
                            </small>
                            <div class="invalid-feedback" id="recipient-error"></div>
                        </div>

                        <!-- Amount -->
                        <div class="mb-3">
                            <label for="amount" class="form-label">
                                <i class="fas fa-coins me-2"></i>Amount (USDC)
                            </label>
                            <div class="input-group">
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    id="amount" 
                                    name="amount" 
                                    placeholder="0.00" 
                                    step="0.01"
                                    min="0.01"
                                    required>
                                <span class="input-group-text">USDC</span>
                            </div>
                            <small class="form-text text-muted">
                                Minimum: 0.01 USDC
                            </small>
                            <div class="invalid-feedback" id="amount-error"></div>
                        </div>

                        <!-- Fee Information -->
                        <div class="alert alert-info" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Network Fee:</strong> Solana network fees typically cost $0.00025 or less
                        </div>
                    </div>

                    <!-- Summary Section -->
                    <div class="form-section" id="summary-section">
                        <div class="form-section-title">Transaction Summary</div>
                        
                        <div class="wallet-info">
                            <p>
                                <strong>From:</strong> <br>
                                <small id="from-wallet" class="text-muted">Your wallet</small>
                            </p>
                            <p>
                                <strong>To:</strong> <br>
                                <small id="to-wallet" class="text-muted">Recipient wallet</small>
                            </p>
                            <p class="mb-0">
                                <strong>Amount:</strong> <br>
                                <span id="transfer-amount" class="balance-display">0.00</span>
                                <span class="ms-2">USDC</span>
                            </p>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Initiate Transfer
                        </button>
                        <button type="reset" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>
                            Clear Form
                        </button>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="terms-check" required>
                        <label class="form-check-label" for="terms-check">
                            I understand that transactions on the blockchain are irreversible
                        </label>
                    </div>
                </form>
            </div>
        </div>

        <!-- Help Section -->
        <div class="card mt-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Need Help?</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="helpAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help1">
                                How do I install Phantom Wallet?
                            </button>
                        </h2>
                        <div id="help1" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                Visit <a href="https://phantom.app" target="_blank">phantom.app</a> and install the extension for your browser. Create or import your wallet, then return here and click "Connect Phantom Wallet".
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help2">
                                What is USDC?
                            </button>
                        </h2>
                        <div id="help2" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                USDC is a stablecoin that represents US Dollars on the blockchain. 1 USDC = $1 USD. It's issued by Circle and Coinbase.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#help3">
                                Is this transaction reversible?
                            </button>
                        </h2>
                        <div id="help3" class="accordion-collapse collapse" data-bs-parent="#helpAccordion">
                            <div class="accordion-body">
                                No, blockchain transactions are permanent and irreversible. Always verify the recipient wallet address before confirming the transfer.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdn.jsdelivr.net/npm/axios@0.21.1/dist/axios.min.js"></script>
<script>
    // USDC Mint Address on Solana
    const USDC_MINT = "EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd";
    const SOLANA_NETWORK = "{{ env('SOLANA_NETWORK', 'mainnet-beta') }}";

    // Phantom Wallet Connection
    let phantomProvider = null;
    let userWallet = null;

    // Check if Phantom is installed
    async function checkPhantomWallet() {
        const { solana } = window;

        if (solana && solana.isPhantom) {
            phantomProvider = solana;
            console.log("Phantom Wallet detected");
            if (phantomProvider.publicKey) {
                userWallet = phantomProvider.publicKey.toString();
                updateWalletUI(userWallet);
                await loadWalletBalance();
            } else {
                updateWalletUI(null);
            }
        } else {
            showAlert("Phantom Wallet not found", "Please install Phantom Wallet extension to continue.", "warning");
            document.getElementById('connect-wallet-btn').disabled = false;
        }
    }

    // Check wallet connection status
    async function checkWalletConnection() {
        if (phantomProvider?.publicKey) {
            userWallet = phantomProvider.publicKey.toString();
            updateWalletUI(userWallet);
            await loadWalletBalance();
            return;
        }

        updateWalletUI(null);
    }

    // Connect wallet on button click
    document.getElementById('connect-wallet-btn').addEventListener('click', async () => {
        try {
            if (!phantomProvider) {
                showAlert("Phantom Wallet not found", "Please install Phantom Wallet to continue.", "error");
                return;
            }

            const response = await phantomProvider.connect();
            userWallet = response.publicKey.toString();
            updateWalletUI(userWallet);
            await loadWalletBalance();
        } catch (err) {
            console.error('Connection error:', err);
            showAlert("Connection Failed", "Could not connect to Phantom Wallet.", "error");
        }
    });

    // Update wallet UI
    function updateWalletUI(wallet) {
        const walletInfo = document.getElementById('wallet-info');
        const statusText = document.getElementById('status-text');
        const spinner = document.getElementById('wallet-spinner');
        const connectBtn = document.getElementById('connect-wallet-btn');
        const balanceSection = document.getElementById('balance-section');
        const transferForm = document.getElementById('transfer-form');

        spinner.style.display = 'none';

        if (wallet) {
            const shortWallet = wallet.substring(0, 6) + '...' + wallet.substring(wallet.length - 6);
            statusText.innerHTML = `<i class="fas fa-check-circle text-success me-2"></i><strong>Connected:</strong> ${shortWallet}`;
            document.getElementById('from-wallet').textContent = shortWallet;
            connectBtn.textContent = "Change Wallet";
            connectBtn.classList.remove('btn-outline-primary');
            connectBtn.classList.add('btn-outline-success');
            balanceSection.style.display = 'block';
            transferForm.style.display = 'block';
        } else {
            statusText.innerHTML = '<i class="fas fa-times-circle text-danger me-2"></i>Not connected';
            connectBtn.textContent = "Connect Phantom Wallet";
            connectBtn.classList.remove('btn-outline-success');
            connectBtn.classList.add('btn-outline-primary');
            balanceSection.style.display = 'none';
            transferForm.style.display = 'none';
        }

        // Display network
        document.getElementById('network-display').textContent = SOLANA_NETWORK === 'mainnet-beta' ? 'Mainnet' : 'Testnet';
    }

    // Load wallet balance
    async function loadWalletBalance() {
        try {
            const response = await axios.get("{{ route('token-transfer.balance') }}");
            if (response.data.success) {
                document.getElementById('balance-value').textContent = response.data.data.balance.toFixed(2);
                document.getElementById('balance-update-time').textContent = 'Updated: ' + new Date().toLocaleTimeString();
            }
        } catch (err) {
            console.error('Error loading balance:', err);
            document.getElementById('balance-value').textContent = 'Error';
        }
    }

    // Form submission
    document.getElementById('transfer-form').addEventListener('submit', async (e) => {
        e.preventDefault();

        const recipientWallet = document.getElementById('recipient_wallet').value.trim();
        const amount = parseFloat(document.getElementById('amount').value);
        const termsCheck = document.getElementById('terms-check').checked;

        // Validate form
        if (!termsCheck) {
            showAlert("Agreement Required", "Please agree to the terms and conditions.", "warning");
            return;
        }

        if (!isValidSolanaAddress(recipientWallet)) {
            showAlert("Invalid Address", "Please enter a valid Solana wallet address.", "error");
            return;
        }

        if (amount <= 0) {
            showAlert("Invalid Amount", "Amount must be greater than 0.", "error");
            return;
        }

        // Update summary
        document.getElementById('to-wallet').textContent = recipientWallet.substring(0, 6) + '...' + recipientWallet.substring(recipientWallet.length - 6);
        document.getElementById('transfer-amount').textContent = amount.toFixed(2);

        // Show confirmation dialog
        if (!confirm(`Confirm transfer of ${amount} USDC to ${recipientWallet.substring(0, 6)}...${recipientWallet.substring(recipientWallet.length - 6)}?\n\nThis action cannot be undone.`)) {
            return;
        }

        await submitTransfer(recipientWallet, amount);
    });

    // Submit transfer
    async function submitTransfer(recipientWallet, amount) {
        try {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

            const response = await axios.post("{{ route('token-transfer.submit') }}", {
                recipient_wallet: recipientWallet,
                amount: amount,
                _token: document.querySelector('input[name="_token"]').value
            });

            if (response.data.success) {
                showAlert("Success", "Token transfer completed successfully!", "success");
                document.getElementById('transfer-form').reset();
                await loadWalletBalance();
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Initiate Transfer';
            }
        } catch (err) {
            const errorMessage = err.response?.data?.message || 'An error occurred during transfer.';
            showAlert("Transfer Failed", errorMessage, "error");
            console.error('Transfer error:', err);
        } finally {
            const submitBtn = document.getElementById('submit-btn');
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Initiate Transfer';
        }
    }

    // Validate Solana address
    function isValidSolanaAddress(address) {
        // Solana addresses are 32-44 characters long and base58 encoded
        return /^[1-9A-HJ-NP-Z]{32,44}$/.test(address);
    }

    // Show alert message
    function showAlert(title, message, type = 'info') {
        const alertContainer = document.getElementById('alert-container');
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

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }

    // Real-time form validation
    document.getElementById('recipient_wallet').addEventListener('blur', () => {
        const wallet = document.getElementById('recipient_wallet').value.trim();
        const errorDiv = document.getElementById('recipient-error');
        
        if (wallet && !isValidSolanaAddress(wallet)) {
            errorDiv.textContent = 'Invalid Solana wallet address';
            document.getElementById('recipient_wallet').classList.add('is-invalid');
        } else {
            errorDiv.textContent = '';
            document.getElementById('recipient_wallet').classList.remove('is-invalid');
        }
    });

    document.getElementById('amount').addEventListener('input', () => {
        const amount = parseFloat(document.getElementById('amount').value);
        document.getElementById('transfer-amount').textContent = (isNaN(amount) ? 0 : amount).toFixed(2);
    });

    // Initialize on page load
    window.addEventListener('load', () => {
        checkPhantomWallet();
    });
</script>
@endsection
