/**
 * Impeccable Toast Notification Manager (Upper-Right Stacking)
 * Handles auto-dismiss with animated countdown timer, hover pause, multi-toast stacking queue, and graceful exit.
 */

class ToastManager {
    constructor() {
        this.container = null;
        this.maxToasts = 4;
        this.toasts = [];
        this.initContainer();
    }

    initContainer() {
        if (!this.container && typeof document !== 'undefined') {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'toast-container';
                container.setAttribute('aria-live', 'polite');
                container.setAttribute('aria-atomic', 'false');
                document.body.appendChild(container);
            }
            this.container = container;
        }
    }

    show(message, type = 'success', duration = 4000, title = null) {
        this.initContainer();
        if (!this.container || !message) return;

        // If stack exceeds capacity, dismiss oldest toast at bottom
        if (this.toasts.length >= this.maxToasts) {
            this.dismiss(this.toasts[this.toasts.length - 1]);
        }

        const icons = {
            success: 'checkmark-circle',
            error: 'alert-circle',
            warning: 'warning-outline',
            info: 'information-circle'
        };

        const defaultTitles = {
            success: 'Success',
            error: 'Error',
            warning: 'Attention',
            info: 'Information'
        };

        const toastType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
        const iconName = icons[toastType];
        const displayTitle = title !== null ? title : defaultTitles[toastType];

        const toastEl = document.createElement('div');
        toastEl.className = 'toast-item';
        toastEl.setAttribute('role', 'alert');

        toastEl.innerHTML = `
            <div class="toast-icon toast-${toastType}">
                <ion-icon name="${iconName}"></ion-icon>
            </div>
            <div class="toast-content">
                ${displayTitle ? `<div class="toast-title">${this.escapeHtml(displayTitle)}</div>` : ''}
                <div class="toast-message">${this.escapeHtml(message)}</div>
            </div>
            <button type="button" class="toast-close" aria-label="Dismiss notification">
                <ion-icon name="close-outline"></ion-icon>
            </button>
            <div class="toast-progress toast-${toastType}"></div>
        `;

        const progressBar = toastEl.querySelector('.toast-progress');
        const closeBtn = toastEl.querySelector('.toast-close');

        const toastObj = {
            element: toastEl,
            duration: duration,
            remaining: duration,
            startTime: Date.now(),
            animFrame: null,
            isPaused: false
        };

        // Prepend so latest notification sits prominently at the top of the stack
        this.toasts.unshift(toastObj);
        this.container.prepend(toastEl);

        closeBtn.addEventListener('click', () => {
            this.dismiss(toastObj);
        });

        // Countdown timer & animation
        const updateProgress = () => {
            if (!toastObj.isPaused) {
                const elapsed = Date.now() - toastObj.startTime;
                const percent = Math.max(0, 1 - (elapsed / toastObj.duration));
                if (progressBar) {
                    progressBar.style.transform = `scaleX(${percent})`;
                }

                if (elapsed >= toastObj.duration) {
                    this.dismiss(toastObj);
                    return;
                }
            }
            toastObj.animFrame = requestAnimationFrame(updateProgress);
        };

        toastObj.animFrame = requestAnimationFrame(updateProgress);

        // Pause countdown on hover
        toastEl.addEventListener('mouseenter', () => {
            toastObj.isPaused = true;
            toastObj.remaining -= (Date.now() - toastObj.startTime);
        });

        toastEl.addEventListener('mouseleave', () => {
            toastObj.isPaused = false;
            toastObj.startTime = Date.now() - (toastObj.duration - toastObj.remaining);
        });
    }

    dismiss(toastObj) {
        if (!toastObj || !toastObj.element) return;
        
        if (toastObj.animFrame) {
            cancelAnimationFrame(toastObj.animFrame);
        }

        const el = toastObj.element;
        el.classList.add('toast-leaving');

        // Remove from tracked array immediately
        this.toasts = this.toasts.filter(t => t !== toastObj);

        el.addEventListener('animationend', () => {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, { once: true });

        // Fallback removal if animationend doesn't fire
        setTimeout(() => {
            if (el.parentNode) {
                el.parentNode.removeChild(el);
            }
        }, 300);
    }

    escapeHtml(str) {
        if (typeof str !== 'string') return '';
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}

// Instantiate globally
const toastManager = new ToastManager();

window.toast = {
    show: (msg, type, dur, title) => toastManager.show(msg, type, dur, title),
    success: (msg, dur, title) => toastManager.show(msg, 'success', dur, title),
    error: (msg, dur, title) => toastManager.show(msg, 'error', dur, title),
    warning: (msg, dur, title) => toastManager.show(msg, 'warning', dur, title),
    info: (msg, dur, title) => toastManager.show(msg, 'info', dur, title),
};

window.showToast = (msg, type, dur, title) => toastManager.show(msg, type, dur, title);

export default toastManager;
