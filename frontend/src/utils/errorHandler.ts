import { eventBus, AppEvents } from '@/utils/eventBus'

export interface AppError {
  message: string
  context?: string
  timestamp?: Date
}

class ErrorHandler {
  private errorLog: AppError[] = []

  handle(error: Error | string, context?: string): void {
    const appError: AppError = {
      message: typeof error === 'string' ? error : error.message,
      context,
      timestamp: new Date(),
    }

    this.errorLog.push(appError)
    console.error('[ErrorHandler]', appError)

    eventBus.emit(AppEvents.ERROR_OCCURRED, appError)
  }

  getErrors(): AppError[] {
    return [...this.errorLog]
  }

  clearErrors(): void {
    this.errorLog = []
  }

  getLastError(): AppError | null {
    return this.errorLog[this.errorLog.length - 1] || null
  }
}

export const errorHandler = new ErrorHandler()
