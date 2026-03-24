@extends('layouts.header')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>

#canvas {
    background: #ffffff;
    background-image: 
        linear-gradient(rgba(200, 200, 200, 0.15) 1px, transparent 1px),
        linear-gradient(90deg, rgba(200, 200, 200, 0.15) 1px, transparent 1px);
    background-size: 20px 20px;
    position: relative;
    overflow: hidden;
    cursor: default;
}

/* Node Base */
.flowchart-node {
    position: absolute;
    background: white;
    border: 2px solid #333;
    cursor: move;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 14px;
    padding: 10px;
    box-sizing: border-box;
}

.flowchart-node.selected {
    border-color: #2196F3;
    box-shadow: 0 0 0 2px rgba(33, 150, 243, 0.3);
}

.flowchart-node:hover {
    border-color: #666;
}

/* Node text */
.node-text {
    outline: none;
    width: 100%;
    text-align: center;
    word-wrap: break-word;
}

/* Resize handles */
.resize-handle {
    position: absolute;
    width: 8px;
    height: 8px;
    background: #2196F3;
    border: 1px solid white;
    display: none;
}

.flowchart-node.selected .resize-handle {
    display: block;
}

.resize-handle.nw { top: -4px; left: -4px; cursor: nw-resize; }
.resize-handle.ne { top: -4px; right: -4px; cursor: ne-resize; }
.resize-handle.sw { bottom: -4px; left: -4px; cursor: sw-resize; }
.resize-handle.se { bottom: -4px; right: -4px; cursor: se-resize; }
.resize-handle.n { top: -4px; left: 50%; margin-left: -4px; cursor: n-resize; }
.resize-handle.s { bottom: -4px; left: 50%; margin-left: -4px; cursor: s-resize; }
.resize-handle.w { top: 50%; left: -4px; margin-top: -4px; cursor: w-resize; }
.resize-handle.e { top: 50%; right: -4px; margin-top: -4px; cursor: e-resize; }

/* Delete button */
.delete-btn {
    position: absolute;
    top: -12px;
    right: -12px;
    width: 28px;
    height: 28px;
    background: #f44336;
    color: white;
    border: 2px solid white;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    line-height: 24px;
    text-align: center;
    display: none;
    font-weight: bold;
    z-index: 20;
}

.delete-btn:hover {
    background: #d32f2f;
    transform: scale(1.1);
}

/* Connection points (arrows on each side) */
.connection-point {
    position: absolute;
    width: 12px;
    height: 12px;
    background: #4CAF50;
    border: 2px solid white;
    border-radius: 50%;
    cursor: crosshair;
    display: none;
    z-index: 15;
}

.flowchart-node:hover .connection-point,
.flowchart-node.selected .connection-point {
    display: block;
}

.connection-point:hover {
    background: #2196F3;
    transform: scale(1.3);
}

.conn-top { top: -6px; left: 50%; margin-left: -6px; }
.conn-bottom { bottom: -6px; left: 50%; margin-left: -6px; }
.conn-left { top: 50%; left: -6px; margin-top: -6px; }
.conn-right { top: 50%; right: -6px; margin-top: -6px; }

/* When connecting */
.connecting {
    border-color: #4CAF50 !important;
    border-width: 3px !important;
}

.flowchart-node.selected .delete-btn {
    display: block;
}

/* Terminal/Start/End - Rounded Rectangle */
.shape-terminal {
    border-radius: 50px;
}

/* Process - Rectangle */
.shape-process {
    border-radius: 4px;
}

/* Decision - Diamond */
.shape-decision {
    background: white;
    border: 2px solid #333;
    transform: rotate(45deg);
    overflow: visible;
}

.shape-decision .node-text {
    transform: rotate(-45deg);
    max-width: 70%;
    padding: 5px;
}

.shape-decision .resize-handle {
    transform: rotate(-45deg);
}

.shape-decision .delete-btn {
    transform: rotate(-45deg);
}

.shape-decision .connection-point {
    transform: rotate(-45deg);
}

/* Data/IO - Parallelogram */
.shape-data {
    position: relative;
    background: white;
    border: none;
}

