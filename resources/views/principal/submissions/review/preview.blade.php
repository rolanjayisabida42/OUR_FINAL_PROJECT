
@php
    $fileUrl = route(
        'principal.submissions.review.files.download',
        [
            'submission' => $submission,
            'file' => $file
        ]
    );

    $backUrl = route(
        'principal.submissions.review.show',
        $submission
    );

    $extension = strtolower(
        pathinfo(
            $file->original_name,
            PATHINFO_EXTENSION
        )
    );

    $expectedSize = (int) ($file->size_bytes ?? 0);
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>
        {{ $file->original_name }} | Excel Preview
    </title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

    <style>
        /* ======================================
           GLOBAL
        ====================================== */

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            font-family: Arial, sans-serif;
            color: #1e293b;
            background: #f8fafc;
        }

        button,
        a {
            font: inherit;
        }

        button {
            cursor: pointer;
        }

        .hidden {
            display: none !important;
        }


        /* ======================================
           APPLICATION LAYOUT
        ====================================== */

        .excel-app {
            display: flex;
            flex-direction: column;
            width: 100%;
            height: 100vh;
            height: 100dvh;
            min-height: 0;
            background: white;
        }


        /* ======================================
           TOP TOOLBAR
        ====================================== */

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 65px;
            padding: 12px 18px;
            background: #142b61;
            color: white;
            flex-shrink: 0;
        }

        .topbar-left,
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .topbar-left {
            flex: 1;
        }

        .excel-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 9px;
            background: #0d9488;
            color: white;
            font-size: 20px;
            font-weight: 800;
        }

        .filename-wrapper {
            min-width: 0;
        }

        .filename {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 14px;
            font-weight: 800;
        }

        .subtitle {
            margin-top: 5px;
            font-size: 11px;
            color: #bfdbfe;
        }

        .toolbar-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 13px;
            border: 1px solid #ffffff40;
            border-radius: 8px;
            background: #ffffff15;
            color: white;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .toolbar-btn:hover {
            background: #ffffff30;
        }

        .toolbar-btn.green {
            background: #0d9488;
            border-color: #0d9488;
        }

        .toolbar-btn.green:hover {
            background: #0f766e;
        }


        /* ======================================
           SECOND TOOLBAR
        ====================================== */

        .subbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding: 9px 16px;
            min-height: 48px;
            background: #f8fafc;
            border-bottom: 1px solid #cbd5e1;
            flex-shrink: 0;
        }

        .load-status {
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .subbar-controls {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .readonly {
            padding: 6px 10px;
            border-radius: 7px;
            background: #d1fae5;
            color: #047857;
            font-size: 11px;
            font-weight: bold;
        }

        .zoom-btn {
            width: 29px;
            height: 29px;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            background: white;
            color: #334155;
            font-size: 17px;
        }

        .zoom-value {
            min-width: 43px;
            font-size: 12px;
            font-weight: 700;
            text-align: center;
        }


        /* ======================================
           SPREADSHEET VIEWPORT
        ====================================== */

        .spreadsheet-viewport {
            flex: 1;
            min-height: 0;
            width: 100%;
            overflow: auto;
            background: white;
        }

        .grid-surface {
            position: relative;
            width: max-content;
            min-width: 100%;
        }

        .excel-table {
            border-collapse: separate;
            border-spacing: 0;
            table-layout: fixed;
            font-family: Arial, sans-serif;
            color: #000;
            background: white;
        }

        .excel-table th,
        .excel-table td {
            box-sizing: border-box;
            padding: 2px 4px;
            border-right: 1px solid #dbe2ea;
            border-bottom: 1px solid #dbe2ea;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            vertical-align: middle;
            font-size: 11px;
        }

        .excel-table td {
            overflow: hidden;
            background-color: white;
        }


        /* ======================================
           COLUMN HEADERS
        ====================================== */

        .excel-table thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            height: 30px;
            padding: 5px 3px;
            background: #e8eef6;
            color: #475569;
            font-weight: 700;
            text-align: center;
            overflow: visible;
        }


        /* ======================================
           ROW HEADERS
        ====================================== */

        .excel-table tbody th {
            position: sticky;
            left: 0;
            z-index: 6;
            width: 48px;
            min-width: 48px;
            padding: 3px;
            background: #e8eef6;
            color: #475569;
            font-size: 10px;
            font-weight: 500;
            text-align: center;
            overflow: visible;
        }

        .excel-table thead th:first-child {
            left: 0;
            z-index: 10;
            background: #dce6f2;
        }


        /* ======================================
           EXCEL BORDERS
        ====================================== */

        .excel-table td.diag-up {
            background-image: linear-gradient(
                135deg,
                transparent calc(50% - .5px),
                #6b7280 50%,
                transparent calc(50% + .5px)
            );
        }

        .excel-table td.diag-down {
            background-image: linear-gradient(
                45deg,
                transparent calc(50% - .5px),
                #6b7280 50%,
                transparent calc(50% + .5px)
            );
        }

        .excel-table td.diag-both {
            background-image:
                linear-gradient(
                    45deg,
                    transparent 49%,
                    #6b7280 50%,
                    transparent 51%
                ),
                linear-gradient(
                    135deg,
                    transparent 49%,
                    #6b7280 50%,
                    transparent 51%
                );
        }


        /* ======================================
           DRAG COLUMN RESIZER
        ====================================== */

        .excel-column-resizer {
            position: absolute;
            right: -4px;
            top: 0;
            width: 9px;
            height: 100%;
            z-index: 20;
            cursor: col-resize;
            touch-action: none;
        }

        .excel-column-resizer:hover {
            background: rgba(13, 148, 136, 0.45);
        }


        /* ======================================
           DRAG ROW RESIZER
        ====================================== */

        .excel-row-resizer {
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 9px;
            z-index: 20;
            cursor: row-resize;
            touch-action: none;
        }

        .excel-row-resizer:hover {
            background: rgba(13, 148, 136, 0.45);
        }


        /* ======================================
           DRAGGING CURSOR
        ====================================== */

        body.excel-resizing,
        body.excel-resizing * {
            user-select: none !important;
            -webkit-user-select: none !important;
        }

        body.excel-resizing-column,
        body.excel-resizing-column * {
            cursor: col-resize !important;
        }

        body.excel-resizing-row,
        body.excel-resizing-row * {
            cursor: row-resize !important;
        }


        /* ======================================
           WORKBOOK IMAGES
        ====================================== */

        .image-layer {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 2;
        }

        .image-layer img {
            position: absolute;
            object-fit: fill;
        }


        /* ======================================
           WORKSHEET TABS
        ====================================== */

        .sheet-tabs {
            display: flex;
            flex-shrink: 0;
            min-height: 45px;
            overflow-x: auto;
            border-top: 1px solid #cbd5e1;
            background: #f8fafc;
        }

        .sheet-tab {
            min-width: max-content;
            padding: 12px 20px;
            border: 0;
            border-right: 1px solid #e2e8f0;
            border-bottom: 3px solid transparent;
            background: transparent;
            color: #475569;
            font-size: 12px;
            font-weight: 700;
        }

        .sheet-tab:hover {
            background: #e2e8f0;
        }

        .sheet-tab.active {
            background: white;
            color: #0d9488;
            border-bottom-color: #0d9488;
        }


        /* ======================================
           BOTTOM BAR
        ====================================== */

        .bottom-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            min-height: 46px;
            padding: 8px 14px;
            border-top: 1px solid #cbd5e1;
            background: #f8fafc;
            flex-shrink: 0;
        }

        .bottom-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 7px;
        }

        .page-btn {
            padding: 7px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: white;
            color: #334155;
            font-size: 11px;
            font-weight: 700;
        }

        .page-btn:hover:not(:disabled) {
            background: #e2e8f0;
        }

        .page-btn:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .range-status {
            font-size: 11px;
            font-weight: 600;
            color: #64748b;
        }


        /* ======================================
           MESSAGES
        ====================================== */

        .message {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-height: 300px;
            padding: 40px 20px;
            text-align: center;
            color: #64748b;
        }

        .message h3 {
            margin: 0;
            font-size: 17px;
            color: #142b61;
        }

        .message p {
            max-width: 650px;
            margin: 0;
            font-size: 13px;
            line-height: 1.7;
            white-space: pre-wrap;
        }

        .message.error h3,
        .message.error p {
            color: #b91c1c;
        }


        /* ======================================
           MOBILE
        ====================================== */

        @media (max-width: 700px) {
            .topbar {
                padding: 10px;
            }

            .excel-logo {
                display: none;
            }

            .filename {
                max-width: 145px;
                font-size: 12px;
            }

            .subtitle {
                display: none;
            }

            .toolbar-btn {
                padding: 8px;
            }

            .bottom-bar {
                align-items: flex-start;
            }
        }

    </style>
