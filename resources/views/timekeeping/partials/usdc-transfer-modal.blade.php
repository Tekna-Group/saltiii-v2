<!-- USDC Transfer Modal -->
<div class="modal fade" id="usdcTransferModal" tabindex="-1" aria-labelledby="usdcTransferLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <h5 class="modal-title" id="usdcTransferLabel">
                    <i class="fas fa-exchange-alt me-2"></i>USDC Payroll Transfer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div id="transfer-alert-container"></div>

                <!-- Step 1: Transfer Details -->
                <div id="step-details">
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-user me-2"></i>Payment Information</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="text-muted small mb-1">Employee</p>
                                    <p class="h6" id="modal-user-name">--</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="text-muted small mb-1">Total Hours</p>
                                    <p class="h6" id="modal-total-hours">--</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Conversion -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-calculator me-2"></i>Amount Conversion</h6>
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">PHP</span>
                                        <input type="text" class="form-control" id="modal-php-amount" readonly>
                                    </div>
                                    <small class="text-muted d-block mt-2">Total in Philippine Peso</small>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">USDC</span>
                                        <input type="text" class="form-control" id="modal-usdc-amount" readonly>
                                    </div>
                                    <small class="text-muted d-block mt-2">Amount to send on Solana</small>
                                </div>
                            </div>

                            <div class="alert alert-info alert-sm mb-0" role="alert">
                                <small>
                                    <strong>Exchange Rate:</strong> <span id="modal-exchange-rate">1 USDC = -- PHP</span>
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Adjustments (shown only when adjustments exist) -->
                    <div class="card mb-3 border-0 shadow-sm" id="adjustments-card" style="display:none;">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-sliders-h me-2"></i>Payroll Adjustments</h6>
                            <div id="adjustments-list" class="mb-2"></div>
                            <div class="border-top pt-2">
                                <div class="d-flex justify-content-between small text-muted mb-1">
                                    <span>Gross Pay</span>
                                    <span id="modal-gross-amount">--</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-1">
                                    <span class="text-success">+ Additions</span>
                                    <span class="text-success" id="modal-adj-adds">--</span>
                                </div>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span class="text-danger">- Deductions</span>
                                    <span class="text-danger" id="modal-adj-deducts">--</span>
                                </div>
                                <div class="d-flex justify-content-between fw-semibold">
                                    <span>Net Pay (PHP)</span>
                                    <span class="text-primary" id="modal-net-php">--</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Wallet Info -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title mb-3"><i class="fas fa-wallet me-2"></i>Recipient Wallet</h6>
                            <div class="form-group">
                                <label class="form-label small">Solana Wallet Address</label>
                                <input type="text" class="form-control" id="modal-wallet-address" readonly>
                                <small class="text-muted d-block mt-2">USDC will be sent to this wallet</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Phantom Connection -->
                <div id="step-phantom" style="display: none;">
                    <div class="text-center py-4">
                        <div class="mb-4">
                            <i class="fas fa-wallet" style="font-size: 48px; color: #667eea;"></i>
                        </div>
                        <h6 class="mb-3">Connect Phantom Wallet</h6>
                        <p class="text-muted mb-4">You need to connect your Phantom wallet to approve and send this USDC transfer.</p>
                        
                        <button type="button" class="btn btn-primary btn-lg w-100" id="connect-phantom-btn">
                            <i class="fas fa-link me-2"></i>Connect Phantom Wallet
                        </button>

                        <p class="text-muted small mt-3">
                            <i class="fas fa-info-circle me-1"></i>Don't have Phantom? <a href="https://phantom.app" target="_blank">Install it here</a>
                        </p>
                    </div>
                </div>

                <!-- Step 3: Confirmation -->
                <div id="step-confirmation" style="display: none;">
                    <div class="alert alert-warning mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Important:</strong> This action cannot be undone. Please verify all details before confirming.
                    </div>

                    <div class="card mb-3 border-success border-2">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Sending</p>
                                    <p class="h5 mb-0">
                                        <span id="confirm-usdc-amount">--</span>
                                        <small>USDC</small>
                                    </p>
                                </div>
                                <div class="col-6">
                                    <p class="text-muted small mb-1">Worth</p>
                                    <p class="h5 mb-0">
                                        <span id="confirm-php-amount">--</span>
                                        <small>PHP</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="confirm-agreement" required>
                        <label class="form-check-label" for="confirm-agreement">
                            I confirm that I want to transfer this amount to the recipient wallet. This action cannot be undone.
                        </label>
                    </div>
                </div>

                <!-- Step 4: Processing -->
                <div id="step-processing" style="display: none;">
                    <div class="text-center py-5">
                        <div class="spinner-border mb-3" role="status">
                            <span class="visually-hidden">Processing...</span>
                        </div>
                        <h6>Processing Transfer</h6>
                        <p class="text-muted small">Please do not close this window. Waiting for blockchain confirmation...</p>
                    </div>
                </div>

                <!-- Step 5: Success -->
                <div id="step-success" style="display: none;">
                    <div class="text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                        </div>
                        <h6 class="mb-3">Transfer Successful!</h6>
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <p class="text-muted small mb-1">Transaction Signature</p>
                                <p class="small font-monospace text-break" id="success-transaction">--</p>
                            </div>
                        </div>
                        <p class="text-muted small">The USDC has been transferred to the wallet. Payment record has been created.</p>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="modal-cancel-btn" data-bs-dismiss="modal">Cancel</button>
                
                <button type="button" class="btn btn-primary" id="modal-next-btn" style="display: none;">
                    <i class="fas fa-arrow-right me-2"></i>Next
                </button>
                
                <button type="button" class="btn btn-primary" id="modal-confirm-btn" style="display: none;">
                    <i class="fas fa-wallet me-2"></i>Open Phantom & Transfer
                </button>
                
                <button type="button" class="btn btn-success" id="modal-complete-btn" style="display: none;" data-bs-dismiss="modal">
                    <i class="fas fa-check-circle me-2"></i>Done
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Solana Web3.js — provides window.solanaWeb3 -->
<script src="https://unpkg.com/@solana/web3.js@1.98.0/lib/index.iife.min.js"></script>