.shape-data::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%);
    z-index: -1;
}

/* Document */
.shape-document {
    position: relative;
    background: white;
    border: 2px solid #333;
    border-bottom: none;
    border-radius: 4px 4px 0 0;
}

.shape-document::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: -2px;
    right: -2px;
    height: 20px;
    background: white;
    border: 2px solid #333;
    border-top: none;
    border-radius: 0 0 50% 50%;
    clip-path: ellipse(50% 50% at 50% 0%);
}

/* Predefined Process - Rectangle with double lines */
.shape-predefined {
    border-radius: 4px;
    position: relative;
    padding-left: 15px;
    padding-right: 15px;
}

.shape-predefined::before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 8px;
    width: 2px;
    background: #333;
}

.shape-predefined::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    right: 8px;
    width: 2px;
    background: #333;
}

/* Manual Input - Trapezoid */
.shape-manual-input {
    position: relative;
    background: white;
    border: none;
}

.shape-manual-input::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(0 25%, 100% 0, 100% 100%, 0 100%);
    z-index: -1;
}

/* Manual Operation - Trapezoid inverted */
.shape-manual-operation {
    position: relative;
    background: white;
    border: none;
}

.shape-manual-operation::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(10% 0, 90% 0, 100% 100%, 0 100%);
    z-index: -1;
}

/* Database - Cylinder */
.shape-database {
    border-radius: 4px;
    position: relative;
    border-top: none;
    padding-top: 25px;
}

.shape-database::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 25px;
    background: white;
    border: 2px solid #333;
    border-radius: 50% / 40%;
    border-bottom: 1px solid #333;
}

.shape-database .node-text {
    margin-top: 0;
}

/* Stored Data - Curved Rectangle */
.shape-stored-data {
    border-radius: 0 50% 50% 0;
}

/* Display */
.shape-display {
    position: relative;
    background: white;
    border: none;
}

.shape-display::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(10% 0, 100% 0, 90% 100%, 0 100%, 0 50%);
    z-index: -1;
}

/* Delay - Rounded on one side */
.shape-delay {
    border-radius: 0 50% 50% 0;
}

/* Or */
.shape-or {
    border-radius: 50%;
}

/* Summing Junction */
.shape-junction {
    border-radius: 50%;
    width: 40px !important;
    height: 40px !important;
    min-width: 40px;
    min-height: 40px;
    padding: 0;
}

/* Merge */
.shape-merge {
    position: relative;
    background: white;
    border: none;
}

.shape-merge::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(50% 0, 100% 50%, 50% 100%, 0 50%);
    z-index: -1;
}

/* Connector */
.shape-connector {
    border-radius: 50%;
    width: 30px !important;
    height: 30px !important;
    min-width: 30px;
    min-height: 30px;
    padding: 0;
    font-size: 12px;
}

/* Off-page Connector */
.shape-offpage {
    position: relative;
    background: white;
    border: none;
}

.shape-offpage::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 100%, 0 70%);
    z-index: -1;
}

/* Card - Punched Card */
.shape-card {
    position: relative;
    background: white;
    border: none;
}

.shape-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(10% 0, 100% 0, 100% 100%, 0 100%, 0 10%);
    z-index: -1;
}

/* Collate */
.shape-collate {
    position: relative;
    background: white;
    border: none;
}

.shape-collate::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(0 0, 100% 100%, 0 100%);
    z-index: -1;
}

.shape-collate::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: 2px solid #333;
    border-left: none;
    border-bottom: none;
    clip-path: polygon(100% 0, 100% 100%, 0 0);
    z-index: -1;
}

/* Sort */
.shape-sort {
    position: relative;
    background: white;
    border: none;
}

.shape-sort::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(50% 0, 100% 50%, 50% 100%, 0 50%);
    z-index: -1;
}

/* Extract */
.shape-extract {
    position: relative;
    background: white;
    border: none;
}

.shape-extract::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(50% 0, 100% 100%, 0 100%);
    z-index: -1;
}

/* Preparation - Hexagon */
.shape-preparation {
    position: relative;
    background: white;
    border: none;
}