</head>


<body>

<div class="excel-app" id="excelApp">

    <!-- ======================================
         TOP BAR
    ======================================= -->

    <header class="topbar">

        <div class="topbar-left">

            <a href="{{ $backUrl }}"
               class="toolbar-btn">
                ← Back
            </a>

            <div class="excel-logo">
                X
            </div>

            <div class="filename-wrapper">

                <div class="filename"
                     title="{{ $file->original_name }}">
                    {{ $file->original_name }}
                </div>

                <div class="subtitle">
                    SHS Forms · Excel Document Preview
                </div>

            </div>

        </div>


        <div class="topbar-right">

            <button id="fullscreenBtn"
                    type="button"
                    class="toolbar-btn">
                ⛶ Full Screen
            </button>

            <a href="{{ $fileUrl }}"
               class="toolbar-btn green">
                ↓ Download
            </a>

        </div>

    </header>


    <!-- ======================================
         SECOND TOOLBAR
    ======================================= -->

    <div class="subbar">

        <span id="excelStatus"
              class="load-status"
              role="status"
              aria-live="polite">
            Loading Excel workbook...
        </span>

        <div class="subbar-controls">

            <span class="readonly">
                Read Only
            </span>

            <button id="zoomOut"
                    type="button"
                    class="zoom-btn">
                −
            </button>

            <span id="zoomValue"
                  class="zoom-value">
                100%
            </span>

            <button id="zoomIn"
                    type="button"
                    class="zoom-btn">
                +
            </button>

        </div>

    </div>


    <!-- ======================================
         MAIN EXCEL VIEWER
    ======================================= -->

    <main id="viewport"
          class="spreadsheet-viewport">

        <!-- LOADING -->
        <div id="loading"
             class="message">

            <h3>
                Opening Excel Workbook
            </h3>

            <p>
                Reading worksheet values and formatting...
            </p>

        </div>


        <!-- ERROR -->
        <div id="error"
             class="message error hidden"
             role="alert">

            <h3>
                Unable to Preview Excel File
            </h3>

            <p id="errorMessage"></p>

        </div>


        <!-- EXCEL GRID -->
        <div id="gridSurface"
             class="grid-surface hidden">

            <table id="excelTable"
                   class="excel-table"
                   aria-label="Excel worksheet preview">
            </table>

            <div id="imageLayer"
                 class="image-layer">
            </div>

        </div>

    </main>


    <!-- ======================================
         WORKSHEET TABS
    ======================================= -->

    <nav id="sheetTabs"
         class="sheet-tabs"
         aria-label="Excel worksheets">
    </nav>


    <!-- ======================================
         BOTTOM BAR
    ======================================= -->

    <footer class="bottom-bar">

        <div class="bottom-actions">

            <button id="prevRows"
                    type="button"
                    class="page-btn">
                ← Previous Rows
            </button>

            <button id="nextRows"
                    type="button"
                    class="page-btn">
                Next Rows →
            </button>

            <button id="prevCols"
                    type="button"
                    class="page-btn">
                ← Columns
            </button>

            <button id="nextCols"
                    type="button"
                    class="page-btn">
                Columns →
            </button>

        </div>

        <span id="rangeStatus"
              class="range-status">
            No worksheet loaded
        </span>

    </footer>

