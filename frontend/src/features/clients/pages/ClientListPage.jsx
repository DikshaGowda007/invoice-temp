import { Pencil, Plus, Search, Trash2, Users } from 'lucide-react'
import { useMemo, useState } from 'react'
import { Link } from 'react-router-dom'
import { ConfirmDialog } from '@/components/common/ConfirmDialog'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { useClientList } from '@/features/clients/hooks/useClientList'
import { avatarColorFor, initialsFor } from '@/utils/avatarColor'
import { clientEditPath, ROUTES } from '@/utils/routePaths'

export default function ClientListPage() {
  const {
    clients,
    isLoading,
    isError,
    errorMessage,
    pendingDeleteClient,
    requestDelete,
    cancelDelete,
    confirmDelete,
    isDeleting,
  } = useClientList()
  const [search, setSearch] = useState('')

  const [prevDeleteClient, setPrevDeleteClient] = useState(pendingDeleteClient)
  const [deleteTargetName, setDeleteTargetName] = useState(pendingDeleteClient?.name ?? null)
  if (pendingDeleteClient !== prevDeleteClient) {
    setPrevDeleteClient(pendingDeleteClient)
    if (pendingDeleteClient) setDeleteTargetName(pendingDeleteClient.name)
  }

  const filteredClients = useMemo(() => {
    const query = search.trim().toLowerCase()
    if (!query) return clients
    return clients.filter(
      (client) =>
        client.name?.toLowerCase().includes(query) || client.email?.toLowerCase().includes(query),
    )
  }, [clients, search])

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <h1 className="text-lg font-semibold">Clients</h1>
        <Button render={<Link to={ROUTES.CLIENT_NEW} />}>
          <Plus data-icon="inline-start" size={14} />
          Add Client
        </Button>
      </div>

      {clients.length > 0 && (
        <div className="relative mb-4 w-70">
          <Search
            size={15}
            className="pointer-events-none absolute top-1/2 left-2.75 -translate-y-1/2 text-muted-foreground"
          />
          <Input
            value={search}
            onChange={(e) => setSearch(e.target.value)}
            placeholder="Search clients..."
            aria-label="Search clients"
            className="pl-8"
          />
        </div>
      )}

      {isLoading && (
        <Card size="sm" className="py-1.5">
          <CardContent className="animate-pulse px-4">
            {[0, 1, 2].map((i) => (
              <div key={i} className="flex items-center gap-3 border-b border-border py-3.5 last:border-0">
                <div className="h-7.5 w-7.5 rounded-full bg-muted" />
                <div className="flex-1">
                  <div className="h-3.5 w-32 rounded bg-muted" />
                  <div className="mt-2 h-3 w-40 rounded bg-muted" />
                </div>
              </div>
            ))}
          </CardContent>
        </Card>
      )}

      {!isLoading && isError && (
        <Card>
          <CardContent className="py-10 text-center">
            <p className="text-sm text-destructive">{errorMessage}</p>
          </CardContent>
        </Card>
      )}

      {!isLoading && !isError && clients.length === 0 && (
        <Card>
          <CardContent className="flex flex-col items-center gap-1.5 py-14 text-center">
            <div className="mb-2 flex h-14 w-14 items-center justify-center rounded-full bg-accent text-primary">
              <Users size={26} strokeWidth={1.7} />
            </div>
            <p className="text-[15px] font-semibold">No clients yet</p>
            <p className="max-w-xs text-[13px] leading-relaxed text-muted-foreground">
              Add your first client to start creating and sending invoices.
            </p>
            <Button render={<Link to={ROUTES.CLIENT_NEW} />} className="mt-2.5">
              <Plus data-icon="inline-start" size={14} />
              Add Client
            </Button>
          </CardContent>
        </Card>
      )}

      {!isLoading && !isError && clients.length > 0 && (
        <Card size="sm" className="py-1.5">
          <CardContent className="px-0">
            {filteredClients.length === 0 ? (
              <p className="px-4 py-8 text-center text-sm text-muted-foreground">
                No clients match &quot;{search}&quot;.
              </p>
            ) : (
              <table className="w-full border-collapse text-sm">
                <thead>
                  <tr>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Client
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Phone
                    </th>
                    <th className="border-b border-border px-4 pb-2.5 text-left text-xs font-medium text-muted-foreground">
                      Address
                    </th>
                    <th className="border-b border-border px-4 pb-2.5" />
                  </tr>
                </thead>
                <tbody>
                  {filteredClients.map((client) => (
                    <tr key={client.id} className="group">
                      <td className="border-b border-border px-4 py-3 group-last:border-0">
                        <div className="flex items-center gap-2.5">
                          <div
                            className={`flex h-7.5 w-7.5 shrink-0 items-center justify-center rounded-full text-[11.5px] font-semibold ${avatarColorFor(client.id)}`}
                          >
                            {initialsFor(client.name) || '?'}
                          </div>
                          <div className="min-w-0">
                            <p className="truncate font-medium">{client.name}</p>
                            {client.email && (
                              <p className="truncate text-xs text-muted-foreground">{client.email}</p>
                            )}
                          </div>
                        </div>
                      </td>
                      <td className="max-w-32 border-b border-border px-4 py-3 break-words text-muted-foreground group-last:border-0">
                        {client.phone || '—'}
                      </td>
                      <td className="max-w-50 border-b border-border px-4 py-3 break-words text-muted-foreground group-last:border-0">
                        {client.address || '—'}
                      </td>
                      <td className="border-b border-border px-2 py-3 text-right group-last:border-0">
                        <div className="flex justify-end gap-0.5">
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            render={<Link to={clientEditPath(client.id)} aria-label={`Edit ${client.name}`} />}
                          >
                            <Pencil size={14} />
                          </Button>
                          <Button
                            variant="ghost"
                            size="icon-sm"
                            aria-label={`Delete ${client.name}`}
                            onClick={() => requestDelete(client)}
                          >
                            <Trash2 size={14} />
                          </Button>
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            )}
          </CardContent>
        </Card>
      )}

      <ConfirmDialog
        open={pendingDeleteClient != null}
        onOpenChange={(open) => !open && cancelDelete()}
        title={`Delete ${deleteTargetName}?`}
        description="This can't be undone. Any invoices linked to this client will keep their record, but you won't be able to bill them again until you re-add them."
        confirmLabel="Delete"
        onConfirm={confirmDelete}
        isConfirming={isDeleting}
      />
    </div>
  )
}