.shape-preparation::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: white;
    border: 2px solid #333;
    clip-path: polygon(15% 0, 85% 0, 100% 50%, 85% 100%, 15% 100%, 0 50%);
    z-index: -1;
}

/* Arrow */
.shape-arrow {
    position: relative;
    background: white;
    border: none;
    clip-path: polygon(0 30%, 70% 30%, 70% 0, 100% 50%, 70% 100%, 70% 70%, 0 70%);
}

.shape-arrow::after {
    content: '';
    position: absolute;
    top: 2px;
    left: 2px;
    right: 2px;
    bottom: 2px;
    background: white;
    clip-path: polygon(0 30%, 70% 30%, 70% 0, 100% 50%, 70% 100%, 70% 70%, 0 70%);
}

.shape-arrow::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: #333;
    clip-path: polygon(0 30%, 70% 30%, 70% 0, 100% 50%, 70% 100%, 70% 70%, 0 70%);
}

.shape-arrow .node-text {
    position: relative;
    z-index: 3;
}


/* Toolbox */
.toolbox {
    background: white;
    border-radius: 8px;
    padding: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.toolbox h6 {
    color: #424242;
    font-weight: 600;
    margin-bottom: 12px;
    margin-top: 15px;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.toolbox h6:first-child {
    margin-top: 0;
}

.shape-btn {
    width: 100%;
    text-align: left;
    margin-bottom: 6px;
    border: 1px solid #ddd;
    background: white;
    padding: 8px 10px;
    border-radius: 4px;
    font-size: 12px;
    cursor: grab;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.shape-btn:active {
    cursor: grabbing;
}

.shape-btn:hover {
    border-color: #2196f3;
    background: #f5f5f5;
    transform: translateX(2px);
}

/* Shape preview icons */
.shape-preview {
    width: 30px;
    height: 30px;
    border: 2px solid #333;
    background: white;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preview-terminal { border-radius: 15px; }
.preview-process { border-radius: 2px; }
.preview-decision { 
    transform: rotate(45deg);
    width: 24px;
    height: 24px;
}
.preview-data { 
    clip-path: polygon(10% 0%, 100% 0%, 90% 100%, 0% 100%);
}
.preview-document { 
    clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 85%, 0 70%);
}
.preview-predefined { 
    border-left: 3px double #333;
    border-right: 3px double #333;
}
.preview-preparation { 
    clip-path: polygon(20% 0, 80% 0, 100% 50%, 80% 100%, 20% 100%, 0 50%);
}
.preview-manual-input { 
    clip-path: polygon(0 30%, 100% 0, 100% 100%, 0 100%);
}
.preview-manual-operation { 
    clip-path: polygon(15% 0, 85% 0, 100% 100%, 0 100%);
}
.preview-display { 
    clip-path: polygon(20% 0, 100% 0, 80% 100%, 0 100%, 0 50%);
}
.preview-card { 
    clip-path: polygon(15% 0, 100% 0, 100% 100%, 0 100%, 0 15%);
}
.preview-database { 
    border-radius: 2px;
    position: relative;
}
.preview-database::before {
    content: '';
    position: absolute;
    top: -2px;
    left: -2px;
    right: -2px;
    height: 8px;
    background: white;
    border: 2px solid #333;
    border-radius: 50% 50% 0 0;
    border-bottom: none;
}
.preview-stored-data { 
    border-radius: 0 50% 50% 0;
}
.preview-delay { 
    border-radius: 0 50% 50% 0;
}
.preview-or { 
    border-radius: 50%;
}
.preview-merge { 
    clip-path: polygon(50% 0, 100% 50%, 50% 100%, 0 50%);
}
.preview-extract { 
    clip-path: polygon(50% 0, 100% 100%, 0 100%);
}
.preview-collate { 
    clip-path: polygon(50% 0, 100% 100%, 0 100%);
}
.preview-sort { 
    clip-path: polygon(50% 0, 100% 50%, 50% 100%, 0 50%);
}
.preview-connector { 
    border-radius: 50%;
    width: 20px;
    height: 20px;
}
.preview-offpage { 
    clip-path: polygon(0 0, 100% 0, 100% 70%, 50% 100%, 0 70%);
}
.preview-junction { 
    border-radius: 50%;
    width: 16px;
    height: 16px;
}
.preview-arrow {
    width: 30px;
    height: 30px;
    position: relative;
    border: none;
}
.preview-arrow::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    width: 20px;
    height: 2px;
    background: #333;
    transform: translateY(-50%);
}
.preview-arrow::after {
    content: '';
    position: absolute;
    top: 50%;
    right: 2px;
    width: 0;
    height: 0;
    border-left: 8px solid #333;
    border-top: 5px solid transparent;
    border-bottom: 5px solid transparent;
    transform: translateY(-50%);
}

/* Toolbar */
.toolbar {
    background: white;
    padding: 12px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    margin-bottom: 15px;
}

.toolbar button {
    margin-right: 8px;
}

/* Connection lines */
svg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

svg * {
    pointer-events: auto;
}

.flowchart-node {
    z-index: 2;
}

.connection-line {
    stroke: #333;
    stroke-width: 3;
    fill: none;
    pointer-events: stroke;
    cursor: pointer;
}

.connection-line:hover {
    stroke: #2196F3;
    stroke-width: 4;
}

.arrow {
    fill: #333;
    pointer-events: auto;
}

.connection-line:hover + .arrow,
.arrow:hover {
    fill: #2196F3;
}

/* Connection mode */
.connection-mode {
    cursor: crosshair !important;
}

.connection-mode .flowchart-node {
    cursor: crosshair !important;
}

</style>

<div class="container-fluid">

    <!-- TOOLBAR -->
    <div class="toolbar d-flex align-items-center">
        <h4 class="mr-auto mb-0">{{ $diagram->name }} &nbsp;</h4>
        <button class="btn btn-outline-info btn-sm" onclick="toggleConnectionMode()">
            <i class="fas fa-info-circle"></i> How to Connect
        </button>
        <button class="btn btn-outline-secondary btn-sm" onclick="clearCanvas()">
            <i class="fas fa-trash"></i> Clear
        </button>
        <button class="btn btn-outline-secondary btn-sm" onclick="exportDiagram()">
            <i class="fas fa-download"></i> Export
        </button>
        <button class="btn btn-primary btn-sm" onclick="saveDiagram(event)">
            <i class="fas fa-save"></i> Save
        </button>
    </div>

    <div class="row">

        <!-- LEFT TOOLBOX -->
        <div class="col-md-2">
            <div class="toolbox">
                
                <h6>Basic Flowchart</h6>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'terminal')">
                    <div class="shape-preview preview-terminal"></div>
                    <span>Terminal (Start/End)</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'process')">
                    <div class="shape-preview preview-process"></div>
                    <span>Process</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'decision')">
                    <div class="shape-preview preview-decision"></div>
                    <span>Decision</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'data')">
                    <div class="shape-preview preview-data"></div>
                    <span>Data (I/O)</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'document')">
                    <div class="shape-preview preview-document"></div>
                    <span>Document</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'predefined')">
                    <div class="shape-preview preview-predefined"></div>
                    <span>Predefined Process</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'preparation')">
                    <div class="shape-preview preview-preparation"></div>
                    <span>Preparation</span>
                </button>

                <h6>Input/Output</h6>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'manual-input')">
                    <div class="shape-preview preview-manual-input"></div>
                    <span>Manual Input</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'manual-operation')">
                    <div class="shape-preview preview-manual-operation"></div>
                    <span>Manual Operation</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'display')">
                    <div class="shape-preview preview-display"></div>
                    <span>Display</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'card')">
                    <div class="shape-preview preview-card"></div>
                    <span>Card (Punched Card)</span>
                </button>

                <h6>Data Storage</h6>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'database')">
                    <div class="shape-preview preview-database"></div>
                    <span>Database</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'stored-data')">
                    <div class="shape-preview preview-stored-data"></div>
                    <span>Stored Data</span>
                </button>
                
                <h6>Additional</h6>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'delay')">
                    <div class="shape-preview preview-delay"></div>
                    <span>Delay</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'or')">
                    <div class="shape-preview preview-or"></div>
                    <span>Or</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'merge')">
                    <div class="shape-preview preview-merge"></div>
                    <span>Merge</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'extract')">
                    <div class="shape-preview preview-extract"></div>
                    <span>Extract</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'collate')">
                    <div class="shape-preview preview-collate"></div>
                    <span>Collate</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'sort')">
                    <div class="shape-preview preview-sort"></div>
                    <span>Sort</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'arrow')">
                    <div class="shape-preview preview-arrow"></div>
                    <span>Arrow</span>
                </button>

                <h6>Connectors</h6>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'connector')">
                    <div class="shape-preview preview-connector"></div>
                    <span>Connector</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'offpage')">
                    <div class="shape-preview preview-offpage"></div>
                    <span>Off-page Connector</span>
                </button>
                <button class="shape-btn" draggable="true" ondragstart="dragShape(event, 'junction')">
                    <div class="shape-preview preview-junction"></div>
                    <span>Junction</span>
                </button>

            </div>
        </div>

        <!-- CANVAS -->
        <div class="col-md-10">
            <div id="canvas" style="width:100%; height:750px; border:1px solid #ddd; border-radius: 8px;">
                <svg id="connectionSvg"></svg>
            </div>
        </div>

    </div>

