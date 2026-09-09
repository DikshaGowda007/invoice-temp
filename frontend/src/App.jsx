function App() {
  return (
    <div className="flex min-h-svh items-center justify-center bg-[var(--bg-app)] text-[var(--text)]">
      <div className="flex flex-col items-center gap-3 rounded-[14px] border border-[var(--border)] bg-[var(--bg-surface)] px-10 py-8 text-center shadow-sm">
        <div className="flex h-9 w-9 items-center justify-center rounded-[10px] bg-[var(--primary)]">
          <svg
            width="18"
            height="18"
            viewBox="0 0 24 24"
            fill="none"
            stroke="white"
            strokeWidth="2.2"
            strokeLinecap="round"
            strokeLinejoin="round"
          >
            <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
            <path d="M9.5 12h5" />
            <path d="M9.5 15.5h5" />
          </svg>
        </div>
        <h1 className="text-lg font-semibold">InvoiceHub</h1>
        <p className="max-w-xs text-sm text-[var(--text-muted)]">
          Scaffold is up. Milestone 1 (Sanctum auth + client CRUD) starts next.
        </p>
      </div>
    </div>
  )
}

export default App
