// Screenshot-Bereichsauswahl mit nativer Browser Screen Capture API
// Keine externen Abhängigkeiten erforderlich

console.log('screenshot-capture.js geladen (native API Version)');

// Globale Variablen
window.screenshotCaptureMode = false;
window.currentScreenshotStep = null;
window.currentScreenshotTutorial = null;

/**
 * Erstellt einen Overlay für die Bereichsauswahl auf dem Video-Stream
 * @param {HTMLVideoElement} video - Das Video-Element mit dem Bildschirm-Stream
 * @returns {Promise<Blob>} - Das ausgewählte Bild als Blob
 */
function createSelectionOverlay(video) {
    return new Promise((resolve, reject) => {
        // Overlay-Container erstellen
        const overlay = document.createElement('div');
        overlay.id = 'screenshot-selection-overlay';
        overlay.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 99999;
            cursor: crosshair;
        `;

        // Video-Element für den Bildschirm-Stream
        const videoContainer = document.createElement('div');
        videoContainer.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        `;
        
        video.style.cssText = `
            width: 100%;
            height: 100%;
            object-fit: none;
            pointer-events: none;
        `;
        videoContainer.appendChild(video);
        overlay.appendChild(videoContainer);

        // Auswahl-Rechteck
        const selection = document.createElement('div');
        selection.style.cssText = `
            position: absolute;
            border: 2px dashed #00ff00;
            background: rgba(0, 255, 0, 0.1);
            display: none;
            pointer-events: none;
            z-index: 100000;
        `;
        overlay.appendChild(selection);

        // Hinweis-Text
        const hint = document.createElement('div');
        hint.style.cssText = `
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 100001;
            pointer-events: none;
            text-align: center;
        `;
        hint.textContent = 'Ziehen Sie ein Rechteck um den Bereich, den Sie aufnehmen möchten. ESC zum Abbrechen.';
        overlay.appendChild(hint);

        document.body.appendChild(overlay);

        let isDragging = false;
        let startX = 0;
        let startY = 0;

        // Video-Größe anpassen
        function adjustVideoSize() {
            const vw = window.innerWidth;
            const vh = window.innerHeight;
            const videoRatio = video.videoWidth / video.videoHeight;
            const screenRatio = vw / vh;

            if (videoRatio > screenRatio) {
                video.style.width = '100%';
                video.style.height = 'auto';
                video.style.position = 'absolute';
                video.style.left = '0';
                video.style.top = '50%';
                video.style.transform = 'translateY(-50%)';
            } else {
                video.style.height = '100%';
                video.style.width = 'auto';
                video.style.position = 'absolute';
                video.style.top = '0';
                video.style.left = '50%';
                video.style.transform = 'translateX(-50%)';
            }
        }

        // Warten bis das Video bereit ist
        if (video.readyState >= 2) {
            adjustVideoSize();
        } else {
            video.addEventListener('loadeddata', adjustVideoSize);
        }

        // Maus-Events
        overlay.addEventListener('mousedown', (e) => {
            if (e.button === 0) { // Linke Maustaste
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                selection.style.left = startX + 'px';
                selection.style.top = startY + 'px';
                selection.style.width = '0';
                selection.style.height = '0';
                selection.style.display = 'block';
            }
        });

        overlay.addEventListener('mousemove', (e) => {
            if (!isDragging) return;

            const currentX = e.clientX;
            const currentY = e.clientY;

            const left = Math.min(startX, currentX);
            const top = Math.min(startY, currentY);
            const width = Math.abs(currentX - startX);
            const height = Math.abs(currentY - startY);

            selection.style.left = left + 'px';
            selection.style.top = top + 'px';
            selection.style.width = width + 'px';
            selection.style.height = height + 'px';
        });

        overlay.addEventListener('mouseup', (e) => {
            if (!isDragging) return;
            isDragging = false;

            const currentX = e.clientX;
            const currentY = e.clientY;

            const left = Math.min(startX, currentX);
            const top = Math.min(startY, currentY);
            const width = Math.abs(currentX - startX);
            const height = Math.abs(currentY - startY);

            // Wenn der Bereich zu klein ist, abbrechen
            if (width < 10 || height < 10) {
                cleanup();
                reject(new Error('Bereich zu klein'));
                return;
            }

            // Screenshot aus dem Video aufnehmen
            captureRegion(video, left, top, width, height, overlay, resolve, reject);
        });

        // ESC zum Abbrechen
        const keyHandler = (e) => {
            if (e.key === 'Escape') {
                cleanup();
                reject(new Error('Abgebrochen'));
            }
        };
        document.addEventListener('keydown', keyHandler);

        function cleanup() {
            document.body.removeChild(overlay);
            document.removeEventListener('keydown', keyHandler);
        }
    });
}

