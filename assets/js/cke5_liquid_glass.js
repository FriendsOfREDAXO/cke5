/**
 * CKEditor 5 – Liquid Glass SVG-Filter (Chrome-Enhancement)
 * Basierend auf: https://kube.io/blog/liquid-glass-css-svg/
 *
 * Injiziert einen SVG-feDisplacementMap-Filter in den DOM und
 * aktiviert ihn auf CKEditor-Elementen (nur Chrome, @supports backdrop-filter: url()).
 *
 * Der Filter simuliert Lichtbrechung (Snell-Gesetz) am Glasrand
 * mittels vorberechneter Displacement Map.
 */

(function () {
    'use strict';

    /* ------------------------------------------------------------------
       Nur Chrome unterstützt backdrop-filter: url()
       Prüfen via @supports
    ------------------------------------------------------------------ */
    const supportsBackdropUrl = (function () {
        try {
            const el = document.createElement('div');
            el.style.backdropFilter = 'url(#x)';
            return el.style.backdropFilter !== '';
        } catch {
            return false;
        }
    })();

    if (!supportsBackdropUrl) return;

    /* ------------------------------------------------------------------
       Displacement Map berechnen (vereinfachte konvexe Glasoberfläche)
       Kreissymmetrie: für jeden Pixel den Brechungsvektor berechnen
    ------------------------------------------------------------------ */
    const SIZE = 128;
    const BEZEL = 0.30;      // Anteil der Brezel-Breite (30 % des Radius)
    const IOR = 1.45;        // Brechungsindex Glas
    const MAX_DISP = 18;     // maximale Pixel-Verschiebung

    /**
     * Konvexe Glasoberfläche: Höhenfunktion h(t) für t ∈ [0,1]
     * t=0 = Außenrand, t=1 = Ende der Brezel
     * Smooth convex bell curve
     */
    function surfaceHeight(t) {
        return Math.sin(t * Math.PI / 2);
    }

    /**
     * Ableitung der Höhenfunktion (Approximation)
     */
    function surfaceDerivative(t) {
        const dt = 0.001;
        return (surfaceHeight(Math.min(1, t + dt)) - surfaceHeight(Math.max(0, t - dt))) / (2 * dt);
    }

    /**
     * Brechungswinkel nach Snell-Descartes:
     * n1 * sin(θ1) = n2 * sin(θ2)
     */
    function refract(sinTheta1, n1, n2) {
        const sinTheta2 = (n1 / n2) * sinTheta1;
        if (Math.abs(sinTheta2) >= 1) return 0; // Totalreflexion
        return Math.asin(sinTheta2);
    }

    /* Displacement-Magnitude für jeden Abstand t vom Rand berechnen */
    const displacementMagnitudes = new Float32Array(SIZE / 2);
    for (let i = 0; i < SIZE / 2; i++) {
        const t = i / (SIZE / 2);
        if (t > BEZEL) {
            displacementMagnitudes[i] = 0;
            continue;
        }
        const tNorm = t / BEZEL;
        const deriv = surfaceDerivative(1 - tNorm);
        const normal = { x: -deriv, y: 1 };
        const len = Math.sqrt(normal.x * normal.x + normal.y * normal.y);
        normal.x /= len;
        normal.y /= len;

        const sinTheta1 = Math.abs(normal.x);
        const theta2 = refract(sinTheta1, 1.0, IOR);
        const displacement = Math.tan(theta2) * surfaceHeight(1 - tNorm);

        displacementMagnitudes[i] = displacement;
    }

    /* Normalisieren auf [0, 1] */
    let maxMag = 0;
    for (let i = 0; i < SIZE / 2; i++) {
        if (displacementMagnitudes[i] > maxMag) maxMag = displacementMagnitudes[i];
    }
    if (maxMag === 0) maxMag = 1;

    /* Displacement Map als Canvas zeichnen */
    const canvas = document.createElement('canvas');
    canvas.width = SIZE;
    canvas.height = SIZE;
    const ctx = canvas.getContext('2d');
    const imageData = ctx.createImageData(SIZE, SIZE);
    const data = imageData.data;

    const cx = SIZE / 2;
    const cy = SIZE / 2;

    for (let py = 0; py < SIZE; py++) {
        for (let px = 0; px < SIZE; px++) {
            const dx = px - cx;
            const dy = py - cy;
            const dist = Math.sqrt(dx * dx + dy * dy);
            const radius = SIZE / 2;
            const distFromEdge = radius - dist;

            let r = 128;
            let g = 128;

            if (distFromEdge >= 0 && distFromEdge < (SIZE / 2)) {
                const tIdx = Math.min(Math.floor(distFromEdge), SIZE / 2 - 1);
                const magnitude = displacementMagnitudes[tIdx] / maxMag;

                if (magnitude > 0 && dist > 0) {
                    const angle = Math.atan2(dy, dx);
                    const vecX = Math.cos(angle) * magnitude;
                    const vecY = Math.sin(angle) * magnitude;

                    r = Math.round(128 + vecX * 127);
                    g = Math.round(128 + vecY * 127);
                    r = Math.max(0, Math.min(255, r));
                    g = Math.max(0, Math.min(255, g));
                }
            }

            const idx = (py * SIZE + px) * 4;
            data[idx]     = r;
            data[idx + 1] = g;
            data[idx + 2] = 128;
            data[idx + 3] = 255;
        }
    }

    ctx.putImageData(imageData, 0, 0);
    const dataUrl = canvas.toDataURL('image/png');

    /* ------------------------------------------------------------------
       SVG-Filter in den DOM injizieren
    ------------------------------------------------------------------ */
    const svgNS = 'http://www.w3.org/2000/svg';
    const svg = document.createElementNS(svgNS, 'svg');
    svg.setAttribute('class', 'cke5-liquid-glass-svg');
    svg.setAttribute('aria-hidden', 'true');
    svg.style.cssText = 'position:absolute;width:0;height:0;overflow:hidden;';

    const filter = document.createElementNS(svgNS, 'filter');
    filter.setAttribute('id', 'cke5-liquid-glass-filter');
    filter.setAttribute('x', '0');
    filter.setAttribute('y', '0');
    filter.setAttribute('width', '100%');
    filter.setAttribute('height', '100%');
    filter.setAttribute('color-interpolation-filters', 'sRGB');

    const feImage = document.createElementNS(svgNS, 'feImage');
    feImage.setAttribute('href', dataUrl);
    feImage.setAttribute('x', '0');
    feImage.setAttribute('y', '0');
    feImage.setAttribute('width', String(SIZE));
    feImage.setAttribute('height', String(SIZE));
    feImage.setAttribute('preserveAspectRatio', 'xMidYMid slice');
    feImage.setAttribute('result', 'displacement_map');

    const feDisplace = document.createElementNS(svgNS, 'feDisplacementMap');
    feDisplace.setAttribute('in', 'SourceGraphic');
    feDisplace.setAttribute('in2', 'displacement_map');
    feDisplace.setAttribute('scale', String(MAX_DISP));
    feDisplace.setAttribute('xChannelSelector', 'R');
    feDisplace.setAttribute('yChannelSelector', 'G');

    filter.appendChild(feImage);
    filter.appendChild(feDisplace);
    svg.appendChild(filter);
    document.body.appendChild(svg);

    /* ------------------------------------------------------------------
       CKEditor-Elemente mit data-Attribut markieren
    ------------------------------------------------------------------ */
    function markEditorElements() {
        document.querySelectorAll(
            '.ck.ck-toolbar, .ck.ck-balloon-panel, .ck.ck-dropdown__panel'
        ).forEach(function (el) {
            if (!el.hasAttribute('data-lg-filter')) {
                el.setAttribute('data-lg-filter', 'active');
            }
        });
    }

    /* Einmalig und bei DOM-Änderungen durch CKEditor */
    markEditorElements();

    const observer = new MutationObserver(function (mutations) {
        let shouldMark = false;
        mutations.forEach(function (m) {
            m.addedNodes.forEach(function (node) {
                if (node.nodeType === 1 && node.classList &&
                    (node.classList.contains('ck-toolbar') ||
                     node.classList.contains('ck-balloon-panel') ||
                     node.classList.contains('ck-dropdown__panel'))) {
                    shouldMark = true;
                }
            });
        });
        if (shouldMark) markEditorElements();
    });

    observer.observe(document.body, { childList: true, subtree: true });
}());