</div>

<script>

let nodes = [];
let connections = [];
let selectedNode = null;
let isDragging = false;
let isResizing = false;
let resizeHandle = null;
let dragStartX = 0;
let dragStartY = 0;
let nodeStartX = 0;
let nodeStartY = 0;
let nodeStartWidth = 0;
let nodeStartHeight = 0;
let nodeIdCounter = 0;
let connectionMode = false;
let connectionStart = null;

const canvas = document.getElementById('canvas');
const svg = document.getElementById('connectionSvg');

// Load saved diagram
const savedData = @json($diagram->diagram_json ? json_decode($diagram->diagram_json) : null);
if (savedData && savedData.nodes) {
    nodes = savedData.nodes;
    connections = savedData.connections || [];
    nodeIdCounter = savedData.nodeIdCounter || 0;
    
    nodes.forEach(node => {
        createNodeElement(node);
    });
    
    redrawConnections();
}

// Shape templates
const shapeDefaults = {
    'terminal': { width: 120, height: 60, text: 'Start' },
    'process': { width: 120, height: 60, text: 'Process' },
    'decision': { width: 100, height: 100, text: 'Decision?' },
    'data': { width: 120, height: 60, text: 'Data' },
    'document': { width: 120, height: 80, text: 'Document' },
    'predefined': { width: 120, height: 60, text: 'Subroutine' },
    'manual-input': { width: 120, height: 60, text: 'Input' },
    'manual-operation': { width: 120, height: 60, text: 'Manual' },
    'database': { width: 100, height: 80, text: 'Database' },
    'stored-data': { width: 120, height: 60, text: 'Stored Data' },
    'display': { width: 120, height: 60, text: 'Display' },
    'delay': { width: 100, height: 60, text: 'Delay' },
    'or': { width: 60, height: 60, text: 'OR' },
    'junction': { width: 40, height: 40, text: '' },
    'merge': { width: 80, height: 80, text: '' },
    'connector': { width: 30, height: 30, text: 'A' },
    'offpage': { width: 100, height: 80, text: 'Off-page' },
    'card': { width: 120, height: 80, text: 'Card' },
    'collate': { width: 80, height: 80, text: '' },
    'sort': { width: 80, height: 80, text: '' },
    'extract': { width: 80, height: 80, text: '' },
    'preparation': { width: 130, height: 60, text: 'Preparation' },
    'arrow': { width: 120, height: 60, text: '' }
};

