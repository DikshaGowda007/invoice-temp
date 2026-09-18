import { ArrowRight } from 'lucide-react'
import { Link } from 'react-router-dom'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import { useAuth } from '@/context/AuthContext'
import { ROUTES } from '@/utils/routePaths'

export default function HomePage() {
  const { user } = useAuth()

  return (
    <Card className="mx-auto max-w-lg">
      <CardHeader>
        <CardTitle>Welcome, {user?.name}</CardTitle>
      </CardHeader>
      <CardContent className="flex flex-col gap-3">
        <p className="text-sm text-muted-foreground">
          You&apos;re logged in. Start by adding the clients you invoice — the rest of the
          dashboard is coming next.
        </p>
        <Button render={<Link to={ROUTES.CLIENTS} />} className="self-start">
          Go to clients
          <ArrowRight data-icon="inline-end" size={15} />
        </Button>
      </CardContent>
    </Card>
  )
}
