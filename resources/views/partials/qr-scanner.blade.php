{{-- ADDED: the QR scanner, moved out of student/evaluation.blade.php so the student dashboard can show the same one. Takes no variables; the page supplies its own heading and back link. --}}
            {{-- CHANGED: the viewfinder fills the phone's width (up to 20rem) so the code is easier to frame. --}}
            <div id="qr-viewfinder" class="qr-viewfinder relative mt-6 w-full max-w-[20rem] aspect-square rounded-xl overflow-hidden bg-ink-900">
                <video id="qr-video" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-300" playsinline muted aria-hidden="true"></video>

                <div id="qr-idle" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-ink-300 px-6">
                    <ion-icon name="qr-code-outline" class="text-5xl text-ink-500" aria-hidden="true"></ion-icon>
                    <p class="text-xs font-semibold">Camera is off</p>
                </div>

                <span class="qr-corner qr-corner--tl" aria-hidden="true"></span>
                <span class="qr-corner qr-corner--tr" aria-hidden="true"></span>
                <span class="qr-corner qr-corner--bl" aria-hidden="true"></span>
                <span class="qr-corner qr-corner--br" aria-hidden="true"></span>
                <span id="qr-scanline" class="qr-scanline hidden" aria-hidden="true"></span>
            </div>

            <p id="qr-status" class="mt-4 min-h-[2.5rem] max-w-[20rem] text-xs font-semibold text-ink-600" role="status" aria-live="polite">
                Tap <span class="font-bold text-ink-900">Start scanning</span> and allow camera access.
            </p>

            {{-- CHANGED: full-width, 44px-tall buttons on phones (thumb-sized), side by side from sm up. --}}
            <div class="mt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-2 w-full max-w-[20rem] sm:max-w-none sm:w-auto">
                <button type="button" id="qr-start" class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 min-h-11 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-md transition-colors shadow-2xs cursor-pointer">
                    <ion-icon name="camera-outline" class="text-sm" aria-hidden="true"></ion-icon>
                    <span>Start scanning</span>
                </button>
                <label class="inline-flex items-center justify-center gap-1.5 px-5 py-2.5 min-h-11 bg-white hover:bg-surface text-ink-700 text-xs font-bold rounded-md border border-ink-100 transition-colors cursor-pointer focus-within:ring-2 focus-within:ring-brand-600/30">
                    <ion-icon name="image-outline" class="text-sm" aria-hidden="true"></ion-icon>
                    {{-- CHANGED: "Scan from a photo" is now "Scan a saved image" - nothing is uploaded, the code is read in the browser - and capture="environment" is gone so the button opens the gallery or file picker instead of forcing the camera. --}}
                    <span>Scan a saved image</span>
                    <input type="file" id="qr-photo" accept="image/*" class="sr-only">
                </label>

        <style>
            {{-- CHANGED: the brand green was written out as oklch(0.78 0.15 155) four times; it now comes from the token ramp in resources/css/theme.css. --}}
            .qr-corner { position: absolute; width: 2.25rem; height: 2.25rem; border: 3px solid var(--color-brand-300); pointer-events: none; }
            .qr-corner--tl { top: 0.85rem; left: 0.85rem; border-right: 0; border-bottom: 0; border-top-left-radius: 0.6rem; }
            .qr-corner--tr { top: 0.85rem; right: 0.85rem; border-left: 0; border-bottom: 0; border-top-right-radius: 0.6rem; }
            .qr-corner--bl { bottom: 0.85rem; left: 0.85rem; border-right: 0; border-top: 0; border-bottom-left-radius: 0.6rem; }
            .qr-corner--br { bottom: 0.85rem; right: 0.85rem; border-left: 0; border-top: 0; border-bottom-right-radius: 0.6rem; }
            {{-- CHANGED: the sweep animated `top`, so every frame ran layout and paint for as long as the camera was on. It now animates `transform`, which the compositor handles on its own. --}}
            .qr-scanline {
                position: absolute; left: 1.25rem; right: 1.25rem; top: 1.25rem; height: 2px; border-radius: 2px;
                background: var(--color-brand-300); box-shadow: 0 0 12px 2px color-mix(in oklch, var(--color-brand-300) 55%, transparent);
                animation: qr-sweep 2.2s cubic-bezier(0.65, 0, 0.35, 1) infinite alternate;
                will-change: transform;
            }
            {{-- The viewfinder is a responsive square, so the sweep distance is read from it with container units; translateY percentages would resolve against the 2px line itself. --}}
            .qr-viewfinder { container-type: size; }
            @keyframes qr-sweep { to { transform: translateY(calc(100cqh - 2.5rem - 2px)); } }
            @media (prefers-reduced-motion: reduce) {
                .qr-scanline { animation: none; top: 50%; will-change: auto; }
            }
            .qr-viewfinder.is-found { box-shadow: 0 0 0 4px var(--color-brand-300); }
        </style>

        {{-- CHANGED: jsQR was a blocking <script> here. On the student dashboard that made every page load wait on unpkg for a library nothing uses until a scan button is tapped, so it now loads on first use (see loadJsQr below). --}}
        <script>
        (function () {
            var video = document.getElementById('qr-video');
            var idle = document.getElementById('qr-idle');
            var scanline = document.getElementById('qr-scanline');
            var viewfinder = document.getElementById('qr-viewfinder');
            var statusEl = document.getElementById('qr-status');
            var startBtn = document.getElementById('qr-start');
            var photoInput = document.getElementById('qr-photo');
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d', { willReadFrequently: true });
            var stream = null;
            var done = false;
            var jsQrPromise = null;

            // ADDED: fetches jsQR the first time a student scans, keeping it off the page's critical path.
            function loadJsQr() {
                if (window.jsQR) return Promise.resolve();
                if (!jsQrPromise) {
                    jsQrPromise = new Promise(function (resolve, reject) {
                        var tag = document.createElement('script');
                        tag.src = 'https://unpkg.com/jsqr@1.4.0/dist/jsQR.js';
                        tag.integrity = 'sha384-b5Ya4Bq3qCyz39m2ISh+4DxjAIljdeFwK/BsXLuj9gugaNwAcj/ia15fxNZL9Nlx';
                        tag.crossOrigin = 'anonymous';
                        tag.onload = resolve;
                        tag.onerror = function () {
                            jsQrPromise = null;
                            reject(new Error('scanner-unavailable'));
                        };
                        document.head.appendChild(tag);
                    });
                }
                return jsQrPromise;
            }

            function setStatus(text, tone) {
                statusEl.textContent = text;
                statusEl.className = 'mt-4 min-h-[2.5rem] max-w-[20rem] text-xs font-semibold ' +
                    (tone === 'error' ? 'text-rose-700' : tone === 'ok' ? 'text-brand-700' : 'text-ink-600');
            }

            function stopCamera() {
                if (stream) stream.getTracks().forEach(function (t) { t.stop(); });
                stream = null;
                video.classList.add('opacity-0');
                scanline.classList.add('hidden');
                idle.classList.remove('hidden');
                startBtn.classList.remove('hidden');
            }

            // Only evaluation links for this site open; anything else is explained, not followed.
            function handleCode(text) {
                var url;
                try { url = new URL(text, window.location.href); } catch (e) { url = null; }

                if (!url || url.pathname !== '/student/evaluation' || !url.searchParams.has('signature')) {
                    setStatus("That QR code isn't a stall evaluation code. Scan the code posted at the stall's counter.", 'error');
                    return false;
                }
                if (url.origin !== window.location.origin) {
                    setStatus('That code was made for ' + url.host + ', not this site. Ask the canteen office for the current code.', 'error');
                    return false;
                }

                done = true;
                stopCamera();
                viewfinder.classList.add('is-found');
                setStatus('Code found. Opening the evaluation…', 'ok');
                window.location.assign(url.href);
                return true;
            }

            function decode(source, width, height) {
                var scale = Math.min(1, 720 / Math.max(width, height));
                canvas.width = Math.round(width * scale);
                canvas.height = Math.round(height * scale);
                ctx.drawImage(source, 0, 0, canvas.width, canvas.height);
                var image = ctx.getImageData(0, 0, canvas.width, canvas.height);
                return window.jsQR ? jsQR(image.data, image.width, image.height, { inversionAttempts: 'attemptBoth' }) : null;
            }

            // CHANGED: decode runs about 10 times a second instead of on every animation frame. A QR code
            // does not move that fast, and a 60Hz decode loop heats the phone it is held in.
            var lastDecode = 0;

            function tick(now) {
                if (done || !stream) return;
                if (video.readyState === video.HAVE_ENOUGH_DATA && (now - lastDecode) >= 100) {
                    lastDecode = now;
                    var code = decode(video, video.videoWidth, video.videoHeight);
                    if (code && code.data && handleCode(code.data)) return;
                }
                requestAnimationFrame(tick);
            }

            startBtn.addEventListener('click', function () {
                if (!window.isSecureContext || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    setStatus('Live scanning needs a secure (https) connection. Use "Scan a saved image" instead.', 'error');
                    return;
                }
                setStatus('Starting camera…');
                // CHANGED: the library is fetched before the camera starts, so a failed download is reported instead of a blank viewfinder.
                loadJsQr()
                    .then(function () {
                        return navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } }, audio: false });
                    })
                    .then(function (s) {
                        stream = s;
                        video.srcObject = s;
                        return video.play();
                    })
                    .then(function () {
                        video.classList.remove('opacity-0');
                        idle.classList.add('hidden');
                        scanline.classList.remove('hidden');
                        startBtn.classList.add('hidden');
                        setStatus('Hold the QR code inside the frame.');
                        lastDecode = 0;
                        requestAnimationFrame(tick);
                    })
                    .catch(function (err) {
                        stopCamera();
                        if (err && err.message === 'scanner-unavailable') {
                            setStatus('The scanner could not load. Check your internet connection and try again.', 'error');
                        } else if (err && (err.name === 'NotAllowedError' || err.name === 'SecurityError')) {
                            setStatus('Camera access was blocked. Allow it in your browser settings, or use "Scan a saved image".', 'error');
                        } else if (err && (err.name === 'NotFoundError' || err.name === 'OverconstrainedError')) {
                            setStatus('No camera was found on this device. Use "Scan a saved image" instead.', 'error');
                        } else {
                            setStatus('The camera could not start. Use "Scan a saved image" instead.', 'error');
                        }
                    });
            });

            photoInput.addEventListener('change', function () {
                var file = photoInput.files && photoInput.files[0];
                if (!file) return;
                {{-- CHANGED: wording follows the upload button; the file now comes from the gallery, not a photo taken on the spot. --}}
                setStatus('Reading the image…');
                // CHANGED: waits for the library, which is no longer loaded with the page.
                loadJsQr().then(function () {
                    var img = new Image();
                    img.onload = function () {
                        var code = decode(img, img.naturalWidth, img.naturalHeight);
                        URL.revokeObjectURL(img.src);
                        photoInput.value = '';
                        if (!code || !code.data) {
                            {{-- CHANGED: wording follows the upload button; the student picks an existing image instead of taking one. --}}
                            setStatus('No QR code found in that image. Pick one where the whole code is sharp and in view.', 'error');
                            return;
                        }
                        handleCode(code.data);
                    };
                    img.onerror = function () {
                        photoInput.value = '';
                        setStatus('That file could not be opened as an image.', 'error');
                    };
                    img.src = URL.createObjectURL(file);
                }).catch(function () {
                    photoInput.value = '';
                    setStatus('The scanner could not load. Check your internet connection and try again.', 'error');
                });
            });

            // Release the camera when the student leaves or switches apps.
            document.addEventListener('visibilitychange', function () {
                if (document.hidden && stream && !done) {
                    stopCamera();
                    setStatus('Scanning paused. Tap Start scanning to continue.');
                }
            });
            window.addEventListener('pagehide', stopCamera);
        })();
        </script>
