import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectTrigger, SelectValue } from "@/components/ui/select"
import type { SharedData } from "@/types"
import { useForm, usePage } from "@inertiajs/react"
import { LoaderCircleIcon, SendHorizontalIcon } from "lucide-react"
import { type FormEventHandler, useRef } from "react"

type InviteForm = {
  email: string
  role: string
}

export const InviteUser = () => {
  const { currentOrganization } = usePage<SharedData>().props
  const emailInput = useRef<HTMLInputElement>(null)

  const { data, setData, errors, post, processing } = useForm<Required<InviteForm>>({
    email: "",
    role: "member",
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()
    emailInput.current?.focus()

    post(route("organization.settings.members.invite", [currentOrganization]), {
      preserveScroll: true,
      onSuccess: () => {
        setData("email", "")
        setData("role", "member")
      },
    })
  }

  return (
    <form onSubmit={submit} className="grid gap-2">
      <Label htmlFor="email">Invite by email address</Label>
      <div className="flex gap-2">
        <div className="grid flex-1 gap-2">
          <Input
            ref={emailInput}
            id="email"
            type="email"
            name="email"
            value={data.email}
            onChange={(e) => setData("email", e.target.value)}
            placeholder="test@example.com"
            required
            disabled={processing}
          />

          <InputError className="mt-2" message={errors.email} />
        </div>

        <div className="grid gap-2">
          <Select value={data.role} onValueChange={(value) => setData("role", value)} disabled={processing}>
            <SelectTrigger className="w-[100px]" aria-label="User's role">
              <SelectValue placeholder="Role" />
            </SelectTrigger>
            <SelectContent align="end">
              <SelectGroup>
                <SelectLabel>Role</SelectLabel>
                <SelectItem value="admin">Admin</SelectItem>
                <SelectItem value="member">Member</SelectItem>
              </SelectGroup>
            </SelectContent>
          </Select>

          <InputError className="mt-2" message={errors.role} />
        </div>

        <Button type="submit" disabled={processing}>
          {processing ? <LoaderCircleIcon className="animate-spin" /> : <SendHorizontalIcon />}
          Send invite
        </Button>
      </div>
    </form>
  )
}
