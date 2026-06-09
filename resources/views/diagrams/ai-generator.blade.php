@extends('layouts.header')

@section('css')
<style>
    .wf-shell {
        min-height: calc(100vh - 190px);
    }

    .wf-card {
        background: #fff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
    }

    .wf-setup {
        max-width: 980px;
        margin: 0 auto;
        padding: 28px;
    }

    .wf-setup-grid {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.8fr);
        gap: 24px;
    }

    .wf-title {
        color: #111827;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .wf-muted {
        color: #667085;
        font-size: 13px;
    }

    .wf-example {
        border: 1px solid #dbe5f0;
        background: #fff;
        border-radius: 8px;
        color: #334155;
        display: block;
        font-size: 12px;
        margin-bottom: 8px;
        padding: 10px 12px;
        text-align: left;
        width: 100%;
    }

    .wf-example:hover {
        border-color: #2563eb;
        color: #1d4ed8;
    }

    .wf-builder {
        display: none;
        grid-template-columns: 300px minmax(0, 1fr) 280px;
        gap: 14px;
        min-height: calc(100vh - 190px);
    }

    .wf-panel {
        background: #fff;
        border: 1px solid #e5eaf1;
        border-radius: 8px;
        overflow: hidden;
    }

    .wf-panel-head {
        border-bottom: 1px solid #eef2f7;
        padding: 14px 16px;
    }

    .wf-panel-head h6 {
        color: #111827;
        font-weight: 700;
        margin: 0;
    }

    .wf-panel-body {
        padding: 14px;
    }

    .wf-chat-log {
        background: #f8fafc;
        height: 360px;
        overflow-y: auto;
        padding: 12px;
    }

    .wf-message {
        border-radius: 8px;
        font-size: 13px;
        line-height: 1.45;
        margin-bottom: 10px;
        max-width: 94%;
        padding: 10px 12px;
    }

    .wf-message.assistant {
        background: #fff;
        border: 1px solid #e5e7eb;
        color: #1f2937;
    }

    .wf-message.user {
        background: #2563eb;
        color: #fff;
        margin-left: auto;
    }

    .wf-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .wf-toolbar {
        align-items: center;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        gap: 8px;
        justify-content: space-between;
        padding: 10px 12px;
    }

    .wf-canvas-wrap {
        background-color: #f8fafc;
        background-image: linear-gradient(#edf2f7 1px, transparent 1px), linear-gradient(90deg, #edf2f7 1px, transparent 1px);
        background-size: 24px 24px;
        flex: 1;
        min-height: 680px;
        overflow: auto;
        position: relative;
    }

    #workflowCanvas {
        display: block;
        min-height: 820px;
        min-width: 1500px;
        transform-origin: 0 0;
    }

    .wf-zoom-tools {
        align-items: center;
        display: inline-flex;
        gap: 6px;
        margin-left: 6px;
    }

    .wf-zoom-level {
        color: #475569;
        display: inline-block;
        font-size: 12px;
        min-width: 44px;
        text-align: center;
    }

    .wf-node {
        cursor: move;
    }

    .wf-node .shape-fill {
        fill: #ffffff;
        stroke: #334155;
        stroke-width: 1.6;
        filter: drop-shadow(0 2px 3px rgba(15, 23, 42, 0.12));
        vector-effect: non-scaling-stroke;
    }

    .wf-node.type-terminator .shape-fill {
        fill: #d5e8d4;
        stroke: #82b366;
    }

    .wf-node.type-process .shape-fill {
        fill: #dae8fc;
        stroke: #6c8ebf;
    }

    .wf-node.type-decision .shape-fill {
        fill: #fff2cc;
        stroke: #d6b656;
    }

    .wf-node.type-io .shape-fill {
        fill: #e1d5e7;
        stroke: #9673a6;
    }

    .wf-node.type-database .shape-fill {
        fill: #f8cecc;
        stroke: #b85450;
    }

    .wf-node.type-document .shape-fill {
        fill: #ffe6cc;
        stroke: #d79b00;
    }

    .wf-node.type-connector .shape-fill {
        fill: #f5f5f5;
        stroke: #666666;
    }

    .wf-node.type-subprocess .shape-fill,
    .wf-node.type-preparation .shape-fill,
    .wf-node.type-manual-input .shape-fill,
    .wf-node.type-delay .shape-fill,
    .wf-node.type-offpage .shape-fill {
        fill: #f5f5f5;
        stroke: #666666;
    }

    .wf-node.type-trigger .shape-fill {
        fill: #d5e8d4;
        stroke: #82b366;
    }

    .wf-node.type-app .shape-fill {
        fill: #dae8fc;
        stroke: #6c8ebf;
    }

    .wf-node.type-api,
    .wf-node.type-webhook {
        font-family: Arial, Helvetica, sans-serif;
    }

    .wf-node.type-api .shape-fill,
    .wf-node.type-webhook .shape-fill {
        fill: #e1d5e7;
        stroke: #9673a6;
    }

    .wf-node.type-email .shape-fill {
        fill: #fff2cc;
        stroke: #d6b656;
    }

    .wf-node.type-approval .shape-fill {
        fill: #ffe6cc;
        stroke: #d79b00;
    }

    .wf-node.type-error .shape-fill {
        fill: #f8cecc;
        stroke: #b85450;
    }

    .wf-node.selected .shape-fill {
        stroke: #2563eb;
        stroke-width: 3;
        filter: drop-shadow(0 0 0 rgba(0, 0, 0, 0));
    }

    .wf-node.pending-connect .shape-fill {
        stroke: #16a34a;
        stroke-width: 3;
    }

    .wf-node text {
        dominant-baseline: middle;
        fill: #111827;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 12.5px;
        font-weight: 500;
        pointer-events: none;
        text-anchor: middle;
    }

    .edge-path {
        fill: none;
        stroke: #334155;
        stroke-linecap: round;
        stroke-linejoin: round;
        stroke-width: 1.8;
        vector-effect: non-scaling-stroke;
    }

    .edge-label {
        fill: #334155;
        font-size: 12px;
        font-weight: 700;
        paint-order: stroke;
        stroke: #f8fafc;
        stroke-linejoin: round;
        stroke-width: 5px;
    }

    .wf-port {
        cursor: crosshair;
        display: none;
        fill: #ffffff;
        stroke: #2563eb;
        stroke-width: 1.6;
    }

    .wf-node:hover .wf-port,
    .wf-node.selected .wf-port,
    .wf-node.pending-connect .wf-port {
        display: block;
    }

    .wf-node:hover .shape-fill {
        stroke-width: 2.4;
    }

    .wf-shape-btn {
        align-items: center;
        background: #fff;
        border: 1px solid #dbe5f0;
        border-radius: 8px;
        color: #334155;
        display: flex;
        gap: 10px;
        margin-bottom: 8px;
        padding: 9px 10px;
        text-align: left;
        width: 100%;
    }

    .wf-shape-btn:hover {
        border-color: #2563eb;
        color: #1d4ed8;
    }

    .wf-dot {
        align-items: center;
        border: 2px solid #1f2937;
        display: inline-flex;
        height: 26px;
        justify-content: center;
        width: 34px;
    }

    .wf-dot.terminator { border-radius: 999px; }
    .wf-dot.process { border-radius: 3px; }
    .wf-dot.decision { height: 24px; transform: rotate(45deg); width: 24px; }
    .wf-dot.io { clip-path: polygon(18% 0, 100% 0, 82% 100%, 0 100%); }
    .wf-dot.database { border-radius: 50% 50% 6px 6px / 24% 24% 6px 6px; }
    .wf-dot.document { border-radius: 3px 3px 14px 14px; }
    .wf-dot.connector { border-radius: 50%; height: 26px; width: 26px; }
    .wf-dot.subprocess { border-left: 5px double #1f2937; border-right: 5px double #1f2937; border-radius: 3px; }
    .wf-dot.manual-input { clip-path: polygon(0 28%, 100% 0, 100% 100%, 0 100%); }
    .wf-dot.preparation { clip-path: polygon(18% 0, 82% 0, 100% 50%, 82% 100%, 18% 100%, 0 50%); }
    .wf-dot.delay { border-radius: 0 999px 999px 0; }
    .wf-dot.offpage { clip-path: polygon(0 0, 100% 0, 100% 72%, 50% 100%, 0 72%); }
    .wf-dot.trigger { border-radius: 999px; background: #d5e8d4; }
    .wf-dot.app { border-radius: 6px; background: #dae8fc; }
    .wf-dot.api { clip-path: polygon(10% 0, 90% 0, 100% 50%, 90% 100%, 10% 100%, 0 50%); background: #e1d5e7; }
    .wf-dot.webhook { border-radius: 50%; background: #e1d5e7; }
    .wf-dot.email { clip-path: polygon(0 0, 100% 0, 100% 100%, 0 100%, 50% 45%); background: #fff2cc; }
    .wf-dot.approval { transform: rotate(45deg); background: #ffe6cc; height: 24px; width: 24px; }
    .wf-dot.error { border-radius: 50%; background: #f8cecc; }

    .wf-status {
        color: #64748b;
        font-size: 12px;
    }

    .wf-empty {
        color: #667085;
        left: 50%;
        pointer-events: none;
        position: absolute;
        top: 50%;
        transform: translate(-50%, -50%);
    }

    @media (max-width: 1199px) {
        .wf-builder {
            grid-template-columns: 1fr;
        }

        .wf-setup-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="wf-shell">
    <section class="wf-card wf-setup" id="setupScreen">
        <div class="mb-4">
            <h4 class="wf-title">Create a workflow diagram</h4>
            <p class="wf-muted mb-0">Ask for a process in plain language. The system can generate a full starter workflow even when you only give the topic.</p>
        </div>

        <form id="setupForm" class="wf-setup-grid">
            <div>
                <label class="form-label fw-semibold">What process do you want to map?</label>
                <textarea class="form-control mb-3" id="initialPrompt" rows="9" placeholder="Example: Create me a basic process for recruitment."></textarea>

                <div class="mb-3">
                    <div class="wf-muted mb-2">Examples</div>
                    <button type="button" class="wf-example">Create me a basic process for recruitment.</button>
                    <button type="button" class="wf-example">Create a standard onboarding process for a new employee.</button>
                    <button type="button" class="wf-example">Create an automation that connects GoHighLevel to Slack when a new lead submits a form.</button>
                    <button type="button" class="wf-example">When a customer submits a refund request, our team reviews it. If under $50, approve automatically. Otherwise, escalate to a manager.</button>
                    <button type="button" class="wf-example">A lead fills out a form, sales checks the CRM, qualifies the lead, then sends an onboarding document if approved.</button>
                    <button type="button" class="wf-example">An employee submits an expense report. Finance reviews it. If complete, store it and pay it. If incomplete, ask for corrections.</button>
                </div>
            </div>

            <aside>
                <label class="form-label fw-semibold">Knowledge source</label>
                <select class="form-select mb-3" id="knowledgeSource">
                    <option value="template">Built-in process library</option>
                    <option value="llm">LLM/API generation</option>
                    <option value="online">Online research + LLM</option>
                </select>

                <label class="form-label fw-semibold">Generation method</label>
                <select class="form-select mb-3" id="aiProvider">
                    <option value="prototype">Prototype parser</option>
                    <option value="openai">OpenAI API</option>
                    <option value="anthropic">Claude API</option>
                    <option value="custom">Custom endpoint</option>
                </select>

                <label class="form-label fw-semibold">API key</label>
                <input type="password" class="form-control mb-3" id="apiKey" placeholder="Optional for prototype mode">

                <label class="form-label fw-semibold">Model or endpoint</label>
                <input type="text" class="form-control mb-3" id="modelName" placeholder="Example: gpt-4.1-mini or https://...">

                <div class="alert alert-info small mb-3">
                    Prototype mode uses built-in business process patterns. LLM/API and online options are captured for backend integration, then the same editable builder opens.
                </div>

                <button class="btn btn-primary w-100" type="submit">
                    <i class="ri-sparkling-2-line align-middle"></i> Generate diagram
                </button>
            </aside>
        </form>
    </section>

    <section class="wf-builder" id="builderScreen">
        <aside class="wf-panel">
            <div class="wf-panel-head">
                <h6>Assistant</h6>
            </div>
            <div class="wf-chat-log" id="messages"></div>
            <form class="wf-panel-body" id="refineForm">
                <textarea class="form-control mb-2" id="refinePrompt" rows="4" placeholder="Ask for a change, like: Add a notification step after approval"></textarea>
                <button class="btn btn-primary w-100" type="submit">Update diagram</button>
                <div class="wf-status mt-2" id="chatStatus">Ready</div>
            </form>
        </aside>

        <main class="wf-panel wf-main">
            <div class="wf-toolbar">
                <div class="btn-group">
                    <button class="btn btn-light btn-sm" type="button" id="backBtn"><i class="ri-arrow-left-line"></i> Question</button>
                    <button class="btn btn-light btn-sm" type="button" id="layoutBtn"><i class="ri-node-tree"></i> Arrange</button>
                    <button class="btn btn-light btn-sm" type="button" id="connectBtn"><i class="ri-git-branch-line"></i> Connect</button>
                    <button class="btn btn-light btn-sm" type="button" id="deleteBtn"><i class="ri-delete-bin-line"></i> Delete</button>
                    <button class="btn btn-light btn-sm" type="button" id="clearBtn"><i class="ri-close-circle-line"></i> Clear</button>
                </div>
                <div class="wf-zoom-tools">
                    <button class="btn btn-light btn-sm" type="button" id="zoomOutBtn"><i class="ri-subtract-line"></i></button>
                    <span class="wf-zoom-level" id="zoomLevel">100%</span>
                    <button class="btn btn-light btn-sm" type="button" id="zoomInBtn"><i class="ri-add-line"></i></button>
                    <button class="btn btn-light btn-sm" type="button" id="zoomFitBtn">Fit</button>
                    <button class="btn btn-light btn-sm" type="button" id="zoomResetBtn">100%</button>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" type="button" id="exportPngBtn">PNG</button>
                    <button class="btn btn-outline-primary btn-sm" type="button" id="exportSvgBtn">SVG</button>
                    <button class="btn btn-outline-primary btn-sm" type="button" id="exportPdfBtn">PDF</button>
                </div>
            </div>
            <div class="wf-canvas-wrap">
                <div class="wf-empty" id="emptyState">Generate or add a step to begin.</div>
                <svg id="workflowCanvas" xmlns="http://www.w3.org/2000/svg" width="1500" height="900" role="img" aria-label="Generated workflow diagram">
                    <defs>
                        <marker id="arrowHead" viewBox="0 0 10 10" refX="8" refY="5" markerWidth="8" markerHeight="8" orient="auto-start-reverse">
                            <path d="M 0 0 L 10 5 L 0 10 z" fill="#334155"></path>
                        </marker>
                    </defs>
                    <g id="zoomLayer">
                        <g id="edgesLayer"></g>
                        <g id="nodesLayer"></g>
                    </g>
                </svg>
            </div>
        </main>

        <aside class="wf-panel">
            <div class="wf-panel-head">
                <h6>Add a step</h6>
            </div>
            <div class="wf-panel-body">
                <div class="wf-muted mb-2">Flowchart</div>
                <button type="button" class="wf-shape-btn" data-shape="terminator"><span class="wf-dot terminator"></span> Start or End</button>
                <button type="button" class="wf-shape-btn" data-shape="process"><span class="wf-dot process"></span> Action</button>
                <button type="button" class="wf-shape-btn" data-shape="decision"><span class="wf-dot decision"></span> Branch</button>
                <button type="button" class="wf-shape-btn" data-shape="io"><span class="wf-dot io"></span> Form or output</button>
                <button type="button" class="wf-shape-btn" data-shape="database"><span class="wf-dot database"></span> Saved data</button>
                <button type="button" class="wf-shape-btn" data-shape="document"><span class="wf-dot document"></span> Document</button>
                <button type="button" class="wf-shape-btn" data-shape="connector"><span class="wf-dot connector"></span> Connector</button>

                <div class="wf-muted mt-3 mb-2">More Visio-style</div>
                <button type="button" class="wf-shape-btn" data-shape="subprocess"><span class="wf-dot subprocess"></span> Sub-process</button>
                <button type="button" class="wf-shape-btn" data-shape="manual-input"><span class="wf-dot manual-input"></span> Manual input</button>
                <button type="button" class="wf-shape-btn" data-shape="preparation"><span class="wf-dot preparation"></span> Preparation</button>
                <button type="button" class="wf-shape-btn" data-shape="delay"><span class="wf-dot delay"></span> Delay</button>
                <button type="button" class="wf-shape-btn" data-shape="offpage"><span class="wf-dot offpage"></span> Off-page link</button>

                <div class="wf-muted mt-3 mb-2">Automation systems</div>
                <button type="button" class="wf-shape-btn" data-shape="trigger"><span class="wf-dot trigger"></span> Trigger</button>
                <button type="button" class="wf-shape-btn" data-shape="app"><span class="wf-dot app"></span> App / system</button>
                <button type="button" class="wf-shape-btn" data-shape="api"><span class="wf-dot api"></span> API call</button>
                <button type="button" class="wf-shape-btn" data-shape="webhook"><span class="wf-dot webhook"></span> Webhook</button>
                <button type="button" class="wf-shape-btn" data-shape="email"><span class="wf-dot email"></span> Email / message</button>
                <button type="button" class="wf-shape-btn" data-shape="approval"><span class="wf-dot approval"></span> Approval</button>
                <button type="button" class="wf-shape-btn" data-shape="error"><span class="wf-dot error"></span> Error path</button>

                <hr>

                <label class="form-label fw-semibold">Selected text</label>
                <textarea class="form-control mb-2" id="selectedLabel" rows="3" placeholder="Select an item"></textarea>

                <label class="form-label fw-semibold">Selected type</label>
                <select class="form-select mb-3" id="selectedType">
                    <option value="terminator">Start or End</option>
                    <option value="process">Action</option>
                    <option value="decision">Branch</option>
                    <option value="io">Form or output</option>
                    <option value="database">Saved data</option>
                    <option value="document">Document</option>
                    <option value="connector">Connector</option>
                    <option value="subprocess">Sub-process</option>
                    <option value="manual-input">Manual input</option>
                    <option value="preparation">Preparation</option>
                    <option value="delay">Delay</option>
                    <option value="offpage">Off-page link</option>
                    <option value="trigger">Trigger</option>
                    <option value="app">App / system</option>
                    <option value="api">API call</option>
                    <option value="webhook">Webhook</option>
                    <option value="email">Email / message</option>
                    <option value="approval">Approval</option>
                    <option value="error">Error path</option>
                </select>

                <button class="btn btn-outline-primary w-100 mb-2" type="button" id="applyNodeBtn">Apply changes</button>
                <button class="btn btn-outline-secondary w-100" type="button" id="insertAfterBtn">Add after selected</button>
            </div>
        </aside>
    </section>
</div>
@endsection

@section('js')
@verbatim
<script>
(() => {
    const STORAGE_KEY = 'saltiii-ai-workflow-n8n-prototype';
    const setupScreen = document.getElementById('setupScreen');
    const builderScreen = document.getElementById('builderScreen');
    const setupForm = document.getElementById('setupForm');
    const initialPrompt = document.getElementById('initialPrompt');
    const knowledgeSource = document.getElementById('knowledgeSource');
    const aiProvider = document.getElementById('aiProvider');
    const apiKey = document.getElementById('apiKey');
    const modelName = document.getElementById('modelName');
    const messages = document.getElementById('messages');
    const refineForm = document.getElementById('refineForm');
    const refinePrompt = document.getElementById('refinePrompt');
    const chatStatus = document.getElementById('chatStatus');
    const svg = document.getElementById('workflowCanvas');
    const canvasWrap = document.querySelector('.wf-canvas-wrap');
    const zoomLayer = document.getElementById('zoomLayer');
    const zoomLevel = document.getElementById('zoomLevel');
    const nodesLayer = document.getElementById('nodesLayer');
    const edgesLayer = document.getElementById('edgesLayer');
    const emptyState = document.getElementById('emptyState');
    const selectedLabel = document.getElementById('selectedLabel');
    const selectedType = document.getElementById('selectedType');
    const connectBtn = document.getElementById('connectBtn');

    const shapeSizes = {
        terminator: { w: 160, h: 62 },
        process: { w: 190, h: 70 },
        decision: { w: 150, h: 110 },
        io: { w: 190, h: 70 },
        database: { w: 170, h: 82 },
        document: { w: 190, h: 82 },
        connector: { w: 58, h: 58 },
        subprocess: { w: 190, h: 70 },
        'manual-input': { w: 190, h: 70 },
        preparation: { w: 190, h: 70 },
        delay: { w: 160, h: 70 },
        offpage: { w: 150, h: 90 },
        trigger: { w: 170, h: 62 },
        app: { w: 190, h: 74 },
        api: { w: 190, h: 76 },
        webhook: { w: 170, h: 76 },
        email: { w: 180, h: 76 },
        approval: { w: 150, h: 110 },
        error: { w: 170, h: 76 }
    };

    let diagram = { nodes: [], edges: [], settings: {} };
    let selectedNodeId = null;
    let dragState = null;
    let connectMode = false;
    let connectFromId = null;
    let zoom = 1;
    const minZoom = 0.3;
    const maxZoom = 1.8;

    document.querySelectorAll('.wf-example').forEach(button => {
        button.addEventListener('click', () => {
            initialPrompt.value = button.textContent.trim();
            initialPrompt.focus();
        });
    });

    setupForm.addEventListener('submit', event => {
        event.preventDefault();
        const prompt = initialPrompt.value.trim();
        if (!prompt) {
            initialPrompt.focus();
            return;
        }

        diagram = buildDiagram(prompt);
        diagram.settings = collectSettings();
        autoLayout(diagram, false);
        resizeCanvasToDiagram();
        showBuilder();
        addMessage(prompt, 'user');
        addMessage(generationMessage(), 'assistant');
        render();
        window.setTimeout(fitDiagram, 50);
        persist();
    });

    refineForm.addEventListener('submit', event => {
        event.preventDefault();
        const prompt = refinePrompt.value.trim();
        if (!prompt) return;
        addMessage(prompt, 'user');
        refinePrompt.value = '';
        chatStatus.textContent = 'Updating...';

        window.setTimeout(() => {
            diagram = refineDiagram(diagram, prompt);
            autoLayout(diagram, true);
            resizeCanvasToDiagram();
            render();
            persist();
            addMessage('Updated. Your existing positions are preserved where possible.', 'assistant');
            chatStatus.textContent = 'Ready';
        }, 300);
    });

    document.querySelectorAll('.wf-shape-btn').forEach(button => {
        button.addEventListener('click', () => addManualNode(button.dataset.shape));
    });

    document.getElementById('backBtn').addEventListener('click', () => {
        setupScreen.style.display = 'block';
        builderScreen.style.display = 'none';
    });

    document.getElementById('layoutBtn').addEventListener('click', () => {
        autoLayout(diagram, false);
        resizeCanvasToDiagram();
        render();
        persist();
    });

    document.getElementById('zoomOutBtn').addEventListener('click', () => setZoom(zoom - 0.1));
    document.getElementById('zoomInBtn').addEventListener('click', () => setZoom(zoom + 0.1));
    document.getElementById('zoomResetBtn').addEventListener('click', () => setZoom(1));
    document.getElementById('zoomFitBtn').addEventListener('click', fitDiagram);

    canvasWrap.addEventListener('wheel', event => {
        if (!event.ctrlKey && !event.metaKey) return;
        event.preventDefault();
        const direction = event.deltaY > 0 ? -0.08 : 0.08;
        setZoom(zoom + direction, event);
    }, { passive: false });

    connectBtn.addEventListener('click', () => {
        connectMode = !connectMode;
        connectFromId = null;
        connectBtn.classList.toggle('btn-success', connectMode);
        connectBtn.classList.toggle('btn-light', !connectMode);
        chatStatus.textContent = connectMode ? 'Select two items to connect.' : 'Ready';
        render();
    });

    document.getElementById('deleteBtn').addEventListener('click', deleteSelected);
    document.getElementById('clearBtn').addEventListener('click', () => {
        diagram = { nodes: [], edges: [], settings: collectSettings() };
        selectedNodeId = null;
        connectFromId = null;
        resizeCanvasToDiagram();
        render();
        persist();
    });

    document.getElementById('applyNodeBtn').addEventListener('click', applySelectedChanges);
    document.getElementById('insertAfterBtn').addEventListener('click', () => {
        if (!selectedNodeId) return;
        const node = addNode(diagram.nodes, uniqueId('New step'), 'process', 'New step', true);
        insertNodeAfter(diagram, node.id, selectedNodeId);
        autoLayout(diagram, true);
        resizeCanvasToDiagram();
        selectedNodeId = node.id;
        render();
        syncInspector();
        persist();
    });

    document.getElementById('exportSvgBtn').addEventListener('click', exportSvg);
    document.getElementById('exportPngBtn').addEventListener('click', exportPng);
    document.getElementById('exportPdfBtn').addEventListener('click', exportPdf);

    function collectSettings() {
        return {
            knowledgeSource: knowledgeSource.value,
            provider: aiProvider.value,
            hasApiKey: apiKey.value.trim().length > 0,
            modelOrEndpoint: modelName.value.trim()
        };
    }

    function showBuilder() {
        setupScreen.style.display = 'none';
        builderScreen.style.display = 'grid';
    }

    function setZoom(nextZoom, event) {
        const previousZoom = zoom;
        zoom = Math.min(maxZoom, Math.max(minZoom, Math.round(nextZoom * 100) / 100));
        if (zoom === previousZoom) return;

        const before = event ? getCanvasPointFromMouse(event, previousZoom) : null;
        applyZoom();

        if (event && before) {
            canvasWrap.scrollLeft = before.x * zoom - (event.clientX - canvasWrap.getBoundingClientRect().left);
            canvasWrap.scrollTop = before.y * zoom - (event.clientY - canvasWrap.getBoundingClientRect().top);
        }
    }

    function applyZoom() {
        zoomLayer.setAttribute('transform', `scale(${zoom})`);
        svg.style.width = `${Number(svg.getAttribute('width')) * zoom}px`;
        svg.style.height = `${Number(svg.getAttribute('height')) * zoom}px`;
        zoomLevel.textContent = `${Math.round(zoom * 100)}%`;
    }

    function getCanvasPointFromMouse(event, currentZoom) {
        const rect = canvasWrap.getBoundingClientRect();
        return {
            x: (canvasWrap.scrollLeft + event.clientX - rect.left) / currentZoom,
            y: (canvasWrap.scrollTop + event.clientY - rect.top) / currentZoom
        };
    }

    function fitDiagram() {
        if (!diagram.nodes.length) {
            setZoom(1);
            return;
        }
        resizeCanvasToDiagram();
        const bounds = getDiagramBounds();
        const availableWidth = Math.max(320, canvasWrap.clientWidth - 48);
        const availableHeight = Math.max(320, canvasWrap.clientHeight - 48);
        const fitZoom = Math.min(1, availableWidth / bounds.width, availableHeight / bounds.height);
        setZoom(Math.max(minZoom, fitZoom));
        canvasWrap.scrollLeft = Math.max(0, (bounds.x - 80) * zoom);
        canvasWrap.scrollTop = Math.max(0, (bounds.y - 80) * zoom);
    }

    function resizeCanvasToDiagram() {
        const bounds = getDiagramBounds();
        const width = Math.max(1500, bounds.x + bounds.width + 180);
        const height = Math.max(900, bounds.y + bounds.height + 180);
        svg.setAttribute('width', Math.ceil(width));
        svg.setAttribute('height', Math.ceil(height));
        applyZoom();
    }

    function getDiagramBounds() {
        if (!diagram.nodes.length) return { x: 0, y: 0, width: 1500, height: 900 };
        const minX = Math.min(...diagram.nodes.map(node => node.x || 0));
        const minY = Math.min(...diagram.nodes.map(node => node.y || 0));
        const maxX = Math.max(...diagram.nodes.map(node => (node.x || 0) + node.w));
        const maxY = Math.max(...diagram.nodes.map(node => (node.y || 0) + node.h));
        return {
            x: Math.max(0, minX),
            y: Math.max(0, minY),
            width: Math.max(1, maxX - minX),
            height: Math.max(1, maxY - minY)
        };
    }

    function generationMessage() {
        if (diagram.settings.knowledgeSource === 'online') {
            return 'Generated using the online research setting. In this prototype, the UI captures the setting and falls back to built-in process patterns until the backend web/LLM route is connected.';
        }
        if (diagram.settings.knowledgeSource === 'template' || diagram.settings.provider === 'prototype') {
            return 'Generated from the built-in process library. You can now edit, add steps, drag items, or connect items like a lightweight n8n-style builder.';
        }
        return `Generated using ${diagram.settings.provider}. In this prototype the UI captures the settings; the backend LLM call can be wired next.`;
    }

    function addMessage(text, role) {
        const div = document.createElement('div');
        div.className = `wf-message ${role}`;
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
    }

    function buildDiagram(text) {
        const lower = text.toLowerCase();
        const nodes = [];
        const edges = [];
        addNode(nodes, 'start', 'terminator', 'Start');

        if (isAutomationPrompt(lower)) {
            addNode(nodes, 'trigger_event', 'trigger', inferTriggerLabel(lower));
            addNode(nodes, 'source_system', 'app', inferSourceSystemLabel(lower));
            addNode(nodes, 'receive_webhook', 'webhook', 'Receive webhook');
            addNode(nodes, 'validate_payload', 'decision', 'Valid data?');
            addNode(nodes, 'call_api', 'api', inferApiLabel(lower));
            addNode(nodes, 'update_system', 'app', inferTargetSystemLabel(lower));
            addNode(nodes, 'send_message', 'email', inferMessageLabel(lower));
            addNode(nodes, 'handle_error', 'error', 'Log error and retry');
            addNode(nodes, 'end', 'terminator', 'End');
            connect(edges, 'start', 'trigger_event');
            connect(edges, 'trigger_event', 'source_system');
            connect(edges, 'source_system', 'receive_webhook', 'event');
            connect(edges, 'receive_webhook', 'validate_payload');
            connect(edges, 'validate_payload', 'call_api', 'Yes');
            connect(edges, 'validate_payload', 'handle_error', 'No');
            connect(edges, 'call_api', 'update_system', 'success');
            connect(edges, 'call_api', 'handle_error', 'failed');
            connect(edges, 'update_system', 'send_message');
            connect(edges, 'send_message', 'end');
            connect(edges, 'handle_error', 'end');
            return { nodes, edges, settings: collectSettings() };
        }

        if (isRecruitmentPrompt(lower)) {
            addNode(nodes, 'job_request', 'io', 'Hiring manager submits role request');
            addNode(nodes, 'approve_role', 'decision', 'Role approved?');
            addNode(nodes, 'create_job_post', 'document', 'Create job description');
            addNode(nodes, 'publish_job', 'process', 'Publish job opening');
            addNode(nodes, 'collect_applications', 'io', 'Receive applications');
            addNode(nodes, 'store_candidates', 'database', 'Save candidate profiles');
            addNode(nodes, 'screen_candidates', 'process', 'Screen candidates');
            addNode(nodes, 'shortlist_check', 'decision', 'Shortlisted?');
            addNode(nodes, 'schedule_interview', 'process', 'Schedule interview');
            addNode(nodes, 'interview_candidate', 'process', 'Conduct interview');
            addNode(nodes, 'selection_check', 'decision', 'Selected?');
            addNode(nodes, 'offer_letter', 'document', 'Send offer letter');
            addNode(nodes, 'offer_check', 'decision', 'Offer accepted?');
            addNode(nodes, 'start_onboarding', 'process', 'Start onboarding');
            addNode(nodes, 'notify_rejected', 'process', 'Notify applicant');
            addNode(nodes, 'revise_request', 'process', 'Revise role request');
            addNode(nodes, 'end', 'terminator', 'End');
            connect(edges, 'start', 'job_request');
            connect(edges, 'job_request', 'approve_role');
            connect(edges, 'approve_role', 'create_job_post', 'Yes');
            connect(edges, 'approve_role', 'revise_request', 'No');
            connect(edges, 'revise_request', 'job_request');
            connect(edges, 'create_job_post', 'publish_job');
            connect(edges, 'publish_job', 'collect_applications');
            connect(edges, 'collect_applications', 'store_candidates');
            connect(edges, 'store_candidates', 'screen_candidates');
            connect(edges, 'screen_candidates', 'shortlist_check');
            connect(edges, 'shortlist_check', 'schedule_interview', 'Yes');
            connect(edges, 'shortlist_check', 'notify_rejected', 'No');
            connect(edges, 'schedule_interview', 'interview_candidate');
            connect(edges, 'interview_candidate', 'selection_check');
            connect(edges, 'selection_check', 'offer_letter', 'Yes');
            connect(edges, 'selection_check', 'notify_rejected', 'No');
            connect(edges, 'offer_letter', 'offer_check');
            connect(edges, 'offer_check', 'start_onboarding', 'Yes');
            connect(edges, 'offer_check', 'schedule_interview', 'No');
            connect(edges, 'start_onboarding', 'end');
            connect(edges, 'notify_rejected', 'end');
            return { nodes, edges, settings: collectSettings() };
        }

        if (isOnboardingPrompt(lower)) {
            addNode(nodes, 'accept_offer', 'io', 'New hire accepts offer');
            addNode(nodes, 'create_employee_record', 'database', 'Create employee record');
            addNode(nodes, 'send_requirements', 'document', 'Send onboarding requirements');
            addNode(nodes, 'requirements_check', 'decision', 'Requirements complete?');
            addNode(nodes, 'prepare_accounts', 'process', 'Prepare accounts and access');
            addNode(nodes, 'prepare_equipment', 'process', 'Prepare equipment');
            addNode(nodes, 'orientation', 'process', 'Run orientation');
            addNode(nodes, 'manager_intro', 'process', 'Introduce manager and team');
            addNode(nodes, 'end', 'terminator', 'End');
            connect(edges, 'start', 'accept_offer');
            connect(edges, 'accept_offer', 'create_employee_record');
            connect(edges, 'create_employee_record', 'send_requirements');
            connect(edges, 'send_requirements', 'requirements_check');
            connect(edges, 'requirements_check', 'prepare_accounts', 'Yes');
            connect(edges, 'requirements_check', 'send_requirements', 'No');
            connect(edges, 'prepare_accounts', 'prepare_equipment');
            connect(edges, 'prepare_equipment', 'orientation');
            connect(edges, 'orientation', 'manager_intro');
            connect(edges, 'manager_intro', 'end');
            return { nodes, edges, settings: collectSettings() };
        }

        if (lower.includes('refund')) {
            addNode(nodes, 'request', 'io', 'Customer submits refund request');
            addNode(nodes, 'review', 'process', 'Team reviews request');
            addNode(nodes, 'amount_check', 'decision', extractCondition(text) || 'Under $50?');
            addNode(nodes, 'approve', 'process', 'Approve automatically');
            addNode(nodes, 'escalate', 'process', 'Escalate to manager');
            addNode(nodes, 'notify_approved', 'process', 'Notify customer');
            addNode(nodes, 'store_refund', 'database', 'Store refund record');
            addNode(nodes, 'end', 'terminator', 'End');
            connect(edges, 'start', 'request');
            connect(edges, 'request', 'review');
            connect(edges, 'review', 'amount_check');
            connect(edges, 'amount_check', 'approve', 'Yes');
            connect(edges, 'amount_check', 'escalate', 'No');
            connect(edges, 'approve', 'notify_approved');
            connect(edges, 'escalate', 'notify_approved', 'Manager decision');
            connect(edges, 'notify_approved', 'store_refund');
            connect(edges, 'store_refund', 'end');
            return { nodes, edges, settings: collectSettings() };
        }

        if (lower.includes('expense')) {
            addNode(nodes, 'expense_submit', 'io', 'Employee submits expense report');
            addNode(nodes, 'finance_review', 'process', 'Finance reviews report');
            addNode(nodes, 'complete_check', 'decision', 'Complete?');
            addNode(nodes, 'store_report', 'database', 'Store approved report');
            addNode(nodes, 'pay_expense', 'process', 'Pay reimbursement');
            addNode(nodes, 'request_corrections', 'process', 'Ask for corrections');
            addNode(nodes, 'end', 'terminator', 'End');
            connect(edges, 'start', 'expense_submit');
            connect(edges, 'expense_submit', 'finance_review');
            connect(edges, 'finance_review', 'complete_check');
            connect(edges, 'complete_check', 'store_report', 'Yes');
            connect(edges, 'store_report', 'pay_expense');
            connect(edges, 'pay_expense', 'end');
            connect(edges, 'complete_check', 'request_corrections', 'No');
            connect(edges, 'request_corrections', 'expense_submit', 'Resubmit');
            return { nodes, edges, settings: collectSettings() };
        }

        if (lower.includes('lead')) {
            addNode(nodes, 'lead_form', 'io', 'Lead fills out form');
            addNode(nodes, 'check_crm', 'database', 'Check CRM record');
            addNode(nodes, 'qualify', 'decision', 'Qualified?');
            addNode(nodes, 'send_doc', 'document', 'Send onboarding document');
            addNode(nodes, 'notify_sales', 'process', 'Notify sales owner');
            addNode(nodes, 'nurture', 'process', 'Add to nurture list');
            addNode(nodes, 'end', 'terminator', 'End');
            connect(edges, 'start', 'lead_form');
            connect(edges, 'lead_form', 'check_crm');
            connect(edges, 'check_crm', 'qualify');
            connect(edges, 'qualify', 'send_doc', 'Yes');
            connect(edges, 'send_doc', 'notify_sales');
            connect(edges, 'notify_sales', 'end');
            connect(edges, 'qualify', 'nurture', 'No');
            connect(edges, 'nurture', 'end');
            return { nodes, edges, settings: collectSettings() };
        }

        const steps = inferGenericSteps(text);
        steps.forEach((step, index) => addNode(nodes, `step_${index + 1}`, inferShape(step), titleCaseStep(step)));
        addNode(nodes, 'end', 'terminator', 'End');
        for (let i = 0; i < nodes.length - 1; i++) connect(edges, nodes[i].id, nodes[i + 1].id);
        return { nodes, edges, settings: collectSettings() };
    }

    function refineDiagram(current, text) {
        const next = {
            nodes: current.nodes.map(node => ({ ...node })),
            edges: current.edges.map(edge => ({ ...edge })),
            settings: current.settings || collectSettings()
        };

        const addMatch = text.match(/\badd\s+(?:a|an|the)?\s*(.+?)(?:\s+after\s+(.+)|\s+before\s+(.+)|$)/i);
        if (addMatch) {
            const label = cleanupStep(addMatch[1]);
            const anchorText = cleanupStep(addMatch[2] || addMatch[3] || '');
            const placement = addMatch[3] ? 'before' : 'after';
            const node = addNode(next.nodes, uniqueId(label), inferShape(label), titleCaseStep(label), true);
            insertNodeNear(next, node.id, anchorText, placement);
            return next;
        }

        const node = addNode(next.nodes, uniqueId(text), inferShape(text), titleCaseStep(text), true);
        insertNodeNear(next, node.id, '', 'before-end');
        return next;
    }

    function addManualNode(type) {
        const label = defaultLabel(type);
        const node = addNode(diagram.nodes, uniqueId(label), type, label, true);
        if (selectedNodeId) {
            insertNodeAfter(diagram, node.id, selectedNodeId);
        }
        autoLayout(diagram, true);
        resizeCanvasToDiagram();
        selectedNodeId = node.id;
        render();
        syncInspector();
        persist();
    }

    function defaultLabel(type) {
        return {
            terminator: 'End',
            process: 'New action',
            decision: 'Question?',
            io: 'Form or output',
            database: 'Saved data',
            document: 'Document',
            connector: 'A',
            subprocess: 'Sub-process',
            'manual-input': 'Manual input',
            preparation: 'Prepare step',
            delay: 'Wait',
            offpage: 'Continue',
            trigger: 'Trigger',
            app: 'App or system',
            api: 'Call API',
            webhook: 'Receive webhook',
            email: 'Send message',
            approval: 'Approved?',
            error: 'Handle error'
        }[type] || 'New step';
    }

    function isRecruitmentPrompt(lower) {
        return /\b(recruitment|recruiting|hiring|hire|candidate|applicant|talent acquisition|job opening)\b/.test(lower);
    }

    function isOnboardingPrompt(lower) {
        return /\b(onboarding|new employee|new hire|employee setup)\b/.test(lower);
    }

    function isAutomationPrompt(lower) {
        return /\b(automation|automate|integration|connect|sync|webhook|api|zapier|n8n|trigger|ghl|go high level|hubspot|salesforce|stripe|slack|google sheets)\b/.test(lower);
    }

    function inferTriggerLabel(lower) {
        if (lower.includes('schedule') || lower.includes('daily') || lower.includes('weekly')) return 'Scheduled trigger';
        if (lower.includes('webhook')) return 'Webhook trigger';
        if (lower.includes('form')) return 'Form submitted';
        if (lower.includes('payment') || lower.includes('stripe')) return 'Payment event';
        return 'Automation trigger';
    }

    function inferSourceSystemLabel(lower) {
        if (lower.includes('ghl') || lower.includes('go high level')) return 'GoHighLevel';
        if (lower.includes('hubspot')) return 'HubSpot';
        if (lower.includes('salesforce')) return 'Salesforce';
        if (lower.includes('stripe')) return 'Stripe';
        if (lower.includes('google sheets')) return 'Google Sheets';
        return 'Source system';
    }

    function inferTargetSystemLabel(lower) {
        if (lower.includes('slack')) return 'Update Slack';
        if (lower.includes('google sheets')) return 'Update Google Sheets';
        if (lower.includes('crm')) return 'Update CRM';
        if (lower.includes('hubspot')) return 'Update HubSpot';
        if (lower.includes('salesforce')) return 'Update Salesforce';
        return 'Update target system';
    }

    function inferApiLabel(lower) {
        if (lower.includes('ghl') || lower.includes('go high level')) return 'Call GHL API';
        if (lower.includes('stripe')) return 'Call Stripe API';
        if (lower.includes('hubspot')) return 'Call HubSpot API';
        if (lower.includes('salesforce')) return 'Call Salesforce API';
        return 'Call external API';
    }

    function inferMessageLabel(lower) {
        if (lower.includes('slack')) return 'Send Slack message';
        if (lower.includes('sms')) return 'Send SMS';
        if (lower.includes('email')) return 'Send email';
        return 'Send notification';
    }

    function inferGenericSteps(text) {
        let parts = text
            .split(/[.!?]+|\b(?:then|and then|after that|next)\b/i)
            .map(cleanupStep)
            .filter(Boolean);
        if (!parts.length) parts = ['Receive request', 'Review request', 'Complete work'];
        const joined = parts.join(' ').toLowerCase();
        if (!joined.includes('notify') && /\b(approve|approved|accepted|complete|completed)\b/.test(joined)) parts.push('Notify user');
        if (!joined.includes('store') && /\b(record|report|request|form|data)\b/.test(joined)) parts.push('Store record');
        return parts.slice(0, 12);
    }

    function cleanupStep(text) {
        return (text || '').replace(/^(a|an|the)\s+/i, '').replace(/\s+/g, ' ').replace(/[.?!]$/g, '').trim();
    }

    function titleCaseStep(text) {
        const cleaned = cleanupStep(text);
        return cleaned ? cleaned.charAt(0).toUpperCase() + cleaned.slice(1) : 'New step';
    }

    function extractCondition(text) {
        const match = text.match(/\bif\s+(.+?)(?:,|then|approve|otherwise|else|$)/i);
        return match ? `${titleCaseStep(match[1])}?` : '';
    }

    function inferShape(text) {
        const lower = text.toLowerCase();
        if (/\b(if|whether|decide|decision|check|verify|complete\?|qualified\?|approved\?)\b/.test(lower)) return 'decision';
        if (/\b(trigger|starts when|when .* happens|schedule|cron)\b/.test(lower)) return 'trigger';
        if (/\b(webhook|callback|listener)\b/.test(lower)) return 'webhook';
        if (/\b(api|endpoint|http|request|response|integration)\b/.test(lower)) return 'api';
        if (/\b(email|message|sms|slack|teams|notify|notification)\b/.test(lower)) return 'email';
        if (/\b(approval|approve|approver|sign off|sign-off)\b/.test(lower)) return 'approval';
        if (/\b(error|failed|failure|exception|retry|fallback)\b/.test(lower)) return 'error';
        if (/\b(delay|wait|pause|timer)\b/.test(lower)) return 'delay';
        if (/\b(prepare|setup|configure|initialize)\b/.test(lower)) return 'preparation';
        if (/\b(subprocess|sub-process|sub workflow|child workflow)\b/.test(lower)) return 'subprocess';
        if (/\b(manual input|manual entry|typed by|entered by)\b/.test(lower)) return 'manual-input';
        if (/\b(salesforce|hubspot|ghl|go high level|google sheets|stripe|quickbooks|crm|erp)\b/.test(lower)) return 'app';
        if (/\b(submit|enter|input|form|request|receive|output)\b/.test(lower)) return 'io';
        if (/\b(store|save|retrieve|crm|database|record)\b/.test(lower)) return 'database';
        if (/\b(document|report|file|pdf|invoice|receipt)\b/.test(lower)) return 'document';
        if (/\bconnector|continue on|section\b/.test(lower)) return 'connector';
        if (/\bstart|end|finish|stop\b/.test(lower)) return 'terminator';
        return 'process';
    }

    function addNode(nodes, id, type, label, isNew = false) {
        let normalized = id.replace(/[^a-z0-9_]/gi, '_').toLowerCase();
        let candidate = normalized;
        let count = 2;
        while (nodes.some(node => node.id === candidate)) {
            candidate = `${normalized}_${count++}`;
        }
        const size = shapeSizes[type] || shapeSizes.process;
        const node = { id: candidate, type, label, x: null, y: null, w: size.w, h: size.h, isNew };
        nodes.push(node);
        return node;
    }

    function connect(edges, from, to, label = '') {
        if (!from || !to || from === to) return;
        if (edges.some(edge => edge.from === from && edge.to === to && edge.label === label)) return;
        edges.push({ from, to, label });
    }

    function uniqueId(label) {
        return `step_${cleanupStep(label).toLowerCase().replace(/[^a-z0-9]+/g, '_').replace(/^_|_$/g, '') || Date.now()}`;
    }

    function insertNodeNear(next, nodeId, anchorText, placement) {
        const anchor = findNodeByText(next.nodes, anchorText) || next.nodes.find(node => node.id === 'end');
        if (!anchor) return;
        if (placement === 'before' || placement === 'before-end') return insertNodeBefore(next, nodeId, anchor.id);
        insertNodeAfter(next, nodeId, anchor.id);
    }

    function insertNodeAfter(next, nodeId, anchorId) {
        const outgoing = next.edges.filter(edge => edge.from === anchorId);
        next.edges = next.edges.filter(edge => edge.from !== anchorId);
        connect(next.edges, anchorId, nodeId);
        if (outgoing.length) outgoing.forEach(edge => connect(next.edges, nodeId, edge.to, edge.label));
    }

    function insertNodeBefore(next, nodeId, anchorId) {
        const incoming = next.edges.filter(edge => edge.to === anchorId);
        next.edges = next.edges.filter(edge => edge.to !== anchorId);
        if (incoming.length) incoming.forEach(edge => connect(next.edges, edge.from, nodeId, edge.label));
        connect(next.edges, nodeId, anchorId);
    }

    function findNodeByText(nodes, text) {
        const needle = cleanupStep(text).toLowerCase();
        if (!needle) return null;
        return nodes.find(node => node.label.toLowerCase().includes(needle) || needle.includes(node.label.toLowerCase()));
    }

    function autoLayout(data, preserveMoved) {
        if (!data.nodes.length) return;
        const levels = calculateLevels(data);
        const grouped = new Map();
        data.nodes.forEach(node => {
            const level = levels.get(node.id) || 0;
            if (!grouped.has(level)) grouped.set(level, []);
            grouped.get(level).push(node);
        });
        const startX = 140;
        const startY = 70;
        const gapX = 280;
        const gapY = 145;
        [...grouped.keys()].sort((a, b) => a - b).forEach(level => {
            const row = grouped.get(level);
            row.forEach((node, index) => {
                if (preserveMoved && node.x !== null && node.y !== null && !node.isNew) return;
                node.x = startX + index * gapX + Math.max(0, (3 - row.length) * 90);
                node.y = startY + level * gapY;
                node.isNew = false;
            });
        });
    }

    function calculateLevels(data) {
        const levels = new Map();
        const start = data.nodes.find(node => node.id === 'start') || data.nodes[0];
        if (!start) return levels;
        levels.set(start.id, 0);
        const queue = [start.id];
        while (queue.length) {
            const id = queue.shift();
            const level = levels.get(id) || 0;
            data.edges.filter(edge => edge.from === id).forEach(edge => {
                const nextLevel = level + 1;
                if (!levels.has(edge.to) || nextLevel < levels.get(edge.to)) {
                    levels.set(edge.to, nextLevel);
                    queue.push(edge.to);
                }
            });
        }
        data.nodes.forEach((node, index) => {
            if (!levels.has(node.id)) levels.set(node.id, index);
        });
        return levels;
    }

    function render() {
        nodesLayer.innerHTML = '';
        edgesLayer.innerHTML = '';
        emptyState.style.display = diagram.nodes.length ? 'none' : 'block';
        diagram.edges.forEach(drawEdge);
        diagram.nodes.forEach(drawNode);
        syncInspector();
    }

    function drawEdge(edge) {
        const from = diagram.nodes.find(node => node.id === edge.from);
        const to = diagram.nodes.find(node => node.id === edge.to);
        if (!from || !to) return;
        const start = getConnectionAnchor(from, to, 'out');
        const end = getConnectionAnchor(to, from, 'in');
        const midY = start.y + Math.max(34, (end.y - start.y) / 2);
        const path = svgEl('path', {
            class: 'edge-path',
            'marker-end': 'url(#arrowHead)',
            d: roundedConnectorPath(start, end, midY)
        });
        edgesLayer.appendChild(path);

        if (edge.label) {
            const text = svgEl('text', { class: 'edge-label', x: (start.x + end.x) / 2, y: midY - 8, 'text-anchor': 'middle' });
            text.textContent = edge.label;
            edgesLayer.appendChild(text);
        }
    }

    function drawNode(node) {
        const group = svgEl('g', {
            class: `wf-node type-${node.type} ${selectedNodeId === node.id ? 'selected' : ''} ${connectFromId === node.id ? 'pending-connect' : ''}`,
            'data-id': node.id
        });
        const shape = makeShape(node);
        shape.classList.add('shape-fill');
        group.appendChild(shape);

        wrapText(node.label, node.w - 28).forEach((line, index, lines) => {
            const text = svgEl('text', {
                x: node.x + node.w / 2,
                y: node.y + node.h / 2 + (index - (lines.length - 1) / 2) * 16
            });
            text.textContent = line;
            group.appendChild(text);
        });

        getPorts(node).forEach(port => {
            const circle = svgEl('circle', {
                class: 'wf-port',
                cx: port.x,
                cy: port.y,
                r: 4.5
            });
            group.appendChild(circle);
        });

        group.addEventListener('mousedown', startDrag);
        group.addEventListener('click', selectOrConnect);
        group.addEventListener('dblclick', () => editNode(node.id));
        nodesLayer.appendChild(group);
    }

    function makeShape(node) {
        if (node.type === 'terminator') return svgEl('rect', { x: node.x, y: node.y, width: node.w, height: node.h, rx: node.h / 2, ry: node.h / 2 });
        if (node.type === 'decision') return svgEl('polygon', { points: `${node.x + node.w / 2},${node.y} ${node.x + node.w},${node.y + node.h / 2} ${node.x + node.w / 2},${node.y + node.h} ${node.x},${node.y + node.h / 2}` });
        if (node.type === 'io') return svgEl('polygon', { points: `${node.x + 26},${node.y} ${node.x + node.w},${node.y} ${node.x + node.w - 26},${node.y + node.h} ${node.x},${node.y + node.h}` });
        if (node.type === 'connector') return svgEl('circle', { cx: node.x + node.w / 2, cy: node.y + node.h / 2, r: Math.min(node.w, node.h) / 2 });
        if (node.type === 'database') return svgEl('path', { d: databasePath(node) });
        if (node.type === 'document') return svgEl('path', { d: documentPath(node) });
        if (node.type === 'subprocess') return svgEl('path', { d: subprocessPath(node) });
        if (node.type === 'manual-input') return svgEl('polygon', { points: `${node.x},${node.y + 22} ${node.x + node.w},${node.y} ${node.x + node.w},${node.y + node.h} ${node.x},${node.y + node.h}` });
        if (node.type === 'preparation') return svgEl('polygon', { points: `${node.x + 30},${node.y} ${node.x + node.w - 30},${node.y} ${node.x + node.w},${node.y + node.h / 2} ${node.x + node.w - 30},${node.y + node.h} ${node.x + 30},${node.y + node.h} ${node.x},${node.y + node.h / 2}` });
        if (node.type === 'delay') return svgEl('path', { d: delayPath(node) });
        if (node.type === 'offpage') return svgEl('polygon', { points: `${node.x},${node.y} ${node.x + node.w},${node.y} ${node.x + node.w},${node.y + node.h * 0.72} ${node.x + node.w / 2},${node.y + node.h} ${node.x},${node.y + node.h * 0.72}` });
        if (node.type === 'trigger') return svgEl('rect', { x: node.x, y: node.y, width: node.w, height: node.h, rx: node.h / 2, ry: node.h / 2 });
        if (node.type === 'app') return svgEl('rect', { x: node.x, y: node.y, width: node.w, height: node.h, rx: 14, ry: 14 });
        if (node.type === 'api') return svgEl('polygon', { points: `${node.x + 20},${node.y} ${node.x + node.w - 20},${node.y} ${node.x + node.w},${node.y + node.h / 2} ${node.x + node.w - 20},${node.y + node.h} ${node.x + 20},${node.y + node.h} ${node.x},${node.y + node.h / 2}` });
        if (node.type === 'webhook') return svgEl('path', { d: webhookPath(node) });
        if (node.type === 'email') return svgEl('path', { d: emailPath(node) });
        if (node.type === 'approval') return svgEl('polygon', { points: `${node.x + node.w / 2},${node.y} ${node.x + node.w},${node.y + node.h / 2} ${node.x + node.w / 2},${node.y + node.h} ${node.x},${node.y + node.h / 2}` });
        if (node.type === 'error') return svgEl('path', { d: errorPath(node) });
        return svgEl('rect', { x: node.x, y: node.y, width: node.w, height: node.h, rx: 6, ry: 6 });
    }

    function databasePath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        const r = 17;
        return [
            `M ${x} ${y + r}`,
            `C ${x} ${y - 5}, ${x + w} ${y - 5}, ${x + w} ${y + r}`,
            `L ${x + w} ${y + h - r}`,
            `C ${x + w} ${y + h + 5}, ${x} ${y + h + 5}, ${x} ${y + h - r}`,
            'Z',
            `M ${x} ${y + r}`,
            `C ${x} ${y + r + 22}, ${x + w} ${y + r + 22}, ${x + w} ${y + r}`
        ].join(' ');
    }

    function documentPath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        return [
            `M ${x} ${y}`,
            `H ${x + w}`,
            `V ${y + h - 17}`,
            `C ${x + w - 42} ${y + h + 6}, ${x + 42} ${y + h - 36}, ${x} ${y + h - 10}`,
            'Z'
        ].join(' ');
    }

    function subprocessPath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        return [
            `M ${x} ${y} H ${x + w} V ${y + h} H ${x} Z`,
            `M ${x + 12} ${y} V ${y + h}`,
            `M ${x + w - 12} ${y} V ${y + h}`
        ].join(' ');
    }

    function delayPath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        return [
            `M ${x} ${y}`,
            `H ${x + w - h / 2}`,
            `C ${x + w + h / 2} ${y}, ${x + w + h / 2} ${y + h}, ${x + w - h / 2} ${y + h}`,
            `H ${x}`,
            'Z'
        ].join(' ');
    }

    function webhookPath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        return [
            `M ${x + 18} ${y}`,
            `H ${x + w - 18}`,
            `C ${x + w + 8} ${y}, ${x + w + 8} ${y + h}, ${x + w - 18} ${y + h}`,
            `H ${x + 18}`,
            `C ${x - 8} ${y + h}, ${x - 8} ${y}, ${x + 18} ${y}`,
            'Z'
        ].join(' ');
    }

    function emailPath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        return [
            `M ${x} ${y} H ${x + w} V ${y + h} H ${x} Z`,
            `M ${x} ${y} L ${x + w / 2} ${y + h * 0.56} L ${x + w} ${y}`,
            `M ${x} ${y + h} L ${x + w * 0.38} ${y + h * 0.45}`,
            `M ${x + w} ${y + h} L ${x + w * 0.62} ${y + h * 0.45}`
        ].join(' ');
    }

    function errorPath(node) {
        const x = node.x;
        const y = node.y;
        const w = node.w;
        const h = node.h;
        return [
            `M ${x + w / 2} ${y}`,
            `C ${x + w} ${y}, ${x + w} ${y + h}, ${x + w / 2} ${y + h}`,
            `C ${x} ${y + h}, ${x} ${y}, ${x + w / 2} ${y}`,
            'Z'
        ].join(' ');
    }

    function getPorts(node) {
        return [
            { x: node.x + node.w / 2, y: node.y },
            { x: node.x + node.w, y: node.y + node.h / 2 },
            { x: node.x + node.w / 2, y: node.y + node.h },
            { x: node.x, y: node.y + node.h / 2 }
        ];
    }

    function getConnectionAnchor(node, other, mode) {
        const center = { x: node.x + node.w / 2, y: node.y + node.h / 2 };
        const otherCenter = { x: other.x + other.w / 2, y: other.y + other.h / 2 };
        const dx = otherCenter.x - center.x;
        const dy = otherCenter.y - center.y;

        if (Math.abs(dx) > Math.abs(dy)) {
            return dx > 0
                ? { x: node.x + node.w, y: center.y }
                : { x: node.x, y: center.y };
        }

        if (mode === 'out') {
            return dy >= 0
                ? { x: center.x, y: node.y + node.h }
                : { x: center.x, y: node.y };
        }

        return dy >= 0
            ? { x: center.x, y: node.y }
            : { x: center.x, y: node.y + node.h };
    }

    function roundedConnectorPath(start, end, midY) {
        const r = 10;
        const down = end.y >= start.y;
        const turn1Y = down ? midY - r : midY + r;
        const turn2Y = down ? midY + r : midY - r;

        if (Math.abs(start.x - end.x) < 4) {
            return `M ${start.x} ${start.y} L ${end.x} ${end.y}`;
        }

        return [
            `M ${start.x} ${start.y}`,
            `L ${start.x} ${turn1Y}`,
            `Q ${start.x} ${midY} ${start.x + Math.sign(end.x - start.x) * r} ${midY}`,
            `L ${end.x - Math.sign(end.x - start.x) * r} ${midY}`,
            `Q ${end.x} ${midY} ${end.x} ${turn2Y}`,
            `L ${end.x} ${end.y}`
        ].join(' ');
    }

    function svgEl(name, attrs) {
        const element = document.createElementNS('http://www.w3.org/2000/svg', name);
        Object.entries(attrs).forEach(([key, value]) => element.setAttribute(key, value));
        return element;
    }

    function wrapText(text, maxWidth) {
        const words = text.split(/\s+/);
        const lines = [];
        let line = '';
        const maxChars = Math.max(10, Math.floor(maxWidth / 7));
        words.forEach(word => {
            const test = line ? `${line} ${word}` : word;
            if (test.length > maxChars && line) {
                lines.push(line);
                line = word;
            } else {
                line = test;
            }
        });
        if (line) lines.push(line);
        return lines.slice(0, 4);
    }

    function selectOrConnect(event) {
        const id = event.currentTarget.dataset.id;
        if (!connectMode) {
            selectedNodeId = id;
            render();
            return;
        }
        if (!connectFromId) {
            connectFromId = id;
            selectedNodeId = id;
            chatStatus.textContent = 'Select the item to connect to.';
            render();
            return;
        }
        if (connectFromId !== id) {
            const label = window.prompt('Connection label (optional)', '');
            connect(diagram.edges, connectFromId, id, label || '');
            connectFromId = null;
            connectMode = false;
            connectBtn.classList.remove('btn-success');
            connectBtn.classList.add('btn-light');
            chatStatus.textContent = 'Ready';
            render();
            persist();
        }
    }

    function startDrag(event) {
        if (connectMode) return;
        const id = event.currentTarget.dataset.id;
        const node = diagram.nodes.find(item => item.id === id);
        if (!node) return;
        selectedNodeId = id;
        dragState = { id, startX: event.clientX, startY: event.clientY, nodeX: node.x, nodeY: node.y };
        render();
    }

    window.addEventListener('mousemove', event => {
        if (!dragState) return;
        const node = diagram.nodes.find(item => item.id === dragState.id);
        if (!node) return;
        node.x = dragState.nodeX + event.clientX - dragState.startX;
        node.y = dragState.nodeY + event.clientY - dragState.startY;
        render();
    });

    window.addEventListener('mouseup', () => {
        if (dragState) {
            dragState = null;
            resizeCanvasToDiagram();
            persist();
        }
    });

    function editNode(id) {
        const node = diagram.nodes.find(item => item.id === id);
        if (!node) return;
        const updated = window.prompt('Edit text', node.label);
        if (updated !== null && updated.trim()) {
            node.label = updated.trim();
            node.type = inferShape(node.label);
            resizeNode(node);
            selectedNodeId = id;
            render();
            persist();
        }
    }

    function syncInspector() {
        const node = diagram.nodes.find(item => item.id === selectedNodeId);
        selectedLabel.value = node ? node.label : '';
        selectedType.value = node ? node.type : 'process';
    }

    function applySelectedChanges() {
        const node = diagram.nodes.find(item => item.id === selectedNodeId);
        if (!node) return;
        node.label = selectedLabel.value.trim() || node.label;
        node.type = selectedType.value;
        resizeNode(node);
        render();
        persist();
    }

    function resizeNode(node) {
        const size = shapeSizes[node.type] || shapeSizes.process;
        node.w = size.w;
        node.h = size.h;
    }

    function deleteSelected() {
        if (!selectedNodeId) return;
        diagram.nodes = diagram.nodes.filter(node => node.id !== selectedNodeId);
        diagram.edges = diagram.edges.filter(edge => edge.from !== selectedNodeId && edge.to !== selectedNodeId);
        selectedNodeId = null;
        render();
        persist();
    }

    function persist() {
        localStorage.setItem(STORAGE_KEY, JSON.stringify(diagram));
    }

    function restore() {
        const saved = localStorage.getItem(STORAGE_KEY);
        if (!saved) return;
        try {
            diagram = JSON.parse(saved);
            if (diagram.nodes && diagram.nodes.length) {
                showBuilder();
                resizeCanvasToDiagram();
                render();
                addMessage('Restored your last test diagram.', 'assistant');
            }
        } catch (error) {
            localStorage.removeItem(STORAGE_KEY);
        }
    }

    function serializedSvg() {
        const clone = svg.cloneNode(true);
        clone.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        clone.removeAttribute('style');
        const clonedZoomLayer = clone.querySelector('#zoomLayer');
        if (clonedZoomLayer) clonedZoomLayer.removeAttribute('transform');
        const style = document.createElement('style');
        style.textContent = '.shape-fill{fill:#fff;stroke:#334155;stroke-width:1.6}.type-terminator .shape-fill,.type-trigger .shape-fill{fill:#d5e8d4;stroke:#82b366}.type-process .shape-fill,.type-app .shape-fill{fill:#dae8fc;stroke:#6c8ebf}.type-decision .shape-fill,.type-email .shape-fill{fill:#fff2cc;stroke:#d6b656}.type-io .shape-fill,.type-api .shape-fill,.type-webhook .shape-fill{fill:#e1d5e7;stroke:#9673a6}.type-database .shape-fill,.type-error .shape-fill{fill:#f8cecc;stroke:#b85450}.type-document .shape-fill,.type-approval .shape-fill{fill:#ffe6cc;stroke:#d79b00}.type-connector .shape-fill,.type-subprocess .shape-fill,.type-preparation .shape-fill,.type-manual-input .shape-fill,.type-delay .shape-fill,.type-offpage .shape-fill{fill:#f5f5f5;stroke:#666}.edge-path{fill:none;stroke:#334155;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.edge-label{fill:#334155;font-size:12px;font-weight:700}.wf-port{display:none}text{fill:#111827;font-size:12.5px;font-weight:500;text-anchor:middle;dominant-baseline:middle;font-family:Arial,sans-serif}';
        clone.insertBefore(style, clone.firstChild);
        return new XMLSerializer().serializeToString(clone);
    }

    function download(blob, filename) {
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        link.click();
        URL.revokeObjectURL(url);
    }

    function exportSvg() {
        download(new Blob([serializedSvg()], { type: 'image/svg+xml;charset=utf-8' }), 'workflow-diagram.svg');
    }

    function exportPng() {
        const image = new Image();
        const url = URL.createObjectURL(new Blob([serializedSvg()], { type: 'image/svg+xml;charset=utf-8' }));
        image.onload = () => {
            const canvas = document.createElement('canvas');
            canvas.width = Number(svg.getAttribute('width'));
            canvas.height = Number(svg.getAttribute('height'));
            const ctx = canvas.getContext('2d');
            ctx.fillStyle = '#fff';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(image, 0, 0);
            URL.revokeObjectURL(url);
            canvas.toBlob(blob => download(blob, 'workflow-diagram.png'));
        };
        image.src = url;
    }

    function exportPdf() {
        const win = window.open('', '_blank');
        if (!win) return;
        win.document.write(`<html><head><title>workflow-diagram.pdf</title><style>body{margin:0;padding:24px;font-family:Arial,sans-serif}svg{max-width:100%;height:auto}</style></head><body>${serializedSvg()}<script>window.onload=function(){window.print()}<\/script></body></html>`);
        win.document.close();
    }

    restore();
    applyZoom();
    render();
})();
</script>
@endverbatim
@endsection
