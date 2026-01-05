type EventHandler<T = unknown> = (data: T) => void

class EventBus {
  private events: Map<string, Set<EventHandler<unknown>>>

  constructor() {
    this.events = new Map()
  }

  on<T = unknown>(event: string, handler: EventHandler<T>): void {
    if (!this.events.has(event)) {
      this.events.set(event, new Set())
    }
    this.events.get(event)!.add(handler as EventHandler<unknown>)
  }

  off<T = unknown>(event: string, handler: EventHandler<T>): void {
    const handlers = this.events.get(event)
    if (handlers) {
      handlers.delete(handler as EventHandler<unknown>)
      if (handlers.size === 0) {
        this.events.delete(event)
      }
    }
  }

  emit<T = unknown>(event: string, data?: T): void {
    const handlers = this.events.get(event)
    if (handlers) {
      handlers.forEach((handler) => handler(data))
    }
  }

  once<T = unknown>(event: string, handler: EventHandler<T>): void {
    const onceHandler: EventHandler<T> = (data) => {
      handler(data)
      this.off(event, onceHandler)
    }
    this.on(event, onceHandler)
  }

  clear(): void {
    this.events.clear()
  }
}

export const eventBus = new EventBus()

export enum AppEvents {
  FAVORITE_ADDED = 'favorite:added',
  FAVORITE_REMOVED = 'favorite:removed',
  USER_LOGGED_IN = 'user:logged-in',
  USER_LOGGED_OUT = 'user:logged-out',
  MOVIE_WATCHED = 'movie:watched',
  ERROR_OCCURRED = 'error:occurred',
}