/**
 * Nimmt einen Bereich aus dem Video-Stream auf
 * @param {HTMLVideoElement} video - Das Video-Element
 * @param {number} x - X-Position auf dem Bildschirm
 * @param {number} y - Y-Position auf dem Bildschirm
 * @param {number} width - Breite des Bereichs
 * @param {number} height - Höhe des Bereichs
 * @param {HTMLElement} overlay - Der Overlay-Container
 * @param {Function} resolve - Promise resolve
 * @param {Function} reject - Promise reject
 */
function captureRegion(video, x, y, width, height, overlay, resolve, reject) {
    // Canvas erstellen
    const canvas = document.createElement('canvas');
    canvas.width = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');

    // Koordinaten vom Bildschirm auf das Video mappen
    const vw = window.innerWidth;
    const vh = window.innerHeight;
    
    // Video-Position und Größe ermitteln
    const videoRect = video.getBoundingClientRect();
    
    // Skalierungsfaktor berechnen
    const scaleX = video.videoWidth / videoRect.width;
    const scaleY = video.videoHeight / videoRect.height;

    // Quellkoordinaten auf dem Video berechnen
    const sourceX = (x - videoRect.left) * scaleX;
    const sourceY = (y - videoRect.top) * scaleY;

    // Bild vom Video auf den Canvas zeichnen
    ctx.drawImage(
        video,
        sourceX, sourceY, width * scaleX, height * scaleY,
        0, 0, width, height
    );

    // Cleanup
    const keyHandler = () => {};
    document.body.removeChild(overlay);

    // Canvas zu Blob konvertieren
    canvas.toBlob((blob) => {
        if (blob) {
            resolve(blob);
        } else {
            reject(new Error('Konnte Bild nicht erstellen'));
        }
    }, 'image/png');
}

/**
 * Startet die Screenshot-Bereichsauswahl für einen bestehenden Tutorial-Schritt (edit.blade.php)
 * @param {number} tutorialId - ID des Tutorials
 * @param {number} stepId - ID des Schritts
 */
window.startScreenshotCapture = function(tutorialId, stepId) {
    console.log('=== startScreenshotCapture aufgerufen! ===', tutorialId, stepId);
    
    window.currentScreenshotTutorial = tutorialId;
    window.currentScreenshotStep = stepId;

    // Screen Capture API starten
    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
        alert('Ihr Browser unterstützt keine Bildschirmfreigabe. Bitte verwenden Sie Chrome, Firefox oder Edge.');
        return;
    }

    navigator.mediaDevices.getDisplayMedia({
        video: {
            cursor: 'always'
        },
        audio: false
    }).then((stream) => {
        console.log('=== Bildschirmfreigabe gewährt, Stream erhalten ===');
        // Video-Element erstellen
        const video = document.createElement('video');
        video.srcObject = stream;
        video.autoplay = true;
        video.muted = true;
        video.playsInline = true;

        // Warten bis das Video spielt
        video.play().then(() => {
            console.log('=== Video spielt, Bereichsauswahl-Overlay wird erstellt ===');
            // Bereichsauswahl-Overlay erstellen
            createSelectionOverlay(video).then((imageBlob) => {
                console.log('=== Bereich ausgewählt! ImageBlob erhalten, Größe:', imageBlob.size, 'Typ:', imageBlob.type, '===');
                // Stream stoppen
                stream.getTracks().forEach(track => track.stop());
                
                // Blob als Datei behandeln und hochladen
                const file = new File([imageBlob], 'screenshot.png', { type: 'image/png' });
                console.log('=== Datei erstellt, uploadScreenshotFromBlob wird aufgerufen ===');
                uploadScreenshotFromBlob(tutorialId, stepId, file);
            }).catch((error) => {
                console.log('Screenshot abgebrochen:', error.message);
                stream.getTracks().forEach(track => track.stop());
            });
        }).catch((error) => {
            console.error('Video-Start-Fehler:', error);
            stream.getTracks().forEach(track => track.stop());
        });
    }).catch((error) => {
        console.log('Bildschirmfreigabe abgebrochen:', error);
    });
};

/**
 * Startet die Screenshot-Bereichsauswahl für einen neuen Tutorial-Schritt (create.blade.php)
 * Das Bild wird als Vorschau angezeigt und beim Speichern des Formulars mitgesendet.
 * @param {number} stepIndex - Index des Schritts (0-basiert)
 */