<style>
    #usdcTransferModal .card {
        border-left: 4px solid #667eea;
    }

    #usdcTransferModal .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    #usdcTransferModal .form-control[readonly] {
        background-color: #f8f9fa;
    }

    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
</style>

<script>
    // Network config injected from .env via Blade
    const solanaNetwork   = @json(env('SOLANA_NETWORK', 'mainnet-beta'));
    const solanaRpcProxyUrl = @json(route('Timekeeping.solanaRpc'));
    const usdcMintAddress = @json(env('USDC_MINT_ADDRESS', 'EPjFWaLb3gqP6Cmis3h8PVqeVtSUGhd7xMZLqcRi1Nd'));

    let usdcTransferData = {};
    let currentStep = 'details';
    let phantomConnected = false;
    let userWallet = null;
    let phantomProvider = null;
    let solanaConnection = null;

    function getPhantomProvider() {
        const { solana } = window;
        return solana && solana.isPhantom ? solana : null;
    }

    function syncConnectedWallet(wallet) {
        userWallet = wallet;
        phantomConnected = Boolean(wallet);

        if (window.bulkTransferState) {
            window.bulkTransferState.userWallet = wallet;
            window.bulkTransferState.phantomConnected = Boolean(wallet);
        }
    }

    async function ensurePhantomConnected(forcePrompt = false) {
        phantomProvider = getPhantomProvider();

        if (!phantomProvider) {
            throw new Error('Phantom wallet is not installed');
        }

        if (!forcePrompt && phantomConnected && userWallet) {
            return userWallet;
        }

        if (!forcePrompt && phantomProvider.publicKey) {
            syncConnectedWallet(phantomProvider.publicKey.toString());
            return userWallet;
        }

        if (!forcePrompt) {
            throw new Error('Phantom wallet is not connected');
        }

        const response = await phantomProvider.connect();

        syncConnectedWallet(response.publicKey.toString());
        return userWallet;
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            || document.querySelector('input[name="_token"]')?.value
            || '';
    }

    async function callSolanaRpc(method, params = []) {
        const response = await axios.post(solanaRpcProxyUrl, {
            method,
            params,
            _token: getCsrfToken()
        }, {
            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            }
        });

        if (response.data?.error) {
            throw new Error(response.data.error.message || `Unable to call ${method}.`);
        }

        return response.data?.result;
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    function getSolanaConnection() {
        if (!solanaConnection) {
            solanaConnection = {
                async getAccountInfo(publicKey) {
                    const result = await callSolanaRpc('getAccountInfo', [
                        publicKey.toString(),
                        { encoding: 'base64', commitment: 'confirmed' }
                    ]);

                    return result?.value || null;
                },
                async getBalance(publicKey) {
                    const result = await callSolanaRpc('getBalance', [
                        publicKey.toString(),
                        { commitment: 'confirmed' }
                    ]);

                    return result?.value || 0;
                },
                async getTokenAccountBalance(publicKey) {
                    const result = await callSolanaRpc('getTokenAccountBalance', [
                        publicKey.toString(),
                        { commitment: 'confirmed' }
                    ]);

                    return result?.value || null;
                },
                async getLatestBlockhash() {
                    const result = await callSolanaRpc('getLatestBlockhash', [
                        { commitment: 'confirmed' }
                    ]);

                    return {
                        blockhash: result?.value?.blockhash,
                        lastValidBlockHeight: result?.value?.lastValidBlockHeight
                    };
                },
                async simulateTransaction(transaction) {
                    const serialized = transaction.serialize({
                        requireAllSignatures: false,
                        verifySignatures: false
                    });

                    const encoded = btoa(String.fromCharCode(...serialized));
                    const result = await callSolanaRpc('simulateTransaction', [
                        encoded,
                        {
                            encoding: 'base64',
                            sigVerify: false,
                            replaceRecentBlockhash: false,
                            commitment: 'processed'
                        }
                    ]);

                    return result?.value || null;
                },
                async confirmTransaction(signature) {
                    for (let attempt = 0; attempt < 30; attempt++) {
                        const result = await callSolanaRpc('getSignatureStatuses', [
                            [signature],
                            { searchTransactionHistory: true }
                        ]);
                        const status = result?.value?.[0];

                        if (status?.err) {
                            throw new Error('Solana transaction failed during confirmation.');
                        }

                        if (status && ['confirmed', 'finalized'].includes(status.confirmationStatus)) {
                            return status;
                        }

                        await sleep(1000);
                    }

                    throw new Error('Timed out while waiting for Solana confirmation.');
                }
            };
        }

        return solanaConnection;
    }

    phantomProvider = getPhantomProvider();
    if (phantomProvider) {
        phantomProvider.on?.('accountChanged', (publicKey) => {
            syncConnectedWallet(publicKey ? publicKey.toString() : null);
        });
        phantomProvider.on?.('disconnect', () => {
            syncConnectedWallet(null);
        });

        if (phantomProvider.publicKey) {
            syncConnectedWallet(phantomProvider.publicKey.toString());
        }
    }

    window.ensurePayrollPhantomConnected = ensurePhantomConnected;
    window.getPayrollSolanaConnection = getSolanaConnection;

    // Initialize modal
    const usdcModal = document.getElementById('usdcTransferModal');
    if (usdcModal) {
        usdcModal.addEventListener('show.bs.modal', function (e) {
            const button = e.relatedTarget;
            const userId = button.getAttribute('data-user-id');
            const dateFrom = button.getAttribute('data-date-from');
            const dateTo = button.getAttribute('data-date-to');
            const members = button.getAttribute('data-members') || 'ALL';

            loadTransferData(userId, dateFrom, dateTo, members);
        });
    }

    // Load transfer data
    function loadTransferData(userId, dateFrom, dateTo, members) {
        axios.post("{{ route('Timekeeping.prepareTransfer', '') }}/" + userId, {
            date_from: dateFrom,
            date_to: dateTo,
            members: members,
            _token: document.querySelector('input[name="_token"]').value
        })
        .then(response => {
            if (response.data.success) {
                usdcTransferData = response.data.data;
                displayTransferDetails();
                showStep('details');
            }
        })
        .catch(error => {
            showAlert('Error', 'Failed to load transfer data', 'error');
            console.error(error);
        });
    }

    // Display transfer details
    function displayTransferDetails() {
        const fmt = (n) => Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        document.getElementById('modal-user-name').textContent    = usdcTransferData.user_name;
        document.getElementById('modal-total-hours').textContent  = usdcTransferData.total_hours + ' hrs';
        document.getElementById('modal-php-amount').value         = '₱' + fmt(usdcTransferData.net_php_amount ?? usdcTransferData.total_php_amount);
        document.getElementById('modal-usdc-amount').value        = usdcTransferData.total_usdc_amount.toFixed(2);
        document.getElementById('modal-exchange-rate').textContent = usdcTransferData.exchange_rate.display;
        document.getElementById('modal-wallet-address').value     = usdcTransferData.wallet_address || '(Not set)';

        // Adjustments section
        const adjCard    = document.getElementById('adjustments-card');
        const adjustments = usdcTransferData.adjustments || [];
        const adjAdds    = usdcTransferData.adj_adds    || 0;
        const adjDeducts = usdcTransferData.adj_deducts || 0;

        if (adjustments.length > 0) {
            adjCard.style.display = 'block';

            let html = '';
            adjustments.forEach(function(adj) {
                const sign  = adj.type === 'add' ? '+' : '-';
                const color = adj.type === 'add' ? 'text-success' : 'text-danger';
                html += '<div class="d-flex justify-content-between small mb-1">'
                      + '<span class="' + color + '">' + sign + ' ' + adj.description + '</span>'
                      + '<span class="' + color + ' fw-medium">' + sign + '₱' + fmt(adj.amount) + '</span>'
                      + '</div>';
            });
            document.getElementById('adjustments-list').innerHTML = html;

            document.getElementById('modal-gross-amount').textContent  = '₱' + fmt(usdcTransferData.gross_php_amount ?? usdcTransferData.total_php_amount);
            document.getElementById('modal-adj-adds').textContent      = '+₱' + fmt(adjAdds);
            document.getElementById('modal-adj-deducts').textContent   = '-₱' + fmt(adjDeducts);
            document.getElementById('modal-net-php').textContent       = '₱' + fmt(usdcTransferData.net_php_amount ?? usdcTransferData.total_php_amount);
        } else {
            adjCard.style.display = 'none';
        }

        // Confirmation step values (uses net amount)
        document.getElementById('confirm-usdc-amount').textContent = usdcTransferData.total_usdc_amount.toFixed(2);
        document.getElementById('confirm-php-amount').textContent  = '₱' + fmt(usdcTransferData.net_php_amount ?? usdcTransferData.total_php_amount);
    }

    // Show step
    function showStep(step) {
        currentStep = step;
        
        // Hide all steps
        document.getElementById('step-details').style.display = 'none';
        document.getElementById('step-phantom').style.display = 'none';
        document.getElementById('step-confirmation').style.display = 'none';
        document.getElementById('step-processing').style.display = 'none';
        document.getElementById('step-success').style.display = 'none';

        // Hide all buttons
        document.getElementById('modal-cancel-btn').style.display = 'block';
        document.getElementById('modal-next-btn').style.display = 'none';
        document.getElementById('modal-confirm-btn').style.display = 'none';
        document.getElementById('modal-complete-btn').style.display = 'none';

        // Show current step
        if (step === 'details') {
            document.getElementById('step-details').style.display = 'block';
            document.getElementById('modal-next-btn').style.display = 'block';
        } else if (step === 'phantom') {
            document.getElementById('step-phantom').style.display = 'block';
        } else if (step === 'confirmation') {
            document.getElementById('step-confirmation').style.display = 'block';
            document.getElementById('modal-confirm-btn').style.display = 'block';
        } else if (step === 'processing') {
            document.getElementById('step-processing').style.display = 'block';
            document.getElementById('modal-cancel-btn').style.display = 'none';
        } else if (step === 'success') {
            document.getElementById('step-success').style.display = 'block';
            document.getElementById('modal-complete-btn').style.display = 'block';
            document.getElementById('modal-cancel-btn').style.display = 'none';
        }
    }

    // Next button
    document.getElementById('modal-next-btn').addEventListener('click', async function () {
        if (!usdcTransferData.wallet_address) {
            showAlert('Error', 'Wallet address is not set for this user', 'error');
            return;
        }

        if (!isValidSolanaPublicKey(usdcTransferData.wallet_address)) {
            showAlert('Error', 'The recipient wallet address is not a valid Solana address.', 'error');
            return;
        }

        try {
            await ensurePhantomConnected(false);
            showStep('confirmation');
        } catch (error) {
            showStep('phantom');
        }
    });

    // Connect Phantom
    document.getElementById('connect-phantom-btn').addEventListener('click', async function () {
        try {
            await ensurePhantomConnected(true);
            showStep('confirmation');
            showAlert('Success', 'Phantom wallet connected!', 'success');
        } catch (error) {
            showAlert('Error', error.message || 'Failed to connect Phantom wallet', 'error');
            console.error(error);
        }
    });

    // Confirm button
    document.getElementById('modal-confirm-btn').addEventListener('click', async function () {
        if (!document.getElementById('confirm-agreement').checked) {
            showAlert('Error', 'Please confirm the agreement before proceeding', 'warning');
            return;
        }

        try {
            await ensurePhantomConnected(false);
        } catch (error) {
            showAlert('Error', 'Phantom wallet is not connected', 'error');
            showStep('phantom');
            return;
        }

        showStep('processing');
        processTransferViaPhantom();
    });

    // Process real USDC transfer via Phantom wallet + Solana Web3.js
    async function processTransferViaPhantom() {
        try {
            if (!window.solanaWeb3) {
                throw new Error('Solana Web3 library failed to load. Please refresh the page and try again.');
            }

            const {
                Connection, PublicKey, Transaction, TransactionInstruction, SystemProgram
            } = solanaWeb3;

            const TOKEN_PROGRAM_ID           = new PublicKey('TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA');
            const ASSOCIATED_TOKEN_PROGRAM_ID = new PublicKey('ATokenGPvbdGVxr1b2hvZbsiqW5xWH25efTNsLJe1bJs');
            const SYSVAR_RENT_PUBKEY          = new PublicKey('SysvarRent111111111111111111111111111111111');

            const connection         = getSolanaConnection();
            const usdcMint           = new PublicKey(usdcMintAddress);
            const senderPublicKey    = new PublicKey(userWallet);
            const recipientPublicKey = new PublicKey(usdcTransferData.wallet_address);

            // Derive Associated Token Accounts
            function deriveATA(owner, mint) {
                const [address] = PublicKey.findProgramAddressSync(
                    [owner.toBuffer(), TOKEN_PROGRAM_ID.toBuffer(), mint.toBuffer()],
                    ASSOCIATED_TOKEN_PROGRAM_ID
                );
                return address;
            }

            const senderATA    = deriveATA(senderPublicKey, usdcMint);
            const recipientATA = deriveATA(recipientPublicKey, usdcMint);

            // USDC uses 6 decimal places
            const rawAmount = BigInt(Math.round(usdcTransferData.total_usdc_amount * 1_000_000));
            await validateSenderCanPayUsdc(connection, senderPublicKey, senderATA, rawAmount);

            const transaction = new Transaction();

            // Create recipient ATA if it doesn't exist (payer = sender)
            let recipientAccountInfo;
            try {
                recipientAccountInfo = await connection.getAccountInfo(recipientATA);
            } catch (rpcError) {
                throw normalizeSolanaRpcError(rpcError, 'check the recipient token account');
            }

            if (!recipientAccountInfo) {
                transaction.add(new TransactionInstruction({
                    keys: [
                        { pubkey: senderPublicKey,         isSigner: true,  isWritable: true  },
                        { pubkey: recipientATA,            isSigner: false, isWritable: true  },
                        { pubkey: recipientPublicKey,      isSigner: false, isWritable: false },
                        { pubkey: usdcMint,                isSigner: false, isWritable: false },
                        { pubkey: SystemProgram.programId, isSigner: false, isWritable: false },
                        { pubkey: TOKEN_PROGRAM_ID,        isSigner: false, isWritable: false },
                        { pubkey: SYSVAR_RENT_PUBKEY,      isSigner: false, isWritable: false },
                    ],
                    programId: ASSOCIATED_TOKEN_PROGRAM_ID,
                    data: new Uint8Array(0),
                }));
            }

            // SPL Token Transfer instruction (index 3), amount as 8-byte little-endian uint64
            const transferData = new Uint8Array(9);
            transferData[0] = 3; // instruction index
            let rem = rawAmount;
            for (let i = 1; i <= 8; i++) {
                transferData[i] = Number(rem & BigInt(0xFF));
                rem >>= BigInt(8);
            }

            transaction.add(new TransactionInstruction({
                keys: [
                    { pubkey: senderATA,         isSigner: false, isWritable: true  },
                    { pubkey: recipientATA,       isSigner: false, isWritable: true  },
                    { pubkey: senderPublicKey,    isSigner: true,  isWritable: false },
                ],
                programId: TOKEN_PROGRAM_ID,
                data: transferData,
            }));

            let blockhash;
            try {
                ({ blockhash } = await connection.getLatestBlockhash());
            } catch (rpcError) {
                throw normalizeSolanaRpcError(rpcError, 'fetch the latest blockhash');
            }

            transaction.recentBlockhash = blockhash;
            transaction.feePayer = senderPublicKey;

            assertSingleTransactionSigner(transaction, senderPublicKey);
            await simulatePayrollTransaction(transaction, connection);

            showAlert('Confirm in Phantom', 'Phantom will open now. Review and approve the transfer in your wallet.', 'info');

            // Phantom signs + broadcasts the transaction
            const { signature } = await window.solana.signAndSendTransaction(transaction);

            // Wait for on-chain confirmation before saving
            try {
                await connection.confirmTransaction(signature, 'confirmed');
            } catch (rpcError) {
                throw normalizeSolanaRpcError(rpcError, 'confirm the transaction');
            }

            submitTransferToServer(signature);

        } catch (error) {
            console.error('Phantom transfer failed:', error);
            showAlert('Error', error.message || 'Transfer failed. Check console for details.', 'error');
            showStep('confirmation');
        }
    }

    async function simulatePayrollTransaction(transaction, connection) {
        let simulation;

        try {
            simulation = await connection.simulateTransaction(transaction);
        } catch (rpcError) {
            throw normalizeSolanaRpcError(rpcError, 'simulate the transaction');
        }

        if (simulation?.err) {
            const err = typeof simulation.err === 'string'
                ? simulation.err
                : JSON.stringify(simulation.err);
            const logs = Array.isArray(simulation.logs) ? simulation.logs.slice(-4).join(' ') : '';
            throw new Error(
                'Solana preflight simulation failed before Phantom could approve it. ' +
                `Reason: ${err || 'Unknown simulation error'}. ` +
                (logs ? 'Last logs: ' + logs : 'Please check sender USDC balance, SOL fee balance, recipient wallet, and USDC mint address.')
            );
        }
    }

    async function validateSenderCanPayUsdc(connection, senderPublicKey, senderATA, requiredRawAmount) {
        const senderTokenAccount = await connection.getAccountInfo(senderATA);

        if (!senderTokenAccount) {
            throw new Error(
                'The connected Phantom wallet does not have a USDC token account on Solana mainnet. ' +
                'Connect the wallet that holds your payroll USDC, or send USDC to this wallet first.'
            );
        }

        let tokenBalance;
        try {
            tokenBalance = await connection.getTokenAccountBalance(senderATA);
        } catch (rpcError) {
            throw normalizeSolanaRpcError(rpcError, 'check sender USDC balance');
        }

        const availableRawAmount = BigInt(tokenBalance?.amount || '0');
        if (availableRawAmount < requiredRawAmount) {
            throw new Error(
                `Insufficient USDC in the connected Phantom wallet. ` +
                `Available: ${formatUsdcRawAmount(availableRawAmount)} USDC. ` +
                `Required: ${formatUsdcRawAmount(requiredRawAmount)} USDC.`
            );
        }

        const solLamports = await connection.getBalance(senderPublicKey);
        if (solLamports <= 0) {
            throw new Error(
                'The connected Phantom wallet has no SOL for network fees. ' +
                'Add a small amount of SOL before transferring USDC.'
            );
        }
    }

    function formatUsdcRawAmount(rawAmount) {
        const raw = BigInt(rawAmount);
        const whole = raw / BigInt(1_000_000);
        const fractional = String(raw % BigInt(1_000_000)).padStart(6, '0').replace(/0+$/, '');
        return fractional ? `${whole}.${fractional}` : `${whole}`;
    }

    function assertSingleTransactionSigner(transaction, expectedSigner) {
        const signerSet = new Set();

        transaction.instructions.forEach((instruction) => {
            instruction.keys
                .filter((key) => key.isSigner)
                .forEach((key) => signerSet.add(key.pubkey.toString()));
        });

        if (transaction.feePayer) {
            signerSet.add(transaction.feePayer.toString());
        }

        if (signerSet.size !== 1 || !signerSet.has(expectedSigner.toString())) {
            throw new Error('Phantom requires this payroll transaction to use only the connected wallet as signer.');
        }
    }

    function isValidSolanaPublicKey(address) {
        try {
            if (!window.solanaWeb3 || !address) {
                return false;
            }

            new solanaWeb3.PublicKey(address);
            return true;
        } catch (error) {
            return false;
        }
    }

    function normalizeSolanaRpcError(error, action) {
        const message = String(error?.message || error || '');
        const isForbidden = message.includes('403') || message.toLowerCase().includes('access forbidden');

        if (isForbidden) {
            return new Error(
                `Solana RPC rejected the request while trying to ${action}. ` +
                `Check the server SOLANA_RPC_URL/API key for ${solanaNetwork}.`
            );
        }

        return error instanceof Error ? error : new Error(message || `Unable to ${action}.`);
    }

    // Submit transfer to server
    function submitTransferToServer(transactionSignature) {
        axios.post("{{ route('Timekeeping.processUsdcTransfer') }}", {
            user_id: usdcTransferData.user_id,
            transaction_signature: transactionSignature,
            usdc_amount: usdcTransferData.total_usdc_amount,
            php_amount: usdcTransferData.total_php_amount,
            date_from: usdcTransferData.date_from,
            date_to: usdcTransferData.date_to,
            _token: document.querySelector('input[name="_token"]').value
        })
        .then(response => {
            if (response.data.success) {
                document.getElementById('success-transaction').textContent = transactionSignature;
                showStep('success');
                showAlert('Success', 'USDC transfer completed successfully!', 'success');
                
                // Reload page after 3 seconds
                setTimeout(() => {
                    location.reload();
                }, 3000);
            }
        })
        .catch(error => {
            showAlert('Error', error.response?.data?.message || 'Failed to process transfer', 'error');
            console.error(error);
            showStep('confirmation');
        });
    }

    // Show alert
    function showAlert(title, message, type = 'info') {
        const alertContainer = document.getElementById('transfer-alert-container');
        const alertClass = `alert-${type === 'error' ? 'danger' : type}`;
        const icon = type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : type === 'warning' ? 'exclamation-triangle' : 'info-circle';
        const safeTitle = escapeHtml(title);
        const safeMessage = escapeHtml(message);
        
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${icon} me-2"></i>
                <strong>${safeTitle}:</strong> ${safeMessage}
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

    function escapeHtml(value) {
        const div = document.createElement('div');
        div.textContent = value == null ? '' : String(value);
        return div.innerHTML;
    }
</script>