function dragShape(event, type) {
    event.dataTransfer.setData('shape-type', type);
}

// Drop handler for canvas
canvas.addEventListener('dragover', function(e) {
    e.preventDefault();
});

canvas.addEventListener('drop', function(e) {
    e.preventDefault();
    
    const type = e.dataTransfer.getData('shape-type');
    if (!type) return;
    
    const canvasRect = canvas.getBoundingClientRect();
    const x = e.clientX - canvasRect.left;
    const y = e.clientY - canvasRect.top;
    
    const defaults = shapeDefaults[type];
    const node = {
        id: ++nodeIdCounter,
        type: type,
        x: x - defaults.width / 2,
        y: y - defaults.height / 2,
        width: defaults.width,
        height: defaults.height,
        text: defaults.text
    };
    
    nodes.push(node);
    createNodeElement(node);
});

function createNodeElement(node) {
    const div = document.createElement('div');
    div.className = `flowchart-node shape-${node.type}`;
    div.id = `node-${node.id}`;
    div.style.left = node.x + 'px';
    div.style.top = node.y + 'px';
    div.style.width = node.width + 'px';
    div.style.height = node.height + 'px';
    
    div.innerHTML = `
        <div class="node-text" contenteditable="true">${node.text}</div>
        <div class="delete-btn" onclick="deleteNode(${node.id})">×</div>
        <div class="connection-point conn-top" data-side="top"></div>
        <div class="connection-point conn-right" data-side="right"></div>
        <div class="connection-point conn-bottom" data-side="bottom"></div>
        <div class="connection-point conn-left" data-side="left"></div>
        <div class="resize-handle nw"></div>
        <div class="resize-handle n"></div>
        <div class="resize-handle ne"></div>
        <div class="resize-handle w"></div>
        <div class="resize-handle e"></div>
        <div class="resize-handle sw"></div>
        <div class="resize-handle s"></div>
        <div class="resize-handle se"></div>
    `;
    
    canvas.appendChild(div);
    
    // Node events
    div.addEventListener('mousedown', startDrag);
    
    // Text edit
    const textEl = div.querySelector('.node-text');
    textEl.addEventListener('blur', function() {
        const node = nodes.find(n => n.id == div.id.replace('node-', ''));
        if (node) {
            node.text = this.textContent;
        }
    });
    
    textEl.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            this.blur();
        }
    });
    
    // Resize handles
    div.querySelectorAll('.resize-handle').forEach(handle => {
        handle.addEventListener('mousedown', startResize);
    });
    
    // Connection points
    div.querySelectorAll('.connection-point').forEach(point => {
        point.addEventListener('mousedown', startConnection);
    });
}

