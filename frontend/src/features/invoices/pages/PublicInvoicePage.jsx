import { CreditCard, FileText } from 'lucide-react'
import { StatusBadge } from '@/components/common/StatusBadge'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { usePublicInvoice } from '@/features/invoices/hooks/usePublicInvoice'
import { formatCurrency, formatDate } from '@/utils/format'

export default function PublicInvoicePage() {
  const { invoice, isLoading, isError, errorMessage, payNow, isPaying, payErrorMessage } = usePublicInvoice()

  return (
    <div className="flex min-h-svh flex-col items-center bg-background px-4 py-10">
      <div className="mb-7 flex items-center gap-2.5">
        <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-primary">
          <FileText size={16} className="text-primary-foreground" strokeWidth={2.2} />
        </div>
        <span className="text-[15px] font-semibold tracking-tight">InvoiceHub</span>
      </div>

      <div className="w-full max-w-lg">
        {isLoading && (
          <Card>
            <CardContent className="py-10 text-center text-sm text-muted-foreground">Loading invoice…</CardContent>
          </Card>
        )}

        {!isLoading && (isError || !invoice) && (
          <Card>
            <CardContent className="py-10 text-center text-sm text-destructive">{errorMessage}</CardContent>
          </Card>
        )}

        {!isLoading && invoice && (
          <Card className="shadow-[0_1px_2px_rgba(36,33,28,0.04),0_2px_8px_rgba(36,33,28,0.05)]">
            <CardContent>
              <div className="mb-5 flex items-start justify-between gap-3">
                <div>
                  <p className="text-lg font-semibold">{invoice.invoice_number}</p>
                  <p className="mt-0.5 text-sm text-muted-foreground">Billed to {invoice.client_name || '—'}</p>
                </div>
                <StatusBadge status={invoice.status} />
              </div>

              <div className="mb-5 grid grid-cols-2 gap-x-6 gap-y-3 text-sm">
                <div>
                  <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">
                    Issue date
                  </p>
                  <p className="mt-0.5 font-medium">{formatDate(invoice.issue_date)}</p>
                </div>
                <div>
                  <p className="text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">Due date</p>
                  <p className="mt-0.5 font-medium">{formatDate(invoice.due_date)}</p>
                </div>
              </div>

              <div className="mb-5 rounded-lg border border-border">
                <table className="w-full border-collapse text-sm">
                  <thead>
                    <tr>
                      <th className="border-b border-border px-3 py-2 text-left text-xs font-medium text-muted-foreground">
                        Description
                      </th>
                      <th className="border-b border-border px-3 py-2 text-right text-xs font-medium text-muted-foreground">
                        Qty
                      </th>
                      <th className="border-b border-border px-3 py-2 text-right text-xs font-medium text-muted-foreground">
                        Amount
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    {invoice.line_items.map((lineItem, index) => (
                      <tr key={index}>
                        <td className="border-b border-border px-3 py-2 last:border-0">{lineItem.description}</td>
                        <td className="border-b border-border px-3 py-2 text-right text-muted-foreground last:border-0">
                          {lineItem.quantity}
                        </td>
                        <td className="border-b border-border px-3 py-2 text-right font-medium last:border-0">
                          {formatCurrency(lineItem.line_total)}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>

              <div className="mb-5 space-y-1.5 text-sm">
                <div className="flex justify-between text-muted-foreground">
                  <span>Subtotal</span>
                  <span>{formatCurrency(invoice.subtotal)}</span>
                </div>
                {Number(invoice.tax_amount) > 0 && (
                  <div className="flex justify-between text-muted-foreground">
                    <span>Tax</span>
                    <span>{formatCurrency(invoice.tax_amount)}</span>
                  </div>
                )}
                {Number(invoice.discount_amount) > 0 && (
                  <div className="flex justify-between text-muted-foreground">
                    <span>Discount</span>
                    <span>-{formatCurrency(invoice.discount_amount)}</span>
                  </div>
                )}
                <div className="flex justify-between border-t border-border pt-1.5 text-base font-semibold">
                  <span>Total</span>
                  <span className="text-primary">{formatCurrency(invoice.total)}</span>
                </div>
              </div>

              {(invoice.status === 'SENT' || invoice.status === 'OVERDUE') && (
                <>
                  <Button className="w-full" disabled={isPaying} onClick={() => payNow()}>
                    <CreditCard data-icon="inline-start" size={14} />
                    {isPaying ? 'Processing…' : `Pay ${formatCurrency(invoice.total)}`}
                  </Button>
                  {payErrorMessage && (
                    <p className="mt-2 text-center text-xs text-destructive">{payErrorMessage}</p>
                  )}
                </>
              )}

              {invoice.status === 'PAID' && (
                <p className="rounded-lg bg-success-bg py-2.5 text-center text-sm font-medium text-success">
                  This invoice has been paid. Thank you!
                </p>
              )}
            </CardContent>
          </Card>
        )}
      </div>
    </div>
  )
}
