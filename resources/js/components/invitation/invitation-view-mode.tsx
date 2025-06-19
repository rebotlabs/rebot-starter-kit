import { Alert, AlertDescription } from "@/components/ui/alert"
import { Button } from "@/components/ui/button"
import { useLang } from "@/hooks/useLang"
import type { Invitation } from "@/types"
import { CheckCircleIcon, LockIcon, UserIcon, XCircleIcon } from "lucide-react"

interface InvitationViewModeProps {
  invitation: Invitation & {
    organization: {
      id: number
      name: string
    }
  }
  existingUser: boolean
  isAuthenticated: boolean
  currentUserEmail?: string
  onAccept: () => void
  onReject: () => void
  onLogin: () => void
  acceptProcessing: boolean
  rejectProcessing: boolean
}

export function InvitationViewMode({
  invitation,
  existingUser,
  isAuthenticated,
  currentUserEmail,
  onAccept,
  onReject,
  onLogin,
  acceptProcessing,
  rejectProcessing,
}: InvitationViewModeProps) {
  const { __ } = useLang()

  const canDirectlyAccept = !existingUser || (isAuthenticated && currentUserEmail === invitation.email)

  return (
    <div className="space-y-4">
      {existingUser && !isAuthenticated && (
        <Alert>
          <UserIcon className="h-4 w-4" />
          <AlertDescription>{__("invitations.alerts.account_exists")}</AlertDescription>
        </Alert>
      )}

      {existingUser && isAuthenticated && currentUserEmail !== invitation.email && (
        <Alert>
          <UserIcon className="h-4 w-4" />
          <AlertDescription>{__("invitations.alerts.different_email", { email: invitation.email })}</AlertDescription>
        </Alert>
      )}

      {canDirectlyAccept && existingUser && (
        <div className="flex space-x-3">
          <Button onClick={onAccept} disabled={acceptProcessing} className="flex-1">
            <CheckCircleIcon className="mr-2 h-4 w-4" />
            {__("invitations.buttons.accept_invitation")}
          </Button>
          <Button variant="outline" onClick={onReject} disabled={rejectProcessing}>
            <XCircleIcon className="mr-2 h-4 w-4" />
            {__("invitations.buttons.reject")}
          </Button>
        </div>
      )}

      {existingUser && (!isAuthenticated || currentUserEmail !== invitation.email) && (
        <div className="flex space-x-3">
          <Button onClick={onLogin} className="flex-1">
            <LockIcon className="mr-2 h-4 w-4" />
            {__("invitations.buttons.log_in_to_accept")}
          </Button>
          <Button variant="outline" onClick={onReject} disabled={rejectProcessing}>
            <XCircleIcon className="mr-2 h-4 w-4" />
            {__("invitations.buttons.reject")}
          </Button>
        </div>
      )}
    </div>
  )
}
