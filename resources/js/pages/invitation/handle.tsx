import { InvitationCard } from "@/components/invitation/invitation-card"
import type { Invitation } from "@/types"
import { useTranslations } from "@/utils/translations"
import { Head } from "@inertiajs/react"

interface InvitationHandleProps {
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

export default function InvitationHandle({ invitation, existingUser, isAuthenticated, currentUserEmail }: InvitationHandleProps) {
  const { __ } = useTranslations()

  return (
    <>
      <Head title={__("invitations.page.title")} />

      <div className="bg-background flex min-h-screen items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
        <div className="w-full max-w-md space-y-8">
          <div className="text-center">
            <h1 className="text-foreground text-3xl font-bold">{__("invitations.page.youre_invited")}</h1>
            <p className="text-muted-foreground mt-2">
              {__("invitations.page.join_and_collaborate", { organization: invitation.organization.name })}
            </p>
          </div>

          <InvitationCard invitation={invitation} existingUser={existingUser} isAuthenticated={isAuthenticated} currentUserEmail={currentUserEmail} />

          <div className="text-center">
            <p className="text-muted-foreground text-sm">{__("invitations.footer.terms_agreement")}</p>
          </div>
        </div>
      </div>
    </>
  )
}