</div>


<!-- ======================================
     EXCELJS LIBRARY
======================================= -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', async function () {

    // ======================================
    // LARAVEL VALUES
    // ======================================

    const fileUrl = @json($fileUrl);
    const extension = @json($extension);
    const expectedFileSize = @json($expectedSize);


    // ======================================
    // HTML ELEMENTS
    // ======================================

    const get = id => document.getElementById(id);

    const app = get('excelApp');
    const viewport = get('viewport');
    const surface = get('gridSurface');
    const table = get('excelTable');
    const imageLayer = get('imageLayer');

    const tabs = get('sheetTabs');

    const status = get('excelStatus');

    const loading = get('loading');
    const errorBox = get('error');
    const errorMessage = get('errorMessage');

    const rangeStatus = get('rangeStatus');


    // ======================================
    // VIEW SETTINGS
    // ======================================

    const PAGE_ROWS = 100;
    const PAGE_COLS = 60;

    const ROW_HEADER_WIDTH = 48;
    const COLUMN_HEADER_HEIGHT = 30;

    let workbook = null;

    let sheetIndex = 0;

    let rowOffset = 0;
    let colOffset = 0;

    let zoom = 100;

    let imageUrls = [];

    let renderedTableWidth = 0;


    // ======================================
    // CUSTOM RESIZE STORAGE
    // ======================================

    // These Maps keep resized dimensions while
    // the browser tab is open.
    //
    // Each worksheet stores its own dimensions.
    //
    // No database changes are made.

    const customColumnWidths = new Map();

    const customRowHeights = new Map();


    function dimensionKey(index) {
        return sheetIndex + ':' + index;
    }


    // ======================================
    // EXCEL COLOR CONVERTER
    // ======================================

    function excelColor(color) {

        if (!color || !color.argb) {
            return null;
        }

        const hex = String(color.argb)
            .replace(/^#/, '');

        if (/^[0-9a-fA-F]{8}$/.test(hex)) {
            return '#' + hex.slice(2);
        }

        if (/^[0-9a-fA-F]{6}$/.test(hex)) {
            return '#' + hex;
        }

        return null;
    }


    // ======================================
    // EXCEL BORDER CONVERTER
    // ======================================

    function cssBorder(side) {

        if (!side || !side.style) {
            return null;
        }

        const style = side.style;

        const width =
            style === 'thick' ? 3 :
            [
                'medium',
                'mediumDashed',
                'mediumDashDot',
                'mediumDashDotDot'
            ].includes(style) ? 2 : 1;

        const line =
            style === 'double' ? 'double' :
            style === 'dotted' ||
            style === 'hair' ? 'dotted' :
            style.toLowerCase().includes('dash')
                ? 'dashed'
                : 'solid';

        const color =
            excelColor(side.color) || '#222222';

        return `${width}px ${line} ${color}`;
    }


    // ======================================
    // APPLY CELL FORMATTING
    // ======================================

    function applyStyle(td, cell) {

        if (!cell) {
            return;
        }

        const font = cell.font || {};
        const fill = cell.fill || {};
        const border = cell.border || {};
        const alignment = cell.alignment || {};


        // FONT
        if (font.name) {

            td.style.fontFamily =
                '"' +
                String(font.name).replace(/["']/g, '') +
                '", Arial, sans-serif';
        }

        if (font.size) {
            td.style.fontSize = font.size + 'pt';
        }

        if (font.bold) {
            td.style.fontWeight = 'bold';
        }

        if (font.italic) {
            td.style.fontStyle = 'italic';
        }

        if (font.underline) {
            td.style.textDecoration = 'underline';
        }

        const fontColor = excelColor(font.color);

        if (fontColor) {
            td.style.color = fontColor;
        }


        // BACKGROUND
        if (
            fill.type === 'pattern' &&
            fill.pattern === 'solid'
        ) {

            const color = excelColor(fill.fgColor);

            if (color) {
                td.style.backgroundColor = color;
            }
        }


        // BORDERS
        for (const side of [
            'top',
            'right',
            'bottom',
            'left'
        ]) {

            const value = cssBorder(border[side]);

            if (value) {

                const property =
                    'border' +
                    side.charAt(0).toUpperCase() +
                    side.slice(1);

                td.style[property] = value;
            }
        }


        // DIAGONAL LINES
        if (border.diagonal) {

            const diagonal = border.diagonal;

            if (diagonal.up && diagonal.down) {

                td.classList.add('diag-both');

            } else if (diagonal.up) {

                td.classList.add('diag-up');

            } else if (diagonal.down) {

                td.classList.add('diag-down');
            }
        }


        // HORIZONTAL ALIGNMENT
        const horizontal = alignment.horizontal;

        if (
            horizontal === 'center' ||
            horizontal === 'centerContinuous'
        ) {

            td.style.textAlign = 'center';

        } else if (horizontal === 'right') {

            td.style.textAlign = 'right';

        } else if (horizontal === 'justify') {

            td.style.textAlign = 'justify';

        } else {

            td.style.textAlign = 'left';
        }


        // VERTICAL ALIGNMENT
        td.style.verticalAlign =
            alignment.vertical === 'top' ? 'top' :
            alignment.vertical === 'bottom' ? 'bottom' :
            'middle';


        // WRAPPING
        if (alignment.wrapText === false) {
            td.style.whiteSpace = 'nowrap';
        }


        // ROTATED TEXT
        const rotation = alignment.textRotation;

        if (
            typeof rotation === 'number' &&
            Math.abs(rotation) === 90
        ) {

            td.style.writingMode = 'vertical-rl';
        }
    }


    // ======================================
    // FORMAT CELL VALUE
    // ======================================

    function getCellText(cell) {

        if (!cell) {
            return '';
        }

        const value = cell.value;

        if (
            value === null ||
            value === undefined
        ) {
            return '';
        }

        if (value instanceof Date) {

            return value.toLocaleDateString('en-PH');
        }

        if (typeof value === 'object') {

            if (Array.isArray(value.richText)) {

                return value.richText
                    .map(item => item.text || '')
                    .join('');
            }

            if (value.result !== undefined) {

                return String(value.result ?? '');
            }

            if (value.text !== undefined) {

                return String(value.text);
            }

            return '';
        }

        if (
            typeof value === 'number' &&
            cell.numFmt &&
            cell.numFmt.includes('%')
        ) {

            return (value * 100).toFixed(0) + '%';
        }

        return String(value);
    }


    // ======================================
    // ORIGINAL COLUMN WIDTH
    // ======================================

    function columnWidth(sheet, columnNumber) {

        const override = customColumnWidths.get(
            dimensionKey(columnNumber)
        );

        if (override !== undefined) {
            return override;
        }

        const excelWidth =
            sheet.getColumn(columnNumber).width ||
            8.43;

        return Math.max(
            23,
            Math.min(
                420,
                Math.round(excelWidth * 7 + 5)
            )
        );
    }


    // ======================================
    // ORIGINAL ROW HEIGHT
    // ======================================

    function rowHeight(sheet, rowNumber) {

        const override = customRowHeights.get(
            dimensionKey(rowNumber)
        );

        if (override !== undefined) {
            return override;
        }

        const points =
            sheet.getRow(rowNumber).height ||
            sheet.properties.defaultRowHeight ||
            15;

        return Math.max(
            15,
            Math.min(
                250,
                Math.round(points * 96 / 72)
            )
        );
    }


    // ======================================
    // ADDRESS HELPERS
    // ======================================

    function columnNumber(letters) {

        let result = 0;

        for (const letter of letters) {

            result =
                result * 26 +
                letter.charCodeAt(0) - 64;
        }

        return result;
    }


    function parseAddress(address) {

        const match = String(address)
            .replace(/\$/g, '')
            .match(/^([A-Z]+)([0-9]+)$/);

        if (!match) {
            return null;
        }

        return {
            row: Number(match[2]),
            col: columnNumber(match[1])
        };
    }


    function columnLetter(number) {

        let result = '';

        while (number > 0) {

            number--;

            result =
                String.fromCharCode(
                    65 + number % 26
                ) + result;

            number = Math.floor(number / 26);
        }

        return result;
    }


    // ======================================
    // WORKSHEET MERGED RANGES
    // ======================================

    function getMerges(sheet) {

        const merges = sheet.model.merges || [];

        return merges
            .map(function (range) {

                const [start, end] = range.split(':');

                const a = parseAddress(start);
                const b = parseAddress(end || start);

                if (!a || !b) {
                    return null;
                }

                return { a, b };

            })
            .filter(Boolean);
    }


    // ======================================
    // CLEAN UP IMAGE RESOURCES
    // ======================================

    function clearImages() {

        for (const url of imageUrls) {
            URL.revokeObjectURL(url);
        }

        imageUrls = [];

        imageLayer.replaceChildren();
    }


    // ======================================
    // IMAGE POSITION CALCULATION
    // ======================================

    function axisPosition(
        anchor,
        firstIndex,
        sizeFunction
    ) {

        const whole = Math.floor(anchor);
        const fraction = anchor - whole;

        let result = 0;

        if (whole >= firstIndex) {

            for (
                let i = firstIndex;
                i < whole;
                i++
            ) {
                result += sizeFunction(i);
            }

        } else {

            for (
                let i = whole;
                i < firstIndex;
                i++
            ) {
                result -= sizeFunction(i);
            }
        }

        result += fraction * sizeFunction(whole);

        return result;
    }


    // ======================================
    // DISPLAY WORKBOOK IMAGES
    // ======================================

    function drawImages(
        sheet,
        firstRow,
        firstCol,
        tableWidth
    ) {

        clearImages();

        if (typeof sheet.getImages !== 'function') {
            return;
        }

        imageLayer.style.width =
            tableWidth + 'px';

        imageLayer.style.height =
            table.offsetHeight + 'px';


        for (const item of sheet.getImages()) {

            try {

                const anchor = item.range;

                if (!anchor?.tl) {
                    continue;
                }

                const media = workbook.getImage(
                    item.imageId
                );

                if (!media) {
                    continue;
                }

                let source = '';

                if (media.buffer) {

                    const mime =
                        media.extension === 'jpg'
                            ? 'image/jpeg'
                            : 'image/' +
                              (media.extension || 'png');

                    const blob = new Blob(
                        [media.buffer],
                        { type: mime }
                    );

                    source = URL.createObjectURL(blob);

                    imageUrls.push(source);

                } else if (media.base64) {

                    source = media.base64;

                } else {

                    continue;
                }


                const colSize = index =>
                    columnWidth(sheet, index + 1);

                const rowSize = index =>
                    rowHeight(sheet, index + 1);


                const x =
                    ROW_HEADER_WIDTH +
                    axisPosition(
                        anchor.tl.col,
                        firstCol - 1,
                        colSize
                    );

                const y =
                    COLUMN_HEADER_HEIGHT +
                    axisPosition(
                        anchor.tl.row,
                        firstRow - 1,
                        rowSize
                    );


                let width = 0;
                let height = 0;

                if (anchor.br) {

                    const right =
                        ROW_HEADER_WIDTH +
                        axisPosition(
                            anchor.br.col,
                            firstCol - 1,
                            colSize
                        );

                    const bottom =
                        COLUMN_HEADER_HEIGHT +
                        axisPosition(
                            anchor.br.row,
                            firstRow - 1,
                            rowSize
                        );

                    width = right - x;
                    height = bottom - y;

                } else if (anchor.ext) {

                    width = anchor.ext.width;
                    height = anchor.ext.height;

                } else {

                    continue;
                }


                if (width <= 0 || height <= 0) {
                    continue;
                }

                // Skip images completely outside
                // the visible table section.
                if (
                    x + width < ROW_HEADER_WIDTH ||
                    y + height < COLUMN_HEADER_HEIGHT ||
                    x > tableWidth ||
                    y > table.offsetHeight
                ) {

                    continue;
                }


                const img = document.createElement('img');

                img.src = source;

                img.alt = 'Excel workbook image';

                img.style.left =
                    Math.round(x) + 'px';

                img.style.top =
                    Math.round(y) + 'px';

                img.style.width =
                    Math.round(width) + 'px';

                img.style.height =
                    Math.round(height) + 'px';

                imageLayer.appendChild(img);

            } catch (exception) {

                console.warn(
                    'Excel image render warning:',
                    exception
                );
            }
        }
    }


    // ======================================
    // DRAG TO RESIZE ROW OR COLUMN
    // ======================================

    function addResizeHandle(
        header,
        direction,
        index,
        originalSize
    ) {

        const isColumn = direction === 'column';

        const handle = document.createElement('span');

        handle.className = isColumn
            ? 'excel-column-resizer'
            : 'excel-row-resizer';

        handle.title = isColumn
            ? 'Drag to resize column'
            : 'Drag to resize row';

        handle.setAttribute('aria-hidden', 'true');

        header.appendChild(handle);


        handle.addEventListener(
            'pointerdown',
            function (event) {

                if (event.button !== 0) {
                    return;
                }

                event.preventDefault();
                event.stopPropagation();

                const pointerId = event.pointerId;

                const startPosition = isColumn
                    ? event.clientX
                    : event.clientY;

                const startingTableWidth =
                    renderedTableWidth;

                const visibleFirstCol = colOffset + 1;
                const visibleFirstRow = rowOffset + 1;

                const currentSheetIndex = sheetIndex;

                const minSize = isColumn ? 25 : 16;
                const maxSize = isColumn ? 800 : 350;

                let currentSize = originalSize;


                // ==================================
                // ENABLE RESIZING CURSOR
                // ==================================

                document.body.classList.add(
                    'excel-resizing'
                );

                document.body.classList.add(
                    isColumn
                        ? 'excel-resizing-column'
                        : 'excel-resizing-row'
                );


                // ==================================
                // POINTER MOVE
                // ==================================

                function onMove(moveEvent) {

                    if (moveEvent.pointerId !== pointerId) {
                        return;
                    }

                    moveEvent.preventDefault();

                    const currentPosition = isColumn
                        ? moveEvent.clientX
                        : moveEvent.clientY;

                    const zoomFactor = zoom / 100;

                    const delta =
                        (currentPosition - startPosition) /
                        zoomFactor;

                    const newSize = Math.max(
                        minSize,
                        Math.min(
                            maxSize,
                            Math.round(
                                originalSize + delta
                            )
                        )
                    );

                    if (newSize === currentSize) {
                        return;
                    }

                    currentSize = newSize;

                    const key =
                        currentSheetIndex + ':' + index;


                    // ==================================
                    // RESIZE COLUMN LIVE
                    // ==================================

                    if (isColumn) {

                        customColumnWidths.set(
                            key,
                            newSize
                        );

                        const visibleIndex =
                            index - visibleFirstCol + 1;

                        const colgroup =
                            table.querySelector('colgroup');

                        const columnElement =
                            colgroup?.children[
                                visibleIndex
                            ];

                        if (columnElement) {

                            columnElement.style.width =
                                newSize + 'px';
                        }

                        const newTableWidth =
                            startingTableWidth +
                            newSize -
                            originalSize;

                        table.style.width =
                            newTableWidth + 'px';

                        imageLayer.style.width =
                            newTableWidth + 'px';


                    // ==================================
                    // RESIZE ROW LIVE
                    // ==================================

                    } else {

                        customRowHeights.set(
                            key,
                            newSize
                        );

                        const visibleIndex =
                            index - visibleFirstRow;

                        const rowElement =
                            table.tBodies[0]
                                ?.rows[visibleIndex];

                        if (rowElement) {

                            rowElement.style.height =
                                newSize + 'px';

                            if (rowElement.cells[0]) {

                                rowElement.cells[0]
                                    .style.height =
                                        newSize + 'px';
                            }
                        }
                    }
                }


                // ==================================
                // END RESIZING
                // ==================================

                function onEnd(endEvent) {

                    if (endEvent.pointerId !== pointerId) {
                        return;
                    }

                    window.removeEventListener(
                        'pointermove',
                        onMove
                    );

                    window.removeEventListener(
                        'pointerup',
                        onEnd
                    );

                    window.removeEventListener(
                        'pointercancel',
                        onEnd
                    );

                    document.body.classList.remove(
                        'excel-resizing'
                    );

                    document.body.classList.remove(
                        'excel-resizing-column'
                    );

                    document.body.classList.remove(
                        'excel-resizing-row'
                    );


                    // Refresh merged cells and images
                    // while retaining the scroll position.
                    renderSheet(true);
                }


                window.addEventListener(
                    'pointermove',
                    onMove,
                    { passive: false }
                );

                window.addEventListener(
                    'pointerup',
                    onEnd
                );

                window.addEventListener(
                    'pointercancel',
                    onEnd
                );
            }
        );
    }


    // ======================================
    // RENDER SELECTED WORKSHEET
    // ======================================

    function renderSheet(preserveScroll = false) {

        if (!workbook) {
            return;
        }

        const savedScrollX = viewport.scrollLeft;
        const savedScrollY = viewport.scrollTop;

        const sheet = workbook.worksheets[sheetIndex];

        const totalRows = Math.max(
            sheet.rowCount,
            1
        );

        const totalCols = Math.max(
            sheet.columnCount,
            1
        );


        const firstRow = rowOffset + 1;

        const lastRow = Math.min(
            totalRows,
            firstRow + PAGE_ROWS - 1
        );

        const firstCol = colOffset + 1;

        const lastCol = Math.min(
            totalCols,
            firstCol + PAGE_COLS - 1
        );


        // ==================================
        // CLEAR PREVIOUS TABLE
        // ==================================

        table.replaceChildren();

        clearImages();


        // ==================================
        // COLUMN WIDTHS
        // ==================================

        let tableWidth = ROW_HEADER_WIDTH;

        const colgroup = document.createElement(
            'colgroup'
        );

        const rowHeaderColumn =
            document.createElement('col');

        rowHeaderColumn.style.width =
            ROW_HEADER_WIDTH + 'px';

        colgroup.appendChild(rowHeaderColumn);


        const colSizes = {};

        for (
            let c = firstCol;
            c <= lastCol;
            c++
        ) {

            const width = columnWidth(sheet, c);

            colSizes[c] = width;

            tableWidth += width;

            const col = document.createElement('col');

            col.style.width = width + 'px';

            colgroup.appendChild(col);
        }

        table.appendChild(colgroup);

        table.style.width = tableWidth + 'px';

        renderedTableWidth = tableWidth;


        // ==================================
        // MERGED CELLS
        // ==================================

        const mergeRoots = new Map();
        const covered = new Set();


        for (const merge of getMerges(sheet)) {

            const r1 = Math.max(
                firstRow,
                merge.a.row
            );

            const r2 = Math.min(
                lastRow,
                merge.b.row
            );

            const c1 = Math.max(
                firstCol,
                merge.a.col
            );

            const c2 = Math.min(
                lastCol,
                merge.b.col
            );

            if (r1 > r2 || c1 > c2) {
                continue;
            }

            const key = r1 + ':' + c1;

            mergeRoots.set(key, {

                originalRow: merge.a.row,

                originalCol: merge.a.col,

                rowSpan: r2 - r1 + 1,

                colSpan: c2 - c1 + 1
            });


            for (let r = r1; r <= r2; r++) {

                for (let c = c1; c <= c2; c++) {

                    if (r === r1 && c === c1) {
                        continue;
                    }

                    covered.add(r + ':' + c);
                }
            }
        }


        // ==================================
        // COLUMN HEADINGS
        // ==================================

        const thead = document.createElement('thead');

        const headRow = document.createElement('tr');

        const corner = document.createElement('th');

        headRow.appendChild(corner);


        for (
            let c = firstCol;
            c <= lastCol;
            c++
        ) {

            const th = document.createElement('th');

            th.textContent = columnLetter(c);

            // NEW: Draggable column boundary
            addResizeHandle(
                th,
                'column',
                c,
                colSizes[c]
            );

            headRow.appendChild(th);
        }

        thead.appendChild(headRow);

        table.appendChild(thead);


        // ==================================
        // WORKSHEET ROWS
        // ==================================

        const tbody = document.createElement('tbody');


        for (
            let r = firstRow;
            r <= lastRow;
            r++
        ) {

            const tr = document.createElement('tr');

            const height = rowHeight(sheet, r);

            tr.style.height = height + 'px';


            // ==================================
            // ROW NUMBER HEADER
            // ==================================

            const rowHeader = document.createElement(
                'th'
            );

            rowHeader.textContent = String(r);

            rowHeader.style.height =
                height + 'px';


            // NEW: Draggable row boundary
            addResizeHandle(
                rowHeader,
                'row',
                r,
                height
            );

            tr.appendChild(rowHeader);


            // ==================================
            // WORKSHEET CELLS
            // ==================================

            for (
                let c = firstCol;
                c <= lastCol;
                c++
            ) {

                const key = r + ':' + c;

                if (covered.has(key)) {
                    continue;
                }


                const td = document.createElement('td');

                const merge = mergeRoots.get(key);

                const actualRow = merge
                    ? merge.originalRow
                    : r;

                const actualCol = merge
                    ? merge.originalCol
                    : c;

                const cell = sheet.getCell(
                    actualRow,
                    actualCol
                );


                // DISPLAY CELL VALUE
                td.textContent = getCellText(cell);


                // DISPLAY EXCEL FORMATTING
                applyStyle(td, cell);


                // MERGED CELL DIMENSIONS
                if (merge) {

                    td.rowSpan = merge.rowSpan;

                    td.colSpan = merge.colSpan;
                }

                tr.appendChild(td);
            }

            tbody.appendChild(tr);
        }


        table.appendChild(tbody);


        // ==================================
        // REDRAW WORKBOOK IMAGES
        // ==================================

        drawImages(
            sheet,
            firstRow,
            firstCol,
            tableWidth
        );


        // ==================================
        // UPDATE PAGINATION
        // ==================================

        get('prevRows').disabled =
            rowOffset === 0;

        get('nextRows').disabled =
            lastRow >= totalRows;

        get('prevCols').disabled =
            colOffset === 0;

        get('nextCols').disabled =
            lastCol >= totalCols;


        rangeStatus.textContent =
            'Rows ' +
            firstRow +
            '–' +
            lastRow +
            ' | Columns ' +
            columnLetter(firstCol) +
            '–' +
            columnLetter(lastCol);


        status.textContent =
            sheet.name +
            ' · Enhanced Excel Preview';


        // ==================================
        // SHOW GRID
        // ==================================

        surface.classList.remove('hidden');

        updateZoom();


        // ==================================
        // PRESERVE SCROLL AFTER RESIZING
        // ==================================

        if (preserveScroll) {

            viewport.scrollLeft = savedScrollX;
            viewport.scrollTop = savedScrollY;

        } else {

            viewport.scrollLeft = 0;
            viewport.scrollTop = 0;
        }


        // ==================================
        // UPDATE WORKSHEET TABS
        // ==================================

        [...tabs.children].forEach(
            function (tab, index) {

                tab.classList.toggle(
                    'active',
                    index === sheetIndex
                );
            }
        );
    }


    // ======================================
    // WORKSHEET TABS
    // ======================================

    function renderTabs() {

        tabs.replaceChildren();


        workbook.worksheets.forEach(
            function (sheet, index) {

                const button = document.createElement(
                    'button'
                );

                button.type = 'button';

                button.className = 'sheet-tab';

                button.textContent = sheet.name;


                button.addEventListener(
                    'click',
                    function () {

                        sheetIndex = index;

                        rowOffset = 0;
                        colOffset = 0;

                        renderSheet();
                    }
                );

                tabs.appendChild(button);
            }
        );
    }


    // ======================================
    // ROW NAVIGATION
    // ======================================

    get('prevRows').addEventListener(
        'click',
        function () {

            rowOffset = Math.max(
                0,
                rowOffset - PAGE_ROWS
            );

            renderSheet();
        }
    );


    get('nextRows').addEventListener(
        'click',
        function () {

            rowOffset += PAGE_ROWS;

            renderSheet();
        }
    );


    // ======================================
    // COLUMN NAVIGATION
    // ======================================

    get('prevCols').addEventListener(
        'click',
        function () {

            colOffset = Math.max(
                0,
                colOffset - PAGE_COLS
            );

            renderSheet();
        }
    );


    get('nextCols').addEventListener(
        'click',
        function () {

            colOffset += PAGE_COLS;

            renderSheet();
        }
    );


    // ======================================
    // ZOOM CONTROLS
    // ======================================

    function updateZoom() {

        surface.style.zoom = zoom + '%';

        get('zoomValue').textContent =
            zoom + '%';
    }


    get('zoomOut').addEventListener(
        'click',
        function () {

            zoom = Math.max(
                50,
                zoom - 10
            );

            updateZoom();
        }
    );


    get('zoomIn').addEventListener(
        'click',
        function () {

            zoom = Math.min(
                200,
                zoom + 10
            );

            updateZoom();
        }
    );


    // ======================================
    // FULL SCREEN
    // ======================================

    get('fullscreenBtn').addEventListener(
        'click',
        async function () {

            try {

                if (!document.fullscreenElement) {

                    await app.requestFullscreen();

                } else {

                    await document.exitFullscreen();
                }

            } catch (exception) {

                console.error(
                    'Fullscreen error:',
                    exception
                );
            }
        }
    );


    // ======================================
    // LOAD EXCEL DOCUMENT
    // ======================================

    try {

        // VERIFY EXCELJS
        if (typeof ExcelJS === 'undefined') {

            throw new Error(
                'ExcelJS library could not load. ' +
                'Check your internet connection.'
            );
        }


        // SUPPORTED FILE TYPE
        if (extension !== 'xlsx') {

            throw new Error(
                'Enhanced Excel Preview currently supports ' +
                'XLSX files only.'
            );
        }


        // FETCH PRIVATE EXCEL FILE
        const response = await fetch(
            fileUrl,
            {
                method: 'GET',
                credentials: 'same-origin',
                cache: 'no-store'
            }
        );


        if (!response.ok) {

            throw new Error(
                'Unable to load Excel file. HTTP ' +
                response.status
            );
        }


        // READ BINARY DATA
        const buffer = await response.arrayBuffer();

        const bytes = new Uint8Array(buffer);


        // VERIFY RESPONSE SIZE
        if (
            expectedFileSize > 0 &&
            bytes.length !== expectedFileSize
        ) {

            throw new Error(
                'Received file size does not match ' +
                'the original uploaded file.'
            );
        }


        // VERIFY XLSX HEADER
        if (
            bytes[0] !== 0x50 ||
            bytes[1] !== 0x4B ||
            bytes[2] !== 0x03 ||
            bytes[3] !== 0x04
        ) {

            throw new Error(
                'Laravel did not return a valid XLSX file.'
            );
        }


        // PARSE WORKBOOK
        status.textContent =
            'Loading Excel formatting...';

        workbook = new ExcelJS.Workbook();

        await workbook.xlsx.load(buffer);


        // VERIFY WORKSHEETS
        if (!workbook.worksheets.length) {

            throw new Error(
                'No readable worksheets were found.'
            );
        }


        // DISPLAY WORKBOOK
        loading.classList.add('hidden');

        renderTabs();

        renderSheet();


    } catch (exception) {

        console.error(
            'Excel Preview Error:',
            exception
        );

        loading.classList.add('hidden');

        surface.classList.add('hidden');

        errorMessage.textContent =
            exception.message ||
            'Unable to preview the Excel file.';

        errorBox.classList.remove('hidden');
    }

});
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/exceljs/4.4.0/exceljs.min.js"></script>

</body>
</html>