function startDrag(e) {
    if (e.target.classList.contains('node-text') || 
        e.target.classList.contains('delete-btn') ||
        e.target.classList.contains('resize-handle') ||
        e.target.classList.contains('connection-point')) {
        return;
    }
    
    const nodeEl = e.currentTarget;
    const nodeId = parseInt(nodeEl.id.replace('node-', ''));
    
    // Select node
    selectNode(nodeId);
    
    // Start dragging
    isDragging = true;
    selectedNode = nodeId;
    dragStartX = e.clientX;
    dragStartY = e.clientY;
    
    const node = nodes.find(n => n.id === nodeId);
    nodeStartX = node.x;
    nodeStartY = node.y;
    
    e.preventDefault();
}

function startConnection(e) {
    e.stopPropagation();
    e.preventDefault();
    
    const nodeEl = e.target.closest('.flowchart-node');
    const nodeId = parseInt(nodeEl.id.replace('node-', ''));
    const side = e.target.dataset.side;
    
    console.log('Connection point clicked:', nodeId, side);
    
    if (!connectionStart) {
        // Start connection
        connectionStart = { nodeId, side };
        nodeEl.classList.add('connecting');
        connectionMode = true;
        canvas.classList.add('connection-mode');
        console.log('Connection started from node', nodeId, side);
    } else if (connectionStart.nodeId !== nodeId) {
        // Complete connection
        const newConnection = {
            from: connectionStart.nodeId,
            fromSide: connectionStart.side,
            to: nodeId,
            toSide: side
        };
        connections.push(newConnection);
        console.log('Connection created:', newConnection);
        
        document.getElementById(`node-${connectionStart.nodeId}`).classList.remove('connecting');
        connectionStart = null;
        connectionMode = false;
        canvas.classList.remove('connection-mode');
        redrawConnections();
    } else {
        console.log('Cannot connect node to itself');
    }
}

