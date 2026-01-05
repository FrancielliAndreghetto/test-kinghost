type EventHandler<T = any> = (data: T) => void

class EventBus {
  private events: Map<string, Set<EventHandler>>

  constructor() {
    this.events = new Map()
  }

  /**
   * Subscribe to an event
   */
  on<T = any>(event: string, handler: EventHandler<T>): void {
    if (!this.events.has(event)) {
      this.events.set(event, new Set())
    }
    this.events.get(event)!.add(handler)
  }

  /**
   * Unsubscribe from an event
   */
  off<T = any>(event: string, handler: EventHandler<T>): void {
    const handlers = this.events.get(event)
    if (handlers) {
      handlers.delete(handler)
      if (handlers.size === 0) {
        this.events.delete(event)
      }
    }
  }

  /**
   * Emit an event
   */
  emit<T = any>(event: string, data?: T): void {
    const handlers = this.events.get(event)
    if (handlers) {
      handlers.forEach((handler) => handler(data))
    }
  }

  /**
   * Subscribe to an event only once
   */
  once<T = any>(event: string, handler: EventHandler<T>): void {
    const onceHandler: EventHandler<T> = (data) => {
      handler(data)
      this.off(event, onceHandler)
    }
    this.on(event, onceHandler)
  }

  /**
   * Clear all event listeners
   */
  clear(): void {
    this.events.clear()
  }
}

// Singleton instance - Observer Pattern
export const eventBus = new EventBus()

// Event types for type safety
export enum AppEvents {
  FAVORITE_ADDED = 'favorite:added',
  FAVORITE_REMOVED = 'favorite:removed',
  USER_LOGGED_IN = 'user:logged-in',
  USER_LOGGED_OUT = 'user:logged-out',
  MOVIE_WATCHED = 'movie:watched',
  ERROR_OCCURRED = 'error:occurred',
}
