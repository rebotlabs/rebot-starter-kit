import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import AuthLayout from "@/layouts/auth-layout"
import { useTranslations } from "@/utils/translations"
import { Head, useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import slugify from "slugify"

type FormData = {
  name: string
  slug: string
}

export default function CreateOrganization() {
  const { __ } = useTranslations()
  const { data, setData, errors, post, processing } = useForm<FormData>({
    name: "",
    slug: "",
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()

    post(route("onboarding.organization.store"))
  }

  return (
    <AuthLayout title={__("organizations.create.title")} description={__("organizations.create.description")}>
      <Head title={__("organizations.create.page_title")} />

      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <div className="grid gap-2">
            <Label htmlFor="name">{__("organizations.create.name_label")}</Label>
            <Input
              id="name"
              type="text"
              name="name"
              placeholder={__("organizations.create.name_placeholder")}
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
            <Label htmlFor="slug">{__("organizations.create.slug_label")}</Label>
            <Input
              id="slug"
              type="text"
              name="slug"
              placeholder={__("organizations.create.slug_placeholder")}
              autoComplete="current-slug"
              value={data.slug}
              onChange={(e) => setData("slug", e.target.value)}
            />

            <InputError message={errors.slug} />
          </div>

          <div className="flex items-center">
            <Button type="submit" className="w-full" disabled={processing}>
              {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
              {__("organizations.create.create_button")}
            </Button>
          </div>
        </div>
      </form>

      <div className="mt-6 flex items-center">
        <div className="border-border flex-1 border-t"></div>
        <span className="text-muted-foreground px-4 text-sm">{__("organizations.select.or")}</span>
        <div className="border-border flex-1 border-t"></div>
      </div>

      <div className="mt-6 text-center">
        <TextLink href={route("logout")} method="post" className="text-sm">
          {__("ui.buttons.log_out")}
        </TextLink>
      </div>
    </AuthLayout>
  )
}
