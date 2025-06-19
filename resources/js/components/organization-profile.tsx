import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import type { Organization } from "@/types"
import { useTranslations } from "@/utils/translations"
import { Transition } from "@headlessui/react"
import { useForm, usePage } from "@inertiajs/react"
import type { FormEventHandler } from "react"

type OrganizationForm = {
  name: string
  slug: string
}

export const OrganizationProfile = () => {
  const { __ } = useTranslations()
  const { organization } = usePage<{ organization: Organization }>().props

  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Required<OrganizationForm>>({
    name: organization.name,
    slug: organization.slug,
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()

    patch(route("organization.settings.update", [organization]), {
      preserveScroll: true,
    })
  }

  return (
    <form onSubmit={submit}>
      <Card>
        <CardHeader>
          <CardTitle>{__("organizations.settings.general_info")}</CardTitle>
          <CardDescription>{__("organizations.settings.update_info")}</CardDescription>
        </CardHeader>

        <CardContent className="space-y-6">
          <div className="grid gap-2">
            <Label htmlFor="name">{__("organizations.settings.name_label")}</Label>

            <Input
              id="name"
              className="mt-1 block w-full"
              value={data.name}
              onChange={(e) => setData("name", e.target.value)}
              required
              autoComplete="off"
              placeholder={__("organizations.settings.name_placeholder")}
            />

            <InputError className="mt-2" message={errors.name} />
          </div>

          <div className="grid gap-2">
            <Label htmlFor="slug">{__("organizations.settings.slug_label")}</Label>

            <Input
              id="slug"
              className="mt-1 block w-full"
              value={data.slug}
              onChange={(e) => setData("slug", e.target.value)}
              required
              autoComplete="off"
              placeholder={__("organizations.settings.slug_placeholder")}
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
            <p className="text-muted-foreground text-sm">{__("ui.actions.saved")}</p>
          </Transition>
          <Button disabled={processing}>{__("ui.actions.save")}</Button>
        </CardFooter>
      </Card>
    </form>
  )
}
