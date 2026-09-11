import { useNavigate } from 'react-router-dom'

export default function ComingSoonPage() {
  const navigate = useNavigate()

  return (
    <div className="relative flex min-h-svh flex-col items-center justify-center gap-5 overflow-hidden bg-linear-to-br from-background via-[#fdf1ea] to-accent p-4 text-center">
      <div
        aria-hidden
        className="pointer-events-none absolute -top-64 -right-56 hidden h-[820px] w-[820px] rounded-full sm:block"
        style={{
          background:
            'radial-gradient(circle, rgba(217,119,87,0.18) 0%, rgba(217,119,87,0) 68%)',
        }}
      />

      <div className="relative flex h-14 w-14 items-center justify-center rounded-2xl bg-primary shadow-[0_10px_24px_rgba(217,119,87,0.35)]">
        <svg
          width="28"
          height="28"
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

      <div className="relative">
        <h1 className="text-3xl font-extrabold tracking-tighter text-foreground">Coming soon</h1>
        <p className="mt-2 text-[15px] text-muted-foreground">
          This page isn&apos;t built yet — check back soon.
        </p>
      </div>

      <button
        type="button"
        onClick={() => navigate(-1)}
        className="relative text-sm font-bold text-primary hover:underline"
      >
        Back to previous page
      </button>
    </div>
  )
}
