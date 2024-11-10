class ErrorHandler {
    constructor() {
        this.errorListeners = [];
        this.setupGlobalHandlers();
    }

    setupGlobalHandlers() {
        window.onerror = (message, source, lineno, colno, error) => {
            this.handleError({
                type: 'global',
                message,
                source,
                lineno,
                colno,
                error
            });
            return false;
        };

        window.addEventListener('unhandledrejection', (event) => {
            this.handleError({
                type: 'promise',
                message: event.reason?.message || 'Promise rejected',
                error: event.reason
            });
        });
    }

    handleError(errorInfo) {
        console.error('Error occurred:', errorInfo);

        // Log to monitoring service if available
        this.logToMonitoring(errorInfo);

        // Notify listeners
        this.notifyListeners(errorInfo);

        // Handle specific error types
        switch (errorInfo.type) {
            case 'api':
                this.handleApiError(errorInfo);
                break;
            case 'game':
                this.handleGameError(errorInfo);
                break;
            case 'network':
                this.handleNetworkError(errorInfo);
                break;
            default:
                this.handleGenericError(errorInfo);
        }
    }

    handleApiError(error) {
        const statusCode = error.response?.status;
        
        switch (statusCode) {
            case 401:
                // Unauthorized - redirect to login
                window.location.href = '/login';
                break;
            case 403:
                // Forbidden - show permission error
                this.showUserError('You do not have permission to perform this action');
                break;
            case 429:
                // Rate limited
                this.showUserError('Please wait a moment before trying again');
                break;
            default:
                this.showUserError('An error occurred while communicating with the server');
        }
    }

    handleGameError(error) {
        // Save game state if possible
        this.saveGameState();

        if (error.recoverable) {
            this.showUserError('An error occurred. The game will attempt to recover...');
            this.attemptRecovery(error);
        } else {
            this.showUserError('A critical error occurred. Please refresh the page to continue.');
        }
    }

    handleNetworkError(error) {
        this.showUserError('Network connection lost. Please check your internet connection.');
        
        // Attempt to reconnect
        this.attemptReconnection();
    }

    handleGenericError(error) {
        this.showUserError('An unexpected error occurred. Please try again.');
    }

    showUserError(message, duration = 5000) {
        // Create or update error toast
        const toast = document.createElement('div');
        toast.className = 'error-toast';
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, duration);
    }

    addListener(listener) {
        this.errorListeners.push(listener);
    }

    removeListener(listener) {
        const index = this.errorListeners.indexOf(listener);
        if (index > -1) {
            this.errorListeners.splice(index, 1);
        }
    }

    notifyListeners(error) {
        this.errorListeners.forEach(listener => {
            try {
                listener(error);
            } catch (e) {
                console.error('Error in error listener:', e);
            }
        });
    }

    logToMonitoring(error) {
        // Implementation for logging to monitoring service
        // e.g., Sentry, LogRocket, etc.
    }

    saveGameState() {
        // Implementation for saving game state
    }

    attemptRecovery(error) {
        // Implementation for recovery attempts
    }

    attemptReconnection() {
        // Implementation for reconnection logic
    }
}

export default new ErrorHandler();