import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Separator } from "@/components/ui/separator"
import { useLang } from "@/hooks/useLang"
import type { Invitation } from "@/types"
import { useForm } from "@inertiajs/react"
import { MailIcon } from "lucide-react"
import { useState } from "react"

import { InvitationLoginForm } from "./invitation-login-form"
import { InvitationRegisterForm } from "./invitation-register-form"
import { InvitationViewMode } from "./invitation-view-mode"

interface InvitationCardProps {
  invitation: Invitation & {
    organization: {
      id: number
      name: string
    }
  }
  existingUser: boolean
  isAuthenticated: boolean
  currentUserEmail?: string
}

export function InvitationCard({ invitation, existingUser, isAuthenticated, currentUserEmail }: InvitationCardProps) {
  const [mode, setMode] = useState<"view" | "register" | "login">("view")
  const { __ } = useLang()

  const { post: postAccept, processing: acceptProcessing } = useForm()
  const { post: postReject, processing: rejectProcessing } = useForm()

  const handleAccept = () => {
    if (existingUser && (!isAuthenticated || currentUserEmail !== invitation.email)) {
      setMode("login")
      return
    }

    postAccept(route("invitation.accept", [invitation.accept_token]))
  }

  const handleReject = () => {
    postReject(route("invitation.reject", [invitation.reject_token]))
  }

  return (
    <Card>
      <CardHeader className="text-center">
        <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
          <MailIcon className="h-6 w-6 text-blue-600" />
        </div>
        <CardTitle>{__("invitations.details.title")}</CardTitle>
        <CardDescription>{__("invitations.details.invited_as_role", { role: invitation.role })}</CardDescription>
      </CardHeader>

      <CardContent className="space-y-6">
        <div className="space-y-2 text-center">
          <p className="text-muted-foreground text-sm">{__("invitations.details.organization")}</p>
          <p className="font-semibold">{invitation.organization.name}</p>
          <p className="text-muted-foreground text-sm">{__("invitations.details.email")}</p>
          <p className="font-semibold">{invitation.email}</p>
          <p className="text-muted-foreground text-sm">{__("invitations.details.role")}</p>
          <p className="font-semibold capitalize">{invitation.role}</p>
        </div>

        <Separator />

        {mode === "view" && (
          <>
            {!existingUser && <InvitationRegisterForm invitation={invitation} onReject={handleReject} rejectProcessing={rejectProcessing} />}

            {existingUser && (
              <InvitationViewMode
                invitation={invitation}
                existingUser={existingUser}
                isAuthenticated={isAuthenticated}
                currentUserEmail={currentUserEmail}
                onAccept={handleAccept}
                onReject={handleReject}
                onLogin={() => setMode("login")}
                acceptProcessing={acceptProcessing}
                rejectProcessing={rejectProcessing}
              />
            )}
          </>
        )}

        {mode === "login" && <InvitationLoginForm invitation={invitation} onCancel={() => setMode("view")} />}
      </CardContent>
    </Card>
  )
}
