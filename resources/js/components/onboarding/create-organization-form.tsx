import InputError from "@/components/input-error"
import TextLink from "@/components/text-link"
import { Button } from "@/components/ui/button"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { useTranslation } from "@/hooks/use-i18n"
import { useForm } from "@inertiajs/react"
import { LoaderCircle } from "lucide-react"
import slugify from "slugify"

type FormData = {
  name: string
  slug: string
}

export function CreateOrganizationForm() {
  const t = useTranslation()
  const { data, setData, errors, post, processing } = useForm<FormData>({
    name: "",
    slug: "",
  })

  const submit = (e: React.FormEvent) => {
    e.preventDefault()

    post(route("onboarding.organization.store"))
  }

  return (
    <>
      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <div className="grid gap-2">
            <Label htmlFor="name">{t("organizations.create.name_label")}</Label>
            <Input
              id="name"
              type="text"
              name="name"
              placeholder={t("organizations.create.name_placeholder")}
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
            <Label htmlFor="slug">{t("organizations.create.slug_label")}</Label>
            <Input
              id="slug"
              type="text"
              name="slug"
              placeholder={t("organizations.create.slug_placeholder")}
              autoComplete="current-slug"
              value={data.slug}
              onChange={(e) => setData("slug", e.target.value)}
            />

            <InputError message={errors.slug} />
          </div>

          <div className="flex items-center">
            <Button type="submit" className="w-full" disabled={processing}>
              {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
              {t("organizations.create.create_button")}
            </Button>
          </div>
        </div>
      </form>

      <div className="mt-6 flex items-center">
        <div className="border-border flex-1 border-t"></div>
        <span className="text-muted-foreground px-4 text-sm">{t("organizations.select.or")}</span>
        <div className="border-border flex-1 border-t"></div>
      </div>

      <div className="mt-6 text-center">
        <TextLink href={route("logout")} method="post" className="text-sm">
          {t("ui.buttons.log_out")}
        </TextLink>
      </div>
    </>
  )
}