window.startScreenshotCaptureForNew = function(stepIndex) {
    console.log('startScreenshotCaptureForNew aufgerufen!', stepIndex);

    // Screen Capture API starten
    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
        alert('Ihr Browser unterstützt keine Bildschirmfreigabe. Bitte verwenden Sie Chrome, Firefox oder Edge.');
        return;
    }

    navigator.mediaDevices.getDisplayMedia({
        video: {
            cursor: 'always'
        },
        audio: false
    }).then((stream) => {
        // Video-Element erstellen
        const video = document.createElement('video');
        video.srcObject = stream;
        video.autoplay = true;
        video.muted = true;
        video.playsInline = true;

        // Warten bis das Video spielt
        video.play().then(() => {
            // Bereichsauswahl-Overlay erstellen
            createSelectionOverlay(video).then((imageBlob) => {
                // Stream stoppen
                stream.getTracks().forEach(track => track.stop());

                // Bild als Data-URL konvertieren und als Vorschau anzeigen
                const reader = new FileReader();
                reader.onload = function(e) {
                    const dataUrl = e.target.result;
                    
                    // Verstecktes Input-Feld für den Screenshot erstellen/aktualisieren
                    let hiddenInput = document.getElementById('screenshot_data_' + stepIndex);
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.id = 'screenshot_data_' + stepIndex;
                        hiddenInput.name = 'steps[' + stepIndex + '][screenshot_data]';
                        
                        // Zum Schritt-Container hinzufügen
                        const stepItem = document.querySelector(`.step-item[data-step="${stepIndex}"]`);
                        if (stepItem) {
                            stepItem.appendChild(hiddenInput);
                        }
                    }
                    hiddenInput.value = dataUrl;
                    
                    // Vorschau anzeigen
                    const previewDiv = document.getElementById('screenshotPreview_' + stepIndex);
                    if (previewDiv) {
                        previewDiv.classList.remove('hidden');
                        // Bestehendes Bild entfernen
                        const existingImg = previewDiv.querySelector('img');
                        if (existingImg) {
                            existingImg.remove();
                        }
                        // Neues Vorschau-Bild erstellen
                        const img = document.createElement('img');
                        img.src = dataUrl;
                        img.className = 'mt-2 w-64 h-auto rounded-lg border';
                        img.title = 'Klicken zum Vergrößern und Hotspot-Position setzen';
                        img.onclick = function() {
                            openHotspotEditor(dataUrl, 0, 0, String(stepIndex), '-1');
                        };
                        previewDiv.appendChild(img);
                    }
                };
                reader.readAsDataURL(imageBlob);
            }).catch((error) => {
                console.log('Screenshot abgebrochen:', error.message);
                stream.getTracks().forEach(track => track.stop());
            });
        }).catch((error) => {
            console.error('Video-Start-Fehler:', error);
            stream.getTracks().forEach(track => track.stop());
        });
    }).catch((error) => {
        console.log('Bildschirmfreigabe abgebrochen:', error);
    });
};

/**
 * Lädt ein Screenshot-Blob hoch
 * @param {number} tutorialId - ID des Tutorials
 * @param {number} stepId - ID des Schritts
 * @param {File} file - Die Bilddatei
 */
function uploadScreenshotFromBlob(tutorialId, stepId, file) {
    console.log('uploadScreenshotFromBlob aufgerufen:', tutorialId, stepId, file.name, 'Größe:', file.size, 'Typ:', file.type);

    const formData = new FormData();
    formData.append('screenshot', file);
    formData.append('step_id', stepId);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    const url = `/admin/tutorials/${tutorialId}/screenshot`;
    console.log('Upload-URL:', url);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(response => {
        console.log('Upload-Antwort Status:', response.status);
        console.log('Upload-Antwort Content-Type:', response.headers.get('content-type'));

        // Versuche JSON zu lesen
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json().then(data => {
                console.log('Upload-Antwort JSON:', data);
                if (data.success) {
                    console.log('Upload erfolgreich, Seite wird neu geladen...');
                    window.location.reload();
                } else {
                    alert('Fehler beim Hochladen des Screenshots: ' + (data.message || 'Unbekannter Fehler'));
                }
            });
        }

        // Bei Status 200 ohne JSON: der Server hat einen Redirect gesendet,
        // den fetch() automatisch verfolgt hat -> HTML-Seite als Antwort
        // -> Upload war erfolgreich, Seite neu laden
        if (response.ok) {
            console.log('Upload erfolgreich (Server-Antwort erhalten), Seite wird neu geladen...');
            window.location.reload();
            return;
        }

        // Fehler als Text lesen
        return response.text().then(text => {
            console.error('Upload-Fehler:', response.status, text);
            alert('Fehler beim Hochladen des Screenshots: HTTP ' + response.status);
        });
    })
    .catch(error => {
        console.error('Upload Netzwerk-Fehler:', error);
        alert('Fehler beim Hochladen des Screenshots: ' + error.message);
    });
}

