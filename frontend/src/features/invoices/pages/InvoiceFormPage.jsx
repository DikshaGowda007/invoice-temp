import { ArrowLeft, Calendar, FileText, Percent, StickyNote, Tag, Users } from 'lucide-react'
import { Controller } from 'react-hook-form'
import { Link } from 'react-router-dom'
import { FormAlert } from '@/components/common/FormAlert'
import { DatePickerField } from '@/components/forms/DatePickerField'
import { FormField } from '@/components/forms/FormField'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select'
import { Textarea } from '@/components/ui/textarea'
import { useInvoiceForm } from '@/features/invoices/hooks/useInvoiceForm'
import { avatarColorFor, initialsFor } from '@/utils/avatarColor'
import { formatDate } from '@/utils/format'
import { ROUTES } from '@/utils/routePaths'

const underlineInputClassName =
  'h-auto rounded-none border-x-0 border-t-0 border-b-[1.5px] border-input bg-transparent px-0.5 py-2 text-[14.5px] shadow-none focus-visible:border-primary focus-visible:ring-0'

const underlineTriggerClassName = `w-full justify-between ${underlineInputClassName}`

export default function InvoiceFormPage() {
  const {
    isEditing,
    register,
    control,
    errors,
    values,
    selectedClient,
    clients,
    isLoadingClients,
    isLoadingInvoice,
    isLoadError,
    handleSubmit,
    isPending,
    errorMessage,
  } = useInvoiceForm()

  return (
    <div>
      <div className="mb-5 flex items-center gap-3 text-sm">
        <Link
          to={ROUTES.INVOICES}
          className="flex items-center gap-1.5 font-medium text-muted-foreground hover:text-foreground"
        >
          <ArrowLeft size={15} />
          Invoices
        </Link>
        <span className="h-4 w-px bg-border" />
        <h1 className="text-lg font-semibold">{isEditing ? 'Edit Invoice' : 'New Invoice'}</h1>
      </div>

      {isLoadingInvoice ? (
        <Card className="max-w-lg">
          <CardContent className="py-10 text-center text-sm text-muted-foreground">Loading invoice…</CardContent>
        </Card>
      ) : isLoadError ? (
        <Card className="max-w-lg">
          <CardContent className="py-10 text-center text-sm text-destructive">
            Couldn&apos;t load this invoice. It may have been deleted.
          </CardContent>
        </Card>
      ) : (
      <div className="flex max-w-4xl items-start gap-7">
        <Card className="min-w-0 flex-1">
          <CardContent>
            <div className="mb-6 flex items-center gap-2.5">
              <div className="flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-[10px] bg-accent text-primary">
                <FileText size={16} />
              </div>
              <div>
                <p className="text-[15px] font-semibold">Invoice details</p>
                <p className="text-xs text-muted-foreground">
                  {isEditing
                    ? 'Update the invoice details below.'
                    : 'Add line items on the next screen once this is saved.'}
                </p>
              </div>
            </div>

            <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-5.5">
              <FormField id="client_id" label="Client" icon={<Users size={9} />} error={errors.client_id?.message}>
                <Controller
                  name="client_id"
                  control={control}
                  render={({ field }) => (
                    <Select
                      items={clients.map((client) => ({ value: String(client.id), label: client.name }))}
                      value={field.value ? String(field.value) : ''}
                      onValueChange={field.onChange}
                      disabled={isLoadingClients}
                    >
                      <SelectTrigger id="client_id" className={underlineTriggerClassName}>
                        <SelectValue
                          placeholder={isLoadingClients ? 'Loading clients…' : 'Select a client'}
                        />
                      </SelectTrigger>
                      <SelectContent align="start" alignItemWithTrigger={false}>
                        {clients.map((client) => (
                          <SelectItem key={client.id} value={String(client.id)}>
                            {client.name}
                          </SelectItem>
                        ))}
                      </SelectContent>
                    </Select>
                  )}
                />
              </FormField>

              <div className="grid grid-cols-2 gap-5">
                <FormField id="issue_date" label="Issue date" icon={<Calendar size={9} />} error={errors.issue_date?.message}>
                  <Controller
                    name="issue_date"
                    control={control}
                    render={({ field }) => (
                      <DatePickerField id="issue_date" value={field.value} onChange={field.onChange} />
                    )}
                  />
                </FormField>
                <FormField id="due_date" label="Due date" icon={<Calendar size={9} />} error={errors.due_date?.message}>
                  <Controller
                    name="due_date"
                    control={control}
                    render={({ field }) => (
                      <DatePickerField id="due_date" value={field.value} onChange={field.onChange} />
                    )}
                  />
                </FormField>
              </div>

              <div className="grid grid-cols-2 gap-5">
                <FormField id="tax_rate" label="Tax rate (%)" icon={<Percent size={9} />} error={errors.tax_rate?.message}>
                  <Input
                    id="tax_rate"
                    type="number"
                    step="0.01"
                    min="0"
                    max="100"
                    placeholder="0"
                    className={underlineInputClassName}
                    {...register('tax_rate')}
                  />
                </FormField>
                <FormField id="discount_amount" label="Discount" icon={<Tag size={9} />} error={errors.discount_amount?.message}>
                  <Input
                    id="discount_amount"
                    type="number"
                    step="0.01"
                    min="0"
                    placeholder="0"
                    className={underlineInputClassName}
                    {...register('discount_amount')}
                  />
                </FormField>
              </div>

              <FormField id="notes" label="Notes" icon={<StickyNote size={9} />} error={errors.notes?.message}>
                <Textarea
                  id="notes"
                  rows={2}
                  placeholder="Payment terms, thank-you note, etc."
                  className={`${underlineInputClassName} min-h-0`}
                  {...register('notes')}
                />
              </FormField>

              <FormAlert>{errorMessage}</FormAlert>

              <div className="mt-1 flex items-center gap-2.5">
                <Button type="submit" disabled={isPending} className="flex-1 shadow-[0_8px_20px_rgba(217,119,87,0.28)]">
                  {isPending ? 'Saving…' : isEditing ? 'Save changes' : 'Create Invoice'}
                </Button>
                <Button type="button" variant="outline" render={<Link to={ROUTES.INVOICES} />}>
                  Cancel
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>

        <div className="sticky top-8 w-65 shrink-0">
          <p className="mb-3 pl-0.5 text-[11px] font-semibold tracking-wide text-muted-foreground uppercase">
            Preview
          </p>
          <Card>
            <CardContent>
              <div className="mb-4 flex items-center gap-2.5 border-b border-border pb-4">
                {selectedClient ? (
                  <div
                    className={`flex h-9.5 w-9.5 shrink-0 items-center justify-center rounded-full text-[14px] font-semibold ${avatarColorFor(selectedClient.id)}`}
                  >
                    {initialsFor(selectedClient.name)}
                  </div>
                ) : (
                  <div className="flex h-9.5 w-9.5 shrink-0 items-center justify-center rounded-full bg-accent text-primary">
                    <Users size={16} />
                  </div>
                )}
                <div className="min-w-0">
                  <p className="truncate text-sm font-semibold">
                    {selectedClient?.name ?? 'No client selected'}
                  </p>
                  <p className="truncate text-xs text-muted-foreground">
                    {selectedClient?.email ?? 'Pick a client to preview'}
                  </p>
                </div>
              </div>
              <div className="flex flex-col gap-2.5 text-[13px] text-muted-foreground">
                <span className="flex items-center justify-between gap-2">
                  <span className="flex items-center gap-1.75">
                    <Calendar size={13} />
                    Issue date
                  </span>
                  <span className="font-medium text-foreground">
                    {values.issue_date ? formatDate(values.issue_date) : '—'}
                  </span>
                </span>
                <span className="flex items-center justify-between gap-2">
                  <span className="flex items-center gap-1.75">
                    <Calendar size={13} />
                    Due date
                  </span>
                  <span className="font-medium text-foreground">
                    {values.due_date ? formatDate(values.due_date) : '—'}
                  </span>
                </span>
                <span className="flex items-center justify-between gap-2">
                  <span className="flex items-center gap-1.75">
                    <Percent size={13} />
                    Tax rate
                  </span>
                  <span className="font-medium text-foreground">{values.tax_rate || 0}%</span>
                </span>
                <span className="flex items-center justify-between gap-2">
                  <span className="flex items-center gap-1.75">
                    <Tag size={13} />
                    Discount
                  </span>
                  <span className="font-medium text-foreground">{values.discount_amount || 0}</span>
                </span>
              </div>
            </CardContent>
          </Card>
          <p className="mt-3 pl-0.5 text-xs leading-relaxed text-muted-foreground">
            {isEditing
              ? 'Line items are managed from the invoice detail page.'
              : 'Line items and the total are added on the next screen after this invoice is created.'}
          </p>
        </div>
      </div>
      )}
    </div>
  )
}