function startResize(e) {
    e.stopPropagation();
    
    isResizing = true;
    resizeHandle = e.target.classList[1]; // nw, n, ne, etc.
    
    const nodeEl = e.target.closest('.flowchart-node');
    const nodeId = parseInt(nodeEl.id.replace('node-', ''));
    selectedNode = nodeId;
    selectNode(nodeId);
    
    dragStartX = e.clientX;
    dragStartY = e.clientY;
    
    const node = nodes.find(n => n.id === nodeId);
    nodeStartX = node.x;
    nodeStartY = node.y;
    nodeStartWidth = node.width;
    nodeStartHeight = node.height;
    
    e.preventDefault();
}

document.addEventListener('mousemove', function(e) {
    if (isDragging && selectedNode) {
        const dx = e.clientX - dragStartX;
        const dy = e.clientY - dragStartY;
        
        const node = nodes.find(n => n.id === selectedNode);
        node.x = nodeStartX + dx;
        node.y = nodeStartY + dy;
        
        const nodeEl = document.getElementById(`node-${selectedNode}`);
        nodeEl.style.left = node.x + 'px';
        nodeEl.style.top = node.y + 'px';
        
        redrawConnections();
    }
    
    if (isResizing && selectedNode) {
        const dx = e.clientX - dragStartX;
        const dy = e.clientY - dragStartY;
        
        const node = nodes.find(n => n.id === selectedNode);
        const nodeEl = document.getElementById(`node-${selectedNode}`);
        
        let newWidth = nodeStartWidth;
        let newHeight = nodeStartHeight;
        let newX = nodeStartX;
        let newY = nodeStartY;
        
        if (resizeHandle.includes('e')) {
            newWidth = Math.max(50, nodeStartWidth + dx);
        }
        if (resizeHandle.includes('w')) {
            newWidth = Math.max(50, nodeStartWidth - dx);
            newX = nodeStartX + dx;
            if (newWidth === 50) newX = nodeStartX + nodeStartWidth - 50;
        }
        if (resizeHandle.includes('s')) {
            newHeight = Math.max(40, nodeStartHeight + dy);
        }
        if (resizeHandle.includes('n')) {
            newHeight = Math.max(40, nodeStartHeight - dy);
            newY = nodeStartY + dy;
            if (newHeight === 40) newY = nodeStartY + nodeStartHeight - 40;
        }
        
        node.width = newWidth;
        node.height = newHeight;
        node.x = newX;
        node.y = newY;
        
        nodeEl.style.width = newWidth + 'px';
        nodeEl.style.height = newHeight + 'px';
        nodeEl.style.left = newX + 'px';
        nodeEl.style.top = newY + 'px';
        
        redrawConnections();
    }
});

document.addEventListener('mouseup', function() {
    isDragging = false;
    isResizing = false;
    resizeHandle = null;
});

function selectNode(nodeId) {
    // Deselect all
    document.querySelectorAll('.flowchart-node').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Select this one
    const nodeEl = document.getElementById(`node-${nodeId}`);
    if (nodeEl) {
        nodeEl.classList.add('selected');
    }
}

function deleteNode(nodeId) {
    // Remove from array
    nodes = nodes.filter(n => n.id !== nodeId);
    
    // Remove connections
    connections = connections.filter(c => c.from !== nodeId && c.to !== nodeId);
    
    // Remove from DOM
    const nodeEl = document.getElementById(`node-${nodeId}`);
    if (nodeEl) {
        nodeEl.remove();
    }
    
    redrawConnections();
}

