export function AuthLayout({ title, description, children, footer }) {
  return (
    <div className="relative flex min-h-svh items-center overflow-hidden bg-linear-to-br from-background via-[#fdf1ea] to-accent">
      <div
        aria-hidden
        className="pointer-events-none absolute -top-64 -right-56 hidden h-[820px] w-[820px] rounded-full sm:block"
        style={{
          background:
            'radial-gradient(circle, rgba(217,119,87,0.18) 0%, rgba(217,119,87,0) 68%)',
        }}
      />

      <svg
        aria-hidden
        width="680"
        height="680"
        viewBox="0 0 24 24"
        fill="none"
        stroke="var(--primary)"
        strokeWidth="0.55"
        strokeLinecap="round"
        strokeLinejoin="round"
        className="pointer-events-none absolute top-1/2 -right-28 hidden -translate-y-1/2 -rotate-8 opacity-16 lg:block"
      >
        <path d="M7 3h7l5 5v13a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z" />
        <path d="M14 3v5h5" />
        <path d="M8.5 13h7" />
        <path d="M8.5 16.5h5" />
      </svg>

      <div
        aria-hidden
        className="pointer-events-none absolute right-32 bottom-28 hidden w-72 rotate-3 rounded-2xl border border-border bg-card p-5 shadow-2xl xl:block"
      >
        <div className="mb-3.5 flex items-center justify-between">
          <span className="text-xs text-muted-foreground">Invoice &middot; INV-1042</span>
          <span className="rounded-full bg-[var(--success-bg)] px-2.5 py-0.5 text-[11px] font-semibold text-[var(--success)]">
            Paid
          </span>
        </div>
        <div className="text-lg font-bold tracking-tight">$3,200.00</div>
        <div className="mt-0.5 text-xs text-muted-foreground">Bloom Studio</div>
      </div>

      <div className="relative mx-auto w-full max-w-sm px-6 md:mx-0 md:ml-40 md:px-0">
        <div className="mb-7.5 flex h-11.5 w-11.5 items-center justify-center rounded-2xl bg-primary shadow-[0_10px_24px_rgba(217,119,87,0.35)]">
          <svg
            width="23"
            height="23"
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

        <h1 className="mb-3 text-4xl font-extrabold tracking-tighter">{title}</h1>
        <p className="mb-9.5 text-[15px] text-muted-foreground">{description}</p>

        {children}

        {footer}
      </div>
    </div>
  )
}
