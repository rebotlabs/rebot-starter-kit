import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import type { Organization } from "@/types"
import { Transition } from "@headlessui/react"
import { useForm, usePage } from "@inertiajs/react"
import type { FormEventHandler } from "react"

type OrganizationForm = {
  name: string
  slug: string
}

export const OrganizationProfile = () => {
  const { organization } = usePage<{ organization: Organization }>().props

  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Required<OrganizationForm>>({
    name: organization.name,
    slug: organization.slug,
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()

    patch(route("organization.settings.general.update", [organization]), {
      preserveScroll: true,
    })
  }

  return (
    <form onSubmit={submit}>
      <Card>
        <CardHeader>
          <CardTitle>General information</CardTitle>
          <CardDescription>Update your organization information</CardDescription>
        </CardHeader>

        <CardContent className="space-y-6">
          <div className="grid gap-2">
            <Label htmlFor="name">Organization name</Label>

            <Input
              id="name"
              className="mt-1 block w-full"
              value={data.name}
              onChange={(e) => setData("name", e.target.value)}
              required
              autoComplete="off"
              placeholder="Organization name"
            />

            <InputError className="mt-2" message={errors.name} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="slug">Organization slug</Label>

            <Input
              id="slug"
              className="mt-1 block w-full"
              value={data.slug}
              onChange={(e) => setData("slug", e.target.value)}
              required
              autoComplete="off"
              placeholder="Organization slug"
            />

            <InputError className="mt-2" message={errors.slug} />
          </div>
        </CardContent>

        <CardFooter>
          <Transition
            show={recentlySuccessful}
            enter="transition ease-in-out"
            enterFrom="opacity-0"
            leave="transition ease-in-out"
            leaveTo="opacity-0"
          >
            <p className="text-muted-foreground text-sm">Saved</p>
          </Transition>
          <Button disabled={processing}>Save</Button>
        </CardFooter>
      </Card>
    </form>
  )
}
