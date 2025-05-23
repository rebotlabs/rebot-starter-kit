import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { TeamSettingsLayout } from "@/layouts/app/team-settings-layout"
import type { Team } from "@/types"
import { Transition } from "@headlessui/react"
import { Head, useForm } from "@inertiajs/react"
import type { FormEventHandler } from "react"

type TeamForm = {
  name: string
  slug: string
}

export default function TeamSettingsGeneral({ team }: { team: Team }) {
  const { data, setData, patch, errors, processing, recentlySuccessful } = useForm<Required<TeamForm>>({
    name: team.name,
    slug: team.slug,
  })

  const submit: FormEventHandler = (e) => {
    e.preventDefault()

    patch(route("team.settings.update", [team]), {
      preserveScroll: true,
    })
  }

  return (
    <TeamSettingsLayout>
      <Head title="General" />
      <form onSubmit={submit}>
        <Card>
          <CardHeader>
            <CardTitle>General information</CardTitle>
            <CardDescription>Update your team information</CardDescription>
          </CardHeader>

          <CardContent className="space-y-6">
            <div className="grid gap-2">
              <Label htmlFor="name">Team name</Label>

              <Input
                id="name"
                className="mt-1 block w-full"
                value={data.name}
                onChange={(e) => setData("name", e.target.value)}
                required
                autoComplete="off"
                placeholder="Team name"
              />

              <InputError className="mt-2" message={errors.name} />
            </div>

            <div className="grid gap-2">
              <Label htmlFor="slug">Team slug</Label>

              <Input
                id="slug"
                className="mt-1 block w-full"
                value={data.slug}
                onChange={(e) => setData("slug", e.target.value)}
                required
                autoComplete="off"
                placeholder="Team slug"
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
    </TeamSettingsLayout>
  )
}
