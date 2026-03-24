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
}

/* VIEW ONLY NODE */
.flowchart-node {
    position: absolute;
    background: white;
    border: 2px solid #333;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    font-size: 14px;
    padding: 10px;
    box-sizing: border-box;
    pointer-events: none; /* disable interaction */
}

/* Shapes */
.shape-terminal { border-radius: 50px; }
.shape-process { border-radius: 4px; }

.shape-decision {
    transform: rotate(45deg);
}
.shape-decision .node-text {
    transform: rotate(-45deg);
}

/* SVG */
svg {
    position: absolute;
    width: 100%;
    height: 100%;
    z-index: 1;
}

.connection-line {
    stroke: #333;
    stroke-width: 2;
    fill: none;
}

.arrow {
    fill: #333;
}
</style>

<div class="container-fluid">

    <div class="mb-3">
        <h4>{{ $diagram->name }}   <button class="btn btn-success btn-sm ml-3" onclick="replicateDiagram()">
        <i class="fas fa-copy"></i> Replicate
    </button></h4>
    </div>

    <div id="canvas" style="width:100%; height:750px; border:1px solid #ddd; border-radius: 8px;">
        <svg id="connectionSvg"></svg>
    </div>

</div>

<script>

let nodes = [];
let connections = [];

const canvas = document.getElementById('canvas');
const svg = document.getElementById('connectionSvg');

// Load saved diagram
const savedData = @json($diagram->diagram_json ? json_decode($diagram->diagram_json) : null);

if (savedData && savedData.nodes) {
    nodes = savedData.nodes;
    connections = savedData.connections || [];

    nodes.forEach(node => {
        createNodeElement(node);
    });

    redrawConnections();
}

// Create node (VIEW ONLY)
function createNodeElement(node) {
    const div = document.createElement('div');
    div.className = `flowchart-node shape-${node.type}`;
    div.style.left = node.x + 'px';
    div.style.top = node.y + 'px';
    div.style.width = node.width + 'px';
    div.style.height = node.height + 'px';

    div.innerHTML = `<div class="node-text">${node.text}</div>`;

    canvas.appendChild(div);
}

// Draw connections
function redrawConnections() {
    svg.innerHTML = '';

    connections.forEach(conn => {
        const fromNode = nodes.find(n => n.id === conn.from);
        const toNode = nodes.find(n => n.id === conn.to);

        if (!fromNode || !toNode) return;

        const fromPos = getConnectionPoint(fromNode, conn.fromSide || 'right');
        const toPos = getConnectionPoint(toNode, conn.toSide || 'left');

        // Line
        const line = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        const d = `M ${fromPos.x} ${fromPos.y} L ${toPos.x} ${toPos.y}`;
        line.setAttribute('d', d);
        line.setAttribute('class', 'connection-line');

        svg.appendChild(line);

        // Arrow
        const angle = Math.atan2(toPos.y - fromPos.y, toPos.x - fromPos.x);
        const size = 10;

        const arrow = document.createElementNS('http://www.w3.org/2000/svg', 'polygon');

        const points = [
            [toPos.x, toPos.y],
            [toPos.x - size * Math.cos(angle - Math.PI / 6), toPos.y - size * Math.sin(angle - Math.PI / 6)],
            [toPos.x - size * Math.cos(angle + Math.PI / 6), toPos.y - size * Math.sin(angle + Math.PI / 6)]
        ];

        arrow.setAttribute('points', points.map(p => p.join(',')).join(' '));
        arrow.setAttribute('class', 'arrow');

        svg.appendChild(arrow);
    });
}

// Get connection points
function getConnectionPoint(node, side) {
    const centerX = node.x + node.width / 2;
    const centerY = node.y + node.height / 2;

    switch(side) {
        case 'top': return { x: centerX, y: node.y };
        case 'bottom': return { x: centerX, y: node.y + node.height };
        case 'left': return { x: node.x, y: centerY };
        case 'right': return { x: node.x + node.width, y: centerY };
        default: return { x: node.x + node.width, y: centerY };
    }
}

</script>

@endsection