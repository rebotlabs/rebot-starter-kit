import InputError from "@/components/input-error"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import AuthLayout from "@/layouts/auth-layout"
import { Head, useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import slugify from "slugify"

type FormData = {
  name: string
  slug: string
}

export default function CreateOrganization() {
  const { data, setData, errors, post, processing } = useForm<FormData>({
    name: "",
    slug: "",
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()

    post(route("onboarding.organization.store"))
  }

  return (
    <AuthLayout title="Create organization" description="Create your own organization.">
      <Head title="Create an organization" />

      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <div className="grid gap-2">
            <Label htmlFor="name">Name</Label>
            <Input
              id="name"
              type="text"
              name="name"
              placeholder="Acme Corporation"
              autoComplete="current-name"
              value={data.name}
              autoFocus
              onChange={(e) => {
                setData("name", e.target.value)
                setData("slug", slugify(e.target.value, { lower: true, trim: true, strict: true }))
              }}
            />

            <InputError message={errors.name} />
          </div>
          <div className="grid gap-2">
            <Label htmlFor="slug">Slug</Label>
            <Input
              id="slug"
              type="text"
              name="slug"
              placeholder="acme-corporation"
              autoComplete="current-slug"
              value={data.slug}
              onChange={(e) => setData("slug", e.target.value)}
            />

            <InputError message={errors.slug} />
          </div>

          <div className="flex items-center">
            <Button type="submit" className="w-full" disabled={processing}>
              {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
              Create organization
            </Button>
          </div>
        </div>
      </form>
    </AuthLayout>
  )
}
