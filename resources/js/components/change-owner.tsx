import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"

export const ChangeOwner = () => {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Team Owner</CardTitle>
        <CardDescription>Manage owner of the team</CardDescription>
      </CardHeader>
      <CardContent>
        <div className="grid gap-4">
          <p className="text-muted-foreground text-sm">
            The team owner is the person who has full control over the team, including billing and settings.
          </p>
        </div>
      </CardContent>
    </Card>
  )
}
