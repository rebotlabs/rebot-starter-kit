import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Dialog, DialogClose, DialogContent, DialogDescription, DialogFooter, DialogTitle, DialogTrigger } from "@/components/ui/dialog"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import type { Organization } from "@/types"
import { useForm } from "@inertiajs/react"
import { type FormEventHandler, useRef } from "react"

interface LeaveOrganizationCardProps {
  organization: Organization
}

export function LeaveOrganizationCard({ organization }: LeaveOrganizationCardProps) {
  const passwordInput = useRef<HTMLInputElement>(null)

  const { data, setData, processing, reset, errors, clearErrors, post } = useForm<{
    password: string
  }>({
    password: "",
  })

  const leaveOrganization: FormEventHandler = (e) => {
    e.preventDefault()

    post(route("organization.settings.member.leave", [organization]), {
      preserveScroll: true,
      onError: () => passwordInput.current?.focus(),
      onFinish: () => reset(),
    })
  }

  const closeModal = () => {
    clearErrors()
    reset()
  }

  return (
    <Card variant="destructive">
      <CardHeader>
        <CardTitle>Leave organization</CardTitle>
        <CardDescription>Leave this organization and remove your access to all its resources</CardDescription>
      </CardHeader>

      <CardContent>
        <div className="relative space-y-0.5">
          <p className="font-medium">Warning</p>
          <p className="text-sm">Please proceed with caution, this cannot be undone.</p>
        </div>
      </CardContent>

      <CardFooter className="justify-end">
        <Dialog>
          <DialogTrigger asChild>
            <Button variant="destructive">Leave organization</Button>
          </DialogTrigger>
          <DialogContent>
            <DialogTitle>Are you sure you want to leave this organization?</DialogTitle>
            <DialogDescription>
              Once you leave <strong>{organization.name}</strong>, you will lose access to all its resources and will need to be re-invited to rejoin.
              Please enter your password to confirm you would like to leave this organization.
            </DialogDescription>
            <form className="space-y-6" onSubmit={leaveOrganization}>
              <div className="grid gap-2">
                <Label htmlFor="password" className="sr-only">
                  Password
                </Label>

                <Input
                  id="password"
                  type="password"
                  name="password"
                  ref={passwordInput}
                  value={data.password}
                  onChange={(e) => setData("password", e.target.value)}
                  placeholder="Password"
                  autoComplete="current-password"
                />

                <InputError message={errors.password} />
              </div>

              <DialogFooter className="gap-2">
                <DialogClose asChild>
                  <Button variant="link" onClick={closeModal}>
                    Cancel
                  </Button>
                </DialogClose>

                <Button variant="destructive" type="submit" disabled={processing}>
                  Leave organization
                </Button>
              </DialogFooter>
            </form>
          </DialogContent>
        </Dialog>
      </CardFooter>
    </Card>
  )
}
