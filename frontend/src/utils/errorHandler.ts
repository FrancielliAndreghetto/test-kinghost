import { eventBus, AppEvents } from '@/utils/eventBus'

export interface AppError {
  message: string
  context?: string
  timestamp?: Date
}

class ErrorHandler {
  private errorLog: AppError[] = []

  /**
   * Handle application errors
   */
  handle(error: Error | string, context?: string): void {
    const appError: AppError = {
      message: typeof error === 'string' ? error : error.message,
      context,
      timestamp: new Date(),
    }

    this.errorLog.push(appError)
    console.error('[ErrorHandler]', appError)

    // Emit error event for observers
    eventBus.emit(AppEvents.ERROR_OCCURRED, appError)
  }

  /**
   * Get error log
   */
  getErrors(): AppError[] {
    return [...this.errorLog]
  }

  /**
   * Clear error log
   */
  clearErrors(): void {
    this.errorLog = []
  }

  /**
   * Get last error
   */
  getLastError(): AppError | null {
    return this.errorLog[this.errorLog.length - 1] || null
  }
}

// Singleton instance
export const errorHandler = new ErrorHandler()
