import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useLang } from "@/hooks/useLang"
import type { Invitation } from "@/types"
import { useForm } from "@inertiajs/react"
import { CheckCircleIcon, XCircleIcon } from "lucide-react"
import { type FormEventHandler } from "react"

type AcceptForm = {
  name: string
  password: string
  password_confirmation: string
}

interface InvitationRegisterFormProps {
  invitation: Invitation & {
    organization: {
      id: number
      name: string
    }
  }
  onReject: () => void
  rejectProcessing: boolean
}

export function InvitationRegisterForm({ invitation, onReject, rejectProcessing }: InvitationRegisterFormProps) {
  const { __ } = useLang()

  const {
    data: acceptData,
    setData: setAcceptData,
    post: postAccept,
    processing: acceptProcessing,
    errors: acceptErrors,
  } = useForm<AcceptForm>({
    name: "",
    password: "",
    password_confirmation: "",
  })

  const handleAccept: FormEventHandler = (e) => {
    e.preventDefault()
    postAccept(route("invitation.accept", [invitation.accept_token]))
  }

  return (
    <form onSubmit={handleAccept} className="space-y-4">
      <div>
        <Label htmlFor="name">{__("invitations.form.full_name")}</Label>
        <Input
          id="name"
          type="text"
          value={acceptData.name}
          onChange={(e) => setAcceptData("name", e.target.value)}
          required
          disabled={acceptProcessing}
          placeholder={__("invitations.form.full_name_placeholder")}
        />
        <InputError className="mt-2" message={acceptErrors.name} />
      </div>

      <div>
        <Label htmlFor="password">{__("invitations.form.password")}</Label>
        <Input
          id="password"
          type="password"
          value={acceptData.password}
          onChange={(e) => setAcceptData("password", e.target.value)}
          required
          disabled={acceptProcessing}
          placeholder={__("invitations.form.password_placeholder")}
        />
        <InputError className="mt-2" message={acceptErrors.password} />
      </div>

      <div>
        <Label htmlFor="password_confirmation">{__("invitations.form.confirm_password")}</Label>
        <Input
          id="password_confirmation"
          type="password"
          value={acceptData.password_confirmation}
          onChange={(e) => setAcceptData("password_confirmation", e.target.value)}
          required
          disabled={acceptProcessing}
          placeholder={__("invitations.form.confirm_password_placeholder")}
        />
        <InputError className="mt-2" message={acceptErrors.password_confirmation} />
      </div>

      <div className="flex space-x-3">
        <Button type="submit" disabled={acceptProcessing} className="flex-1">
          <CheckCircleIcon className="mr-2 h-4 w-4" />
          {__("invitations.buttons.create_account_accept")}
        </Button>
        <Button type="button" variant="outline" onClick={onReject} disabled={rejectProcessing}>
          <XCircleIcon className="mr-2 h-4 w-4" />
          {__("invitations.buttons.reject")}
        </Button>
      </div>
    </form>
  )
}