function redrawConnections() {
    svg.innerHTML = '';
    
    console.log('Redrawing connections:', connections.length);
    
    connections.forEach((conn, index) => {
        const fromNode = nodes.find(n => n.id === conn.from);
        const toNode = nodes.find(n => n.id === conn.to);
        
        if (!fromNode || !toNode) {
            console.log('Connection', index, 'skipped - node not found');
            return;
        }
        
        // Calculate connection point positions
        const fromPos = getConnectionPoint(fromNode, conn.fromSide || 'right');
        const toPos = getConnectionPoint(toNode, conn.toSide || 'left');
        
        console.log('Drawing connection from', fromPos, 'to', toPos);
        
        // Draw line
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        const d = `M ${fromPos.x} ${fromPos.y} L ${toPos.x} ${toPos.y}`;
        line.setAttribute('d', d);
        line.setAttribute('class', 'connection-line');
        line.setAttribute('stroke', '#333');
        line.setAttribute('stroke-width', '3');
        line.setAttribute('fill', 'none');
        line.onclick = function(e) {
            e.stopPropagation();
            if (confirm('Delete this connection?')) {
                connections = connections.filter(c => c !== conn);
                redrawConnections();
            }
        };
        svg.appendChild(line);
        
        // Draw arrow at the end
        const angle = Math.atan2(toPos.y - fromPos.y, toPos.x - fromPos.x);
        const arrowSize = 12;
        
        const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');
        const points = [
            [toPos.x, toPos.y],
            [toPos.x - arrowSize * Math.cos(angle - Math.PI / 6), toPos.y - arrowSize * Math.sin(angle - Math.PI / 6)],
            [toPos.x - arrowSize * Math.cos(angle + Math.PI / 6), toPos.y - arrowSize * Math.sin(angle + Math.PI / 6)]
        ];
        arrow.setAttribute('points', points.map(p => p.join(',')).join(' '));
        arrow.setAttribute('class', 'arrow');
        arrow.setAttribute('fill', '#333');
        arrow.onclick = function(e) {
            e.stopPropagation();
            if (confirm('Delete this connection?')) {
                connections = connections.filter(c => c !== conn);
                redrawConnections();
            }
        };
        svg.appendChild(arrow);
        
        console.log('Connection drawn successfully');
    });
}

function getConnectionPoint(node, side) {
    const centerX = node.x + node.width / 2;
    const centerY = node.y + node.height / 2;
    
    switch(side) {
        case 'top':
            return { x: centerX, y: node.y };
        case 'bottom':
            return { x: centerX, y: node.y + node.height };
        case 'left':
            return { x: node.x, y: centerY };
        case 'right':
            return { x: node.x + node.width, y: centerY };
        default:
            return { x: node.x + node.width, y: centerY };
    }
}

function toggleConnectionMode() {
    // Connection mode is now automatic when clicking connection points
    // This function is kept for compatibility but does nothing
    alert('To connect shapes: Click and hold a green dot on one shape, then click a green dot on another shape.');
}

function clearCanvas() {
    if (confirm('Clear all shapes and connections?')) {
        nodes = [];
        connections = [];
        canvas.querySelectorAll('.flowchart-node').forEach(el => el.remove());
        redrawConnections();
    }
}

function exportDiagram() {
    const data = {
        nodes: nodes,
        connections: connections,
        nodeIdCounter: nodeIdCounter
    };
    
    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = '{{ $diagram->name }}.json';
    a.click();
}

function saveDiagram(event) {
    const data = {
        nodes: nodes,
        connections: connections,
        nodeIdCounter: nodeIdCounter
    };
    
    fetch('/diagram/save/{{ $diagram->id }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            diagram_json: JSON.stringify(data)
        })
    })
    .then(res => res.json())
    .then(() => {
        const btn = event && event.target ? event.target : document.querySelector('.btn-primary');
        if (btn) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i> Saved!';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-success');
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.classList.remove('btn-success');
                btn.classList.add('btn-primary');
            }, 2000);
        } else {
            alert('Diagram saved successfully!');
        }
    })
    .catch(err => {
        alert('Error saving diagram');
        console.error(err);
    });
}

// Click canvas to deselect
canvas.addEventListener('click', function(e) {
    if (e.target === canvas) {
        document.querySelectorAll('.flowchart-node').forEach(el => {
            el.classList.remove('selected');
        });
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Delete' && selectedNode) {
        const activeEl = document.activeElement;
        if (!activeEl.classList.contains('node-text')) {
            deleteNode(selectedNode);
            selectedNode = null;
        }
    }
    
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        saveDiagram(e);
    }
});

</script>

@endsection