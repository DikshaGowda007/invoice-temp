import { ArrowLeft, Mail, MapPin, Phone, StickyNote, User, Users } from 'lucide-react'
import { Link } from 'react-router-dom'
import { FormField } from '@/components/forms/FormField'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { useClientForm } from '@/features/clients/hooks/useClientForm'
import { initialsFor } from '@/utils/avatarColor'
import { ROUTES } from '@/utils/routePaths'

const underlineInputClassName =
  'h-auto rounded-none border-x-0 border-t-0 border-b-[1.5px] border-input bg-transparent px-0.5 py-2 text-[14.5px] shadow-none focus-visible:border-primary focus-visible:ring-0'

const PLACEHOLDER = {
  name: 'Bloom Studio',
  email: 'sarah@bloomstudio.co',
  phone: '(555) 123-4567',
  address: '120 Market St, Suite 4, Portland, OR',
}

export default function ClientFormPage() {
  const { isEditing, register, errors, values, handleSubmit, isPending, isLoadingClient, isLoadError, errorMessage } =
    useClientForm()

  return (
    <div>
      <div className="mb-5 flex items-center gap-3 text-sm">
        <Link
          to={ROUTES.CLIENTS}
          className="flex items-center gap-1.5 font-medium text-muted-foreground hover:text-foreground"
        >
          <ArrowLeft size={15} />
          Clients
        </Link>
        <span className="h-4 w-px bg-border" />
        <h1 className="text-lg font-semibold">{isEditing ? 'Edit Client' : 'Add Client'}</h1>
      </div>

      {isLoadingClient ? (
        <Card className="max-w-lg">
          <CardContent className="py-10 text-center text-sm text-muted-foreground">Loading client…</CardContent>
        </Card>
      ) : isLoadError ? (
        <Card className="max-w-lg">
          <CardContent className="py-10 text-center text-sm text-destructive">
            Couldn&apos;t load this client. It may have been deleted.
          </CardContent>
        </Card>
      ) : (
        <div className="flex max-w-4xl items-start gap-7">
          <Card className="min-w-0 flex-1">
            <CardContent>
              <div className="mb-6 flex items-center gap-2.5">
                <div className="flex h-8.5 w-8.5 shrink-0 items-center justify-center rounded-[10px] bg-accent text-primary">
                  <Users size={16} />
                </div>
                <div>
                  <p className="text-[15px] font-semibold">{isEditing ? 'Edit client' : 'New client'}</p>
                  <p className="text-xs text-muted-foreground">
                    They&apos;ll show up in your client list right away.
                  </p>
                </div>
              </div>

              <form noValidate onSubmit={handleSubmit} className="flex flex-col gap-5.5">
                <FormField id="name" label="Name" icon={<User size={9} />} error={errors.name?.message}>
                  <Input
                    id="name"
                    placeholder={PLACEHOLDER.name}
                    className={underlineInputClassName}
                    {...register('name')}
                  />
                </FormField>

                <div className="grid grid-cols-2 gap-5">
                  <FormField id="email" label="Email" icon={<Mail size={9} />} error={errors.email?.message}>
                    <Input
                      id="email"
                      type="email"
                      placeholder={PLACEHOLDER.email}
                      className={underlineInputClassName}
                      {...register('email')}
                    />
                  </FormField>
                  <FormField id="phone" label="Phone" icon={<Phone size={9} />} error={errors.phone?.message}>
                    <Input
                      id="phone"
                      type="tel"
                      placeholder={PLACEHOLDER.phone}
                      className={underlineInputClassName}
                      {...register('phone')}
                    />
                  </FormField>
                </div>

                <div className="h-px bg-border" />

                <FormField id="address" label="Address" icon={<MapPin size={9} />} error={errors.address?.message}>
                  <Textarea
                    id="address"
                    rows={2}
                    placeholder={PLACEHOLDER.address}
                    className={`${underlineInputClassName} min-h-0`}
                    {...register('address')}
                  />
                </FormField>

                <FormField id="notes" label="Notes" icon={<StickyNote size={9} />} error={errors.notes?.message}>
                  <Textarea
                    id="notes"
                    rows={2}
                    placeholder="Net 30, prefers invoices on the 1st"
                    className={`${underlineInputClassName} min-h-0`}
                    {...register('notes')}
                  />
                </FormField>

                {errorMessage && <p className="text-sm text-destructive">{errorMessage}</p>}

                <div className="mt-1 flex items-center gap-2.5">
                  <Button type="submit" disabled={isPending} className="flex-1 shadow-[0_8px_20px_rgba(217,119,87,0.28)]">
                    {isPending ? 'Saving…' : isEditing ? 'Save changes' : 'Add Client'}
                  </Button>
                  <Button type="button" variant="outline" render={<Link to={ROUTES.CLIENTS} />}>
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
                  <div className="flex h-9.5 w-9.5 shrink-0 items-center justify-center rounded-full bg-info-bg text-[14px] font-semibold text-info">
                    {initialsFor(values.name || PLACEHOLDER.name)}
                  </div>
                  <div className="min-w-0">
                    <p className="truncate text-sm font-semibold">{values.name || PLACEHOLDER.name}</p>
                    <p className="truncate text-xs text-muted-foreground">{values.email || PLACEHOLDER.email}</p>
                  </div>
                </div>
                <div className="flex flex-col gap-2.5 text-[13px] text-muted-foreground">
                  <span className="flex items-start gap-1.75">
                    <Phone size={13} className="mt-0.5 shrink-0" />
                    <span className="min-w-0 break-words">{values.phone || PLACEHOLDER.phone}</span>
                  </span>
                  <span className="flex items-start gap-1.75">
                    <MapPin size={13} className="mt-0.5 shrink-0" />
                    <span className="min-w-0 break-words">{values.address || PLACEHOLDER.address}</span>
                  </span>
                </div>
              </CardContent>
            </Card>
            <p className="mt-3 pl-0.5 text-xs leading-relaxed text-muted-foreground">
              This is how {values.name || PLACEHOLDER.name} will appear in your client list and on invoices.
            </p>
          </div>
        </div>
      )}
    </div>
  )
}