// ============================================================
// Guide-Screenshot-Funktionen (gleiche Logik wie Tutorials)
// ============================================================

/**
 * Screenshot mit Bereichsauswahl für Guide-Schritt starten
 * @param {number} guideId - ID der Anleitung
 * @param {number} stepId - ID des Guide-Schritts
 */
window.startGuideScreenshotCapture = function(guideId, stepId) {
    console.log('=== startGuideScreenshotCapture aufgerufen! ===', guideId, stepId);
    
    window.currentGuideScreenshotGuide = guideId;
    window.currentGuideScreenshotStep = stepId;
    
    if (!navigator.mediaDevices || !navigator.mediaDevices.getDisplayMedia) {
        alert('Ihr Browser unterstützt keine Bildschirmaufnahme. Bitte verwenden Sie Chrome, Firefox oder Edge.');
        return;
    }
    
    navigator.mediaDevices.getDisplayMedia({
        video: {
            cursor: 'always',
        },
        audio: false
    }).then((stream) => {
        console.log('=== Bildschirmfreigabe gewährt, Stream erhalten ===');
        // Video-Element erstellen
        const video = document.createElement('video');
        video.srcObject = stream;
        video.autoplay = true;
        video.muted = true;
        video.style.display = 'none';
        document.body.appendChild(video);

        // Warten bis das Video spielt
        video.play().then(() => {
            console.log('=== Video spielt, Bereichsauswahl-Overlay wird erstellt ===');
            // Bereichsauswahl-Overlay erstellen
            createSelectionOverlay(video).then((imageBlob) => {
                console.log('=== Bereich ausgewählt! ImageBlob erhalten, Größe:', imageBlob.size, 'Typ:', imageBlob.type, '===');
                // Stream stoppen
                stream.getTracks().forEach(track => track.stop());
                
                // Blob als Datei behandeln und hochladen
                const file = new File([imageBlob], 'screenshot.png', { type: 'image/png' });
                console.log('=== Datei erstellt, uploadGuideScreenshotFromBlob wird aufgerufen ===');
                uploadGuideScreenshotFromBlob(guideId, stepId, file);
            }).catch((error) => {
                console.log('Screenshot abgebrochen:', error.message);
                stream.getTracks().forEach(track => track.stop());
            });
        }).catch((error) => {
            console.error('Video-Start-Fehler:', error);
            stream.getTracks().forEach(track => track.stop());
        });
    }).catch((error) => {
        console.log('Bildschirmfreigabe abgebrochen:', error);
    });
};

/**
 * Lädt ein Screenshot-Blob für einen Guide-Schritt hoch
 * @param {number} guideId - ID der Anleitung
 * @param {number} stepId - ID des Guide-Schritts
 * @param {File} file - Die Bilddatei
 */
function uploadGuideScreenshotFromBlob(guideId, stepId, file) {
    console.log('uploadGuideScreenshotFromBlob aufgerufen:', guideId, stepId, file.name, 'Größe:', file.size, 'Typ:', file.type);

    const formData = new FormData();
    formData.append('screenshot', file);
    formData.append('step_id', stepId);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

    const url = `/admin/guides/${guideId}/screenshot`;
    console.log('Upload-URL:', url);

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: formData,
    })
    .then(response => {
        console.log('Upload-Antwort Status:', response.status);
        console.log('Upload-Antwort Content-Type:', response.headers.get('content-type'));

        // Versuche JSON zu lesen
        if (response.headers.get('content-type')?.includes('application/json')) {
            return response.json().then(data => {
                console.log('Upload-Antwort JSON:', data);
                if (data.success) {
                    console.log('Upload erfolgreich, Seite wird neu geladen...');
                    window.location.reload();
                } else {
                    alert('Fehler beim Hochladen des Screenshots: ' + (data.message || 'Unbekannter Fehler'));
                }
            });
        }

        // Bei Status 200 ohne JSON: der Server hat einen Redirect gesendet,
        // den fetch() automatisch verfolgt hat -> HTML-Seite als Antwort
        // -> Upload war erfolgreich, Seite neu laden
        if (response.ok) {
            console.log('Upload erfolgreich (Server-Antwort erhalten), Seite wird neu geladen...');
            window.location.reload();
            return;
        }

        // Fehler als Text lesen
        return response.text().then(text => {
            console.error('Upload-Fehler:', response.status, text);
            alert('Fehler beim Hochladen des Screenshots: HTTP ' + response.status);
        });
    })
    .catch(error => {
        console.error('Upload Netzwerk-Fehler:', error);
        alert('Fehler beim Hochladen des Screenshots: ' + error.message);
    });
}
