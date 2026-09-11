import { AlertTriangle, RotateCw } from 'lucide-react'
import { Component } from 'react'

export default class ErrorBoundary extends Component {
  state = { error: null }

  static getDerivedStateFromError(error) {
    return { error }
  }

  componentDidCatch(error, info) {
    console.error('Uncaught error rendering the app:', error, info)
  }

  handleReload = () => {
    window.location.href = '/'
  }

  render() {
    if (!this.state.error) return this.props.children

    return (
      <div className="flex min-h-svh items-center justify-center bg-background px-4">
        <div className="w-full max-w-sm rounded-2xl border border-border bg-card p-8 text-center shadow-sm">
          <div className="mx-auto mb-4 flex size-12 items-center justify-center rounded-xl bg-[var(--danger-bg)] text-[var(--danger)]">
            <AlertTriangle size={22} />
          </div>
          <h1 className="text-xl font-bold">Something went wrong</h1>
          <p className="mt-2 text-sm text-muted-foreground">
            An unexpected error occurred. Reloading usually fixes it.
          </p>

          {import.meta.env.DEV && (
            <pre className="mt-4 max-h-40 overflow-auto rounded-lg bg-muted p-3 text-left text-xs text-destructive">
              {this.state.error.message}
            </pre>
          )}

          <button
            type="button"
            onClick={this.handleReload}
            className="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-full bg-primary py-2.5 text-sm font-bold text-primary-foreground transition hover:opacity-90"
          >
            <RotateCw size={15} />
            Reload page
          </button>
        </div>
      </div>
    )
  }
}
